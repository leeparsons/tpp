<?php
/**
 * User: leeparsons
 * Date: 02/03/2014
 * Time: 20:52
 */

class TppStoreAdminControllerProducts extends TppStoreAbstractBase {

    public static function renderProducts()
    {
        wp_enqueue_style('tpp_style', TPP_STORE_PLUGIN_URL . '/admin/assets/css/style.css');

        $page = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT);

        $products = self::getProductsModel()->getProducts($page);

        $total = self::getProductsModel()->getProducts(null, true);

        include TPP_STORE_PLUGIN_DIR . 'admin/views/products/list.php';
    }

    public function renderProduct()
    {

        $product_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (intval($product_id) < 1) {
            echo 'No product selected that exists!';
        } else {


            $product = $this->getProductModel()->setData(array(
                'product_id'    =>  $product_id
            ))->getProductById();

            include TPP_STORE_PLUGIN_DIR . 'admin/views/products/default.php';

        }


    }

}
 