<?php
/**
 * User: leeparsons
 * Date: 03/12/2013
 * Time: 16:40
 */

if (!class_exists('TppStoreAbstractModelResource')) {

    abstract class TppStoreAbstractModelResource extends TppStoreAbstractInstantiable {


        protected $_table = 'products';

        public function getTable()
        {
            return $this->_table;
        }

        public function setData($data = array())
        {
            if (is_array($data) || is_object($data)) {
                foreach ($data as $k => $v) {
                    $this->$k = $v;
                }
            }
            return $this;
        }

        public function reset()
        {
            foreach ($this as $k => $v) {
                if ($k != '_table') {
                    $this->$k = null;
                }
            }
        }



    }

}