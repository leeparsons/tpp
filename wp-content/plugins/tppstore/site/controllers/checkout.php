<?php

class TppStoreControllerCheckout extends TppStoreAbstractBase {

    private $store = null;

    private $store_id = 0;

    protected $gateway = null;

    protected $gateways = array();

    protected function __construct()
    {

        $this->store = new TppStoreModelStore();

    }

    protected function __initialise()
    {


        $this->validateStoreId();

        if (strtolower($this->store->paypal_email) == 'rosie@rosieparsons.com' || strtolower($this->store->paypal_email) == 'parsolee@gmail.com') {
            include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/direct.php';
            $this->gateway = new TppStoreAdapterPaypalDirect();
        } else {
            include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/parallel.php';
            $this->gateway = new TppStoreAdapterPaypalParallel();
        }


//        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
//        $this->gateway = new TppStoreAdapterPaypal();
    }

    public function applyRewriteRules()
    {
        add_rewrite_rule('shop/checkout/([^/]+)?', 'index.php?tpp_pagename=tpp_checkout&tpp_checkout_method=$matches[1]', 'top');
        add_rewrite_rule('shop/checkout/?', 'index.php?tpp_pagename=tpp_checkout', 'top');
    }


    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        $method = get_query_var('tpp_checkout_method');


        if ($pagename == 'tpp_checkout') {

            $this->__initialise();

            $this->pageTitle('Checkout');
            $this->setPageDescription('Checkout');

            switch ($method) {
                case 'payment':


                    $this->_setWpQueryOk();

                    $this->processPayment();

                    break;

                case 'success':
                    $this->_setWpQueryOk();
                    $this->confirmPayment();


                    break;
                case 'cancel':

                    $this->_setWpQueryOk();

                    $this->cancelledPayment();

                    break;

                default:

                    //process

                    if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                        $this->_setWpQueryOk();
                        $this->processPayment();
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

        $this->store_id = filter_input(INPUT_POST, 'store', FILTER_SANITIZE_NUMBER_INT);

        if (intval($this->store_id) == 0) {
            $this->loadCartStoreFromSession();
        }

        if (intval($this->store_id) > 0) {
            $this->store->setData(array('store_id'   =>  $this->store_id))->getStoreByID();

            if (intval($this->store->store_id) <= 0) {
                $this->redirect('/shop/cart');
            }
        } else {
            $this->redirect('/shop/cart');
        }

        $this->saveCartStoreToSession($this->store->store_id);

    }

    private function processPayment()
    {

        if (false === $user = TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            $this->redirectToLogin('redirect=' . urlencode('/shop/cart'));
        }




/*
        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';

        $gateway = new TppStoreAdapterPaypal();
*/


        //save the store_id into a session so that we can determine the store that this person is paying for!




        $this->gateway->setOrder($this->store->store_id);

        $data = $this->gateway->process();

        //determine if order status and complete eth actions as neccessary

        //add the order to the database!


        if ($this->gateway->currency != 'GBP') {

            if (!$this->gateway instanceof TppStoreAdapterPaypal) {
                if (!class_exists(TppStoreAdapterPaypal)) {
                    include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
                }
                $gateway2 = new TppStoreAdapterPaypal();
            } else {
                $gateway2 = $this->gateway;
            }

            $gateway2->generateExchangeRates();
            $conversion_rates = TppCacher::getInstance()->readCache();
        } else {
            $conversion_rates = NULL;
        }

        $order = TppStoreModelOrder::getInstance()->setData(array(
            'store_id'          =>  $this->store->store_id,
            'currency'          =>  $this->gateway->currency,
            'gateway'           =>  $this->gateway->code,
            'status'            =>  $data['status'],
            'message'           =>  $data['message'] == ''?null:$data['message'],
            'exchange_rates'    =>  $conversion_rates,
            'total'             =>  $this->gateway->getTotal(),
            'commission'        =>  $this->gateway->getCommission(),
            'discount'          =>  $this->gateway->discount,
            'tax'               =>  $this->gateway->tax
        ));

        $order_saved = $order->save();

        //get the products from the order and save it into the order items
        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'      =>  $order->order_id
        ))->setData($this->getOrderData($order->store_id))->save();

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
                'gateway'       =>  $this->gateway->code
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


            TppStoreMessages::getInstance()->addMessage('error', array('payment_error' => 'There was an error completing your purchase. Please contact us quoting your reference: ' . $order->ref . ' and we will assist you in completing your purchase.'));
            TppStoreMessages::getInstance()->saveToSession();

            $this->redirect('/shop/cart/');

