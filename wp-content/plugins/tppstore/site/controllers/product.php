<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 21:43
 */



class TppStoreControllerProduct extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {

        add_rewrite_rule('shop/email_share/?', 'index.php?tpp_pagename=tpp_email_share', 'top');


//        add_rewrite_rule('shop/([^/]+)/product/([^/]+)?', 'index.php?pagename=tpp_product&store_slug=$matches[1]&product_slug=$matches[2]', 'bottom');

        add_rewrite_rule('shop/store/([^/]+)/?', 'index.php?tpp_pagename=tpp_vendor-store&store_slug=$matches[1]', 'top');

        add_rewrite_rule('shop/download/([^/]+)/(.*)?', 'index.php?tpp_pagename=tpp_download&product_id=$matches[1]&shop_args=$matches[2]', 'top');

        add_rewrite_rule('shop/product/preview', 'index.php?tpp_pagename=tpp_product_preview', 'top');

        add_rewrite_rule('shop/([^/]+)?/product/([^/]+)?', 'index.php?tpp_pagename=tpp_product&store_slug=$matches[1]&product_slug=$matches[2]', 'top');



//        add_rewrite_rule('shop/(.*)/product/([^/]+)?', 'index.php?pagename=tpp_product&store_slug=$matches[1]&product_slug=$matches[2]', 'bottom');


