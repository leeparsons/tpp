<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 21:53
 */

if (!class_exists('TppStoreAbstractInstantiable')) {
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



        protected function startSession()
        {
            ob_start();
            if (!session_id()) {
                session_start();
            }
            ob_end_clean();
        }
    }
}
