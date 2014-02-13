<?php
/**
 * PayPal Adaptive Payment Gateway
 *
 * Provides a PayPal Adaptive Payment Gateway.
 *
 * @class 		WC_Paypal_Adaptive_Payments
 * @package		WooCommerce
 * @category	Payment Gateways
 * @author		Lee Parsons
 */

if (class_exists('WC_Payment_Gateway')) {




class WC_Paypal_Adaptive_Payments extends WC_Payment_Gateway {

    public $sandbox_url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay';
    public $live_url = '';

    public function __construct()
    {
        $this->id = 'paypal_adaptive_payments';



        $this->icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_SbyPP_mc_vs_ms_ae_UK.png';
        $this->has_fields = true;
        //$this->method_title     = __( 'Paypal Adaptive Payments', 'woocommerce' );
        $this->title     = __( 'Paypal Adaptive Payments', 'woocommerce' );

        $this->method_description = 'Allows adaptive payments enabling you to take commission';

        $this->sandbox = get_settings('sandbox');

        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );


    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     *
     * @since 1.0.0
     */
    public function admin_options() {

        ?>
    <h3><?php _e('PayPal standard', 'woocommerce'); ?></h3>
    <p><?php _e('PayPal standard works by sending the user to PayPal to enter their payment information.', 'woocommerce'); ?></p>
    <table class="form-table">
        <?php
        if ( $this->is_valid_for_use() ) :

            // Generate the HTML For the settings form.
            $this->generate_settings_html();

        else :

            ?>
            <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( 'PayPal does not support your store currency.', 'woocommerce' ); ?></p></div>
            <?php

        endif;
        ?>
    </table><!--/.form-table-->
    <?php
    } // End admin_options()





    /**
     * Check if this gateway is enabled and available in the user's country
     */
    protected function is_valid_for_use() {
        return in_array(get_option('woocommerce_currency'), array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP'));
    }


    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable Adaptive Payments', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default' => __( 'Paypal', 'woocommerce' ),
                'desc_tip'      => true,
            ),
            'description' => array(
                'title' => __( 'Customer Message', 'woocommerce' ),
                'type' => 'textarea',
                'default' => ''
            ),
            'sandbox'   =>  array(
                'title' =>  __( 'Sandbox Gateway', 'woocommerce' ),
                'type'  =>  'checkbox',
                'description'   =>  __( 'Check this box to enable sandbox environment for testing' , 'woocommerce'),
                'label' => __( 'Enable Sandbox', 'woocommerce' ),
                'default'       =>  'no',
                'desc_tip'  =>  true
            ),
            'appid'     =>  array(
                'title' =>  __( 'API ID', 'woocommerce'),
                'type'  =>  'text',
                'description'   =>  __( 'Your APP ID available from Paypal', 'woocommerce'),
                'label'         =>  __( 'API ID', 'woocommerce'),
                'desc_tip'       =>  true
            ),
            'paypal_email'  =>  array(
                'title' =>  __( 'Your Paypal Email Address', 'woocommerce' ),
                'type'  =>  'text',
                'description'   =>  __( 'The paypal account email address to receive commission', 'woocommerce' ),
                'label'         =>  __( 'Commission paypal account email address', 'woocommerce' ),
                'desc_tip'      =>  true
            ),
            'commission'  =>  array(
                'title' =>  __( 'Your Commission as a %', 'woocommerce' ),
                'type'  =>  'text',
                'description'   =>  __( 'Enter your comission amount as a %', 'woocommerce' ),
                'label'         =>  __( 'Commission percentage', 'woocommerce' ),
                'desc_tip'      =>  true
            )
        );
    }


    public function process_payment( $order_id ) {
        global $woocommerce;
        $order = new WC_Order( $order_id );

        // Mark as on-hold (we're awaiting the cheque)
        $order->update_status('on-hold', __( 'Verifying Paypal Account', 'woocommerce' ));

        if ($this->settings['sandbox'] == 'yes') {
            $ch = curl_init($this->sandbox_url);

            $apiUser = 'squibe_1316263024_biz_api1.gmail.com';//'rosie_api1.rosieparsons.com';
            $apiPWD = '1316263084';//'LR3X8R7NGPVYFFGD';
            $apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AlnQ.aSfCWj2p0FjqqYWF3rWDveN';//'ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp';

            $headers = array();
            $headers[0] = "Content-Type: text/namevalue"; // either text/namevalue or text/xml
            $headers[1] = "X-PAYPAL-SECURITY-USERID: " . $apiUser;//API user
            $headers[2] = "X-PAYPAL-SECURITY-PASSWORD: " . $apiPWD;//API PWD
            $headers[3] = "X-PAYPAL-SECURITY-SIGNATURE: " . $apiSig;//API Sig
            $headers[4] = "X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T";//APP ID
            $headers[5] = "X-PAYPAL-REQUEST-DATA-FORMAT: NV";//Set Name Value Request Format
            $headers[6] = "X-PAYPAL-RESPONSE-DATA-FORMAT: NV";//Set Name Value Response Format


            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


            $endpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments/Pay";

            // Sandbox API credentials for the API Caller account
//           $endpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments/Pay";
//            echo "<br />Endpoint: " . $endpoint . "<br />";
//            echo "<br />Headers: <br />";
//            print_r($headers);
//            echo "<br />";
//            $api_str = requestPayKey();
//
//            echo "<br />Request String: " . $api_str . "<br />";
////make the API Call and echo out the headers and the request string
//            $response = PPHttpPost($endpoint, $api_str, $headers);
//            echo "<br /><br />Response: <br />";
//            print_r($response);
////parse the response
//            $response_ar = explode("&", $response);
//            $p_response = parseAPIResponse($response_ar);




//exit;

            $commission = number_format($order->get_order_total() * $this->settings['commission']/100, 2, '.', '');

            $to_pay = $order->get_order_total() - $commission;


            $cancelUrl = 'http://www.paypal.com';

            $currency = get_option('woocommerce_currency');

            $return = 'https://www.yoursite.com/dir/return.php';

            $email =  'parsolee@gmail.com';

            $reqstr = "actionType=PAY&cancelUrl=$cancelUrl&currencyCode=$currency&returnUrl=$return&requestEnvelope.errorLanguage=en_US";
            $reqstr .= "&receiverList.receiver(0).amount=$commission&receiverList.receiver(0).email=rosie@rosieparsons.com";
            $reqstr .= "&receiverList.receiver(1).amount=$to_pay&receiverList.receiver(1).email=$email";

            $fields = array(
    "actionType"    =>  "PAY",    // Specify the payment action
    "currencyCode"  =>  get_option('woocommerce_currency'),  // The currency of the payment
    "receiverList"  =>  array(
        'receiver[0]'  =>  array(
            'amount'     =>  $commission,
            'email'      =>  $this->settings['paypal_email']
            ),
        'receiver[1]'  =>  array(
            'amount'     =>  $to_pay,
            'email'      =>  $this->settings['paypal_email']
        )
    ),
    'returnUrl' =>  'http://Payment-Success-URL.com/success',
    'cancelUrl' =>  'http://Payment-Cancel-URL.com/cancel'
);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            $url = array();
            foreach ($fields as $field  =>  $value) {
                if (is_array($value)) {

                    $url[] = '';

                    foreach ($value as $k => $v) {

                    }
                } else {
                    $url[]= $field . '=' . $value;
                }
            }

            $url = implode('&', $url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $reqstr);


            curl_setopt($ch, CURLOPT_URL, $this->sandbox_url);

            $res = curl_exec($ch);

            $e = curl_errno($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

        }
exit('https://ppmts.custhelp.com/app/answers/detail/a_id/944/kw/adaptive%20payments');
        // Reduce stock levels
        //$order->reduce_order_stock();

        // Remove cart
        $woocommerce->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

}



function parseAPIResponse($response){
    $parsed_response = array();
    foreach ($response as $i => $value)
    {
        $tmpAr = explode("=", $value);
        if(sizeof($tmpAr) > 1) {
            $parsed_response[$tmpAr[0]] = $tmpAr[1];
        }
    }

    return $parsed_response;
}
function requestPayKey(){
//request token string:
    $reqstr = "actionType=PAY&cancelUrl=http://www.paypal.com&currencyCode=USD&returnUrl=https://www.yoursite.com/dir/return.php&requestEnvelope.errorLanguage=en_US&receiverList.receiver(0).amount=25.00&receiverList.receiver(0).email=receiver.email@domain.comemail=receiver.email@domain.com";
    return $reqstr;
}
function PPHttpPost($my_endpoint, $my_api_str, $headers){
    // setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $my_endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response, use for testing
    // turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    // setting the NVP $my_api_str as POST FIELD to curl
    curl_setopt($ch, CURLOPT_POSTFIELDS, $my_api_str);
    // getting response from server
    $httpResponse = curl_exec($ch);
    if(!$httpResponse)
    {
        $response = "$API_method failed: ".curl_error($ch)."(".curl_errno($ch).")";
        return $response;
    }

    return $httpResponse;
}

}