<?php
/**
 * User: leeparsons
 * Date: 27/12/2013
 * Time: 11:58
 */

if (!class_exists('TppStorePaypalBase')) {
    include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/base.php';
}

class TppStoreAdapterPaypal  extends TppStorePaypalBase {

    public $code = 'paypal_adaptive';
    public $id = 'paypal_adaptive';


    public $sandbox_url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';

    public $sandbox_app_id = 'APP-80W284485P519543T';
    public $sandbox_api_user = 'squibe_1316263024_biz_api1.gmail.com';
    public $sandbox_api_pwd = '1316263084';
    public $sandbox_api_sig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AlnQ.aSfCWj2p0FjqqYWF3rWDveN';
    public $sandbox_endpoint = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';

    public $live_url = 'https://svcs.paypal.com/AdaptivePayments/';
    public $live_app_id = 'K5ULLT635CMGSQTY';
    public $live_api_user = 'rosie_api1.rosieparsons.com';
    public $live_api_pwd = 'K5ULLT635CMGSQTY';
    public $live_api_sig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3';
    public $live_endpoint = 'https://svcs.paypal.com/AdaptivePayments/Pay';




    public function generateExchangeRates()
    {

        //at the moment we only get USD

        global $wpdb;

        $wpdb->query(
            "SELECT currency_code, rate_from_gbp FROM shop_exchange_rates WHERE last_update_time > NOW() - INTERVAL 1 DAY"
        );



        if ($wpdb->num_rows > 0) {
            TppCacher::getInstance()->setCacheName('currency-exchange');
            TppCacher::getInstance()->setCachePath('cart/usd');

            TppCacher::getInstance()->saveCache(array(
                'USD'   =>  $wpdb->last_result[0]->rate_from_gbp
            ));
            return true;
        }

        $this->setUpCurl(
            'ConvertCurrency/',
            'baseAmountList.currency(0).code=GBP&baseAmountList.currency(0).amount=1&convertToCurrencyList.currencyCode=USD&requestEnvelope.errorLanguage=en_US&requestEnvelope.detailLevel=ReturnAll&requestEnvelope.conversionType=RECEIVER_SIDE',
            'JSON'
        );
        $res = $this->execCurl();

        $res = json_decode($res);


        if ($res->responseEnvelope && $res->responseEnvelope->ack == 'Success') {

            $usd_rate = $res->estimatedAmountTable->currencyConversionList[0]->currencyList->currency[0]->amount;



            $wpdb->replace(
                "shop_exchange_rates",
                array(
                    'currency_code'     =>  'USD',
                    'rate_from_gbp'     =>  $usd_rate,
                    'last_update_time'  =>  date('Y-m-d h:i:s')
                ),
                array(
                    '%s',
                    '%f',
                    '%s'
                )
            );


            //now create the cache file

            TppCacher::getInstance()->setCacheName('currency-exchange');
            TppCacher::getInstance()->setCachePath('cart/usd');

            TppCacher::getInstance()->saveCache(array(
                'USD'   =>  $usd_rate
            ));

            return true;
        }

    }






    private function setUpCurl($end_point = 'Pay', $request_string = '', $format = 'NV')
    {
        $ch = curl_init($this->{$this->environment . '_url'} . $end_point);

//        $apiUser = 'squibe_1316263024_biz_api1.gmail.com';//'rosie_api1.rosieparsons.com';
//        $apiPWD = '1316263084';//'LR3X8R7NGPVYFFGD';
//        $apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AlnQ.aSfCWj2p0FjqqYWF3rWDveN';//'ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp';

        $headers = array();
        $headers[0] = "Content-Type: text/namevalue"; // either text/namevalue or text/xml
        $headers[1] = "X-PAYPAL-SECURITY-USERID: " . $this->{$this->environment.'_api_user'};//API user
        $headers[2] = "X-PAYPAL-SECURITY-PASSWORD: " . $this->{$this->environment.'_api_pwd'};//API PWD
        $headers[3] = "X-PAYPAL-SECURITY-SIGNATURE: " . $this->{$this->environment.'_api_sig'};//API Sig
        $headers[4] = "X-PAYPAL-APPLICATION-ID: " . $this->{$this->environment.'_app_id'};//APP ID
        $headers[5] = "X-PAYPAL-REQUEST-DATA-FORMAT: NV";//Set Name Value Request Format
        $headers[6] = "X-PAYPAL-RESPONSE-DATA-FORMAT: $format";//Set Name Value Response Format


        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);

