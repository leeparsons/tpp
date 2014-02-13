<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:21
 */


if (!class_exists('TppStoreAbstractModelResource')) {
    include_once TPP_STORE_PLUGIN_DIR . 'site/factory/abstracts/model_resource.php';
}

Abstract class TppStoreAbstractModelBase extends TppStoreAbstractModelResource {

    Abstract public function save();
    Abstract public function validate();

}