           // include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            //exit;


        } else {


//            $order->setData(array(
//                'status'    =>  'pending'
//            ))->save();
//
//            $payment->setData(array(
//                'status'    =>  'pending'
//            ))->save();

            //update the order to say it's processing at gateway

            $order->setData(array(
                'message'   =>  'sending to payment gateway'
            ))->save();




            $this->setCheckoutSessionData($order, $payment);


            $this->redirect($data['redirect'], false);

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
            $this->redirect('/shop/myaccount/purchases/');

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

            $this->redirect('/shop/myaccount/purchases/');

        }



        $this->gateway->setOrder($order->store_id);

        $payment_data = $this->gateway->confirmPayment();



        if (strtolower($payment_data->status) != 'success') {
            $order->setData(array(
                    'status'        =>  $payment_data->status,
                    'message'       =>  'received back from gateway: failed payment - see payment message'
                )
            )->save();

            $payment->setData(array(
                'status'        =>  $payment_data->payment_status,
                'gateway_data'  =>  serialize($payment_data),
                'message'       =>  $payment_data->payment_message
            ))->save();



            $this->deleteCheckoutDataSession();

            TppStoreMessages::getInstance()->addMessage('error', 'There was an error taking your payment: ' . $payment_data->payment_message .  '. Please contact us with reference: ' . $order->ref);
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirect('shop/cart/');


        }


        //update the order to say it is complete!
         //update the order data!
        $order->setData(array(
                'status'        =>  'complete',
                'message'       =>  'received back from gateway, payment complete - see payment message for more information'
                //'commission'    =>  $payment_data->store_owner_payment->receiver->amount
                //commission already accounted for
            )
        )->save();

        $payment->setData(array(
            'gateway_data'  =>  serialize($payment_data),
            'status'        =>  $payment_data->payment_status,
            'message'       =>  $payment_data->payment_message

        ))->save();


        //remove the store and its products from the cart
        TppStoreModelCart::getInstance()->removeStore($order->store_id);
        $this->deleteCheckoutDataSession();

        //send confirmation email

        $user = $this->getUserModel()->setData(array(
            'user_id'   =>  $order->user_id
        ))->getUserByID();

        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'  =>  $order->order_id
        ))->getLineItems(true);

        $store = $this->getStoreModel()->setData(array(
            'store_id'  =>  $order->store_id
        ))->getStoreByID();

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/order_confirm_customer.php';

        $body = ob_get_contents();

        $this->sendMail($user->email, 'Your order was successful', $body);

        ob_end_clean();

        //delete the store from the cart

        //update the products quantity!
        $_product = TppStoreModelProduct::getInstance();
        $product_ids = array();

        $product_quantity_warn = array();

        if (is_array($order_items) && !empty($order_items)) {
            foreach ($order_items as $product) {
                $_product->setData(array(
                    'product_id'    =>  $product->product_id
                ))->getProductById();

                $original_stock = $_product->quantity_available;

                $_product->quantity_available = $_product->quantity_available - $product->order_quantity;

                if ($original_stock >= 4 && $_product->quantity_available < 4) {
                    $product_quantity_warn[] = $_product;
                }

                $_product->updateQuantity();

                $product_ids[] = $_product->product_id;
            }

        }


        $this->getUserDiscountModel()->setData(array(
            'user_id'       =>  $user->user_id,
            'product_ids'   =>  $product_ids
        ))->incrementUses();

        $store = $this->getStoreModel()->setData(array(
            'store_id'  =>  $order->store_id
        ))->getStoreByID();


        //send confirmation to store owner
        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/order_confirm_store_owner.php';

        $body = ob_get_contents();

        $store->getUser();

        $this->sendMail($store->getUser()->email,  $user->first_name . ' has placed an order on your store', $body);

        ob_end_clean();

        //send email for quantity notifications
        if (!empty($product_quantity_warn)) {
            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/product_low_stock_notification.php';

            $body = ob_get_contents();

            $this->sendMail($store->getUser()->email, 'Your stock is running low', $body);

            ob_end_clean();
        }


        //send to rosie
        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/order_confirm_tpp.php';

        $body = ob_get_contents();

        $this->sendMail('rosie@thephotographyparlour.com', 'An order has been placed', $body);

        ob_end_clean();

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
            $this->redirect('/shop/myaccount/purchases/');
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
            $this->redirect('/shop/myaccount/purchases/');
        }




        $this->gateway->cancel();

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

        $_SESSION['tpp_checkout_store'] = null;
        unset($_SESSION['tpp_checkout_store']);

        $_SESSION['tpp_store_checkout_temp'] = null;
        unset($_SESSION['tpp_store_checkout_temp']);
    }

    private function renderCheckout()
    {

        wp_enqueue_script('jquery');

        include TPP_STORE_PLUGIN_DIR . '/site/views/checkout/default.php';
    }

    private function getOrderData()
    {
        $cart = TppStoreModelCart::getInstance()->getCart(true);

        $obj = new stdClass();

        //get the product ids, load them from the products table to get the most recent information

        $product_ids = array();

        foreach ($cart['stores'][$this->store->store_id]['products'] as $product) {
            $product_ids[] = $product->product_id;
        }

        $products = $this->getProductsModel()->getProductsByIDs($product_ids, false);

        unset($product_ids);

        $obj->products = $products;

        foreach ($obj->products as &$product) {
            $product->discount = $cart['stores'][$this->store->store_id]['products'][$product->product_id]->discount;
            //overrides the defualt price with the converted price for this transaction
            $product->price = $cart['stores'][$this->store->store_id]['products'][$product->product_id]->getFormattedPrice(false);
            $product->order_quantity = $cart['stores'][$this->store->store_id]['products'][$product->product_id]->order_quantity;
            $product->line_total = $cart['stores'][$this->store->store_id]['products'][$product->product_id]->getLineItemFormattedTotal(false, true);
            $product->price_includes_tax = 1;
        }

        $obj->store_id = $this->store->store_id;

        return $obj;
    }

    private function saveCartStoreToSession()
    {
        $_SESSION['tpp_checkout_store'] = $this->store->store_id;
    }

    private function loadCartStoreFromSession()
    {
        if (isset($_SESSION['tpp_checkout_store'])) {
            $this->store_id = $_SESSION['tpp_checkout_store'];
        } else {
            $this->store_id = 0;
        }
    }


}