<?php
/**
 * User: leeparsons
 * Date: 01/03/2014
 * Time: 21:08
 */
 
Abstract class TppStorePaypalBase {

    public $id = '';

    protected  $products = array();
    protected $total = 0;

    public $currency;
    protected  $commission = 0;
    protected  $commission_rate = 12.5;
    public  $tax = 0;
    public $discount = 0;

    protected  $store_email = '';
    protected  $purchaser_email = "rosie@rosieparsons.com";

    protected $paypal_email = '';

    public $sandbox_url = '';

    public $sandbox_app_id = '';
    public $sandbox_api_user = '';
    public $sandbox_api_pwd = '';
    public $sandbox_api_sig = '';
    public $sandbox_endpoint = '';

    public $live_url = '';
    public $live_app_id = '';
    public $live_api_user = '';
    public $live_api_pwd = '';
    public $live_api_sig = '';
    public $live_endpoint = '';

    //curl
    protected $ch = null;

    public $store_name = '';

    protected $environment = 'sandbox';

    public $to_pay = 0;

    public function __construct()
    {

        $this->icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_SbyPP_mc_vs_ms_ae_UK.png';
        $this->has_fields = true;

        global $wpdb;

        $wpdb->get_results(
            "SELECT config FROM shop_adapters WHERE adapter = '" . $this->id . "'",
            OBJECT_K
        );

        if ($wpdb->num_rows > 0) {
            $config = $wpdb->last_result[0]->config;

            $config = json_decode($config);

            foreach ($config as $key => $setting) {
                $this->$key = $setting;
            }

        //} else {
          //  throw new Exception('Could not find the payment gateway configuration');
        }

    }

    /*
    * total paid
    */
    public function getTotal()
    {
        return $this->total;
    }

    public function getCommission()
    {
        return $this->commission;
    }

    public function setOrder($store_id = 0)
    {
        $cart = TppStoreModelCart::getInstance()->getCart(true);

        $this->products = $cart['stores'][$store_id]['products'];

        $this->total = number_format($cart['stores'][$store_id]['total'], 2, '.', '');

        $this->setUpStoreDetails($store_id);

        $this->discount = 0;

        foreach ($cart['stores'][$store_id]['products'] as $product) {
            $this->discount +=  $product->formatAmount($product->order_quantity * $product->discount, false);
        }

        //$this->discount = $cart['stores'][$store_id]['discount'];

        $this->tax = $cart['stores'][$store_id]['tax'];

        //$this->purchaser_email = $user->email;

        $commission = number_format(($this->commission_rate / 100) * $this->total, 2, '.', '');

        $this->commission = $commission;

        $this->to_pay = $this->total - $this->commission;


    }

    /*
     * set up a one off payment
     */
    public function setUpOneOffPaymentOrder(TppStoreModelStore $store, $order_details)
    {

        $this->total = $order_details['total'];

        $this->setUpStoreDetails($store);
        $this->discount = 0;
        $this->tax = 0;

        $this->commission = number_format(($this->commission_rate / 100) * $this->total, 2, '.', '');

        $this->currency = $order_details['currency'];

        $this->to_pay = $this->total - $this->commission;

    }

    /*
     * sets up the relevant store details
     * @param integer | object $store can be a store id or a store object
     */
    protected function setUpStoreDetails($store)
    {

        if (! $store instanceof TppStoreModelStore ) {
            $store = TppStoreModelStore::getInstance()->setData(array(
                'store_id'  =>  $store
            ));
            $store->getStoreById();

        }

        $this->store_email = $store->paypal_email;

        $this->store_name = $store->store_name;

        $this->commission_rate = $store->commission;

        $this->currency = geo::getInstance()->getCurrency();//$store->currency;

    }

    protected function getStoreEmail()
    {

        return $this->store_email;
    }
}