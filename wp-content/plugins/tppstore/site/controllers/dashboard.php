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

        add_rewrite_rule('shop/dashboard/product_delete/?', 'index.php?tpp_pagename=tpp_product_delete', 'top');
        add_rewrite_rule('shop/dashboard/mentor_delete/?', 'index.php?tpp_pagename=tpp_mentor_delete', 'top');

        add_rewrite_rule('shop/dashboard/product/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=edit_product&product_id=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/product/new/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_product', 'top');

        add_rewrite_rule('shop/dashboard/product/upload_file/?', 'index.php?tpp_pagename=tpp_product_upload_file&path=product', 'top');

        add_rewrite_rule('shop/dashboard/product/upload/?', 'index.php?tpp_pagename=tpp_product_upload&path=product', 'top');

        add_rewrite_rule('shop/dashboard/mentor-sessions/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=mentor_sessions&mentor_id=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/mentor_session/new/(.*)/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_mentor_session&mentor_id=$matches[1]', 'top');
        add_rewrite_rule('shop/dashboard/mentor_session/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=edit_mentor_session&product_id=$matches[1]', 'top');


        add_rewrite_rule('shop/dashboard/mentor/new/?', 'index.php?tpp_pagename=tpp_dashboard&path=new_mentor', 'top');
        add_rewrite_rule('shop/dashboard/mentor/edit/([^/]+)/?', 'index.php?tpp_pagename=tpp_dashboard&path=mentor&mentor_id=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/mentor/upload/?', 'index.php?tpp_pagename=tpp_mentor_upload', 'top');


        add_rewrite_rule('shop/dashboard/purchase/([^/]+)?', 'index.php?tpp_pagename=tpp_dashboard&path=purchase&args=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/profile/edit', 'index.php?tpp_pagename=tpp_profile_edit', 'top');

        add_rewrite_rule('shop/dashboard/profile/upload', 'index.php?tpp_pagename=tpp_profile_upload', 'top');

        add_rewrite_rule('shop/dashboard/preview_close', 'index.php?tpp_pagename=tpp_preview_close', 'top');

        add_rewrite_rule('shop/dashboard/?([^/]+)/page/([^/])', 'index.php?tpp_pagename=tpp_dashboard&path=$matches[1]&page=$matches[2]', 'top');

        add_rewrite_rule('shop/dashboard/store/golive', 'index.php?tpp_pagename=tpp_dashboard&path=storegolive', 'top');
        add_rewrite_rule('shop/dashboard/store/gooffline', 'index.php?tpp_pagename=tpp_dashboard&path=storegooffline', 'top');

        add_rewrite_rule('shop/dashboard/order/(.*)/?', 'index.php?tpp_pagename=tpp_dashboard&path=order&args=$matches[1]', 'top');

        add_rewrite_rule('shop/dashboard/events', 'index.php?tpp_pagename=tpp_dashboard&path=events', 'top');

        add_rewrite_rule('shop/dashboard/event/new', 'index.php?tpp_pagename=tpp_dashboard&path=new_event', 'top');

        add_rewrite_rule('shop/dashboard/event/edit/(.*)?', 'index.php?tpp_pagename=tpp_dashboard&path=edit_event&product_id=$matches[1]', 'top');


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


            case 'tpp_product_delete':


                TppStoreControllerProduct::getInstance()->delete();
                break;

            case 'tpp_mentor_delete':
                TppStoreControllerMentors::getInstance()->delete();
                break;

            case 'tpp_product_upload':

                $this->_setWpQueryOk();
                TppStoreControllerProduct::getInstance()->uploadImage();
                break;

            case 'tpp_product_upload_file':

                $this->_setWpQueryOk();
                TppStoreControllerProduct::getInstance()->uploadFile();

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
                //if (TppStoreBrowserLibrary::getInstance()->getBrowserVersion() < 6 || TppStoreBrowserLibrary::getInstance()->getBrowserVersion() >= 7) {
                    TppStoreMessages::getInstance()->addMessage('error', array("We have released an update for Safari image uploads. Please clear your browser cache to make sure you have the latest version of the website.<script>alert('FYI: we have released a fix for image uploads for Safari. Please try it out and let us know if you find any glitches! You may need to clear your browser cache to see the improvements.')</script>"));
                //}
                break;

            default:

                break;
        }


        $this->_is_dashboard = true;


        if ($user->user_type == 'store_owner') {
            if ($user->user_src == '') {
                TppStoreMessages::getInstance()->addMessage('error', array('profile-image' => 'Complete your profile: <a href="/shop/dashboard/profile/edit/" class="btn btn-primary">upload an image</a><br><br>Uploading a personal photo of you builds trust with your customers and can increase sales.'));
            }
        }



        if (strtotime($user->last_dashboard_visit) < strtotime('-1days')) {
            TppStoreMessages::getInstance()->addMessage('message', 'Hello and Congratulations on your new store!');
            $user->setData(array(
                'last_dashboard_visit'  =>  date('Y-m-d H:i:s')
            ))->save();
            TppStoreControllerUser::getInstance()->saveUserToSession($user);
        }



        wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css', array(), '1');


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
            $mentor_count = 0;
            $event_count = 0;
            $part = $part == 'store'?$part:'';
        } else {
            //for the sidebar
            $product_count = $this->getProductCount($store);
            $mentor_count = $this->getMentorCount($store);
            $event_count = $this->getEventCount($store);
        }


        if ($part !== 'store-pages' && $part !== 'store') {
            if (intval($store->store_id) < 1) {
                //try and load it from the database
                TppStoreMessages::getInstance()->addMessage('message', 'To get started with selling your products, <a href="/shop/dashboard/store/" class="btn btn-primary">create your store</a>.');
            } else {

                //load the store from the database to make sure we are getting the latest information for it!
                $store->getStoreByID();

                if (!filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)) {
                    TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                    TppStoreMessages::getInstance()->addMessage('error', 'To complete your seller registration please complete the following: <br><br>Paypal Email address for receiving payments <a href="/shop/dashboard/store/#ecommerce" class="btn btn-primary">Complete this step</a>');
                } elseif (strpos($part, 'product') === false && strpos($part, 'mentor_session') === false && $product_count == 0 && $mentor_count == 0) {
                    TppStoreMessages::getInstance()->addMessage('message', 'Ready to start listing products? <a href="/shop/dashboard/product_add/" class="btn btn-primary">click here</a>.');
                }
            }
            //determine if the user has set any store pages yet?
