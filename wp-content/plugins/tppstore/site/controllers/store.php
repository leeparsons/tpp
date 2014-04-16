<?php
/**
 * User: leeparsons
 * Date: 03/12/2013
 * Time: 20:39
 */
 
class TppStoreControllerStore extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {


//        add_action( 'template_redirect', function() {
//            TppStoreControllerStore::getInstance()->templateRedirect();
//        } );



        add_rewrite_rule('shop/sell-with-us/?', 'index.php?tpp_pagename=tpp_apply', 'top');

        add_rewrite_rule('shop/application-status/?', 'index.php?tpp_pagename=tpp_application_status', 'top');

        add_rewrite_rule('shop/apply-success/?', 'index.php?tpp_pagename=tpp_apply_success', 'top');

        add_rewrite_rule('shop/ask/([^/]+)?', 'index.php?tpp_pagename=tpp_ask&args=$matches[1]', 'top');

        add_rewrite_rule('shop/terms/([^/]+)?', 'index.php?tpp_pagename=tpp_terms&args=$matches[1]', 'top');

        add_rewrite_rule('shop/ask/?', 'index.php?tpp_pagename=tpp_ask_post', 'top');

        add_rewrite_rule('^shop$', 'index.php?tpp_pagename=tpp_shop', 'top');

