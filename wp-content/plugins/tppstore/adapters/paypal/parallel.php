<?php
/**
 * User: leeparsons
 * Date: 28/02/2014
 * Time: 14:28
 */

if (!class_exists('TppStorePaypalBase')) {
    include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/base.php';
}

class TppStoreAdapterPaypalParallel extends TppStorePaypalBase {

    public $code = 'paypal_parallel';
    public $id = 'paypal_parallel';

    public $sandbox_url = "https://api.sandbox.paypal.com/";

    public $live_url = "https://api-3t.paypal.com/";
    public $live_api_user = "rosie_api1.rosieparsons.com";
    public $live_api_password = "K5ULLT635CMGSQTY";
    public $live_api_signature = "AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3";
    public $live_checkout_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout";

    public $sandbox_api_user = "rosie_api1.rosieparsons.com";
    public $sandbox_api_password = "LR3X8R7NGPVYFFGD";
    public $sandbox_api_signature = "ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp";
    public $sandbox_checkout_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout";

    public $paypal_email = 'rosie@rosieparsons.com';

    protected $environment = 'live';

    private function sendCurl($end_point = 'Pay', $request_string = '', $count = 0)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->{$this->environment . '_url'} . $end_point);

        curl_setopt($ch, CURLOPT_POST, $count);

        if (substr($request_string, -1) == '&') {
            $request_string = substr($request_string, 0, -1);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PayPal-PHP-SDK');
        curl_setopt( $ch, CURLOPT_TIMEOUT, 60);
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10);





        $res = curl_exec($ch);


        curl_close($ch);

        $result = explode('&', $res);


        $token = '';
        $success = false;

        $data = new stdClass();

        $data->token = null;
        $data->status = 'pending';




        $message = '';

        foreach ($result as $tmpArr) {

            $tmp = explode('=', $tmpArr);
            if ($tmp[0] == 'TOKEN') {
                $data->token = urldecode($tmp[1]);
            } elseif ($tmp[0] == 'ACK') {
                $data->status = strtolower($tmp[1]);
            } elseif ($tmp[0] == 'L_SHORTMESSAGE0' || $tmp[0] == 'L_LONGMESSAGE0') {
                $data->message .= ($data->message == ''?", ":'') . urldecode($tmp[1]);
            } elseif (stripos($tmp[0], 'message') === false && stripos($tmp[0], 'error') === false) {
                $data->{$tmp[0]} = strtolower(urldecode($tmp[1]));
            }
        }

        if ($data->status == 'failure') {
            TppStoreLibraryLogger::getInstance()->add(0, 'paypal setExpressCheckout', $data->status, array(
                    'amount'    =>  $this->total,
                    'message'   =>  $data->message
                )
            );
        }

        return $data;

    }

    public function process() {

        $return = site_url() . '/shop/checkout/success/';
        $cancel_url = site_url() . '/shop/checkout/cancel/';
        $checkout_url = $this->{$this->environment . '_checkout_url'};



        //parallel

        $uid = uniqid();

        $fields = array(
            'USER'			                            =>	$this->{$this->environment . '_api_user'},
            'PWD'   		                            =>	$this->{$this->environment . '_api_password'},
            'SIGNATURE'                                 =>	$this->{$this->environment . '_api_signature'},
            'METHOD'									=>	'SetExpressCheckout',
            'CANCELURL'									=>	$cancel_url,
            'RETURNURL'									=>	$return,
            'VERSION'									=>	'93',
            'PAYMENTREQUEST_0_CURRENCYCODE'				=>	$this->currency,
            'PAYMENTREQUEST_0_AMT'					    =>	$this->to_pay,
            'PAYMENTREQUEST_0_ITEMAMT'                  =>  $this->to_pay,
            'PAYMENTREQUEST_0_TAXAMT'                   =>  0,
            'PAYMENTREQUEST_0_PAYMENTACTION'            =>  'Order',
            'PAYMENTREQUEST_0_DESC'                     =>  'Purchase from ' . $this->store_name,
            'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'    =>  $this->store_email,
            'PAYMENTREQUEST_0_PAYMENTREQUESTID'         =>  $uid . '_PAYMENT1',
            'PAYMENTREQUEST_1_CURRENCYCODE'				=>	$this->currency,
            'PAYMENTREQUEST_1_AMT'					    =>	$this->commission,
            'PAYMENTREQUEST_1_ITEMAMT'                  =>  $this->commission,
            'PAYMENTREQUEST_1_TAXAMT'                   =>  0,
            'PAYMENTREQUEST_1_PAYMENTACTION'            =>  'Order',
            'PAYMENTREQUEST_1_DESC'                     =>  'Purchase from ' . $this->store_name,
            'PAYMENTREQUEST_1_SELLERPAYPALACCOUNTID'    =>  $this->paypal_email,
            'PAYMENTREQUEST_1_PAYMENTREQUESTID'         =>  $uid . '_PAYMENT2'
        );

        $this->setPaymentRequestId(1, $uid . '_PAYMENT1');
        $this->setPaymentRequestId(2, $uid . '_PAYMENT2');

        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        $p_response = $this->sendCurl('nvp', $fields_string, count($fields));




        if ($p_response->status == 'failure') {
            return array(
                'status'        =>  'failed',
                'redirect'      =>  false,
                'message'       =>  $p_response->message,
                'gateway_data'  =>  $p_response
            );
        } else {

            $_SESSION['tpp_store_pay_key'] = $p_response->token;

            return array(
                'status'    =>  'pending',
                'redirect'  =>  $checkout_url . '&token=' . $p_response->token,
                'message'   =>  'sending to checkout',
                'gateway_data'  =>  $p_response->CORRELATIONID
            );
        }
    }

    public function confirmPayment()
    {

        $token = isset($_SESSION['tpp_store_pay_key'])?$_SESSION['tpp_store_pay_key']:false;

        if (false === $token) {
            $title = 'Oops!';
            TppStoreMessages::getInstance()->addMessage('error', 'There was an error with your payment: we could not determine your token. Please contact us.');
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }


        $order_id = isset($_SESSION['tpp_store_checkout_temp']['order_id'])?$_SESSION['tpp_store_checkout_temp']['order_id']:0;
        $payment_id = isset($_SESSION['tpp_store_checkout_temp']['payment_id'])?$_SESSION['tpp_store_checkout_temp']['payment_id']:0;

        if (intval($order_id) <= 0 || intval($payment_id) <= 0) {
            $title = 'Oops!';
            TppStoreMessages::getInstance()->addMessage('error', 'There was an error with your payment. We could not find your order reference. Please contact us.');
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }


        /* live */
        $fields = array(
            'USER'										=>	$this->{$this->environment . '_api_user'},
            'PWD'										=>	$this->{$this->environment . '_api_password'},
            'SIGNATURE'									=>	$this->{$this->environment . '_api_signature'},
            'VERSION'									=>	'93',
            'METHOD'									=>	'GetExpressCheckoutDetails',
            'TOKEN'										=>	$token
        );
        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        $p_response = $this->sendCurl('nvp', $fields_string, count($fields));

        $order = new TppStoreModelOrder();
        $order->setData(array(
            'order_id'  =>  $order_id
        ))->getOrderById();


        if ($p_response->status == 'partialsuccess') {

            $headers = "From: Rosie Parsons <rosie@thephotographyparlour.com>" . "\r\n";
            $headers .= "Reply-to: rosie@thephotographyparlour.com" . "\r\n";
            $headers .= "Return-Path: rosie@thephotographyparlour.com" . "\r\n";
            //$headers .= "Organization: The Photography Parlour" . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            $headers .= "X-Priority: 3" . "\r\n";
            $headers .= "X-Mailer: PHP". phpversion() . "\r\n";
            mail('parsolee@gmail.com', 'partial success','order: ' . $order_id . ' , Amount: ' . $this->total. ', commission: ' . $this->commission . ' data: ' . print_r($p_response, true) , $headers, "-f rosie@thephotographyparlour.com");




        }

        if ($p_response->status == 'success') {

            $return = site_url() . '/shop/checkout/success/';
            $cancel_url = site_url() . '/shop/checkout/cancel/';
            $checkout_url = $this->{$this->environment . '_checkout_url'};

            //now do the express checkout!
            $fields = array(
                'USER'	    				=>	$this->{$this->environment . '_api_user'},
                'PWD'						=>	$this->{$this->environment . '_api_password'},
                'SIGNATURE'                 =>	$this->{$this->environment . '_api_signature'},
                'VERSION'					=>	'93',
                'METHOD'					=>	'DoExpressCheckoutPayment',
                'TOKEN'						=>	$token,
                'PAYMENTACTION'				=>	'Sale',
                'PAYERID'					=>	filter_input(INPUT_GET, 'PayerID', FILTER_SANITIZE_STRING),
                'PAYMENTREQUEST_0_AMT'				=>	$order->total - $order->commission,
                'PAYMENTREQUEST_0_ITEMAMT'			=>	$order->total - $order->commission,
                'PAYMENTREQUEST_0_TAXAMT'			=>	0,
                'PAYMENTREQUEST_0_CURRENCYCODE'		=>	$order->currency,
                'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	$this->store_email,
                'PAYMENTREQUEST_0_PAYMENTREQUESTID'     =>  $this->getPaymentRequestId(1),
                'PAYMENTREQUEST_1_PAYMENTREQUESTID'     =>  $this->getPaymentRequestId(2),
                'PAYMENTREQUEST_1_AMT'				=>	$order->commission,
                'PAYMENTREQUEST_1_ITEMAMT'			=>	$order->commission,
                'PAYMENTREQUEST_1_TAXAMT'			=>	0,
                'PAYMENTREQUEST_1_CURRENCYCODE'		=>	$order->currency,
                'PAYMENTREQUEST_1_SELLERPAYPALACCOUNTID'	=>	$this->paypal_email,
                'CANCELURL'					=>	$cancel_url,
                'RETURNURL'					=>	$return
            );




            $fields_string = '';
            foreach($fields as $key=>$value) {
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');

            $p_response = $this->sendCurl('nvp', $fields_string, count($fields));


            $store_owner_payment_status = urldecode($p_response->PAYMENTINFO_0_PAYMENTSTATUS);
            $tpp_payment_status = urldecode($p_response->PAYMENTINFO_1_PAYMENTSTATUS);

            $reason = '';

            if ($store_owner_payment_status == 'pending') {
                $payment_status = 'pending';
                $reason .= property_exists($p_response, 'PAYMENTINFO_0_PENDINGREASON')?$p_response->PAYMENTINFO_0_PENDINGREASON:'';
            }

            if ($tpp_payment_status == 'pending') {
                $payment_status = 'pending';
                if ($reason != '') {
                    $reason .= ', ';
                }
                $reason .= property_exists($p_response, 'PAYMENTINFO_1_PENDINGREASON')?$p_response->PAYMENTINFO_1_PENDINGREASON:'';
            } elseif ($p_response->status == 'success') {
                $payment_status = 'completed';
            }

            $data = array(
                'status'                =>  $p_response->status,
                'transaction_id'        =>  urldecode($p_response->PAYMENTINFO_0_TRANSACTIONID),
                'currency'              =>  strtoupper($p_response->PAYMENTINFO_0_CURRENCYCODE),
                'senderEmail'           =>  urldecode($p_response->EMAIL),
                'correlation_id'        =>  urldecode($p_response->CORRELATIONID),
                'payment_date'          =>  urldecode($p_response->TIMESTAMP),
                'tpp_payment'           =>  $order->commission,
                'store_owner_payment'   =>  $order->total - $order->commission,
                'receiver_email'        =>  urldecode($p_response->PAYMENTINFO_0_SELLERPAYPALACCOUNTID),
                'payment_status'        =>  $payment_status,
                'payment_message'       =>  $reason,
                'fee'                   =>  $p_response->fee
            );

            $data = (object)$data;


        } else {

            if ($p_response->status == 'partialsuccess') {
                $data = new stdClass();
                $data->status = 'partialsuccess';
                $data->reason = property_exists($p_response, 'checkoutstatus') ? $p_response->checkoutstatus : 'Not all money could be transferred';

                $data->data = $p_response;
            } else {
                $data = new stdClass();
                $data->status = 'failed';
            }
        }


        return $data;
    }

    public function cancel()
    {
        $request_token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

        $token = isset($_SESSION['tpp_store_pay_key'])?$_SESSION['tpp_store_pay_key']:false;

        if ($request_token != $token) {
            TppStoreMessages::getInstance()->addMessage('error', 'Could not validate your request');
            $title = 'Oops!';
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }



    }

    private function setPaymentRequestId($id = 1, $payment_id = '')
    {
        if (!isset($_SESSION['tpp_payment_parallel'])) {
            $_SESSION['tpp_payment_parallel'] = array(
                'payment_request_id'    =>  array(
                    $id =>  $payment_id
                )
            );
        } else {
            $_SESSION['tpp_payment_parallel']['payment_request_id'][$id] = $payment_id;
        }
    }

    private function getPaymentRequestId($id = 1) {

        if (isset($_SESSION['tpp_payment_parallel']['payment_request_id'][$id])) {
            return $_SESSION['tpp_payment_parallel']['payment_request_id'][$id];
        } else {
            return '';
        }

    }

}