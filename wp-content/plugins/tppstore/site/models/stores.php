<?php
/**
 * User: leeparsons
 * Date: 14/12/2013
 * Time: 17:42
 */
 
class TppStoreModelStores extends TppStoreAbstractModelResource {

    protected static $_stores = array();

    protected function __construct()
    {
        //force real singleton
    }

    public static function getInstance($store_id = 0)
    {
        if (intval($store_id) > 0) {
            //save teh store id in object cache!
            if (!isset(self::$_stores[$store_id])) {
                self::$_stores[$store_id] = TppStoreModelStore::getInstance();
                self::$_stores[$store_id]->setData(array(
                    'store_id'  =>  $store_id
                ))->getStoreByID();
            }
            return self::$_stores[$store_id];
        } else {
            return parent::getInstance();
        }
    }




}