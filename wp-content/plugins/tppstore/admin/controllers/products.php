<?php
/**
 * User: leeparsons
 * Date: 02/03/2014
 * Time: 20:52
 */

class TppStoreAdminControllerProducts extends TppStoreAbstractAdminBase {

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

    public function sidebarFavouritesList()
    {

        $products = $this->getAdminProductsModel()->getSidebarFavouritesList();

        include TPP_STORE_PLUGIN_DIR . 'admin/views/products/sidebar_favourites.php';

    }

    public function homePageFavouritesList()
    {

        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);
        $direction = 'desc';

        switch ($sort) {
            case 'az':
                $sort = 'p.product_title';
                $direction = 'asc';
                break;
            case 'za':
                $sort = 'p.product_title';

                break;
            default:
                $sort = 'best_selling';
                break;
        }

        //$products = $this->getAdminProductsModel()->getHomepageFavouritesList();
        $products = $this->getAdminFavouritesModel()->setData(array(
            'position'  =>  'homepage'
        ))->getFavouriteProducts($sort, $direction);
        include TPP_STORE_PLUGIN_DIR . 'admin/views/products/homepage_favourites.php';
    }


    public function saveHomepageProducts()
    {
        $nonce = filter_input(INPUT_POST, 'save_homepage_nonce', FILTER_SANITIZE_STRING);

        if (!wp_verify_nonce($nonce, 'save_homepage')) {
            throw new Exception('You are not authorised to make this change to a category');
        }

        $favourites = $this->getAdminFavouritesModel();

        if (true === $favourites->readFromPost()) {
            if (true === $favourites->save()) {
                TppStoreMessages::getInstance()->addMessage('message', 'Saved');
            } else {
                TppStoreMessages::getInstance()->addMessage('error', 'Not Saved');
            }
        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Not Saved');
        }


        TppStoreMessages::getInstance()->saveToSession();
        $this->redirect($_POST['_wp_http_referer']);

    }

}
 