        $this->ch = $ch;
    }

    private function execCurl()
    {
        $res = curl_exec($this->ch);

//        $e = curl_errno($this->ch);
  //      $info = curl_getinfo($this->ch);

        curl_close($this->ch);

        $this->ch = null;

        return $res;
    }

    public function process()
    {
        //$purchaser_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING, FILTER_VALIDATE_EMAIL);
        //$endpoint = $this->sandbox_endpoint;

        $return = site_url() . '/shop/checkout/success';

        $cancelUrl = site_url() . '/shop/checkout/cancel';

        //pay for each product

        $commission = number_format(($this->commission_rate / 100) * $this->total, 2, '.', '');

        $this->commission = $commission;

        $to_pay = number_format($this->total *( 1 - ($this->commission_rate / 100)), 2, '.', '');


        $currency = $this->currency;

        $store_owner_email = $this->getStoreEmail();



        $reqstr = array(
            "actionType=PAY",
            "cancelUrl=$cancelUrl",
            "currencyCode=$currency",
            "returnUrl=$return",
            "requestEnvelope.errorLanguage=en_US",
            "receiverList.receiver(0).amount=" . $commission,
            //"receiverList.receiver(0).primary=false",
            "receiverList.receiver(0).email=" . $this->paypal_email,
            "receiverList.receiver(1).amount=" . $this->total,
            //"receiverList.receiver(1).primary=true",
            "receiverList.receiver(1).email=" . $store_owner_email,
        );




        $reqstr = implode("&", $reqstr);


        $this->setUpCurl('Pay', $reqstr);

        unset($to_pay);
        unset($return);
        unset($cancelUrl);
        unset($currency);
        unset($commission);

        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

//        $url = array();
//        foreach ($fields as $field  =>  $value) {
//            if (is_array($value)) {
//
//                $url[] = '';
//
//                foreach ($value as $k => $v) {
//
//                }
//            } else {
//                $url[]= $field . '=' . $value;
//            }
//        }
//
//        unset($field);
//        unset($value);
//        unset($k);
//        unset($v);
//        unset($fields);
//
//        $url = implode('&', $url);

        unset($reqstr);


        $res = $this->execCurl();

        $p_response = $this->parseResponse($res);

        if (!isset($p_response['responseEnvelope.ack']) || $p_response['responseEnvelope.ack'] == 'Failure') {
            return array(
                'status'        =>  'failed',
                'redirect'      =>  false,
                'message'       =>  $p_response['error(0).message'],
                'gateway_data'  =>  $p_response
            );
        } else {

            $_SESSION['tpp_store_pay_key'] = $p_response['payKey'];

            return array(
                'status'    =>  'processing',
                'redirect'  =>  'https://www.' . ($this->environment == 'sandbox'?'sandbox.':'') . 'paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey=' . $p_response['payKey'],
                'message'   =>  $p_response['error(0).message']
            );
        }


        //for now

//Build the HTML Form
   ?><html><body>
<script src ='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>
<form action="https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay" target="PPDGFrame" class="standard">
    <input id='type' type='hidden' name='expType' value='light'>
    <input id='paykey' type='hidden' name='payKey' value='<?php echo $p_response['payKey'] ?>'>
    <input type='submit' id='submitBtn' value='Pay with PayPal'>
    <script type="text/javascript" charset="utf-8">
        var embeddedPPFlow = new PAYPAL.apps.DGFlow({trigger: 'submitBtn'});
    </script>
</form>

        </body></html><?php
exit;
        exit('https://ppmts.custhelp.com/app/answers/detail/a_id/944/kw/adaptive%20payments');

    }


    public function confirmPayment()
    {

        $pay_key = $_SESSION['tpp_store_pay_key'];

        if ($pay_key) {
            $ch = curl_init();
            $ch = curl_init($this->{$this->environment . '_url'} . 'PaymentDetails' . '?payKey=' . $pay_key . '&requestEnvelope.errorLanguage=en_US');

//        $apiUser = 'squibe_1316263024_biz_api1.gmail.com';//'rosie_api1.rosieparsons.com';
//        $apiPWD = '1316263084';//'LR3X8R7NGPVYFFGD';
//        $apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AlnQ.aSfCWj2p0FjqqYWF3rWDveN';//'ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp';

            $headers = array();
            $headers[1] = "X-PAYPAL-SECURITY-USERID: " . $this->{$this->environment.'_api_user'};//API user
            $headers[2] = "X-PAYPAL-SECURITY-PASSWORD: " . $this->{$this->environment.'_api_pwd'};//API PWD
            $headers[3] = "X-PAYPAL-SECURITY-SIGNATURE: " . $this->{$this->environment.'_api_sig'};//API Sig
            $headers[4] = "X-PAYPAL-APPLICATION-ID: " . $this->{$this->environment.'_app_id'};//APP ID
            $headers[5] = "X-PAYPAL-REQUEST-DATA-FORMAT: NV";//Set Name Value Request Format
            $headers[6] = "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON";//Set Name Value Response Format


            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            unset($headers);



            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $res = curl_exec($ch);
            $e = curl_errno($ch);

            curl_close($ch);

            $p_response = $this->parseResponse($res, 'JSON');

            if ($p_response->responseEnvelope->ack == 'Success') {
                $data = array(
                    'status'                =>  'Success',
                    'currency'              =>  $p_response->currencyCode,
                    'senderEmail'           =>  $p_response->senderEmail,
                    'payKey'                =>  $p_response->payKey,
                    'feesPayer'             =>  $p_response->feesPayer,
                    'sender'                =>  $p_response->sender,
                    'correlation_id'        =>  $p_response->responseEnvelope->correlation_id,
                    'payment_date'          =>  $p_response->responseEnvelope->timestamp,
                    'tpp_payment'           =>  $p_response->paymentInfoList->paymentInfo[0],
                    'store_owner_payment'   =>  $p_response->paymentInfoList->paymentInfo[1]
                );

                $data = (object)$data;



            } else {
                $data = new stdClass();
                $data->status = 'failed';
            }

        } else {
            $data = new stdClass();
            $data->status = 'failed';
        }


        return $data;
    }

    public function cancel()
    {
        $_SESSION['tpp_store_pay_key'] = null;
        unset($_SESSION['tpp_store_pay_key']);
    }

    private function parseResponse($res = false, $format = 'NV')
    {
        if ($format == 'NV') {

            $tmp = explode('&', $res);
            $p_response = array();

            if (!empty($tmp)) {


                foreach ($tmp as $v) {
                    $_tmp = explode('=', $v);
                    $p_response[$_tmp[0]] = urldecode($_tmp[1]);
                }

            }

            return $p_response;
        } elseif ($res = json_decode($res)) {
            return $res;
        }

    }



}



