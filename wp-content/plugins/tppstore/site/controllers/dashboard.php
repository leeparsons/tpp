<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 19:10
 */

if (!class_exists('TppStoreAbstractBase')) {
    require TPP_STORE_PLUGIN_DIR . 'site/factory/abstracts/base.php';
}

class TppStoreControllerDashboard extends TppStoreAbstractBase {

    public function applyRewriteRules()
    {


        add_rewrite_rule('shop/dashboard/store/upload/?', 'index.php?tpp_pagename=tpp_store_upload&path=product', 'top');

        add_rewrite_rule('shop/dashboard/product/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=edit_product&product_id=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/product/new/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_product', 'top');

        add_rewrite_rule('shop/dashboard/product/upload/?', 'index.php?tpp_pagename=tpp_product_upload&path=product', 'top');

        add_rewrite_rule('shop/dashboard/mentor_session/new/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_mentor_session', 'top');
        add_rewrite_rule('shop/dashboard/mentor_session/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=edit_mentor_session&product_id=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/mentor/new/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_mentor', 'top');
        add_rewrite_rule('shop/dashboard/mentor/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=mentor&mentor_id=$matches[1]', 'top');


        add_rewrite_rule('shop/dashboard/order/([^/]+)?', 'index.php?tpp_pagename=tpp_dashboard&path=order&args=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/profile/edit', 'index.php?tpp_pagename=tpp_profile_edit', 'top');

        add_rewrite_rule('shop/dashboard/profile/upload', 'index.php?tpp_pagename=tpp_profile_upload', 'top');

        add_rewrite_rule('shop/dashboard/preview_close', 'index.php?tpp_pagename=tpp_preview_close', 'top');

        add_rewrite_rule('shop/dashboard/?([^/]+)/page/([^/])', 'index.php?tpp_pagename=tpp_dashboard&path=$matches[1]&page=$matches[2]', 'top');

        add_rewrite_rule('shop/dashboard/store/golive', 'index.php?tpp_pagename=tpp_dashboard&path=storegolive', 'top');
        add_rewrite_rule('shop/dashboard/store/gooffline', 'index.php?tpp_pagename=tpp_dashboard&path=storegooffline', 'top');

        add_rewrite_rule('shop/dashboard/?([^/]+)', 'index.php?tpp_pagename=tpp_dashboard&path=$matches[1]', 'top');
        add_rewrite_rule('shop/dashboard/?', 'index.php?tpp_pagename=tpp_dashboard', 'top');


       //flush_rewrite_rules(true);

    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');
        $path = get_query_var('path');



        switch (strtolower($pagename)) {
            case 'tpp_dashboard':

                $this->_setWpQueryOk();

                $this->renderDashboard($path);
                break;

            case 'tpp_product_upload':

                $this->_setWpQueryOk();
                TppStoreControllerProduct::getInstance()->uploadImage();
                break;

            case 'tpp_store_upload':

                $this->_setWpQueryOk();
                TppStoreControllerStore::getInstance()->uploadImage();
                break;

            case 'tpp_profile_upload':

                $this->_setWpQueryOk();
                $this->uploadImage();

                break;
            case 'tpp_profile_edit':


                $this->enqueueAccountResources();

                $this->_setWpQueryOk();
                $this->renderProfileEdit();

                break;

            case 'tpp_preview_close':

                $this->_destroyPreviewSession();


                $this->_exitStatus('success', false);

                break;

            case 'tpp_profile':

                $args = get_query_var('args');

                $this->renderProfile($args);

                break;



            default:


                break;

        }



    }