        //flush_rewrite_rules(true);

    }

    public function templateRedirect()
    {
        $pagename = get_query_var('tpp_pagename');

        switch (strtolower($pagename)) {
            case 'tpp_shop':
                $this->_setWpQueryOk();
                $this->_renderStores();

                break;

            case 'tpp_apply':

                $this->_setWpQueryOk();
                $this->renderApplicationForm();

                break;

            case 'tpp_apply_success':

                $this->_setWpQueryOk();
                $this->renderSuccess();

                break;

            case 'tpp_application_status':
                $this->_setWpQueryOk();
                $this->returnApplicationStatus();
                break;

            case 'tpp_ask':

                $this->_renderAskForm();
                break;

            case 'tpp_ask_post':

                $this->_actionAsk();

                break;

            case 'tpp_terms':

                $this->_storeTerms();

                break;



            default:

                break;

        }

    }

    private function _renderAskForm($question = false)
    {

        $store_slug = get_query_var('args');

        $store = $this->getStoreModel()->getStoreBySlug($store_slug);

        if (intval($store->store_id) < 1) {
            $this->_setWpQuery403();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }

        //is the user logged in?
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('message', 'Please login to ask the store owner a question');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin('redirect=' . urlencode('/shop/ask/' . $store_slug));
        }

        $this->_setWpQueryOk();

        if (false === $question) {
            $question = $this->getMessageModel();
        }



        include TPP_STORE_PLUGIN_DIR . 'site/views/store/ask.php';

        exit;

    }

    private function _renderStores()
    {
        //determine the user id from the author
        $stores = $this->getStoreModel()->getStores();

        include TPP_STORE_PLUGIN_DIR . 'site/views/stores.php';

        exit;
    }

    private function _storeTerms()
    {
        $store_slug = get_query_var('args');

        $store = $this->getStoreModel()->getStoreBySlug($store_slug);


        if (intval($store->store_id) <= 0) {

            $this->_setWpQuery404();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {
            include TPP_STORE_PLUGIN_DIR . 'site/views/store/terms.php';
        }

        exit;

    }

    private function _actionAsk()
    {
        $store_id = filter_input(INPUT_POST, 'store', FILTER_SANITIZE_NUMBER_INT);

        $store = $this->getStoreModel()->setData(array(
            'store_id'  =>  $store_id
        ))->getStoreByID();


        if (intval($store->store_id) < 1) {
            $this->_setWpQuery403();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {

            //validate the message

            $question = $this->getMessageModel();

            if (false === $question->readFromPost()) {
                $this->_renderAskForm($question);
            } else {



                $question->setData(array(
                    'receiver'  =>  $store->user_id,
                    'subject'   =>  'Question about your store'
                ));

                if (false !== $question->save()) {

                    $title = 'Question sent!';

                    $message = $question;

                    ob_start();

                    include TPP_STORE_PLUGIN_DIR . 'emails/private_message_received.php';

                    $body = ob_get_contents();

                    ob_end_clean();

                    $this->sendMail($message->getReceiver(true)->email, 'You have received a private message from: ' . $message->getSender()->getName(), $body);


                    TppStoreMessages::getInstance()->addMessage('message', 'Your question has been submitted.');

                }
                TppStoreMessages::getInstance()->saveToSession();

                $this->redirectToReferer();


            }



        }
        exit;
    }


    public function uploadImage()
    {


        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->_exitStatus('You are not authorised', true);
        }

        $save_path = false;

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            //load the temp path
            if (false === ($save_path = $this->loadStoreTempPath())) {
                $this->_exitStatus('Could not determine your store. Please logout and login again.', true);
            }
        }


        //get the store save path!
        if (false === $save_path && false === ($save_path = $store->getImageDirectory(true))) {
            $save_path = $this->loadStoreFromSession();
        }

        if (!class_exists('TppStoreLibraryFileImage')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/file/image.php';
        }

        //create the save path if it doesn't exist:

        $directory = new TppStoreDirectoryLibrary();

        $directory->setDirectory($save_path);

        if (!$directory->createDirectory()) {
            $this->_exitStatus('Unable to create the save directory', true);
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



        } else {
            $image->createImageFromInput($save_path);
        }

        $image->setFile($save_path . $image->getUploadedName());

        $image->resize(TppStoreModelProductImages::getSize('thumb'));

        $this->_exitStatus('success', false, array(
            'saved_name'    =>  $image->getUploadedName()
        ));
    }


    public function renderApplicationForm()
    {

        $user = TppStoreControllerUser::getInstance()->loadUserFromSession();

        $this->deleteStoreFromSession();

        if (false !== $user && $user->user_type == 'store_owner') {
            $title = 'Oops!';
            $message = 'You are already selling with us.';
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        } else {

            $store = TppStoreControllerStore::getInstance()->loadStoreFromSession();

            if (false !== $store) {

                $title = 'Oops!';
                $message = 'You have already applied to sell with us!';
                include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
                exit;
            } elseif (false !== $user) {

                //determine if a store exists by this user?

                $store = $this->getStoreModel()->setData(array(
                    'user_id'   =>  $user->user_id
                ));

                $store->getStoreByUserID();

                if (intval($store->store_id) > 0) {
                    $title = 'Oops!';
                    $message = 'You have already applied to sell with us!';
                    include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
                    exit;
                }
            }

            $store = $this->getStoreModel();

            //assume no store has yet been created
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {


                $agree = filter_input(INPUT_POST, 'apply_agree', FILTER_SANITIZE_NUMBER_INT);

                if (intval($agree) != 1) {

                    //save the post into store object
                    $store->readFromPost(true);

                    TppStoreMessages::getInstance()->addMessage('error', 'Please agree to our terms and conditions');



                } else {
                    //determine if the user is logged in, if so, don't create a new user account
                    //but try and create their store based on posted information


                    $save_store = true;



                    if (false === $user) {

                        //create a new user account

                        $user = $this->getUserModel();

                        if ($user->readFromPost()) {
                            $user->setData(array(
                                'enabled'   =>  1
                            ));
                            if ($user->save()) {
                                TppStoreMessages::getInstance()->addMessage('message', 'Your user account has been created');
                                TppStoreControllerUser::getInstance()->saveUserToSession($user);
                            }
                        } else {
                            $save_store = false;
                        }

                    }
                    $store->url = filter_input(INPUT_POST, 'store_website', FILTER_SANITIZE_STRING);

                    if ($store->readFromPost(true)) {

                        if (true === $save_store && $store->save(true, true)) {
                            $this->saveStoreToSession($store);
                            $store->url = filter_input(INPUT_POST, 'store_website', FILTER_SANITIZE_STRING);
                            $store->newsletter = filter_input(INPUT_POST, 'apply_comms', FILTER_SANITIZE_NUMBER_INT);
                            $store->how = filter_input(INPUT_POST, 'apply_how', FILTER_SANITIZE_STRING);
                            $this->sendApplicationNotification($store, $user);


                            $store_meta = $this->getStoreApplicationModel();
                            $store_meta->setData(array(
                                'store_id'      =>  $store->store_id,
                                'how'           =>  $store->how,
                                'newsletter'    =>  $store->newsletter,
                                'website'       =>  $store->url
                            ))
                                ->save();

                            $this->redirect('/shop/apply-success');

                        }
                    }

                }

            }



            $this->pageTitle('Sell with The Photography parlour');
            $this::$_meta_description = 'Apply to sell with us';

            wp_enqueue_style('tpp-apply', '/wp-content/plugins/tppstore/site/assets/css/apply.css');
            wp_enqueue_style('tpp-login', '/wp-content/plugins/tppstore/site/assets/css/login.css');

            if (false === $user) {
                $user = $this->getUserModel();
            }

            include TPP_STORE_PLUGIN_DIR . 'site/views/apply/apply.php';


        }



        exit;
    }


    public function renderSuccess()
    {

//        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
//            $this->redirectToLogin();
//        }

        $user = $this->getUserModel();
        wp_enqueue_style('tpp-apply', '/wp-content/plugins/tppstore/site/assets/css/apply.css');

        include TPP_STORE_PLUGIN_DIR . 'site/views/apply/success.php';
        exit;
    }


    public function save()
    {

        if (false === $store = $this->loadStoreFromSession()) {
            TppStoreMessages::getInstance()->addMessage('error', array('store'  =>  'Could not determine your store'));
            return false;
        }

        if (false === $store->readFromPost()) {
            return false;
        } else {
            return $store->save();
        }
    }





    public function saveStoreToSession(TppStoreModelStore $store)
    {

        if (is_null($store->store_id) || intval($store->store_id) <= 0) {
            return false;
        }

        $_SESSION['tpp_store_store'] = serialize($store);

    }





    /*
     * if this user is loaded, then retrieve the user from the user controller session.
     * Returns false otherwise
     */
    public function loadStoreFromSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (isset($_SESSION['tpp_store_store'])) {
            return unserialize($_SESSION['tpp_store_store']);
        } else {
            return false;
        }
    }

    public function deleteStoreFromSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (isset($_SESSION['tpp_store_store'])) {
            $_SESSION['tpp_store_store'] = null;
            unset($_SESSION['tpp_store_store']);
        }
    }

    public function saveStoreTempPath(TppStoreModelStore $store)
    {

        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (intval($store->store_id) > 0) {
            $path = WP_CONTENT_DIR . '/uploads/store/' . $store->store_id . '/';
        } else {
            $path = WP_CONTENT_DIR . '/uploads/store/' . uniqid('new_store_') . '/';
        }

        $_SESSION['tpp_store_save_path'] = $path;
    }

    public function loadStoreTempPath()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (isset($_SESSION['tpp_store_save_path']) && !empty($_SESSION['tpp_store_save_path'])) {
            return $_SESSION['tpp_store_save_path'];
        } else {
            return false;
        }

    }

    public function deleteStoreTempPath()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        $_SESSION['tpp_store_save_path'] = null;
        unset($_SESSION['tpp_store_save_path']);
    }

    public function sendApplicationNotification(TppStoreModelStore $store, TppStoreModelUser $user)
    {
        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/application_notification_applicant.php';

        $body = ob_get_contents();

        ob_end_clean();

        $this->sendMail($user->email, 'Thank you for your application to sell with us', $body);

        unset($body);

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/application_notification_admin.php';

        $body = ob_get_contents();

        ob_end_clean();

        $this->sendMail('rosie@thephotographyparlour.com', 'The Photography Parlour seller application: ' . $user->getName(), $body);


    }


    /*
     * Returns just the application status of this store.
     */
    private function returnApplicationStatus()
    {

        $this->_setJsonHeader();

        if (false === ($store = $this->loadStoreFromSession())) {
            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                //we are not logged in so exit an error
                $this->_exitStatus('error', true, array('message' => 'Unable to find your store or login session'));
            } elseif ($user->facebook_user_id == $_POST['fid'] && !empty($user->facebook_user_id)) {
                //get the store by this user id!

                $store = $this->getStoreModel()->setData(array(
                    'user_id'   =>  $user->user_id
                ));

                $store->getStoreByUserID();

            } else {
                //they're trying to hack something perhaps?
                //The facebook ids do not match as this entry point is only accessible via facebook connect on apply page
                $this->_exitStatus('error', true, 'Unable to find your store or login session');
            }
        }



        //determine if the store exists, and what state the application is in...

        if (intval($store->store_id) > 0) {
            $this->_exitStatus('success', false, array(
                'state'     =>  intval($store->approved),
                'user'      =>  array(
                    'first_name'    =>  $user->first_name,
                    'last_name'     =>  $user->last_name,
                    'title'         =>  $user->title,
                    'email'         =>  $user->email
                )
            ));
        } else {
            $this->_exitStatus('success', false, array(
                'state'  =>  -2,
                'user'      =>  array(
                    'first_name'    =>  $user->first_name,
                    'last_name'     =>  $user->last_name,
                    'title'         =>  $user->title,
                    'email'         =>  $user->email
                )
            ));
        }
    }

    public function sendStoreGoLiveNotification(TppStoreModelStore $store)
    {

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/store_go_live.php';

        $body = ob_get_contents();

        ob_end_clean();

        $this->sendMail('rosie@thephotographyparlour.com', 'A store - ' . $store->getSafeTitle() . ' - has gone live! ', $body);

    }

    public function sendStoreGoOfflineNotification(TppStoreModelStore $store)
    {

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'emails/store_go_offline.php';

        $body = ob_get_contents();

        ob_end_clean();

        $this->sendMail('rosie@thephotographyparlour.com', 'A store - ' . $store->getSafeTitle() . ' - has gone OFFLINE! ', $body);

    }

}