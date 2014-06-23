<?php
/**
 * User: leeparsons
 * Date: 01/12/2013
 * Time: 16:45
 */

class TppStoreControllerRegister extends TppStoreAbstractBase {



    public function applyRewriteRules()
    {




        add_rewrite_rule('^shop/store_register/fb/ajax', 'index.php?pagename=tpp_shopregister_fb_ajax_callback&shop_args=$matches[1]', 'top');

        add_rewrite_rule('^shop/store_register/fb/complete', 'index.php?pagename=tpp_shopregister_fb_callback&shop_args=$matches[1]', 'top');



        add_rewrite_rule('^shop/store_register/fb', 'index.php?pagename=tpp_shopregister_fb&shop_args=$matches[1]', 'top');



        add_rewrite_rule('^shop/store_register/([^/]+)?', 'index.php?pagename=tpp_shopregister&shop_args=$matches[1]', 'top');

        add_rewrite_rule('^shop/store_register', 'index.php?pagename=tpp_shopregister&shop_args=$matches[1]', 'top');

        add_filter('query_vars', function($vars) {
            $vars[] = 'shop_args';
            return $vars;
        } );

        //flush_rewrite_rules(true);
    }


    public function templateRedirect($args = array())
    {


        $pagename = get_query_var('name');
        $args = get_query_var('shop_args');

        switch (strtolower($pagename)) {

            case 'tpp_shopregister':
                $this->_setWpQueryOk();
                $this->renderRegistrationFrom($args);

                break;

            case 'tpp_shopregister_fb_ajax_callback':
                $this->_setWpQueryOk();
                $this->facebookAjaxCallback();
                break;

            case 'tpp_shopregister_fb_callback':
                //shouldn't be used anymore
                $this->_setWpQueryOk();
                $this->facebookRegistration();

                break;

            case 'tpp_shopregister_fb':
                //shouldn't be used anymore
                $this->_setWpQueryOk();
                $this->facebookPost();
                break;


            default:

                break;

        }



    }


    protected function renderRegistrationFrom($args)
    {
        if (method_exists($this, 'renderRegistrationStep' . $args)) {
            $this->{'renderRegistrationStep' . $args}();
        } else {
            $this->renderRegistrationStep1($args);
        }

        exit;
    }

    protected function renderRegistrationStep1($args = '')
    {


        if (false !== TppStoreControllerUser::getInstance()->loadUserFromSession()) {
            if ($args == 'component') {
                header('Content-type: application/json');

                exit(json_encode(array(
                    'link'  =>  '/shop/store_register/2'
                )));
            } else {
                $this->redirectToStage(2);
            }
        }

        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING);

        $fname = filter_input(INPUT_POST, 'your_first_name', FILTER_SANITIZE_STRING);

        $lname = filter_input(INPUT_POST, 'your_last_name', FILTER_SANITIZE_STRING);

        $email = filter_input(INPUT_POST, 'your_email', FILTER_SANITIZE_STRING);

        $password = filter_input(INPUT_POST, 'your_password', FILTER_SANITIZE_STRING);

        $confirm_password = filter_input(INPUT_POST, 'your_password_confirm', FILTER_SANITIZE_STRING);

        $error = array();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //determine if all other fields are set?
            if (!empty($error) || !empty($_SESSION['shop_captcha_answer']) && strtolower($answer) != $_SESSION['shop_captcha_answer']) {
                TppStoreMessages::getInstance()->addMessage('error', array('summation' => 'Please enter the correct answer'));
            } else {
                if (md5($password) !== md5($confirm_password)) {
                    TppStoreMessages::getInstance()->addMessage('error', array('pasword'    =>  'Password did not match'));
                } elseif (is_null($password) || trim($password) == '') {
                    TppStoreMessages::getInstance()->addMessage('error', array('password' => 'Please enter a password'));
                }

                $data = array(
                    'email'         =>  $email,
                    'first_name'    =>  $fname,
                    'last_name'     =>  $lname,
                    'password'      =>  $password,
                    'enabled'       =>  1
                );

                $user_model = $this->getUserModel();



                $user_model->setData($data)->save();
            }
            $error = TppStoreMessages::getInstance()->getMessages('error');

