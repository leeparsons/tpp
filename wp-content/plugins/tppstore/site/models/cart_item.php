<?php



class TppStoreModelCartItem extends TppStoreAbstractModelBaseProduct {


    public $product_image = array();
    public $line_total = 0;
    public $order_quantity = 0;
    public $store_email = null;
    public $currency = 'null';
    public $discount = 0;

    public function setData($data)
    {

        if (is_array($data)) {
            foreach ($this as $key => $value)
            {
                if (isset($data[$key])) {
                    $this->$key = $data[$key];
                }
            }

        } else {

            $reflect = new ReflectionObject($this);

            $public_properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

                foreach ($public_properties as $property)
            {
                if (property_exists($data, $property->name)) {
                    $this->{$property->name} = $data->{$property->name};
                }
            }
        }
    }



    public function getImage($size = 'thumb', $html = false, $attribs = array())
    {

        if (is_object($this->product_image)) {
            return $this->product_image->getSrc($size, $html, $attribs);
        } else {
            return '';
        }
    }



    public function getStoreEmail()
    {
        return filter_var($this->store_email, FILTER_VALIDATE_EMAIL);
    }

    /*
     * Override the base method as discount already includes tax at this point!
     */
    public function getFormattedTax($with_currency = false, $with_discount = false, $order_quantity = 1)
    {
        $price = 0;
        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
            $discount = $this->discount;
        } else {
            $discount = geo::getInstance()->convertCurrency($this->discount, $this->currency);
        }


        if (intval($this->price_includes_tax) == 1) {
            if (true === $with_discount) {

                //tax = (price - discount) * 100 / tax rate

                //PRICE INCLUDES TAX: tax_price = (_untaxed * (1 + (tax_rate/100))) - discount

                //_untaxed = (tax_price + discount) / (1 + (tax_rate/100))

                $tax = ($price + $discount) / (1 + ($this->tax_rate)/100);

                //$tax = $this->format($price - ($price / (1 + ($this->tax_rate/100))));
            } else {
                $tax = $price * (1 -  1 / (1 + ($this->tax_rate/100)));
            }
        } else {
            //price does not include tax!
            if (true === $with_discount) {

                //tax on discount = discount  = dis * 1.2


                $discounted_tax = $discount * ( 1 -  1 / (1 + ($this->tax_rate / 100)));


                $tax = ($this->tax_rate * $price / 100) - $discounted_tax;

                //discount includes tax
                //$tax = ($this->tax_rate * $price / 100) - ( $this->discount / $this->tax_rate);

                //$tax = $this->tax_rate * ($price - $this->discount)/ 100;
            } else {
                $tax = $this->tax_rate * $price / 100;
            }
        }

        if (true === $with_currency) {
            return $this->getFormattedCurrency() . $this->format($order_quantity * $this->format($tax));
        } else {
            return $this->format($order_quantity * $this->format($tax));
        }

    }

    public function getLineItemFormattedTotal($with_currency = false, $with_discount = false, $convert_currency = true)
    {

        $price = 0;
        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
            $price = $this->price;
            $discount = $this->discount;
        } else {
            $discount = geo::getInstance()->convertCurrency($this->discount, $this->currency);
        }

        //discount includes tax!

        if (intval($this->price_includes_tax) == 1) {
            if (true === $with_discount) {
                $price = $price  - $discount;
            }
        } else {
            if (true === $with_discount) {
                $price = ($price * (1 + ($this->tax_rate/100))) - $discount;
            } else {
                $price = $price * (1 + ($this->tax_rate/100));
            }
        }

        $price = $this->order_quantity * $this->format( $price);

        if (true === $with_currency) {
            return $this->getFormattedCurrency() . $price;
        } else {
            return $price;
        }
    }

    /*
     * gets the discounted price for this product.
     * @param line_total - determines whether or not to get the line total for this item according to quantity being ordered
     */
    public function getDiscountedPrice($line_total = false)
    {
        //$denominator = (true === $line_total?1:$this->order_quantity);


        if (intval($this->price_includes_tax) == 0) {
            return ($this->price * (1 + ($this->tax_rate/100))) - $this->discount;
        }

        return $this->price - $this->discount;
    }

    public function getFormattedDiscountedPrice($with_currency = false)
    {
        if (true === $with_currency) {
            return $this->getFormattedCurrency() . number_format($this->getDiscountedPrice(), 2);
        } else {
            return number_format($this->getDiscountedPrice(), 2);
        }

    }

    public function formatAmount($amount = 0, $with_currency = false, $convert = true)
    {
        return parent::formatAmount($amount, $with_currency, $convert); // TODO: Change the autogenerated stub
    }



}