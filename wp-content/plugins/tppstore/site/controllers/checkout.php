<?php

class TppStoreControllerCheckout extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
//        add_action( 'template_redirect', function() {
//            TppStoreControllerCheckout::getInstance()->templateRedirect();
//        } );


        add_rewrite_rule('shop/checkout/([^/]+)?', 'index.php?tpp_pagename=tpp_checkout&tpp_checkout_method=$matches[1]', 'top');
        add_rewrite_rule('shop/checkout/?', 'index.php?tpp_pagename=tpp_checkout', 'top');



        //flush_rewrite_rules(true);
    }


    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        $method = get_query_var('tpp_checkout_method');

        if ($pagename == 'tpp_checkout') {


            add_filter( 'wp_title', function() {TppStoreAbstractBase::pageTitle('Checkout');}, 10, 2);
            $this::$_meta_description = 'Checkout';

            switch ($method) {
                case 'payment':



                    $store_id = $this->validateStoreId();
                    $this->_setWpQueryOk();

                    $this->processPayment($store_id);

                    break;

                case 'success':
                    $this->_setWpQueryOk();

                    //get the transaction information

                    $this->confirmPayment();


                    break;
                case 'cancel':


                    $this->_setWpQueryOk();

                    //get the transaction information

                    $this->cancelledPayment();

                    break;

                default:

                    //process

                    $store_id =  $this->validateStoreId();

                    if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                        $this->_setWpQueryOk();
                        $this->processPayment($store_id);
                    } else {
                        $this->redirectToLogin('redirect=' . urlencode('/shop/checkout'));
                    }


//                    $this->renderCheckout($store_id);
                    break;
            }


            exit;

        }
    }

    private function validateStoreId()
    {
        $store_id = filter_input(INPUT_POST, 'store', FILTER_SANITIZE_NUMBER_INT);

        if (intval($store_id) > 0) {
            $store = $this->getStoreModel()->setData(array('store_id'   =>  $store_id))->getStoreByID();

            if (intval($store->store_id) <= 0) {
                $this->redirect('/shop/cart');
            }
        } else {
            $this->redirect('/shop/cart');
        }

        return $store_id;
    }

    private function processPayment($store_id = 0)
    {

        if (false === $user = TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            $this->redirectToLogin('redirect=' . urlencode('/shop/cart'));
        }

        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';

        $gateway = new TppStoreAdapterPaypal();

        $gateway->setOrder($store_id);

        $data = $gateway->process();

        //determine if order status and complete eth actions as neccessary

        //add the order to the database!

        $order = TppStoreModelOrder::getInstance()->setData(array(
            'store_id'      =>  $store_id,
            'total'         =>  $gateway->getTotal(),
            'commission'    =>  $gateway->getCommission(),
            'gateway'       =>  'paypal',
            'status'        =>  $data->status,
            'message'       =>  $data->message?:'pending'
        ));

        $order_saved = $order->save();

        $payment_saved = false;

        if (true === $order_saved) {
            $payment = $order->getPaymentModel()->setData(array(
                'order_id'      =>  $order->order_id,
                'amount'        =>  $order->total,
                'status'        =>  $order->status,
                'user_data'     =>  array(
                    'user_id'       =>  $user->user_id,
                    'first_name'    =>  $user->first_name,
                    'last_name'     =>  $user->last_name,
                    'email'         =>  $user->email
                ),
                'gateway_data'  =>  serialize($data),
                'gateway'       =>  'paypal'
            ));
            $payment_saved = $payment->save();
        }

        if (false === $order_saved || false === $payment_saved) {

            $title = 'Unable to process your payment';

            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }

        if (false === $data['redirect']) {

//            $order->setData(array(
//                'status'    =>  'failed'
//            ))->save();

//            $payment->setData(array(
//                'status'    =>  'failed'
//            ))->save();

            $title = 'Unable to process your payment';

            TppStoreMessages::getInstance()->addMessage('error', array('payment_error' => $data['message']));

            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;


        } else {

            //pending payment
            $this->setCheckoutSessionData($order, $payment);

//            $order->setData(array(
//                'status'    =>  'pending'
//            ))->save();
//
//            $payment->setData(array(
//                'status'    =>  'pending'
//            ))->save();

            $this->redirect($data['redirect']);

        }


    }

    private function confirmPayment()
    {

        if (false === $user = TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            $this->redirectToLogin('redirect=' . urlencode('/shop/cart'));
        }

        $order_id = $this->getOrderIdFromSession();
        $payment_id = $this->getPaymentIdFromSession();


        if (intval($order_id) < 1 || intval($payment_id) < 1) {
            $this->deleteCheckoutDataSession();
            $this->redirect('/shop/myaccount/orders');

        }
       $payment = $this->getPaymentModel()->setData(array(
            'payment_id'  =>  $payment_id
        ));

        $order = $this->getOrderModel()->setData(array(
            'order_id'  =>  $order_id
        ));

        $order->getOrderById();

        $payment->getPaymentById();

        if (intval($payment->payment_id) < 1 || intval($order->order_id) < 1) {

            $this->deleteCheckoutDataSession();

            $this->redirect('/shop/myaccount/orders');

        }

        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';

        $gateway = new TppStoreAdapterPaypal();

        $payment_data = $gateway->confirmPayment();

        //get the products from the order and save it into the order items
        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'      =>  $order->order_id
        ))->setData($this->getOrderData($order->store_id))->save();

        if (strtolower($payment_data->status) != 'success') {
            $order->setData(array(
                    'status'        =>  $payment_data->status
                )
            )->save();

            $payment->setData(array(
                'status'        =>  $payment_data->status,
                'gateway_data'  =>  serialize($payment_data)
            ))->save();

            $this->deleteCheckoutDataSession();
            $this->redirect('/shop/myaccount/orders');

        }


        //update the order to say it is complete!
         //update the order data!
        $order->setData(array(
                'status'        =>  'complete',
                'commission'    =>  $payment_data->store_owner_payment->receiver->amount
            )
        )->save();

        $payment->setData(array(
            'status'        =>  'paid',
            'gateway_data'  =>  serialize($payment_data)
        ))->save();

        //send confirmation email

        $user = $this->getUserModel()->setData(array(
            'user_id'   =>  $order->user_id
        ))->getUserByID();

        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'  =>  $order->order_id
        ))->getLineItems();

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/order_confirm_customer.php';

        $body = ob_get_contents();

        $this->sendMail($user->email, 'Your order was successful', $body);

        ob_end_clean();

        //delete the store from the cart

        //update the products quantity!
        $_product = TppStoreModelProduct::getInstance();

        foreach ($order_items->products as $product) {
            $_product->setData(array(
                'product_id'    =>  $product->product_id
            ))->getProductById();

            $_product->quantity_available = $_product->quantity_available - $product->order_quantity;

            $_product->updateQuantity();
        }


        //remove the store and its products from the cart
        TppStoreModelCart::getInstance()->removeStore($order->store_id);
        $this->deleteCheckoutDataSession();

        include TPP_STORE_PLUGIN_DIR . 'site/views/checkout/success.php';

        exit;
    }


    public function cancelledPayment()
    {
        if (false === $user = TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            $this->redirectToLogin('redirect=' . urlencode('/shop/cart'));
        }

        $order_id = $this->getOrderIdFromSession();
        $payment_id = $this->getPaymentIdFromSession();

        if (intval($order_id) < 1 || intval($payment_id) < 1) {
            $this->deleteCheckoutDataSession();
            $this->redirect('/shop/myaccount/orders');
        }

        $payment = $this->getPaymentModel()->setData(array(
            'payment_id'  =>  $payment_id
        ));
        $order = $this->getOrderModel()->setData(array(
            'order_id'  =>  $order_id
        ));

        $order->getOrderById();

        $payment->getPaymentById();

        if (intval($payment->payment_id) < 1 || intval($order->order_id) < 1) {
            $this->deleteCheckoutDataSession();
            $this->redirect('/shop/myaccount/orders');
        }

        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';

        $gateway = new TppStoreAdapterPaypal();

        $gateway->cancel();

        $order->setData(array(
            'status'    =>  'cancelled'
        ))->save();
        $payment->setData(array(
            'status'    =>  'cancelled'
        ))->save();

        $this->deleteCheckoutDataSession();

        include TPP_STORE_PLUGIN_DIR . 'site/views/checkout/cancelled.php';

        exit;
    }

    private function setCheckoutSessionData(TppStoreModelOrder $order, TppStoreModelPayment $payment)
    {
        $_SESSION['tpp_store_checkout_temp']['order_id'] = $order->order_id;
        $_SESSION['tpp_store_checkout_temp']['payment_id'] = $payment->payment_id;
    }

    private function getOrderIdFromSession()
    {
        return $_SESSION['tpp_store_checkout_temp']['order_id'];
    }

    private function getPaymentIdFromSession()
    {
        return $_SESSION['tpp_store_checkout_temp']['payment_id'];
    }

    private function deleteCheckoutDataSession()
    {
        $_SESSION['tpp_store_checkout_temp'] = null;
        unset($_SESSION['tpp_store_checkout_temp']);
    }

    private function renderCheckout($store_id = 0)
    {

        wp_enqueue_script('jquery');

        include TPP_STORE_PLUGIN_DIR . '/site/views/checkout/default.php';
    }

    private function getOrderData($store_id)
    {
        $cart = TppStoreModelCart::getInstance()->getCart(true);

        $obj = new stdClass();

        //get the product ids, load them from the products table to get the most recent information

        $product_ids = array();

        foreach ($cart['stores'][$store_id]['products'] as $product) {
            $product_ids[] = $product->product_id;
        }

        $products = $this->getProductsModel()->getProductsByIDs($product_ids, false);

        unset($product_ids);

        $obj->products = $products;

        $obj->store_id = $store_id;

        return $obj;
    }

}