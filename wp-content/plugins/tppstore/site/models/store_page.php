<?php
/**
 * User: leeparsons
 * Date: 29/01/2014
 * Time: 16:13
 */
 
class TppStoreModelStorePage extends TppStoreAbstractModelResource {

    public $store_id = null;
    public $content = null;
    public $page = null;

    protected $_table = 'shop_product_store_pages';

    public function getTitle()
    {
        return $this->page;
    }

    public function getSeoTitle()
    {
        return $this->page;
    }

    public function getSeoDescription()
    {
        return substr(strip_tags($this->content), 0, 150);
    }

    public function getDescription()
    {
        return substr(strip_tags($this->content), 0, 150);
    }
}