//            if (strpos($part, 'product') === false && strpos($part, 'mentor') === false  && intval($store->approved) == 1 && $store->getPages(false)->getPageCount() < 1) {
//                TppStoreMessages::getInstance()->addMessage('message', array('store_terms' => 'Complete your store by filling in your own store <a href="/shop/dashboard/store-pages/" class="btn btn-primary">terms and conditions</a>, which could include your policy for refunds and cancellations, what happens if you cannot fulfill an order and your sale processes and timescales for services and mentor sessions.'));
//            }
        }

        $this->_destroyPreviewSession();

        switch ($part) {

            case 'purchases':
                TppStoreControllerOrders::getInstance()->renderPurchaseList($user, $store);
                break;

            case 'orders':
                TppStoreControllerOrders::getInstance()->renderReceivedOrders($user, $store);
                break;

            case 'order':
                TppStoreControllerOrders::getInstance()->renderReceivedOrder($user, $store);
                break;

            case 'purchase':
                $order_id = get_query_var('args');
                TppStoreControllerOrders::getInstance()->renderPurchase($order_id, $user, $store);
                break;

            case 'products':


                $page = get_query_var('page')?:1;

                if ($page > 1) {
                    $limit = (($page-1) * 20);
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

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/' . $part . '/list.php';
                break;

            case 'mentors':

                $page = get_query_var('page')?:1;

                if ($page > 1) {
                    $limit = (($page-1) * 20);
                } else {
                    $limit = 0;
                }
                unset($page);

                wp_enqueue_script('table', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/table-ck.js', null, 1, true);

                $mentors = $this->getMentorsModel()->setData(array(
                    'store_id'  =>  $store->store_id
                ))->getMentorsByStore($limit);

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/mentors/list.php';

                break;

            case 'product_add':



                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/add_splash.php';

                break;
            case 'new_product':
            case 'edit_product':
            case 'new_mentor_session':
            case 'edit_mentor_session':


                //TppStoreMessages::getInstance()->addMessage('error', 'Please note we are in the process of upgrading the products. Your images may not upload. Please check back in an hour.');

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


                    $already_enabled = $product->enabled;

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
                            $this->setPreviewSession($product, $ordered_images);

                            $this->_setJsonHeader();
                            $this->_exitStatus('success', false, array(
                                'location'  =>  '/shop/product/preview'
                            ));

                        }



                        if ($product->save()) {


                            if (intval($store->enabled) == 1 && $product->notify_live === true) {
                                TppStoreControllerProduct::getInstance()->sendProductLiveNotification($product, $store);
                            }

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

                                        if (intval($already_enabled) == 0 && $product->enabled == 1) {
                                            TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your session and it is now live on the site! <a href="' . $product->getPermalink() . '" class="btn btn-primary" target="_blank">View now</a>');
                                        }

                                        TppStoreMessages::getInstance()->addMessage('message', '<div class="align-left" style="margin-right:10px;">Share your session: </div><div class="align-left" style="margin-right:10px;"><div class="fb-share-button" data-href="' . $product->getPermalink() . '" data-type="button"></div></div> <div class="align-left" style="margin-right:10px;"><script type="IN/Share" data-url="' . $product->getPermalink() . '" data-counter="right"></script><script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script></div> <div class="align-left" style="margin-right:10px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $product->getpermalink() . '">Tweet</a></div><div class="align-left" style="margin-right:10px;"><div class="g-plusone" data-href="' . $product->getPermalink() . '"></div></div>');



                                    } else {
                                        if (intval($already_enabled) == 0 && $product->enabled == 1 && $product->getImages(0, false, true) > 0) {
                                            TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your session!');
                                        } else {
                                            TppStoreMessages::getInstance()->addMessage('message', 'Session Saved!');
                                        }
                                    }
                                    TppStoreMessages::getInstance()->saveToSession();


                                    $product->getMentor();


                                    $this->redirectToDashboard('mentor-sessions/' . $product->getMentor()->mentor_id);
                                } else {
                                    if (intval($product->enabled) == 1 && intval($store->enabled) == 1) {

                                        TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your product and it is now live on the site! <a href="' . $product->getPermalink() . '" class="btn btn-primary" target="_blank">View now</a>');
                                        TppStoreMessages::getInstance()->addMessage('message', '<div class="align-left" style="margin-right:10px;">Share your product: </div><div class="align-left" style="margin-right:10px;"><div class="fb-share-button" data-href="' . $product->getPermalink() . '" data-type="button"></div></div> <div class="align-left" style="margin-right:10px;"><script type="IN/Share" data-url="' . $product->getPermalink() . '" data-counter="right"></script><script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script></div> <div class="align-left" style="margin-right:10px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $product->getpermalink() . '">Tweet</a></div><div class="align-left" style="margin-right:10px;"><div class="g-plusone" data-href="' . $product->getPermalink() . '"></div></div>');                                    } elseif (intval($product->enabled == 0 && $already_enabled == 1)) {
                                        TppStoreMessages::getInstance()->addMessage('message', 'Your product is offline!');
                                    } else {
                                        TppStoreMessages::getInstance()->addMessage('message', 'Your product has been saved!');
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
                            } else {



                                TppStoreMessages::getInstance()->saveToSession();

                                if ($product->product_type < 4) {
                                    $this->redirectToDashboard('product/edit/' . $product->product_id);
                                } elseif ($product->product_type == 4) {
                                    $this->redirectToDashboard('mentor_session/edit/' . $product->product_id);
                                } else {
                                    $this->redirectToDashboard('event/edit/' . $product->product_id);
                                }


                            }
                        }
//                        } elseif ($preview == 1) {
//                            $this->_exitStatus('error', true, array('errors' =>  TppStoreMessages::getInstance()->getMessages()));
//                        }
                    } else {



                        $this->saveTempStorePathSession($product_id, $store->store_id);
                    }

                    $store_id = $store->store_id;

                    if (empty($_POST)) {
                        $product->deleteTemporaryImages();
                    }

                    TppStoreControllerProduct::getInstance()->setProductUploadDirectoryFromSession($product->product_id);


                    switch ($part) {
                        case 'new_product':
                        case 'edit_product':



                        $categories_model = $this->getCategoriesModel();
                        $categories_model->getCategories(array(
                            'heirarchical'  =>  true,
                            'type'          =>  'assoc',
                            'exclude'        =>  array(
                                3,
                                2
                            )
                        ));

                        $categories = $categories_model->categories;

                        //if the product has not been saved, then it will have id 0, and we will need to set a session
                        //to determine the temporary store to save images into


                        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/product_edit.php';

                            break;

                        default:

                            $categories_model = $this->getCategoriesModel();
                            $categories_model->getCategories(array(
                                'heirarchical'  =>  true,
                                'type'          =>  'assoc',
                                'category_id'   =>  array(
                                    2
                                )
                            ));

                            $categories = $categories_model->categories;

                            //get the mentor that should be selected

                            $selected_mentor = get_query_var('mentor_id');

                            $mentors = $this->getMentorsModel()->setData(array(
                                'store_id'  =>  $store->store_id
                            ))->getMentorsByStore(0, 0);

                            if ($selected_mentor == '') {

                                $product->getMentor();

                                $selected_mentor = $product->getMentor()->mentor_id;
                            }


                            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/mentor_session/default.php';

                            exit;
                            break;
                    }

                }



                break;

            case 'mentor_sessions':


                $mentor_id = get_query_var('mentor_id');

                if (intval($mentor_id) == 0) {
                    TppStoreMessages::getInstance()->addMessage('error', 'No Mentor Selected');
                    TppStoreMessages::getInstance()->saveToSession();
                    $this->redirectToDashboard();
                }


                $mentor = $this->getMentorModel()->setData(array(
                    'mentor_id' =>  $mentor_id
                ))->getMentorById();

                if (intval($mentor_id) > 0 && $mentor->store_id != $store->store_id) {
                    $this->redirectToDashboard();
                }

                $page = get_query_var('page')?:1;

//                if ($page > 1) {
//                    $limit = (($page-1) * 20);
//                } else {
//                    $limit = 0;
//                }

                $mentors_model = $this->getMentorsModel()->setData(array(
                    'store_id'  =>  $store->store_id
                ));

                $mentor_session_count = $mentors_model->getMentorSessionCountByMentor($mentor_id);

                if ($mentor_session_count > 0) {
                    $mentor_sessions = $mentors_model
                        ->getMentorSessionsByMentor($mentor_id, $page, 20);
                } else {
                    $mentor_sessions = array();
                }

                unset($page);

                unset($limit);


                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/mentor_sessions/list.php';

                break;

            case 'mentor':
            case 'new_mentor':

                TppStoreControllerMentors::getInstance()->renderMentorForm($store);

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
                            TppStoreMessages::getInstance()->addMessage('message', array('View your store: <a target="_blank" href="' . $store->getPermalink(false, true) . '">open store page</a>'));
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

                wp_enqueue_script('jquery', 'jquery', array('jquery'), 1, true);
                wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', array('jquery'), '1', true);
                wp_enqueue_script('file_uploads', '/assets/js/file_upload.js', array('jquery'), '1', true);
                wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css', array(), '1');
                wp_enqueue_script('store_js', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/store-ck.js', array('jquery'), 1, true);

                include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/store.php';
                break;

            case 'events':

                TppStoreControllerEvents::renderDashboardList($store, $user, $product_count, $event_count, $mentor_count);

                break;

            case 'new_event':
            case 'edit_event':
                TppStoreControllerEvents::getInstance()->renderEventDashboardForm($store, $user);

                break;

            case 'messages':
                TppStoreControllerMessages::getInstance()->renderMessageList();
                exit;
                break;

            case 'message':
                TppStoreControllerMessages::getInstance()->renderMessage();
                exit;
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

    public function getOrderCount(TppStoreModelStore $store)
    {
        return $this->getOrderModel()->setData(array(
            'store_id' =>   $store->store_id
        ))->getOrdersReceivedByStore(null, true);
    }

    public function getPurchaseCount(TppStoreModelUser $user)
    {
        return $this->getOrderModel()->setData(array(
            'user_id'   =>  $user->user_id
        ))->getOrdersByUser(null, true);
    }

    public function getProductCount(TppStoreModelStore $store)
    {
        return $this->getProductsModel()->setData(array('store_id'    =>  $store->store_id))->getProductCountByStore(false, 'all');
    }

    public function getMentorCount(TppStoreModelStore $store)
    {
        return $this->getMentorsModel()->setData(array('store_id'    =>  $store->store_id))->getMentorCountByStore();
    }

    public function getUnreadMessagesCount(TppStoreModelUser $user)
    {
        return $this->getMessagesModel()->setData(array(
            'receiver'   =>  $user->user_id
        ))->getUnreadMessages(null, true);
    }

    public function getEventCount(TppStoreModelStore $store)
    {
        return $this->getEventsModel()->setData(array('store_id'    =>  $store->store_id))->getEventCountByStore();
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

    public function setPreviewSession(TppStoreModelProduct $product, $images = false)
    {
        $_SESSION['preview_session']['product'] = serialize($product);
        $_SESSION['preview_session']['images'] = serialize($images);
    }

    public function _destroyPreviewSession()
    {
        $_SESSION['preview_session'] = null;
        unset($_SESSION['preview_session']);
    }


    public function renderSidebar()
    {
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $user = $this->getUserModel();
        }

        $store = TppStoreControllerStore::getInstance()->loadStoreFromSession();

        if (intval($store->store_id) < 1) {
            $product_count = 0;
            $mentor_count = 0;
            $event_count = 0;
        } else {
            //for the sidebar
            $product_count = $this->getProductCount($store);
            $mentor_count = $this->getMentorCount($store);
            $event_count = $this->getEventCount($store);
        }

        $purchase_count = $this->getPurchaseCount($user);


        if ($user->user_type == 'store_owner') {
            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/sidebars/dashboard.php';
        } else {
            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/sidebars/account.php';
        }
    }

}
