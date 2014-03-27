<?php
/**
 * User: leeparsons
 * Date: 30/12/2013
 * Time: 12:07
 */
 
class TppStoreAdminControllerDefault {


    public static function renderDashboard()
    {
        include TPP_STORE_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function renderProductsMenu()
    {
        include TPP_STORE_PLUGIN_DIR . 'admin/views/menu/products.php';
    }

}