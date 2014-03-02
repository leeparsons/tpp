<?php
/**
 * User: leeparsons
 * Date: 29/12/2013
 * Time: 09:56
 */
 
class TppStoreModelCurrency extends TppStoreAbstractModelResource {

    public $currency = 'GBP';
    protected $option_min_price = null;

    public function getFormattedCurrency($with_currency = true, $currency = false, $html = true)
    {

        if (false === $with_currency) {
            return '';
        }

        if (false === $currency) {
            $currency = geo::getInstance()->getCurrency();
        }

        if ($html === true) {
            switch ($currency) {
                case 'USD':
                    return '&dollar;';
                    break;

                default:
                    return '&pound;';
                    break;
            }
        } else {
            switch ($currency) {
                case 'USD':
                    return '$';
                    break;

                default:
                    return '£';
                    break;
            }

        }
    }

    /*
     * Takes into account any option prices that may be lower than the base price of the product
     */
    public function getFormattedMinPrice($with_currency = false)
    {

        if ($this->currency !== geo::getInstance()->getCurrency()) {

        }

        if (is_null($this->option_min_price)) {
            return $this->getFormattedPrice($with_currency);
        } else {
            if (true === $with_currency) {
                return $this->getFormattedCurrency() . ($this->price > $this->option_min_price?$this->option_min_price:$this->price);
            } else {
                return $this->price > $this->option_min_price?$this->option_min_price:$this->price;
            }
        }

    }

//    public function getFormattedPrice($with_currency = false)
//    {
//
//
//        if (true === $with_currency) {
//            return $this->getFormattedCurrency() . number_format($this->price * (1+($this->tax_rate/100)), 2, '.', '');
//        } else {
//            return number_format($this->price * (1+($this->tax_rate/100)), 2, '.', '');
//        }
//    }

    public function getFormattedPrice($with_currency = false, $convert_using_geo_location = true)
    {

        if (false === $convert_using_geo_location) {
            $price = $this->price;
        } elseif (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
        }

        if ($this->price_includes_tax == '1') {
            return $this->getFormattedCurrency($with_currency) . $this->format($price);
        } else {
            return $this->getFormattedCurrency($with_currency) . $this->format($price * (1+($this->tax_rate/100)));
        }
    }





    public function getFormattedTax($with_currency = false, $with_discount = false, $order_quantity = 1)
    {
        $price = 0;
        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
        }


        if (intval($this->price_includes_tax) == 1) {
            if (true === $with_discount) {
                $tax = $this->format($price - (($price - $this->discount) / (1 + ($this->tax_rate/100))));
            } else {
                $tax = $this->format($price * (1 -  1 / (1 + ($this->tax_rate/100))));
            }
        } else {
            if (true === $with_discount) {
                $tax = $this->format($this->tax_rate * ($price - $this->discount)/ 100);
            } else {
                $tax = $this->format($this->tax_rate * $price / 100);
            }
        }

        if (true === $with_currency) {
            return $this->getFormattedCurrency() . $this->format($order_quantity * $this->format($tax));
        } else {
            return $this->format($order_quantity * $this->format($tax));
        }

    }


    public function formatAmount($amount = 0, $with_currency = false, $convert = true)
    {



        if ($convert === false || false === ($price = geo::getInstance()->convertCurrency($amount, $this->currency))) {
            $price = $amount;
        }

        if ($with_currency === true) {
            return $this->getFormattedCurrency() . $this->format($price);
        } else {
            return $this->format($price);
        }
    }


    protected function format($number = 0)
    {

        return number_format($number, 2, '.', '');
    }
}