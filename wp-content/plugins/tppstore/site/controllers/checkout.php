<?php


class TppStoreControllerCheckout extends TppStoreAbstractBase {

    private $store = null;

    private $store_id = 0;

    protected $gateway = null;

    protected $gateways = array();

    protected $conversion_rates = array();

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


       // include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
       // $this->gateway = new TppStoreAdapterPaypal();
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


                case 'oneoffpayment':
                    $this->_makeOneOffPayment();
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




        if (filter_input(INPUT_POST, 'agree_newsletter', FILTER_SANITIZE_NUMBER_INT) == 1) {
            $signup = $this->getEmailSignupModel();

            $signup->setData(array(
                'email'         =>  $user->email,
                'source'        =>  'checkout',
                'user_id'       =>  $user->user_id,
                'first_name'    =>  $user->first_name,
                'last_name'     =>  $user->last_name
            ))->save();
        }
/*
        include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';

        $gateway = new TppStoreAdapterPaypal();
*/


        //save the store_id into a session so that we can determine the store that this person is paying for!




        $this->gateway->setOrder($this->store->store_id);

        if ($this->gateway->getTotal() > 0) {
            $data = $this->gateway->process();
        } else {
            $data = array(
                'status'    =>  'complete',
                'message'   =>  'free checkout',
                'redirect'  =>  'free_checkout'
            );
        }


        //determine if order status and complete eth actions as neccessary

        //add the order to the database!


        $this->generateConversionRates();

