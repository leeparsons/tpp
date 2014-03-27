<?php
/**
 * User: leeparsons
 * Date: 23/12/2013
 * Time: 18:50
 */
 
class TppStoreControllerAccount extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {

//        add_action( 'template_redirect', function() {
//            TppStoreControllerAccount::getInstance()->templateRedirect();
//        } );

        add_rewrite_rule('shop/myaccount/messages/page/([^/]+)', 'index.php?tpp_pagename=tpp_messages&page=$matches[1]', 'top');

        add_rewrite_rule('shop/myaccount/messages/?', 'index.php?tpp_pagename=tpp_messages', 'top');




        add_rewrite_rule('shop/myaccount/message/create/?', 'index.php?tpp_pagename=tpp_message_create', 'top');

        add_rewrite_rule('shop/myaccount/message/delete/?', 'index.php?tpp_pagename=tpp_message_delete', 'top');
        add_rewrite_rule('shop/myaccount/message/reply/?', 'index.php?tpp_pagename=tpp_message_reply', 'top');


        add_rewrite_rule('shop/myaccount/message/(.*)?', 'index.php?tpp_pagename=tpp_message&args=$matches[1]', 'top');

        add_rewrite_rule('shop/profile/([^/]+)?', 'index.php?tpp_pagename=tpp_profile&args=$matches[1]', 'top');

        add_rewrite_rule('shop/myaccount/purchase/([^/]+)?', 'index.php?tpp_pagename=tpp_account_purchase&args=$matches[1]', 'top');

        add_rewrite_rule('shop/myaccount/profile/edit', 'index.php?tpp_pagename=tpp_profile_edit', 'top');

        add_rewrite_rule('shop/myaccount/profile/upload', 'index.php?tpp_pagename=tpp_profile_upload', 'top');

        add_rewrite_rule('shop/myaccount/(.*)?', 'index.php?tpp_pagename=tpp_account&args=$matches[1]', 'top');

        add_rewrite_rule('shop/myaccount/?', 'index.php?tpp_pagename=tpp_account', 'top');


    }


    public function templateRedirect()
    {
        $pagename = get_query_var('tpp_pagename');
        $args = get_query_var('args');

        switch ($pagename) {
            case 'tpp_account':

                $this->_setWpQueryOk();
                $this->renderAccountPage($args);

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

            case 'tpp_account_purchase':
                $this->enqueueAccountResources();

                TppStoreControllerOrders::getInstance()->renderPurchase($args);

                break;

            case 'tpp_profile':


                $this->renderProfile($args);

                break;

            case 'tpp_messages':
                wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css', array(), '1');


                TppStoreControllerMessages::getInstance()->renderMessageList();
                exit;
                break;

            case 'tpp_message':
                wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css', array(), '1');

                TppStoreControllerMessages::getInstance()->renderMessage();
                exit;
                break;

            case 'tpp_message_create':
                TppStoreControllerMessages::getInstance()->actionCreate();
                exit;
                break;

            case 'tpp_message_reply':
                TppStoreControllerMessages::getInstance()->actionReply();
                break;

            case 'tpp_message_delete':
                TppStoreControllerMessages::getInstance()->actionDelete();
                break;

            default:

                break;
        }


    }

    public function renderAccountPage($args = '')
    {


        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }


        //determine if this user is now a store owner?
        $store = $this->getStoreModel()->setData(array(
            'user_id'   =>  $user->user_id
        ))->getStoreByUserID();

        if (intval($store->store_id) > 0 && intval($store->approved) == 1 && $user->user_type == 'store_owner') {
            TppStoreControllerStore::getInstance()->saveStoreToSession($store);
            $user->getUserById();
            TppStoreControllerUser::getInstance()->saveUserToSession($user);
            $this->redirectToDashboard($args);
        }


        $this->enqueueAccountResources();

        switch ($args) {
            case 'store':

                if (intval($store->approved) == 1) {
                    TppStoreMessages::getInstance()->addMessage('error', 'You store has been approved, but you are pending approval for a seller profile. Please contact us on <a rel="nofollow" href="mailto:rosie@thephotographyparlour.com" class="btn btn-primary">rosie@thephotographyparlour.com</a>');
                    TppStoreMessages::getInstance()->saveToSession();
                    $this->redirectToDashboard('store');
                }

            if ($store->readFromPost(true)) {

                $store_id = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_NUMBER_INT);

                if (intval($store_id) > 0) {

                    $store->setData(array(
                        'store_id'  =>  $store_id
                    ));

                    if ($store->save(true, true)) {
                        $this->redirectToAccount();
                    }
                } else {
                    TppStoreMessages::getInstance()->addMessage('error', 'We detected an error: your store was not found. Please contact us with error code: snf1001');
                }


            }
            include TPP_STORE_PLUGIN_DIR . 'site/views/account/store.php';

                break;

            case 'purchases':

                TppStoreControllerOrders::getInstance()->renderPurchaseList($user, $store);

               // $this->renderPurchases($user, $store);
                break;

            default:
                include TPP_STORE_PLUGIN_DIR . 'site/views/account/default.php';

                break;
        }



        exit;
    }




    public function renderProfile($user_id = 0)
    {

        $error = false;

        $can_edit = false;

        if (intval($user_id) <= 0) {
            $error = true;
        } else {

            if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession()) && $user->user_id === $user_id) {
                $can_edit = true;
            } else {
                $user = $this->getUserModel()->setData(array(
                    'user_id'   =>  $user_id
                ))->getUserByID(1);

                if (intval($user->user_id) <= 0 || $user->enabled == 0) {
                    $error = true;
                }
            }
        }

        if (true === $error) {
            $this->_setWpQuery404();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {

            if ($can_edit === true) {

                if ($user->user_type == 'store_owner') {
                    $store = $this->getStoreModel()->setData(array(
                        'user_id'  =>  $user->user_id
                    ));
                    $store->getStoreByUserID();
                    $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
                    $mentor_count = TppStoreControllerDashboard::getInstance()->getMentorCount($store);
                } else {
                    $product_count = 0;
                    $mentor_count = 0;
                }



                $this->enqueueAccountResources();

            }



            $this->_setWpQueryOk();

            include TPP_STORE_PLUGIN_DIR . 'site/views/account/profile/default' . ($can_edit === true?'_logged_in':'') . '.php';
        }

        exit;
    }



