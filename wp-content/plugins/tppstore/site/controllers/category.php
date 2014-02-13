<?php
/**
 * User: leeparsons
 * Date: 12/12/2013
 * Time: 20:37
 */
 
class TppStoreControllerCategory extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {

//        add_action( 'template_redirect', function() {
//            TppStoreControllerCategory::getInstance()->templateRedirect();
//        } );

        add_rewrite_rule('shop/category/(.*)/page/([^/]+)?', 'index.php?pagename=tpp_category&category_slug=$matches[1]&page=$matches[2]', 'top');


        add_rewrite_rule('shop/category/(.*)?', 'index.php?pagename=tpp_category&category_slug=$matches[1]', 'top');

        add_filter('query_vars', function($vars) {
            $vars[] = 'category_slug';
            return $vars;
        } );

        //flush_rewrite_rules(true);
    }


    public function templateRedirect()
    {
        $pagename = get_query_var('name');
        $slug = get_query_var('category_slug');

        $slug = trim($slug);

        if ($slug !== '') {

            if (substr($slug, -1) == '/') {
                $slug = substr($slug, 0, -1);
            }

            if (false !== strpos($slug, '/')) {

                $slug = substr($slug, strrpos($slug, '/') + 1);
            }


            switch (strtolower($pagename)) {
                case 'tpp_category':

                    $this->_setWpQueryOk();



                    $this->renderCategoryPage($slug);
                    break;

                default:

                    break;
            }
            exit;
        }
    }

    public function getFeaturedCategories($product_count = true)
    {


        //force it to get the top level categories

        $model = $this->getCategoriesModel();
        $model->getCategories(array(
            'featured'      =>  true,
            'product_count' =>  $product_count
        ));


        return $model->categories;
    }


    private function renderCategoryPage($slug = '')
    {

        $category = $this->getCategoryModel();

        $category->getCategoryBySlug($slug);

        if (intval($category->category_id) <= 0) {

            $message = 'Sorry, that category does not exist.';

            $title = 'Category Not Found';

            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {

            $page = get_query_var('page');

            if (intval($page) == 0) {
                $page = 1;
            }

            if (intval($page) > 0) {
                $limit = intval($page)*20;
            } else {
                $limit = 20;
            }


            $products = $category->getProducts(true, array(
                'start' =>  20*(intval($page) - 1),
                'limit' =>  $limit
            ));

            $total = 0;

            if (count($products) > 0) {
                $total = $category->getProducts(false, array('count'    =>  true));
            }

            include TPP_STORE_PLUGIN_DIR . 'site/views/category.php';
        }

        exit;

    }
}