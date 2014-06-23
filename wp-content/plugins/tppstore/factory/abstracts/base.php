<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:16
 */

if (!class_exists('TppStoreAbstractInstantiable')) {
    include TPP_STORE_PLUGIN_DIR . 'factory/abstracts/instantiable.php';
}

Abstract class TppStoreAbstractBase extends TppStoreAbstractInstantiable {

    public static $_meta_description= '';

    public static $_meta_title = '';

    protected $_is_dashboard = false;

    public function isDashboard()
    {
        return $this->_is_dashboard;
    }



    public static function checkRobot()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mc_name = filter_input(INPUT_POST, 'mc_name', FILTER_UNSAFE_RAW);

            if (!is_null($mc_name) && $mc_name != '') {
                self::getInstance()->_setWpQuery403();
                throw new Exception('No bot access allowed');
            }
        }
    }

    public static function pageTitle($entity = false)
    {

        $title = array();
        if (is_array($entity)) {
            foreach ($entity as $en) {
                if (is_string($en)) {
                    $title[] = esc_attr($en);
                } else {
                    $title[] = esc_attr($en->getSeoTitle());
                }
            }
        } elseif (is_object($entity)) {
            $title[] = esc_attr($entity->getSeoTitle());
        } elseif (is_string($entity)) {
            $title[] = $entity;
        }


        TppStoreAbstractBase::$_meta_title .= implode(' - ', $title);
    }

    public function setPageDescription($entity = false)
    {

        if (is_array($entity)) {
            foreach ($entity as $en) {
                TppStoreAbstractBase::$_meta_description .= $en->getSeoDescription();
            }
        } elseif (is_string($entity)) {
            TppStoreAbstractBase::$_meta_description = $entity;
        } elseif (is_object($entity)) {
            TppStoreAbstractBase::$_meta_description = $entity->getSeoDescription();
        }
    }

    public static function pageDescription($entity = false)
    {

        $description = array();
        if (is_array($entity)) {
            foreach ($entity as $en) {
                $description[] = $en->getSeoDescription();
            }
        } else {
            $description[] = $entity->getSeoDescription();
        }


        echo implode('. ', $description);
    }

    public function getWishlistModel()
    {
        return new TppStoreModelWishlist();
    }

    public function getCartModel()
    {
        return new TppStoreModelCart();
    }

    public function getStoreModel()
    {
        return new TppStoreModelStore();
    }

    public function getRatingModel()
    {
        return new TppStoreModelRating();
    }

    public function getProductImagesModel()
    {
        return new TppStoreModelProductImages();
    }

    public function getEmailSignupModel()
    {
        return new TppStoreModelEmailSignup();
    }

    public function getProductModel()
    {
        return new TppStoreModelProduct();
    }

    public function getProductsModel()
    {
        return new TppStoreModelProducts();
    }

    public function getEventsModel()
    {
        return new TppStoreModelModelEvents();
    }

    public function getMentorsModel()
    {
        return new TppStoreModelMentors();
    }

    public function getMentorModel()
    {
        return new TppStoreModelMentor();
    }

    public function getUserDiscountModel()
    {
        return new TppStoreModelUserDiscount();
    }

    public function getUserModel()
    {
        return new TppStoreModelUser();
    }

    public function getCategoriesModel()
    {
        return new TppStoreModelCategories();
    }

    public function getStorePagesModel()
    {
        return new TppStoreModelStorePages();
    }

    public function getOrderInfoModel()
    {
        return new TppStoreModelOrderInfo();
    }


    public function getEventModel()
    {
        return new TppStoreModelEvent();
    }

    public function getOrderModel()
    {
        return new TppStoreModelOrder();
    }

    public function getOrderItemsModel()
    {
        return new TppStoreModelOrderItems();
    }

    public function getMessagesModel()
    {
        return new TppStoreModelMessages();
    }

    public function getMessageModel()
    {
        return new TppStoreModelMessage();
    }

    public function getMentorSpecialismsModel()
    {
        return new TppStoreModelMentorSpecialisms();
    }

    public function getStoreApplicationModel()
    {

        if (!class_exists('TppStoreAdminModelStoreMeta')) {
            include TPP_STORE_PLUGIN_DIR . 'admin/models/store_meta.php';
        }

        return new TppStoreAdminModelStoreMeta();

    }

    public function getPaymentModel()
    {
        return new TppStoreModelPayment();
    }

    public function redirectToDashboard($path = '')
    {
        if ($path !== '') {
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
        }
        header('Location: /shop/dashboard/' . $path);
        exit;
    }

    public function redirectToAccount($path = '')
    {


        if ($path !== '') {
            $path = '/' . $path;
        }

        if ($path !== '') {
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
        }

        if (substr($path, 0, 1) == '') {
            $path = substr($path, 1);
        }

        header('Location: /shop/myaccount/' . $path);
        exit;
    }

    public function redirect($path = '/', $add_trailing_slash = true)
    {

        if ($add_trailing_slash === true && $path !== '' && strpos($path, '?') === false) {
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
        }
        header('Location: ' . $path);
        die;
    }

    public function redirectToLogin($args = '')
    {

        if ($args != '') {
            $args = '?' . $args;
        }
        header('Location: /shop/store_login/' . $args);
        exit;
    }

    public function __call($name, $arguments)
    {

        if (stripos($name, 'get') !== false && stripos($name, 'model') !== false) {
            $part = substr(substr($name, 3), 0, stripos($name, 'model') - 3);
            $model = 'TppStoreModel' . $part;
            if (class_exists($model)) {
                return new $model($arguments);
            } else {
                //TODO: try to locate this file?

                if (substr($part, 0, 5) == 'Admin') {
                    //locate this file in the admin!

                    $file = strtolower(substr($part, 5));

                    if (file_exists(TPP_STORE_PLUGIN_DIR . 'admin/models/' . $file . '.php')) {
                        include_once TPP_STORE_PLUGIN_DIR . 'admin/models/' . $file . '.php';
                        return new $model($arguments);
                    }
                }
            }
        }

        return null;
    }


    public function __get($key)
    {
        return '';
    }

    protected function redirectToStage($stage = 1)
    {
        header('Location: /shop/store_register/' . $stage);
        exit;
    }

    protected function redirectToReferer()
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ' . '/');
        }
        exit;
    }

    protected function _setWpQuery404()
    {
        global $wp_query;
        header('HTTP/1.1 400 Not Found');
        $wp_query->is_404 = true;

    }


    protected function _setWpQuery403()
    {
        global $wp_query;
        header('HTTP/1.1 403 Forbidden');
        $wp_query->is_403 = true;
        $wp_query->is_404 = false;

    }


    protected function _setJsonHeader()
    {
        header('Content-type: application/json');
    }

    protected function _setWpQuery412()
    {
        global $wp_query;
        header('HTTP/1.1 412 Precondition Failed');
        $wp_query->is_412 = true;
        $wp_query->is_404 = true;
    }

    protected function _setWpQueryOk()
    {

        global $wp_query;
        header('HTTP/1.1 200 OK');
        $wp_query->is_404 = false;


    }

    private function _setUncachedHeader()
    {

        header('Cache-Control:public, max-age=0');
        header('Expires: Mon, 25 Jun 2012 21:31:12 GMT');

    }


    /*
     * @param status = status string
     */
    protected function _exitStatus($status, $error = false, $data = array()) {

        header('Content-type: application/json');
        echo json_encode(array('status' => $status, 'error' => $error, 'data'  =>  $data));
        exit;
    }

    protected function sendMail($to, $subject, $message)
    {



        if (getenv('ENVIRONMENT') == 'local' || strtolower($_SERVER['HTTP_HOST']) == 'dev.thephotographyparlour.com') {
            $to = 'parsolee@gmail.com';

        }

        $headers = "From: Rosie Parsons <rosie@thephotographyparlour.com>" . "\r\n";
        $headers .= "Reply-to: rosie@thephotographyparlour.com" . "\r\n";
        $headers .= "Return-Path: rosie@thephotographyparlour.com" . "\r\n";
        //$headers .= "Organization: The Photography Parlour" . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "X-Priority: 3" . "\r\n";
        $headers .= "X-Mailer: PHP". phpversion() . "\r\n";
        mail($to, $subject, $message, $headers, "-f rosie@thephotographyparlour.com");
    }



}