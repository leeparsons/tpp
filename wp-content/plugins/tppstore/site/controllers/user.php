<?php
/**
 * User: leeparsons
 * Date: 02/12/2013
 * Time: 10:30
 */
 
class TppStoreControllerUser extends TppStoreAbstractBase {


    private $checked_user_validity = false;

    public function applyRewriteRules()
    {


        add_rewrite_rule('shop/store_login/fb', 'index.php?pagename=tpp_login_fb&args=$matches[1]', 'top');


        add_rewrite_rule('shop/store_login/?', 'index.php?pagename=tpp_login&args=$matches[1]', 'top');
        add_rewrite_rule('shop/store_logout/?', 'index.php?pagename=tpp_logout&args=$matches[1]', 'top');

        add_rewrite_rule('shop/password_reset/?', 'index.php?pagename=tpp_password_reset', 'top');



        //flush_rewrite_rules(true);

    }

    public function registerActions()
    {
//        add_action('tpp_login', function() {
//            TppStoreControllerUser::getInstance()->login();
//        });
    }

    public function templateRedirect()
    {

        $pagename = get_query_var('name');
        $args = get_query_var('args');

        switch (strtolower($pagename)) {

            case 'tpp_login_fb':
                $this->_setWpQueryOk();
                $this->_loginWithFacebook($args);
                break;

            case 'tpp_login':
                $this->_setWpQueryOk();
                $this->renderLoginForm($args);

                break;

            case 'tpp_logout':
                $this->_setWpQueryOk();
                $this->logOut();
                break;

            case 'tpp_password_reset':

                $this->_setWpQueryOk();
                $this->renderPasswordResetForm();

                break;

            default:

                break;

        }



    }

    public function renderAccount($args = false)
    {



        switch ($args) {
            case 'details':
                include TPP_STORE_PLUGIN_DIR . 'site/views/account/details.php';

                break;
            default:
                include TPP_STORE_PLUGIN_DIR . 'site/views/account/default.php';
                break;
        }


        exit;
    }

    public function renderLoginForm($args)
    {

        $redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_STRING);