//    public function renderOrders(TppStoreModelUser $user, TppStoreModelStore $store)
//    {
//
//
//        $page = get_query_var('page');
//
//        $page = $page?:1;
//
//        $orders = $this->getOrderModel()->setData(array(
//            'user_id'   =>  $user->user_id
//        ))->getOrdersByUser($page);
//
//
//        if (intval($store->store_id) > 1) {
//            $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
//            $mentor_sessions_count = TppStoreControllerDashboard::getInstance()->getMentorSessionCount($store);
//        } else {
//            $product_count = 0;
//            $mentor_sessions_count = 0;
//        }
//
//        include TPP_STORE_PLUGIN_DIR . 'site/views/account/orders/history.php';
//    }

    private function enqueueAccountResources()
    {
        wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css', array(), '1');
    }

    public function renderProfileEdit()
    {

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (false !== $user->readFromPost()) {
                if (true === $user->save(true, true)) {
                    TppStoreControllerUser::getInstance()->saveUserToSession($user);
                    TppStoreMessages::getInstance()->addMessage('message', array('profile'  =>  'Profile Updated'));
                    TppStoreMessages::getInstance()->saveToSession();
                    if ($user->user_type == 'store_owner') {
                        $this->redirect('/shop/myaccount/profile/edit');
                    } else {
                        $this->redirect('/shop/myaccount');
                    }
                }
            }
        }


        wp_enqueue_script('jquery', 'jquery', array('jquery'), '1', true);
        wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', array('jquery'), '1', true);
        wp_enqueue_script('file_uploads', '/assets/js/file_upload-ck.js', array('jquery'), '1.2', true);
        wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css', array(), '1');



        if (false !== ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
            $mentor_count = TppStoreControllerDashboard::getInstance()->getMentorCount($store);
        } else {
            $product_count = 0;
            $mentor_count = 0;
        }

        include TPP_STORE_PLUGIN_DIR . '/site/views/account/profile/edit.php';

        exit;
    }


    private function uploadImage()
    {
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->_exitStatus('You are not authorised', true);
        }

        //get the profile save path!

        if (false === ($save_path = $user->getImageDirectory(true))) {
            $this->_exitStatus('Could not create your store directory. Please contact us', true);
        }

        if (!class_exists('TppStoreLibraryFileImage')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/file/image.php';
        }

        $image = new TppStoreLibraryFileImage();


        if (isset($_FILES['pic'])) {
            $image->setUploadedFile($_FILES['pic']);

            if (false === $image->validateUploadedFile($image::$image_mime_type)) {
                $this->_exitStatus($image->getError(), true);
            }

            //move the uploaded file

            if (false === $image->moveUploadedFile($save_path)) {
                $this->_exitStatus($image->getError(), true);
            }

            $image->setFile($save_path . $image->getUploadedName());
        } else {
            if (false === $image->createImageFromInput($save_path)) {
                $this->_exitStatus('Error! Please upload an image.', true);
            }

            if (false === $image->validateUploadedFile($image::$image_mime_type)) {
                $this->_exitStatus($image->getError(), true);
            }
        }

        $image->setFile($save_path . $image->getUploadedName());

        $image->resize(array('thumb'    =>  array('width'    => 250, 'height'    =>  250)));

        $this->_exitStatus('success', false, array(
            'saved_name'    =>  $image->getUploadedName()
        ));
    }
}