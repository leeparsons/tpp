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

    public static $_meta_description= null;

    public static $_meta_title = null;

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
                $title[] = esc_attr($en->getTitle());
            }
        } elseif (is_object($entity)) {
            $title[] = esc_attr($entity->getTitle());
        } elseif (is_string($entity)) {
            $title[] = $entity;
        }


        TppStoreAbstractBase::$_meta_title = implode(' - ', $title);
    }

    public static function pageDescription($entity = false)
    {

        $description = array();
        if (is_array($entity)) {
            foreach ($entity as $en) {
                $description[] = $en->getDescription();
            }
        } else {
            $description[] = $entity->getDescription();
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

    public function getProductModel()
    {
        return new TppStoreModelProduct();
    }

    public function getProductsModel()
    {
        return new TppStoreModelProducts();
    }

    public function getMentorsModel()
    {
        return new TppStoreModelmentors();
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

    public function getOrderModel()
    {
        return new TppStoreModelOrder();
    }

    public function getOrderItemsModel()
    {
        return new TppStoreModelOrderItems();
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
        header('Location: /shop/dashboard/' . $path);
        exit;
    }

    public function redirectToAccount($path = '')
    {
        if ($path !== '') {
            $path = '/' . $path;
        }

        header('Location: /shop/myaccount' . $path);
        exit;
    }

    public function redirect($path = '/')
    {
        header('Location: ' . $path);
        exit;
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
            $model = 'TppStoreModel' . substr(substr($name, 3), 0, stripos($name, 'model') - 3);
            if (class_exists($model)) {
                return new $model($arguments);
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
        $headers = "From: Rosie Parsons <rosie@thephotographyparlour.com>" . "\r\n";
        $headers .= "Reply-to: rosie@thephotographyparlour.com" . "\r\n";
        $headers .= "Return-Path: rosie@thephotographyparlour.com" . "\r\n";
        //$headers .= "Organization: The Photography Parlour" . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "X-Priority: 3" . "\r\n";
        $headers .= "X-Mailer: PHP". phpversion() . "\r\n";
        mail($to, $subject, $message, $headers, "-frosie@thephotographyparlour.com");
    }



}