    public function renderDashboard($part = '')
    {




        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession() )) {
            $this->redirectToLogin('redirect=' . urlencode($_SERVER['REQUEST_URI']));
        } elseif ($user->user_type == 'buyer') {
            $this->redirectToAccount();
        }

        switch (TppStoreBrowserLibrary::getInstance()->getBrowserName())
        {
            case 'Microsoft Internet Explorer':
                TppStoreMessages::getInstance()->addMessage('error', array("We've detected you are using a browser that does not support advanced features of this dashboard. Please use <b>Firefox</b>, <b>Google Chrome</b> or <b>Safari</b> to experience the full power of your dashboard!<br><br>Continuing to use your dashboard in this browser will result in un expected reults!!<br><br>Things that will not work:<br><strong>Image Uploads</strong>"));
                break;

            case 'Apple Safari':
                if (TppStoreBrowserLibrary::getInstance()->getBrowserVersion() < 6) {
                    TppStoreMessages::getInstance()->addMessage('error', array("We've detected you are using a browser that does not support advanced features of this dashboard. Please use the latest versions of either <b>Firefox</b>, <b>Google Chrome</b> or <b>Safari</b> to experience the full power of your dashboard!<br><br>Continuing to use your dashboard in this browser/version will result in un expected reults!!<br><br>Things that will not work:<br><strong>Image Uploads</strong>"));
                }
                break;

            default:

                break;
        }







        if (is_null($user->last_dashboard_visit)) {
            TppStoreMessages::getInstance()->addMessage('message', 'Hello and Congratulations on your new store!');
            $user->setData(array(
            'last_dashboard_visit'  =>  date('Y-m-d H:i:s')
            ))->save();
            TppStoreControllerUser::getInstance()->saveUserToSession($user);
        }



        wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css');

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession()) ) {
            //try and load it from the database

            $store = $this->getStoreModel()->setData(array('user_id'    =>  $user->user_id));
            $store->getStoreByUserID();

            if (intval($store->store_id) > 0) {
                TppStoreControllerStore::getInstance()->saveStoreToSession($store);
            } else {
                $store->reset();
            }
        }



        //if the user does not have a store created, stop them doing anything else that depends on their store!

        if (intval($store->store_id) < 1) {
            $product_count = 0;
            $part = $part == 'store'?$part:'';
        } else {
            //for the sidebar
            $product_count = $this->getProductCount($store);
            $mentor_sessions_count = $this->getMentorSessionCount($store);
        }


        if ($part !== 'store-pages' && $part !== 'store') {
            if (intval($store->store_id) < 1) {
                //try and load it from the database
                TppStoreMessages::getInstance()->addMessage('message', 'To get started with selling your products, <a href="/shop/dashboard/store" class="btn btn-primary">create your store</a>.');
            } else {

                //load the store from the database to make sure we are getting the latest information for it!
                $store->getStoreByID();

                if (!filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)) {
                    $store->setData(array(
                        'enabled'   =>  0
                    ))->save(false, true);
                    TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                    TppStoreMessages::getInstance()->addMessage('message', 'To complete your seller registration please complete the following: <br><br>Paypal Email address for receiving payments <a href="/shop/dashboard/store#ecommerce" class="btn btn-primary">Complete this step</a>');
                } elseif (strpos($part, 'product') === false && strpos($part, 'mentor_session') === false && $product_count == 0 && $mentor_sessions_count == 0) {
                    TppStoreMessages::getInstance()->addMessage('message', 'Ready to start listing products? <a href="/shop/dashboard/product_add" class="btn btn-primary">click here</a>.');
                }
            }
            //determine if the user has set any store pages yet?
            if (strpos($part, 'product') === false && strpos($part, 'mentor') === false  && intval($store->approved) == 1 && $store->getPages(false)->getPageCount() < 1) {
                TppStoreMessages::getInstance()->addMessage('message', array('store_terms' => 'Complete your store by filling in your own store <a href="/shop/dashboard/store-pages" class="btn btn-primary">terms and conditions</a>, which could include your policy for refunds and cancellations, what happens if you cannot fulfill an order and your sale processes and timescales for services and mentor sessions.'));
            }
        }

        $this->_destroyPreviewSession();

        switch ($part) {

            case 'orders':
                TppStoreControllerOrders::getInstance()->renderDashboardList($user, $store);
                break;

            case 'order':
                $order_id = get_query_var('args');
                TppStoreControllerAccount::getInstance()->renderOrder($order_id, $user, $store);
                break;

            case 'products':
            case 'mentors':

                $page = get_query_var('page')?:1;

                if ($page > 1) {
                    $limit = (($page-1) * 20) + 1;
                } else {
                    $limit = 0;
                }
                unset($page);

                switch ($part) {
                    case 'products':
                        $type = 'Products';
                    break;

                    default:
                        $type = 'Mentor Sessions';
                        break;
                }

                $products = $this->getProductsModel()->setData(array(
                    'store_id'  =>  $store->store_id
                ))->getProductsByStore($limit, 20, 'all', $part == 'mentors');

                unset($limit);

                wp_enqueue_script('table', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/table-ck.js', null, 1, true);

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/products_list.php';
                break;


            case 'product_add':

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/add_splash.php';

                break;
            case 'new_product':
            case 'edit_product':
            case 'new_mentor_session':
            case 'edit_mentor_session':


                if (!class_exists('TppStoreBrowserLibrary')) {
                    include TPP_STORE_PLUGIN_DIR . 'libraries/browser.php';
                }

                $product_id = get_query_var('product_id');



                if (is_null($product_id) || $product_id == '') {

                    //new product
                    $product = $this->getProductModel()->setData(array(
                        'store_id'    =>  $store->store_id
                    ));

                } else {



                    $product = $this->getProductModel()->setData(array(
                        'product_id'    =>  $product_id
                    ))->getProductByID();
                }

                if ($product->store_id != $store->store_id) {
                    TppStoreMessages::getInstance()->addMessage('error', array('product'    =>  'You are not authorised to edit this product'));
                    TppStoreMessages::getInstance()->saveToSession();
                    include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/default.php';
                } else {


                    if ($product->readFromPost()) {
                        $preview = filter_input(INPUT_POST, 'preview', FILTER_SANITIZE_NUMBER_INT);

                        //trial
                        if (intval($preview) === 1) {
                            //show the preview!

                            $images = $this->getProductImagesModel()->setData(array(
                                'store_id'  =>  $store->store_id
                            ));
                            $images->retrieveUsingSession(true);

                            //arrange the images!

                            $tmp = $images->images;

                            $ordered_images = array();

                            foreach ($tmp as $index => $image) {
                                $ordered_images[intval($image->ordering)] = $image;
                            }

                            ksort($ordered_images);



                            $product->getDiscount(false)->readFromPost();

                            //store any images into this product...
                            $this->_setPreviewSession($product, $ordered_images);

                            $this->_setJsonHeader();
                            $this->_exitStatus('success', false, array(
                                'location'  =>  '/shop/product/preview'
                            ));

                        }

                        if ($product->save()) {



//                            if ($preview == 1) {
//
//                                $this->deleteTempStorePathSession();
//                                $this->saveTempStorePathSession($product->product_id, $product->store_id);
//
//                                $this->_setJsonHeader();
//                                $this->_exitStatus('success', false, array(
//                                    'location'  =>  $product->getPermalink() . '?preview=1',
//                                    'product'   =>  $product->product_id
//                                ));
//                            } else {
                                $this->deleteTempStorePathSession();
                                if ($product->product_type == 4) {
                                    if ($product->enabled && $store->enabled) {

                                        TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your session and it is now live on the site! <a href="' . $product->getPermalink() . '" class="btn btn-primary" target="_blank">View now</a>');
                                        TppStoreMessages::getInstance()->addMessage('message', 'Share your session: <div class="fb-share-button" data-href="' . $product->getPermalink() . '" data-type="button"></div>');

                                    } else {
                                        TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your session!');

                                    }
                                    TppStoreMessages::getInstance()->saveToSession();
                                    $this->redirectToDashboard('mentor/edit/' . $product->product_id);
                                } else {
                                    if (intval($product->enabled) == 1 && intval($store->enabled) == 1) {

                                        TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your product and it is now live on the site! <a href="' . $product->getPermalink() . '" class="btn btn-primary" target="_blank">View now</a>');
                                        TppStoreMessages::getInstance()->addMessage('message', 'Share your product: <div class="fb-share-button" data-href="' . $product->getPermalink() . '" data-type="button"></div>');

                                    } else {
                                        TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your product!');

                                    }
                                    TppStoreMessages::getInstance()->saveToSession();
                                    $this->redirectToDashboard('product/edit/' . $product->product_id);
                                }
                            //}
                        } else {
                            if (intval($product->product_id) == 0) {
                                $product->setData(array(
                                    'enabled'   =>  0
                                ));
                            }
                        }
//                        } elseif ($preview == 1) {
//                            $this->_exitStatus('error', true, array('errors' =>  TppStoreMessages::getInstance()->getMessages()));
//                        }
                    } else {



                        $this->saveTempStorePathSession($product_id, $store->store_id);
                    }

                    $store_id = $store->store_id;



                    switch ($part) {
                        case 'new_product':
                        case 'edit_product':



                        $categories_model = $this->getCategoriesModel();
                        $categories_model->getCategories(array('heirarchical'   =>  true, 'type'    =>  'assoc'));

                        $categories = $categories_model->categories;

                        //if the product has not been saved, then it will have id 0, and we will need to set a session
                        //to determine the temporary store to save images into


                        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/product_edit.php';

                            break;

                        default:
                            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/mentor/default.php';

                            break;
                    }

                }



                break;

            case 'mentor':
            case 'new_mentor':

                TppStoreControllerMentors::getInstance()->renderMentorForm();
            exit('Sorry, we are currently working on updating this section');

                break;

            case 'store-pages':

                if (intval($store->store_id) < 1) {

                    TppStoreMessages::getInstance()->addMessage('error', 'Create a store first');

                    $this->redirectToDashboard();

                }



                if ($store->getPages()->readFromPost()) {
                    if ($store->getPages()->save()) {
                        $this->redirectToDashboard('store-pages');
                    }
                }

                $store_pages = $this->getStorePagesModel()->setData(array(
                    'store_id'  =>  $store->store_id
                ))->getPages();

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/store-pages.php';

                break;

            case 'storegolive':
            case 'storegooffline':


                if (intval($store->store_id) > 0) {

                    if ($part == 'storegolive') {
                        if (false === $store->setData(array(
                            'enabled'   =>  1
                        ))->save()) {
                            TppStoreMessages::getInstance()->addMessage('error', array('There was an error making your store go live.'));
                            TppStoreMessages::getInstance()->saveToSession();
                            $this->redirectToDashboard();
                        } else {
                            TppStoreMessages::getInstance()->addMessage('message', array('Your store is live!'));
                            TppStoreMessages::getInstance()->saveToSession();
                            TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                            TppStoreControllerStore::getInstance()->sendStoreGoLiveNotification($store);
                            $this->redirectToDashboard();
                        }
                    } else {
                        if (false === $store->setData(array(
                                'enabled'   =>  0
                            ))->save()) {
                            TppStoreMessages::getInstance()->addMessage('error', array('There was an error making your store go offline.'));
                            TppStoreMessages::getInstance()->saveToSession();
                            $this->redirectToDashboard();
                        } else {
                            TppStoreMessages::getInstance()->addMessage('message', array('Your store is now offline.'));
                            TppStoreMessages::getInstance()->saveToSession();
                            TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                            TppStoreControllerStore::getInstance()->sendStoreGoOfflineNotification($store);
                            $this->redirectToDashboard();
                        }
                    }

                } else {
                    TppStoreMessages::getInstance()->addMessage('error', array('Create a store first'));
                    TppStoreMessages::getInstance()->saveToSession();
                    $this->redirectToDashboard();
                }

                break;

            case 'store':

                $store->getStoreById();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    if (false !== $store->readFromPost()) {

                        if (1 === ($resubmit = intval(filter_input(INPUT_POST, 'resubmit', FILTER_SANITIZE_NUMBER_INT)))) {
                            $store->setData(array('approved' => 0));
                        }

                        $alert_enabled_state = -1;

                        if (intval($store->enabled) == 0 && intval($store->approved) == 1) {
                            $alert_enabled_state = 0;
                        }

                        if (true === $store->save()) {
                            $this->deleteTempStorePathSession();
                            TppStoreControllerStore::getInstance()->saveStoreToSession($store);

                            $alert_enabled_state += intval($store->enabled);

                            if ($resubmit === 1) {
                                TppStoreControllerStore::getInstance()->sendApplicationNotification($store, $user);
                            } elseif ($alert_enabled_state == 1) {
                                TppStoreControllerStore::getInstance()->sendStoreGoLiveNotification($store);
                            }
                            $this->redirectToDashboard();
                        }
                    } else {
                        TppStoreMessages::getInstance()->saveToSession();
                        $this->redirectToDashboard('store');
                    }
                } else {
                    if (intval($store->store_id) < 1) {
                        TppStoreControllerStore::getInstance()->saveStoreTempPath($store);
                    } else {
                        //refresh store incase anything changed like approval
                        $store->getStoreByID();
                        TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                    }
                }

                wp_enqueue_script('jquery', 'jquery', 'jquery', '1', true);
                wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', 'jquery', '1', true);
                wp_enqueue_script('file_uploads', '/assets/js/file_upload.js', 'jquery', '1', true);
                wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css');
                wp_enqueue_script('store_js', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/store-ck.js', 'jquery', 1, true);

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/store.php';
                break;

            case 'orders':

                TppStoreControllerOrder::getInstance()->renderDashboardList();

                break;

            default:




                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/default.php';
                break;
        }
        //get the products for this store



        exit;
    }

    public function saveTempStorePathSession($product_id = null, $store_id = null)
    {
        if ((is_null($product_id) || $product_id == 0) && !is_null($store_id)) {
            $_SESSION['images_store'][$store_id]['tmp_product'] = WP_CONTENT_DIR . '/uploads/store/' . $store_id . '/new_' . uniqid() . '/';
        } elseif (!is_null($store_id)) {
            $_SESSION['images_store'][$store_id]['tmp_product'] = WP_CONTENT_DIR . '/uploads/store/' . $store_id . '/' . $product_id  . '/';
        }
    }

    private function deleteTempStorePathSession()
    {
        if (!empty($_SESSION['images_store'])) {
            $_SESSION['images_store'] = null;
            unset($_SESSION['images_store']);
        }
    }

    public function loadTempStorePathSession($store_id = null)
    {
        if (!is_null($store_id)) {
            return empty($_SESSION['images_store'][$store_id]['tmp_product'])?false:$_SESSION['images_store'][$store_id]['tmp_product'];
        } else {
            return false;
        }
    }

    public function getProductCount(TppStoreModelStore $store)
    {
        return $this->getProductsModel()->setData(array('store_id'    =>  $store->store_id))->getProductCountByStore(false, 'all');
    }

    public function getMentorSessionCount(TppStoreModelStore $store)
    {
        return $this->getProductsModel()->setData(array('store_id'    =>  $store->store_id))->getProductCountByStore(true, 'all');
    }


    public function loadPreviewSession() {

        if (isset($_SESSION['preview_session']) && !empty($_SESSION['preview_session'])) {
            $obj = new stdClass();
            $obj->product = unserialize($_SESSION['preview_session']['product']);
            $obj->images = unserialize($_SESSION['preview_session']['images']);
            return $obj;
        } else {
            return false;
        }


    }

    private function _setPreviewSession(TppStoreModelProduct $product, $images = false)
    {
        $_SESSION['preview_session']['product'] = serialize($product);
        $_SESSION['preview_session']['images'] = serialize($images);
    }

    private function _destroyPreviewSession()
    {
        $_SESSION['preview_session'] = null;
        unset($_SESSION['preview_session']);
    }



}
