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

    public function getPermalink()
    {
        return TppStoreModelProduct::getInstance()->setData(array(
            'product_id'    =>  $this->product_id,
            'product_slug'  =>  $this->product_slug
        ))->getPermalink();
    }

    public function getStoreEmail()
    {
        return filter_var($this->store_email, FILTER_VALIDATE_EMAIL);
    }



    /*
     * gets the discounted price for this product.
     * @param line_total - determines whether or not to get the line total for this item according to quantity being ordered
     */
    public function getDiscountedPrice($line_total = false)
    {
        $denominator = (true === $line_total?1:$this->order_quantity);
        return $this->price - $this->discount/$denominator;
    }

    public function getFormattedDiscountedPrice($with_currency = false)
    {
        if (true === $with_currency) {
            return $this->getFormattedCurrency() . number_format($this->getDiscountedPrice(), 2);
        } else {
            return number_format($this->getDiscountedPrice(), 2);
        }
    }


}