        $this->saveOrderAndPayment($data, $user);


    }

    private function generateConversionRates()
    {
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

            if (is_serialized($conversion_rates)) {
                $conversion_rates = unserialize($conversion_rates);
            }

            if (is_array($conversion_rates) && isset($conversion_rates[$this->gateway->currency])) {
                $conversion_rates = $conversion_rates[$this->gateway->currency];
            } else {
                $conversion_rates = 1;
            }

        } else {
            $conversion_rates = 1;
        }

        $this->conversion_rates = $conversion_rates;
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

        mail('parsolee@gmail.com', 'order details', print_r($order, true));

        if ($order->order_type == 'default') {
            $this->gateway->setOrder($order->store_id);
        } else {
            $this->gateway->setUpOneOffPaymentOrder($this->getStoreModel()->getStoreByID($order->store_id), array(
                'total'     =>  $order->total,
                'currency'  =>  $order->currency
            ));
        }


        if ($this->gateway->getTotal() > 0) {
            $payment_data = $this->gateway->confirmPayment();
        } else {
            $payment_data = new stdClass();
            $payment_data->status = 'success';
            $payment_data->gateway_data = null;
            $payment_data->payment_message = 'free checkout';
        }



        if (strtolower($payment_data->status) != 'success') {
            $order->setData(array(
                    'status'        =>  $payment_data->status,
                    'message'       =>  'received back from gateway: failed payment - see payment message. Payment status: ' . $payment_data->status
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



            if ($this->gateway->getTotal() > 0) {

            //update the order to say it is complete!
             //update the order data!
            $order->setData(array(
                    'status'        =>  'complete',
                    'message'       =>  'received back from gateway, payment complete - see payment message for more information'
                    //'commission'    =>  $payment_data->store_owner_payment->receiver->amount
                    //commission already accounted for
                )
            )->save();
        }

        $payment->setData(array(
            'gateway_data'  =>  serialize($payment_data),
            'status'        =>  $payment_data->payment_status,
            'message'       =>  $payment_data->payment_message

        ))->save();


        //remove the store and its products from the cart
        TppStoreModelCart::getInstance()->removeStore($order->store_id);
        $this->deleteCheckoutDataSession();


        $user = $this->getUserModel()->setData(array(
            'user_id'   =>  $order->user_id
        ))->getUserByID();

        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'  =>  $order->order_id
        ))->getLineItems(true);

        $store = $this->getStoreModel()->setData(array(
            'store_id'  =>  $order->store_id
        ))->getStoreByID();



        //delete the store from the cart

        //update the products quantity!
        $_product = TppStoreModelProduct::getInstance();
        $product_ids = array();

        $product_quantity_warn = array();

        $email_product_groups = array();

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

                if (!isset($email_product_groups[$_product->product_type])) {
                    $email_product_groups[$_product->product_type] = array();
                }
                $email_product_groups[$_product->product_type][] = $product;
            }

        }


        $this->getUserDiscountModel()->setData(array(
            'user_id'       =>  $user->user_id,
            'product_ids'   =>  $product_ids
        ))->incrementUses();



        foreach ($email_product_groups as $type => $products) {
            //send confirmation emails
            ob_start();

            switch ($type) {
                case '1':
                    //download
                    include TPP_STORE_PLUGIN_DIR . 'emails/order/confirm_customer_download.php';

                    $subject = 'Instant Download';

                    break;
                case '2':
                    //service
                case '3':
                    //product
                    include TPP_STORE_PLUGIN_DIR . 'emails/order/confirm_customer_product.php';

                $subject = 'Purchase';

                    break;

                case '5':
                    //event
                    include TPP_STORE_PLUGIN_DIR . 'emails/order/confirm_customer_event.php';

                    $subject = 'Event / Workshop';

                    break;

                case '4':
                    //mentor session
                    include TPP_STORE_PLUGIN_DIR . 'emails/order/confirm_customer_mentor_session.php';
                    $subject = 'Mentor Session';

                    break;

                default:

                    if ($order->order_type == 'oneoff') {
                        include TPP_STORE_PLUGIN_DIR . 'emails/order/confirm_customer_oneoff.php';
                        $subject = 'one off payment';
                    }

                    break;
            }

            $body = ob_get_contents();

            $this->sendMail($user->email, 'Your order was successful: ' . $subject, $body);

            ob_end_clean();

        }

        unset($email_product_groups);
        unset($products);


        //send confirmation to store owner
        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/order_confirm_store_owner.php';

        $body = ob_get_contents();

        $store->getUser();

        ob_end_clean();

        $this->sendMail($store->getUser()->email,  $user->first_name . ' has placed an order on your store', $body);


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

        $url = $user->user_type == 'store_owner'?'dashboard':'myaccount';

        $this->redirect('/shop/' . $url . '/purchase/' . $order->order_id);


       // include TPP_STORE_PLUGIN_DIR . 'site/views/checkout/success.php';

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


    /*
     * Saves the current order and payment into the database
     * @param array $data the data returned from the payment gateway
     * @param string $order_type one from the enum list on the database table
     * @param object $user the current logged in user object
     * @param $generic_data any information to be stored for usefulness later
     */
    private function saveOrderAndPayment($data = array(), $user, $order_type = 'default', $generic_data = array())
    {
        $order = TppStoreModelOrder::getInstance()->setData(array(
            'store_id'          =>  $this->store->store_id,
            'currency'          =>  $this->gateway->currency,
            'gateway'           =>  $this->gateway->code,
            'status'            =>  $data['status'],
            'message'           =>  $data['message'] == ''?null:$data['message'],
            'exchange_rates'    =>  $this->conversion_rates,
            'total'             =>  $this->gateway->getTotal(),
            'commission'        =>  $this->gateway->getCommission(),
            'discount'          =>  $this->gateway->discount,
            'tax'               =>  $this->gateway->tax,
            'order_type'        =>  $order_type
        ));

        $order_saved = $order->save();

        if ($order_saved && $order_type == 'oneoff') {
            $this->getOrderInfoModel()->setData(array(
                'order_id'  =>  $order->order_id,
                'data'      =>  $generic_data
            ))->save();
        }

        if ($order_type == 'default') {
            $order_data = $this->getOrderData($order->store_id);
        } else {
            $order_data = new stdClass();

            $product = new stdClass();

            $product->store_id = $this->store->store_id;
            $product->discount = 0;
            $product->price = $this->gateway->getTotal();
            $product->order_quantity = 1;
            $product->price_includes_tax = 1;
            $product->line_total = $product->price;
            $product->product_title = 'One off payment';

            $order_data->products = array(
                $product
            );



            $order_data->store_id = $this->store->store_id;



        }

        //get the products from the order and save it into the order items
        $order_items = $this->getOrderItemsModel()->setData(array(
            'order_id'      =>  $order->order_id
        ))->setData($order_data)->save();

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

        if ($data['redirect'] == 'free_checkout') {

            $this->sendMail('parsolee@gmail.com', 'payment', 'order: ' . $order->order_id . ' , geo currency: ' . geo::getInstance()->getCurrency() . ', order currency: ' . $order->currency . ', Amount: ' . $order->total . ', commission: ' . $order->commission);
            $this->setCheckoutSessionData($order, $payment);


            $this->confirmPayment();

        } elseif (false === $data['redirect']) {

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


            $this->sendMail('parsolee@gmail.com', 'payment', 'order: ' . $order->order_id . ' , geo currency: ' . geo::getInstance()->getCurrency() . ', order currency: ' . $order->currency . ', Amount: ' . $order->total . ', commission: ' . $order->commission);
            $this->setCheckoutSessionData($order, $payment);


            $this->redirect($data['redirect'], false);

        }
    }

    /*
     * Makes a one off payment to the selected store!
     */
    public function _makeOneOffPayment()
    {

        if (false === $user = TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            $this->redirectToLogin('redirect=' . urlencode('/shop/cart'));
        }


        if (intval($this->store->store_id) > 1) {

            //determine if the payment information is set?
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_STRING);

            $amount = floatval($amount);

            if ($amount == 0) {
                $this->redirect('/shop/oneoffpayment/');
            }

            $notes = filter_input(INPUT_POST, 'notes', FILTER_UNSAFE_RAW);

            $reference = filter_input(INPUT_POST, 'reference', FILTER_UNSAFE_RAW);

            $this->gateway->setUpOneOffPaymentOrder($this->store, array(
                'total'     =>  $amount,
                'currency'  =>  filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_STRING)
            ));



            $data = $this->gateway->process();

            $this->generateConversionRates();

            $this->saveOrderAndPayment($data, $user, 'oneoff', array(
                'notes'     =>  $notes,
                'reference' =>  $reference
            ));


        } else {
            $this->redirect('/shop/oneoffpayment/');
        }

        exit;
    }

}