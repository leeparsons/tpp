<?php
/**
 * User: leeparsons
 * Date: 28/02/2014
 * Time: 14:28
 */

if (!class_exists('TppStorePaypalBase')) {
    include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/base.php';
}

class TppStoreAdapterPaypalDirect extends TppStorePaypalBase {

    public $code = 'paypal_direct';
    public $id = 'paypal_direct';

    public $sandbox_url = "https://api.sandbox.paypal.com/";

    public $live_url = "https://api-3t.paypal.com/";
    public $live_api_user = "rosie_api1.rosieparsons.com";
    public $live_api_password = "K5ULLT635CMGSQT";
    public $live_api_signature = "AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3";
    public $live_checkout_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout";



    public $sandbox_api_user = "rosie_api1.rosieparsons.com";
    public $sandbox_api_password = "LR3X8R7NGPVYFFGD";
    public $sandbox_api_signature = "ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp";
    public $sandbox_checkout_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout";


    private function sendCurl($end_point = 'Pay', $request_string = '', $count = 0)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->{$this->environment . '_url'} . $end_point);


        curl_setopt($ch, CURLOPT_POST, $count);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

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

        $fields = array(
            'USER'										=>	$this->{$this->environment . '_api_user'},
            'PWD'										=>	$this->{$this->environment . '_api_password'},
            'SIGNATURE'									=>	$this->{$this->environment . '_api_signature'},
            'VERSION'									=>	'72.0',
            'PAYMENTACTION'								=>	'AUTHORIZATION',
            'METHOD'									=>	'SetExpressCheckout',
            'PAYMENTREQUEST_0_NUMBER'					=>	'123',
            'PAYMENTACTION'								=>	'Sale',
            'PAYMENTREQUEST_0_AMT'						=>	$this->total,
            'PAYMENTREQUEST_0_ITEMAMT'					=>	$this->total,
            'PAYMENTREQUEST_0_TAXAMT'					=>	'0.00',
            'PAYMENTREQUEST_0_SHIPPINGAMT'				=>	'0.00',
            'PAYMENTREQUEST_0_HANDLINGAMT'				=>	'0.00',
            'PAYMENTREQUEST_0_INSURANCEAMT'				=>	'0.00',
            'PAYMENTREQUEST_0_CURRENCYCODE'				=>	$this->currency,
            'L_PAYMENTREQUEST_0_NAME0'					=>	'',
            'L_PAYMENTREQUEST_0_DESCRIPTION0'			=>	'',
            'L_PAYMENTREQUEST_0_AMT0'					=>	$this->total,
            'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	'rosie@rosieparsons.com',
            'CANCELURL'									=>	$cancel_url,
            'RETURNURL'									=>	$return,
            'PAYMENTREQUEST_0_ALLOWEDPAYMENTMETHOD'		=>	'InstantPaymentOnly'
        );












        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        //pay for each product




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
            'VERSION'									=>	'72.0',
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

        if ($p_response->status == 'success') {

            $return = site_url() . '/shop/checkout/success/';
            $cancel_url = site_url() . '/shop/checkout/cancel/';
            $checkout_url = $this->{$this->environment . '_checkout_url'};

            //now do the express checkout!
            $fields = array(
                'USER'	    				=>	$this->{$this->environment . '_api_user'},
                'PWD'						=>	$this->{$this->environment . '_api_password'},
                'SIGNATURE'                 =>	$this->{$this->environment . '_api_signature'},
                'VERSION'					=>	'72.0',
                'METHOD'					=>	'DoExpressCheckoutPayment',
                'TOKEN'						=>	$token,
                'PAYMENTACTION'				=>	'Sale',
                'PAYERID'					=>	filter_input(INPUT_GET, 'PayerID', FILTER_SANITIZE_STRING),
                'PAYMENTREQUEST_0_AMT'				=>	$order->total,
                'PAYMENTREQUEST_0_ITEMAMT'			=>	$order->total,
                'PAYMENTREQUEST_0_TAXAMT'			=>	$order->tax,
                'PAYMENTREQUEST_0_SHIPPINGAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_HANDLINGAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_INSURANCEAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_CURRENCYCODE'			=>	$order->currency,
                'L_PAYMENTREQUEST_0_NAME0'			    =>	'Purchase ' . $order->ref,
                'L_PAYMENTREQUEST_0_DESCRIPTION0'		=>	'',
                'L_PAYMENTREQUEST_0_AMT0'			=>	$order->total,
                'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	'rosie@rosieparsons.com',
                'CANCELURL'					=>	$cancel_url,
                'RETURNURL'					=>	$return
            );

            $fields_string = '';
            foreach($fields as $key=>$value) {
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');

            $p_response = $this->sendCurl('nvp', $fields_string, count($fields));


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
                'payment_status'        =>  urldecode($p_response->PAYMENTINFO_0_PAYMENTSTATUS),
                'payment_message'       =>  property_exists($p_response, 'PAYMENTINFO_0_PENDINGREASON')?urldecode($p_response->PAYMENTINFO_0_PENDINGREASON):'',
                'fee'                   =>  $p_response->fee
            );

            $data = (object)$data;


        } else {
            $data = new stdClass();
            $data->status = 'failed';
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

}