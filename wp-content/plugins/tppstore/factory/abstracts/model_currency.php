<?php
/**
 * User: leeparsons
 * Date: 29/12/2013
 * Time: 09:56
 */
 
class TppStoreModelCurrency extends TppStoreAbstractModelResource {

    public $currency = 'GBP';
    protected $option_min_price = null;

    public function getFormattedCurrency($with_currency = true)
    {

        if (false === $with_currency) {
            return '';
        }


        switch (geo::getInstance()->getCurrency()) {
            case 'USD':
                return '&dollar;';
                break;

            default:
                return '&pound;';
                break;
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

    public function getFormattedPrice($with_currency = false)
    {

        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
        }

        if ($this->price_includes_tax == 1) {
            return $this->getFormattedCurrency($with_currency) . $this->format($price);
        } else {
            return $this->getFormattedCurrency($with_currency) . $this->format($price * (1+($this->tax_rate/100)));
        }
    }


    public function getLineItemFormattedTotal($with_currency = false, $with_discount = false)
    {

        $price = 0;
        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
        }


        if (intval($this->price_includes_tax) == 1) {
            if (true === $with_discount) {
                $price = $price  - $this->discount;
            }
        } else {
            if (true === $with_discount) {
                $price = ($price - $this->discount) * (1 + ($this->tax_rate/100));
            } else {
                $price = $price * (1 + ($this->tax_rate/100));
            }
        }

        $price = round($this->order_quantity * $price, 2);

        if (true === $with_currency) {
            return $this->getFormattedCurrency() . $price;
        } else {
            return $price;
        }
    }


    public function getFormattedTax($with_currency = false, $with_discount = false, $order_quantity = 1)
    {

        if (intval($this->price_includes_tax) == 1) {
            if (true === $with_discount) {
                $tax = $this->format($this->price - (($this->price - $this->discount) / (1 + ($this->tax_rate/100))));
            } else {
                $tax = $this->format($this->price * (1 -  1 / (1 + ($this->tax_rate/100))));
            }
        } else {
            if (true === $with_discount) {
                $tax = $this->format($this->tax_rate * ($this->price - $this->discount)/ 100);
            } else {
                $tax = $this->format($this->tax_rate * $this->price / 100);
            }
        }

        if (true === $with_currency) {
            return $this->getFormattedCurrency() . $this->format($order_quantity * $this->format($tax));
        } else {
            return $this->format($order_quantity * $this->format($tax));
        }

    }


    public function formatAmount($amount = 0)
    {

        if (false === ($price = geo::getInstance()->convertCurrency($amount, $this->currency))) {
            $price = $amount;
        }

        return $this->format($price, 2);
    }

    protected function format($number = 0)
    {

        return number_format($number, 2, '.', '');
    }
}