<?php
/**
 * User: leeparsons
 * Date: 12/02/2014
 * Time: 08:32
 */

if (!class_exists('geoBase')) {
    include get_template_directory() . '/classes/ip2location/base.php';
}

class geo extends geoBase {


    public $code = false;
    public $country = false;


    public function setData($data = array())
    {

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'countryCode':
                    $this->code = $value;
                    break;
                case 'countryName':
                    $this->country = $value;
                    break;

                default:
                    $this->$key = $value;
                    break;

            }
        }

    }

    public function setCurrency()
    {
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

    public function getCurrencyHtml($code = false)
    {

        if (false === $code) {
            $code = $this->code;
        }
        switch ($code) {
            default:
                return '&dollar;';
                break;
            case 'GB':
                return '&pound;';
                break;
        }
    }



    public function getConversionRates($currency_to)
    {
        if (empty($this->conversion_rates)) {
            TppCacher::getInstance()->setCacheName('currency-exchange');
            TppCacher::getInstance()->setCachePath('cart/' . strtolower($currency_to));

            $conversion_rates = TppCacher::getInstance()->readCache();

            if (false !== $conversion_rates && !empty($conversion_rates)) {

                if (!empty($conversion_rates)) {
                    foreach ($conversion_rates as $currency => $rate) {
                        $this->conversion_rates[$currency] = $rate;
                    }

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
    }

    public function convertCurrency($from_amount = 0.00, $currency_to = 'GBP')
    {

        if ($currency_to === $this->currency) {
            return $from_amount;
        }

        $this->getConversionRates($currency_to);

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