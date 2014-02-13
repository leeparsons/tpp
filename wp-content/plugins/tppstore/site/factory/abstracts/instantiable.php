<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 21:53
 */

Abstract class TppStoreAbstractInstantiable {

    protected static $_instance = array();

    public static function getInstance()
    {

        $class = get_called_class();

        if (empty(self::$_instance[md5($class)])) {

            self::$_instance[md5($class)] = new $class();
        }
        return self::$_instance[md5($class)];
    }

}