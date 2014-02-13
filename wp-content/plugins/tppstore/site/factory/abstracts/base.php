<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:16
 */

if (!class_exists('TppStoreAbstractInstantiable')) {
    include_once TPP_STORE_PLUGIN_DIR . 'site/factory/abstracts/instantiable.php';
}


Abstract class TppStoreAbstractBase extends TppStoreAbstractInstantiable {

    public function getStoreModel()
    {
        return new TppStoreModelStore();
    }

    public function getProductModel()
    {
        return new TppStoreModelProduct();
    }

    public function getProductsModel()
    {
        return new TppStoreModelProducts();
    }

    public function getUserModel()
    {
        return new TppStoreModelUser();
    }

    public function getCategoriesModel()
    {
        return new TppStoreModelCategories();
    }

    public function redirectToDashboard($path = '')
    {
        header('Location: /shop/dashboard/' . $path);
        exit;
    }

    public function redirectToAccount($path = '')
    {
        if ($path !== '') {
            $path = '/' . $path;
        }

        header('Location: /shop/myaccount' . $path);
        exit;
    }

    public function redirect($path = '/')
    {
        header('Location: ' . $path);
        exit;
    }

    public function redirectToLogin()
    {
        header('Location: /shop/store_login/');
        exit;
    }

    public function __call($name, $arguments)
    {

        if (stripos($name, 'get') !== false && stripos($name, 'model') !== false) {
            $model = 'TppStoreModel' . substr(substr($name, 3), 0, stripos($name, 'model') - 3);
            if (class_exists($model)) {
                return new $model($arguments);
            }
        }

        return null;
    }


    public function __get($key)
    {



        return '';
    }

    protected function redirectToStage($stage = 1)
    {
        header('Location: /shop/store_register/' . $stage);
        exit;
    }

    protected function _setWpQueryOk()
    {

        global $wp_query;
        header('HTTP/1.1 200 OK');
        $wp_query->is_404 = false;


    }

}