            if (empty($error)) {
                TPPStoreControllerUser::getInstance()->saveUserToSession($user_model);
                $_SESSION['shop_captcha_answer'] = null;
                unset($_SESSION['shop_captcha_answer']);

                if (filter_input(INPUT_POST, 'newsletter_agree', FILTER_SANITIZE_NUMBER_INT) == 1) {

                    $signup = $this->getEmailSignupModel();

                    $signup->setData(array(
                        'email'         =>  $user_model->email,
                        'source'        =>  'registration',
                        'user_id'       =>  $user_model->user_id,
                        'first_name'    =>  $user_model->first_name,
                        'last_name'     =>  $user_model->last_name
                    ))->save();

                    $body = $user_model->first_name . ' ' . $user_model->last_name . ' has signed up for your newsletter at registration. Their email address is: ' . $user_model->email;

                    $this->sendMail('rosie@thephotographyparlour.com', 'newsletter signup: ' . $user_model->first_name . ' ' . $user_model->last_name, $body);

                }



                if ($user_model->user_type == 'store_owner') {
                    TppStoreMessages::getInstance()->addMessage('message', 'To get started with selling your products, <a href="/shop/dashboard/store/">create your store</a>.');
                    $this->redirectToDashboard();
                } else {
                    $this->redirectToAccount();
                }
            }
        }


        $questions = array(
            array(
                'question'  =>  'What colour is the sky?',
                'answer'    =>  'blue'
            ),
            array(
                'question'  =>  'What colour is grass?',
                'answer'    =>  'green'
            ),
            array(
                'question'  =>  'What colour is sand?',
                'answer'    =>  'yellow'
            )
        );

        $question = $questions[rand(0, count($questions) - 1)];

        $_SESSION['shop_captcha_answer'] = $question['answer'];

        include TPP_STORE_PLUGIN_DIR . 'site/views/register.php';
    }

    protected function renderRegistrationStep2()
    {

        //determine if the user is logged in?

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToStage(1);
        }

        if (($store = TppStoreControllerStore::getInstance()->loadStoreFromSession()) instanceof TppStoreModelStore) {
            $this->redirectToDashboard();
        } else {
            $store = $this->getStoreModel();
        }

        $store_name = filter_input(INPUT_POST, 'store_name', FILTER_SANITIZE_STRING);

        $store_slug = filter_input(INPUT_POST, 'store_slug', FILTER_SANITIZE_STRING);


        $store->setData(array(
                'user_id'       =>  $user->user_id
            )
        );

        $store->getStoreByUserID('all');

        if (!is_null($store->store_id)) {
            TppStoreControllerStore::getInstance()->saveStoreToSession($store);
            $this->redirectToDashboard();
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //determine if these slugs and store names can be used?


            $store->setData(array(
                'store_slug'    =>  $store_slug,
                'store_name'    =>  $store_name
            ));


            $store->save();
            if (TppStoreMessages::getInstance()->getTotal() == 0) {
                TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                $this->redirectToDashboard();
            }

        } else {
            //determine if the store already exists for this user?

            $store->setData(array(
                'user_id'       =>  $user->user_id
            ));

            $store->getStoreByUserID();

            if (!is_null($store_slug)) {
                $store->store_slug = $store_slug;
            }

            if (!is_null($store_name)) {
                $store->store_name = $store_name;
            }

            if ($store->enabled == 1) {
                TppStoreControllerStore::getInstance()->saveStoreToSession($store);
                $this->redirectToDashboard();
            }

        }

        $store_id = $store->store_id;

        include TPP_STORE_PLUGIN_DIR . 'site/views/register_step2.php';
    }


    protected function renderRegistrationStep3()
    {


        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToStage(1);
        }

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $this->redirectToStage(2);
        }

        $this->redirectToDashboard('product/new');

        $products = $this->getProductsModel();

        $products->setData(array('store_id'    =>  $store->store_id));

        if ($products->getProductCountByStore() > 0) {
            $this->redirectToDashBoard();
        }

        $product = $this->getProductModel();

        if ($product->readFromPost()) {
            if ( true === $product->save()) {
                $this->redirectToDashboard();
            }
        }

        $categories_model = $this->getCategoriesModel();
        $categories_model->getCategories(array('heirarchical'   =>  true, 'type'    =>  'assoc'));

        $categories = $categories_model->categories;

        $store_id = $store->store_id;



        include TPP_STORE_PLUGIN_DIR . 'site/views/register_step3.php';

    }

    protected function facebookPost()
    {
        $config = array(
            'appId'                 =>  '270470249767149',
            'secret'                =>  '1be85a5d65aa36bb0a1c5426f7680581',
            'allowSignedRequest'    =>  false // optional but should be set to false for non-canvas apps
        );

        $facebook = new Facebook($config);

        $facebook->destroySession();
        $user_id = $facebook->getUser();

        if (intval($user_id) > 0) {
            $this->destoryFacebookRefererSession();
            $this->registerFacebookUser($facebook);
        } else {
            $this->setFacebookRefererSession();
            $loginUrl = $facebook->getLoginUrl(array(
                'scope'		    =>  'email', // Permissions to request from the user
                'redirect_uri'	=>  get_bloginfo('url') .  '/shop/store_register/fb/complete', // URL to redirect the user to once the login/authorization process is complete.
            ));

            header('location: ' . $loginUrl);
        }
        exit;
    }

    protected function facebookRegistration(){

        $config = array(
            'appId'                 =>  '270470249767149',
            'secret'                =>  '1be85a5d65aa36bb0a1c5426f7680581',
            'allowSignedRequest'    =>  false // optional but should be set to false for non-canvas apps
        );

        $facebook = new Facebook($config);

        $u = $facebook->getUser();

        if (intval($u) > 0) {
            $this->destoryFacebookRefererSession();
            $this->registerFacebookUser($facebook);
            $this->redirectToStage(2);
            exit;
        } else {

            TppStoreMessages::getInstance()->addMessage('error', array('login'   =>  'Could not detect your facebook logged in profile. Facebook may have not logged you in correctly. Please try again.'));
            TppStoreMessages::getInstance()->saveToSession();
            if (false !== ($referer = $this->getFacebookReferer())) {
                $this->destoryFacebookRefererSession();
                $this->redirect($referer);
            } else {
                $this->redirectToLogin();
            }

        }
        exit;
    }

    private function facebookAjaxCallback($data = false)
    {
        header('Content-type: application/json');

        $discover = filter_input(INPUT_POST, 'discover', FILTER_SANITIZE_NUMBER_INT);


        $on_application_form = filter_input(INPUT_POST, 'application_form', FILTER_UNSAFE_RAW);


        $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);

        $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);

        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

        $profile_link = filter_input(INPUT_POST, 'profile_link', FILTER_SANITIZE_STRING);


        $email = filter_input(INPUT_POST, '_email', FILTER_SANITIZE_STRING);

        $facebook_id = filter_input(INPUT_POST, 'fid', FILTER_SANITIZE_STRING);

        //determine first is a user account exists with this email?

        $user = $this->getUserModel()->setData(array(
            'facebook_user_id'  =>  $facebook_id,
            'email'             =>  $email
        ));


        $user->getUserByEmail();

        $redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);


        if (intval($user->user_id) > 0) {
            //link these accounts!

            //get user by facebook:

            $_user_facebook = $this->getUserModel()->setData(array(
                'facebook_user_id'  =>  $facebook_id
                ));

            $_user_facebook->getUserByFacebookID();

            if (intval($_user_facebook->user_id) > 0) {
                if ($_user_facebook->user_id !== $user->user_id) {
                    //two different user accounts, so link the facebook one with the manual one as the main one!

                    if (intval($user->facebook_user_id) <= 0) {
                        $user->setData(array(
                            'facebook_user_id'  =>  $facebook_id,
                            'enabled'           =>  $_user_facebook->enabled
                        ))->save();
                        $user->updateLastVisit();
                        TppStoreControllerUser::getInstance()->saveUserToSession($user);

                        if (intval($discover) === 0 && $on_application_form !== 'true') {
                            if ($user->user_type == 'store_owner') {
                                $json = array('redirect'    =>  $redirect?:'/shop/dashboard');
                            } else {
                                $json = array('redirect'    =>  $redirect?:'/shop/myaccount');
                            }
                        } else {
                            $json = array(
                                'popup'     =>  array(
                                    'name'          =>  $user->first_name,
                                    'last_visit'    =>  $user->getLastVisit()
                                ),
                                'link_text' =>  $user->user_type == 'store_owner'?'My Dashboard':'My Account',
                                'redirect'  =>  $redirect?:($user->user_type == 'store_owner'?'/shop/dashboard':'/shop/myaccount')
                            );
                        }

                        if (true === $user->save()) {
                            $user->updateLastVisit();
                            TppStoreControllerUser::getInstance()->saveUserToSession($user);
                        } else {
                            $this->_exitStatus('Unable to save your account', true);
                        }

                        echo json_encode($json);
                        exit;
                    } else {
                        $this->_setJsonHeader();
                        $this->_exitStatus('error', true, array(
                            'message'   =>  'confused as you have multiple accounts on facebook so it seems',
                            'error'     =>  'The facebook account has an email address registered to it that also belongs to another facebook account.'
                        ));
                    }




                } else {


                    //user has been found and it's the facebook user!
                    if (intval($discover) === 0 && $on_application_form !== 'true') {
                        if ($user->user_type == 'store_owner') {
                            $json = array(
                                'redirect'      =>  $redirect?:'/shop/dashboard',
                                'link_text'     =>  'My Dashboard'
                            );
                        } else {
                            $json = array(
                                'redirect'    =>  $redirect?:'/shop/myaccount',
                                'link_text'     =>  'My Account'
                            );
                        }
                    } else {
                        $json = array(
                            'popup'     =>  array(
                                'name'          =>  $user->first_name,
                                'last_visit'    =>  $user->getLastVisit()
                            ),
                            'link_text' =>  $user->user_type == 'store_owner'?'My Dashboard':'My Account',
                            'redirect'  =>  $redirect?:($user->user_type == 'store_owner'?'/shop/dashboard':'/shop/myaccount')
                        );
                    }

                    $user->setData(array(
                        'enabled'           =>  $_user_facebook->enabled
                    ));

                    if (true === $user->save()) {
                        $user->updateLastVisit();
                        TppStoreControllerUser::getInstance()->saveUserToSession($user);
                    } else {
                        $this->_exitStatus('Unable to save your account', true);
                    }

                    echo json_encode($json);
                    exit;

                }
            }



        }

        //could not find a user with the email address, so see if the facebook id has already been registered on a different account?

        $user->getUserByFacebookID();


        if (intval($user->user_id) <= 0) {


            if (intval($discover) === 1) {
                $this->_exitStatus('no user', true);
            }

            //no user with this facebook account exists.
            //See if a user with the facebook email exists and tie the accounts up if possible.

            $user->setData(array(
                'facebook_user_id'  =>  null,
                'email'             =>  $email
            ));

            //facebook user does not already exist so do not link accounts
            $user->getUserByEmail();

            if (intval($user->user_id) <= 0) {
                //no user exists - lets create one!
                //facebook user already exists. Update their details
                $user->setData(array(
                    'first_name'        =>  $fname,
                    'last_name'         =>  $lname,
                    'gender'            =>  $gender,
                    'f_profile_link'    =>  $profile_link,
                    'activation'        =>  null,
                    'enabled'           =>  1,
                    'facebook_user_id'  =>  $facebook_id
                ));

                if ($user->save()) {
                    $user->updateLastVisit();
                    TppStoreControllerUser::getInstance()->saveUserToSession($user);
                } else {
                    $this->_exitStatus('Unable to create a user account', true);
                }
            } else {

                //user account exists! lets determine if they have a facebook account linked?
                if (intval($user->facebook_user_id) > 0) {
                    //facebook account exists - and it's not this one!
                    $this->_exitStatus('Your facebook email address is already registered but has been linked to a different facebook account. If you believe this is wrong, please contact us.', true);
                } else {

                    //no facebook account for this user so set the current facebook account as their account.
                    //this is safe because we have already tested for this facebook account in the database.

                    $user->setData(array(
                        'facebook_user_id'  =>  $facebook_id,
                        'enabled'           =>  $_user_facebook->enabled
                    ));

                    if ($user->save()) {
                        $user->updateLastVisit();
                        TppStoreControllerUser::getInstance()->saveUserToSession($user);
                    }
                }
            }

        } else {

            //facebook user already exists. Update their details
            $user->setData(array(
                'first_name'        =>  $fname,
                'last_name'         =>  $lname,
                'gender'            =>  $gender,
                'f_profile_link'    =>  $profile_link,
                'enabled'           =>  1
            ));

            if (true === $user->save()) {
                $user->updateLastVisit();
                TppStoreControllerUser::getInstance()->saveUserToSession($user);
            } else {
                $this->_exitStatus('Unable to save your account', true);
            }
        }



        if (intval($discover) === 0 && $on_application_form !== 'true') {
            if ($user->user_type == 'store_owner') {
                $json = array('redirect'    =>  $redirect?:'/shop/dashboard');
            } else {
                $json = array('redirect'    =>  $redirect?:'/shop/myaccount');
            }
        } else {
            $json = array(
                'popup'     =>  array(
                    'name'          =>  $user->first_name,
                    'last_visit'    =>  $user->getLastVisit()
                ),
                'link_text' =>  $user->user_type == 'store_owner'?'My Dashboard':'My Account',
                'redirect'  =>  $redirect?:($user->user_type == 'store_owner'?'/shop/dashboard':'/shop/myaccount')
            );
        }

        echo json_encode($json);
        exit;
    }


    protected function registerFacebookUser(Facebook $facebook)
    {
        $me = $facebook->api('/me');

        $user_model = $this->getUserModel();

        $data = array(
            'email'             =>  empty($me['email'])?:$me['email'],
            'first_name'        =>  empty($me['first_name'])?:$me['first_name'],
            'last_name'         =>  empty($me['last_name'])?:$me['last_name'],
            'facebook_user_id'  =>  $me['id']
        );

        $user_model->setData($data);

        $user_model->getUserByFacebookID();

        if ($user_model->user_id == 0) {
            $user_model->getUserByEmail();
        }

        $user_model->save(false);

        TppStoreControllerUser::getInstance()->saveUserToSession($user_model);


        //user has been saved and registered with facebook.

        //Go to the next step!
    }

    private function setFacebookRefererSession()
    {
        $_SESSION['fb_redirect_url'] = $_SERVER['HTTP_REFERER'];
    }

    private function getFacebookReferer()
    {

        if (isset($_SESSION['fb_redirect_url'])) {
            return $_SESSION['fb_redirect_url'];
        } else {
            return false;
        }

    }

    private function destoryFacebookRefererSession()
    {
        if (isset($_SESSION['fb_redirect_url'])) {
            $_SESSION['fb_redirect_url'] = null;
            unset($_SESSION['fb_redirect_url']);
        }
    }

}