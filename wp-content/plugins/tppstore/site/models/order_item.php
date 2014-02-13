<?php

class TppStoreModelOrderItem extends TppStoreAbstractModelBaseProduct {

    public $order_id = 0;
    public $data = null;

    public $unserialized = false;

    protected $_table = 'shop_order_items';


    public function getData()
    {
        if (false === $this->unserialized) {
            $product = unserialize($this->data);
            $this->setData($product);
            $this->unserialized = true;
        }
        return $this;
    }

}