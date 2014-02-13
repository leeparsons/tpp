<?php
/**
http://geolite.maxmind.com/download/geoip/api/php/
**/

class _geo extends TppStoreAbstractInstantiable {

    public $country = '';
    public $code = false;
    private  $currency = false;
    private $conversion_rates = array();

    public function setCurrency()
    {
        mail('parsolee@gmail.com', 'code', $this->code);
        switch ($this->code) {
            default:
                $this->currency = 'USD';
                break;
            case 'GB':
                $this->currency = 'GBP';
                break;
        }
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function convertCurrency($from_amount = 0.00, $currency_to = 'GBP')
    {

        if ($currency_to === $this->currency) {
            return $from_amount;
        }

        if (empty($this->conversion_rates)) {
            TppCacher::getInstance()->setCacheName('currency-exchange');
            TppCacher::getInstance()->setCachePath('cart/' . strtolower($currency_to));

            $conversion_rates = TppCacher::getInstance()->readCache();

            if (false !== $conversion_rates && !empty($conversion_rates)) {
                foreach ($conversion_rates as $currency => $rate) {
                    $this->conversion_rates[$currency] = $rate;
                }
            } else {

                if (!class_exists('TppStoreAdapterPaypal')) {
                    include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
                }

                $p = new TppStoreAdapterPaypal();
                $p->generateExchangeRates();
                $conversion_rates = TppCacher::getInstance()->readCache();
                if (false !== $conversion_rates && !empty($conversion_rates)) {
                    foreach ($conversion_rates as $currency => $rate) {
                        $this->conversion_rates[$currency] = $rate;
                    }
                }
            }

        }

        if (empty($this->conversion_rates)) {
            return false;
        } elseif (!isset($this->conversion_rates[$currency_to])) {


            if (isset($this->conversion_rates[$this->currency])) {
                //assume we are converting to the currency_to ('gbp') from the current currency 'usd'
                return $from_amount * $this->conversion_rates[$this->currency];
            }

            return false;
        } else {
            return $from_amount / $this->conversion_rates[$currency_to];
        }
    }
}