        if (is_null($redirect)) {
            $redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);
        }

        //determine if the user is already logged in?
        if (false === ($user = $this->loadUserFromSession())) {
            $user = $this->getUserModel();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);

                $user = $this->getUserModel();

                $user->setData(array(
                    'email'     =>  filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_STRING),
                    'password'  =>  filter_input(INPUT_POST, 'tpp_password', FILTER_SANITIZE_STRING),
                ));


                if (false !== $user->authenticate()) {

                    $this->saveUserToSession($user);

                    $this->_clearLogoutCookie();

                    if (!is_null($redirect)) {
                        $this->redirect(urldecode($redirect));
                    }

                    if ($user->user_type == 'store_owner') {
                        $this->redirectToDashboard();
                    } else {
                        $this->redirectToAccount();
                    }

                }
            }

            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/login_form.php';
            exit;
        } else {
            if (!is_null($redirect)) {
                $this->redirect(urldecode($redirect));
            }
            if ($user->user_type == 'buyer') {
                $this->redirectToAccount();
            }

            $this->redirectToDashboard();
        }


    }

    /*
     * This method takes an already authenticated user model and logs in by storing the user details in a session
     */
    public function saveUserToSession(TppStoreModelUser $user)
    {
        $_SESSION['tpp_store_user'] = serialize($user);
    }

    /*
     * Loads a user object from the session and returns it, or returns false if not logged in
     */
    public function loadUserFromSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (empty($_SESSION['tpp_store_user'])) {
            return false;
        } else {
            $user = unserialize($_SESSION['tpp_store_user']);

            if (false == $this->checked_user_validity) {

                $_user = $this->getUserModel()->setData(array(
                    'user_id'   =>  $user->user_id
                ))->getUserByID();

                if ($user->user_id == $_user->user_id && intval($_user->enabled) == 1) {
                    $user->enabled = $_user->enabled;
                    $user->activation = $_user->activation;
                    $user->user_type = $_user->user_type;
                    $this->saveUserToSession($user);
                    return $user;
                } else {
                    $this->deleteUserFromSession();
                    return false;
                }


                $this->checked_user_validity = true;
            }


            return $user;
        }

    }

    public function deleteUserFromSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }
        $_SESSION['tpp_store_user'] = null;
        unset($_SESSION['tpp_store_user']);
    }

    public function logOut()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        $this->deleteUserFromSession();

        TppStoreControllerStore::getInstance()->deleteStoreFromSession();

        $_SESSION['images_store'] = null;
        unset($_SESSION['images_store']);

        setcookie('stay_logged_out', 1, 0, '/');

        $this->redirect();
    }

    public function renderMenuButtons()
    {
        $user = $this->loadUserFromSession();

        include TPP_STORE_PLUGIN_DIR . 'site/views/common/menu.php';
    }

    private function _exitFacebookStatus(TppStoreModelUser $user)
    {

        //update the user last_visit date!
        $last_visited = $user->getLastVisit();

        $user->updateLastVisit();

        $json['redirect'] = '/shop/' . $user->user_type == 'store_owner'?'dashboard':'myaccount';

        $json['link_text'] = $user->user_type == 'store_owner'?'My Dashboard':'My Account';

        $json['popup'] = array();

        if (false !== $last_visited) {
            $json['popup']['name'] = $user->first_name;
            $json['popup']['last_visit'] = $last_visited;
        }


        if ($user->enabled == 0) {
            $json['message']['body'] = 'Your user account has not yet been verified. Please check your email to confirm your user account. <br><br><a class="wrap text-center" href="/shop/store_login/send_verification/">Click here to send a new verification email.</a><br><br><strong class="wrap text-center">OR</strong><br><br><a href="/shop/' . ($user->user_type == 'store_owner'?'dashboard':'myaccount') . '/" class="wrap text-center">Click here to continue to your account</a>';
            $json['message']['header'] = 'Verify your account';
        }

        $this->saveUserToSession($user);

        echo json_encode($json);

        exit;

    }

    public function renderPasswordResetForm()
    {

        $user = $this->getUserModel();

        if (false !== $user->readFromPost()) {
            if (false === $user->getUserByEmail()) {
                TppStoreMessages::getInstance()->addMessage('error', 'Could not find your email address on an account. Please contact us.');
            } elseif (intval($user->user_id) < 1) {
                TppStoreMessages::getInstance()->addMessage('error', 'Could not find your email address on an account. Please contact us.');
            } else {
                $user->generateNewPassword();
                $user->setData(array(

                ));
                if (false !== $user->save(false, true)) {

                    ob_start();

                    include TPP_STORE_PLUGIN_DIR . 'emails/password_reset.php';

                    $body = ob_get_contents();

                    ob_end_clean();

                    $this->sendMail($user->email, 'Your password has been reset', $body);

                    TppStoreMessages::getInstance()->addMessage('message', 'Your new password has been sent to your email address. Please check your inbox and spam/ junk box just incase!');
                    TppStoreMessages::getInstance()->saveToSession();
                    $this->redirect('shop/password_reset/');
                }

            }
        }

        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/user/password_reset.php';

        exit;

    }

    private function _clearLogoutCookie()
    {
        setcookie('stay_logged_out', null, strtotime('-1 days'), '/');
    }

    private function _loginWithFacebook($args = false)
    {

        $this->_clearLogoutCookie();

        header('Content-type: application/json');


        $fid = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_NUMBER_INT);

        //determine if a user exists in the session
        if (false !== ($user = $this->loadUserFromSession())) {
            //user is logged into our store
            if ($user->facebook_user_id == $fid) {
                //user is the same as the facebook user
                $this->_exitFacebookStatus($user);
            } else {
                //the two accounts are different - lets not override!
                //in fact do nothing
                echo (json_encode(array()));
                exit;
            }
        } else {
            $user = $this->getUserModel()->setData(array(
                'facebook_user_id'  =>  $fid
            ))->getUserByFacebookID();

            //see if the user has an account?
            if (intval($user->user_id) <= 0) {
                //no facebook account set - so see if the user account has an email address?
            }

            $this->_exitFacebookStatus($user);
        }

    }
}