        //flush_rewrite_rules(true);

    }



    public function templateRedirect($args = array())
    {


        $pagename = get_query_var('tpp_pagename');
        $args = get_query_var('shop_args');

        switch (strtolower($pagename)) {

            case 'tpp_email_share':
                $this->sendEmailShare();
                break;

            case 'tpp_vendor-store':

                $store_slug = get_query_var('store_slug');
                //determine the user id from the author
                $store = $this->getStoreModel()->getStoreBySlug($store_slug);
                //the author should determine the store!


                $this->pageTitle($store);
                $this->setPageDescription($store);

                $this->renderStoreProducts($store);


                break;


            case 'tpp_product':



                $product_slug = get_query_var('product_slug');
                $store_slug = get_query_var('store_slug');

                $store = $this->getStoreModel()->getStoreBySlug($store_slug);

                if (intval($store->store_id) < 1) {
                    $this->_setWpQuery404();
                    include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
                }


                $product = $this->getProductModel()->getProductBySlug($product_slug, $store->store_id);

                if ($product->product_type == 5) {
                    $product = $this->getEventModel()->setData($product)->loadEventData();
                }

                $this->renderProduct($product, $store);

                break;


            case 'tpp_download':

                if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                    $this->redirectToLogin();
                }

                $product_id = get_query_var('product_id');

                $decrypted_string = TppStoreLibraryEncryption::decrypt($args, true);

                if (strpos($decrypted_string, 'admin_logged_in=true') === false) {
                    if (false === ($order = $this->getOrderModel()->setData(array(
                            'product_id'    =>  $product_id,
                        ))->validatePurchaseByUser($user->user_id))) {
                        $this->_setWpQuery403();

                        $title = 'Oops!';

                        $message = 'Looks like you are not authorised to access this resource.';

                        include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';

                        exit;

                    }

                    $download_file = $decrypted_string;

                } else {

                    $download_file = substr($decrypted_string, strlen('admin_logged_in=true&file='));

                }


                //determine if this user has access to download this file?

                if (!class_exists('TppStoreLibraryFile')) {
                    include TPP_STORE_PLUGIN_DIR . 'libraries/file/file.php';
                }

                $file = new TppStoreLibraryFile();

                $file->setFile(WP_CONTENT_DIR . '/uploads/tpp_products/downloads/' . $product_id . '/' . $download_file);




                if (false === $file->streamFile($download_file)) {
                    $this->_setWpQuery404();

                    $title = 'Sorry, file not found';

                    include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
                    exit;
                }



                break;


            case 'tpp_product_preview':

                if (false === ($obj = TppStoreControllerDashboard::getInstance()->loadPreviewSession()) || $obj->product === false || $obj->images === false) {
                    $this->_setWpQuery403();
                    $title = 'Opps!';
                    $message = 'You are not authorised to view this page';
                    include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';

                } else {




                    if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
                        $this->_setWpQuery403();
                        $title = 'Opps!';
                        $message = 'You are not authorised to view this page';
                        include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
                    } else {
                        $product = $obj->product;
                        $images = $obj->images;

                        unset($obj);

                        include TPP_STORE_PLUGIN_DIR . 'site/views/products/preview.php';

                    }
                }

                exit;

                break;

            default:

                break;

        }



    }


    public function search()
    {
        $s = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING);

        if (is_null($s) || empty($s)) {
            $this->redirect('/shop');
        } else {

            $paged = get_query_var('paged');
            if ($paged == 0) {
                $paged = 1;
            }
            $products = $this->getProductsModel()->search($s, $paged);
            $total = $this->getProductsModel()->search($s, 0, true);
            $image_size = 'thumb';

            if (!wp_is_mobile()) {
                wp_enqueue_script('product_list_resize', TPP_STORE_PLUGIN_URL . '/site/assets/js/list_resize-ck.js', null, 1, true);
            }

            include TPP_STORE_PLUGIN_DIR . 'site/views/search.php';

        }

    }


    protected function renderProduct(TppStoreModelProduct $product, TppStoreModelStore $store)
    {

        if (is_null($product->product_id) || intval($product->product_id) <= 0) {

            $this->_setWpQuery404();

            $message = 'Sorry, the product you searched for could not be found or is no longer available.';

            $title = 'Product not found';

            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {


            $this->_setWpQueryOk();

            $cacher = new TppCacher();

            $cur = geo::getInstance()->getCurrency();

            if (wp_is_mobile() && !tpp_is_ipad()) {
                $cache_path = 'product/' . $product->product_id . '/mobile/' . strtolower($cur);
            } else {
                $cache_path = 'product/' . $product->product_id . '/desktop/' . strtolower($cur);
            }

            $cacher->setCachePath($cache_path);
            unset($cur);
            $cacher->setCacheName($product->product_id);



            $this->pageTitle(array($product, $store));
            $this::$_meta_description = $product->getDescription();
            wp_enqueue_script('jquery');

            include TPP_STORE_PLUGIN_DIR . 'site/views/product.php';

        }

        exit;
    }

    protected function renderStoreProducts(TppStoreModelStore $store)
    {




        if (intval($store->enabled) == 1) {

            $page = get_query_var('paged');

            if ($page == 0) {
                $page = 1;
            }

            $products = $store->getProducts(1, $page);

            $total = $store->getProducts(1, 0, true);

            $this->_setWpQueryOk();

            include TPP_STORE_PLUGIN_DIR . 'site/views/store_products.php';
        } else {
            $this->_setWpQuery404();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        }
        exit;

    }

    protected function renderShopFront()
    {
        include TPP_STORE_PLUGIN_DIR . 'site/views/store_front.php';
        exit;
    }


    public function uploadImage()
    {

        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            $this->_exitStatus('Error! Wrong HTTP method!', true);
        }

        if ( false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->_exitStatus('Could not authenticate your user account', true);
        }

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $this->_exitStatus('Could not authenticate your store', true);
        }

        //determine if the temporary file store has been saved to session?
        if (false === ($save_path = TppStoreControllerDashboard::getInstance()->loadTempStorePathSession($store->store_id))) {
            $this->_exitStatus('Error! Could not save your images. Please contact us for help.', true);
        }

        //determine if the save path can be created

        $directory = new TppStoreDirectoryLibrary();

        if (false === $directory->createDirectory($save_path)) {
            $this->_exitStatus('Error! Could not save your images. Please contact us for help.', true);
        }

        $lib = new TppStoreLibraryFileImage();

        if (isset($_FILES['pic'])) {


            //now try and upload the file!

            $lib->setUploadedFile($_FILES['pic']);

            if (false === $lib->validateUploadedFile($lib::$image_mime_type)) {
                $this->_exitStatus($lib->getError(), true);
            }


            if (false === $lib->moveUploadedFile($save_path)) {
                $this->_exitStatus($lib->getError(), true);
            }

        } else {

            if (false === $lib->createImageFromInput($save_path)) {
                $this->_exitStatus('Error! Please upload an image.', true);
            }

            if (false === $lib->validateUploadedFile($lib::$image_mime_type)) {
                $this->_exitStatus($lib->getError(), true);
            }

        }



        $this->_exitStatus('success', false, array(
            'saved_name'    =>  $lib->getUploadedName(),
        ));
    }

    public function getTopProducts()
    {

        return $this->getProductsModel()->getTopProducts();

    }


    public function renderLatestProductsSideBar()
    {
        $products = $this->getProductsModel()->getLatestProducts();

        if (count($products) > 0) {
            include TPP_STORE_PLUGIN_DIR . 'site/views/products/latest_products.php';
        }

    }

    public function sendEmailShare()
    {
        $product_id = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_NUMBER_INT);

        if (intval($product_id) < 1) {
            $this->_setWpQuery403();
            $this->_exitStatus('product not recognised', true);
        }

        $email_to = filter_input(INPUT_POST, 'femail', FILTER_SANITIZE_STRING);
        $email_from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_STRING);


        $product = $this->getProductModel()->setData(array(
            'product_id'    =>  $product_id
        ))->getProductById();

        if (intval($product->product_id) > 0) {
            //get the email to send the product to!
            $this->_setWpQueryOk();

            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/product_share.php';

            $html = ob_get_contents();

            ob_end_clean();

            $this->sendMail($email_to, $email_from . ' thought you might like this on the photography parlour', $html);
            $this->_exitStatus('email sent', false);
        } else {
            $this->_setWpQuery403();
            $this->_exitStatus('product not recognised', true);
        }

    }


    public function delete()
    {

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this product.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin();
        }

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this product.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin();
        }


        $product_id = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_NUMBER_INT);

        if (intval($product_id) < 1) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this product.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToDashboard('products/');
        }


        $product = $this->getProductModel()->setData(array(
            'product_id'    =>  $product_id
        ))->getProductById();

        if (intval($product->product_id) < 1 || $product->store_id != $store->store_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this product.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToDashboard('products/');
        }

        if (true === $product->delete()) {
            TppStoreMessages::getInstance()->addMessage('message', $product->product_title . ' deleted!');
        }

        TppStoreMessages::getInstance()->saveToSession();


        switch ($product->product_type) {
            case '4':
                $this->redirectToDashboard('mentors/');
                break;

            case '5':
                $this->redirectToDashboard('events/');
                break;
            default:
                $this->redirectToDashboard('products/');
                break;
        }




    }

}