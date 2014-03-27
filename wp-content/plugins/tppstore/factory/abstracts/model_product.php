<?php
/**
 * User: leeparsons
 * Date: 27/01/2014
 * Time: 13:24
 */
 
class TppStoreAbstractModelBaseProduct extends TppStoreModelCurrency {

    public $product_id = null;
    public $product_title = null;
    public $product_slug = null;
    public $product_description = null;
    public $enabled = null;
    public $quantity_available = null;
    public $price = null;
    public $tax_rate = null;
    public $created_on = null;
    public $store_id = null;
    public $excerpt = null;
    public $product_type = null;
    public $product_type_text = null;
    public $unlimited = null;
    public $price_includes_tax = null;
    public $published_date = null;

    protected $_product_download_model = null;
    protected $_p2c_model = null;
    protected $_product_options_model = null;
    protected $_product_images_model = null;
    protected $_product_image_model = null;
    protected $_product_discount_model = null;
    protected $_store_model = null;
    protected $_product_mentor_model = null;
    protected $_mentor_2_product = null;


    public function __construct()
    {
        $this->_product_download_model = new TppStoreModelProductDownload();
        $this->_p2c_model = new TppStoreModelP2c();
        $this->_product_options_model = new TppStoreModelProductOptions();
        $this->_product_images_model = new TppStoreModelProductImages();
        $this->_product_image_model = new TppStoreModelProductImage();
        $this->_mentor_2_product = new TppStoreModelMentor2product();

    }

    public function getMentor2Product()
    {
        return $this->_mentor_2_product;
    }

    public function getProductOptionsModel()
    {
        return $this->_product_options_model;
    }

    public function getProductImage()
    {
        return $this->_product_image_model;
    }


    public function getStore($auto_load = false)
    {
        if (is_null($this->_store_model)) {
            $this->_store_model = new TppStoreModelStore();
            if ($auto_load === true && intval($this->store_id) > 0) {
                $this->_store_model->setData(array(
                    'store_id'  =>  $this->store_id
                ))->getStoreByID();
            }

        }

        return $this->_store_model;

    }

//    /*
//     * override for this product
//     */
//    public function getFormattedPrice($with_currency = false)
//    {
//
//        if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
//            $price = $this->price;
//        }
//
//        if ($this->price_includes_tax == 1) {
//            return $this->getFormattedCurrency($with_currency) . number_format($price, 2);
//        } else {
//            return $this->getFormattedCurrency($with_currency) . number_format($price * (1+($this->tax_rate/100)));
//        }
//    }

    public function getTypeStringSlug()
    {
        switch (intval($this->product_type)) {
            case 1:
                return 'download';
                break;

            case 2:
                return 'service';
                break;

            case 4:
                return 'mentor-session';
                break;

            case 5:
                return 'event-workshop';
                break;

            default:
                //case 3
                return 'product';
                break;
        }
    }

    public function getTypeString()
    {
        switch (intval($this->product_type)) {
            case 1:
                return 'download';
                break;

            case 2:
                return 'service';
                break;

            case 4:
                return 'mentor session';
                break;

            case 5:
                return 'event/ workshop';
                break;

            default:
                //case 3
                return 'product';
                break;
        }
    }

    public function getMentor()
    {
        if (is_null($this->_product_mentor_model)) {
            $this->_product_mentor_model = new TppStoreModelMentor();
            $this->_product_mentor_model->setData(array(
                'product_id'    =>  $this->product_id
            ))->getMentorByProduct(false);
        }

        return $this->_product_mentor_model;
    }

    public function getShortDescription()
    {
        if (is_null($this->excerpt) || trim($this->excerpt) == '') {
            return substr(htmlspecialchars_decode($this->description, ENT_QUOTES), 0, 150);
        } else {
            return $this->excerpt;
        }
    }

    public function getShortTitle()
    {


        if (strlen($this->product_title) > 35) {

            $str = '';

            $tmp = explode(' ', $this->product_title);

            foreach ($tmp as $bit) {
                $str .= $bit . ' ';
                if (strlen($str . ' ' . $bit) > 40) {
                    $str .= ' ...';
                    break;
                }
            }

            return $str;



//        } elseif (strlen($this->product_title) > 27) {
//
//            $str = '';
//
//            $tmp = explode(' ', $this->product_title);
//
//            foreach ($tmp as $bit) {
//                $str .= $bit . ' ';
//                if (strlen($str . ' ' . $bit) > 24) {
//                    $str .= ' ...';
//                    break;
//                }
//            }
//
//            return $str;
//
        } else {
            return $this->product_title;
        }
    }

    public function getSeoTitle()
    {
        return $this->product_title;
    }



    public function getDisplayAvailability()
    {
        if (intval($this->unlimited) == 1) {
            return '<span class="green">Available</span>';
        } elseif (intval($this->quantity_available) > 0) {
            return '<span class="green">' . $this->quantity_available . ' Available</span>';
        } else {
            return '<span class="red">sorry, sold Out!</span>';
        }
    }


    public function getProductType()
    {
        switch (intval($this->product_type))
        {
            case 1:
                return 'Service';
                break;

            case 2:
                return 'Download';
                break;

            case 3:
                return 'Physical Product';
                break;

            case 4:
                return 'Mentor Session';
                break;

            default:


                return null;
                break;
        }
    }

    public function getDiscount($auto_load = true)
    {
        if (is_null($this->_product_discount_model)) {
            $this->_product_discount_model = New TppStoreModelProductDiscount();
            $this->_product_discount_model->setData(array(
                'product_id'    =>  $this->product_id
            ));
            if ($auto_load === true) {
                $this->_product_discount_model->getDiscountByProduct();
            }

        }
        return $this->_product_discount_model;
    }

    /*
     * for seo
     */
    public function getTitle()
    {
        return $this->product_title;
    }

    /*
     * for seo
     */
    public function getDescription()
    {
        if (strlen($this->excerpt) > 0) {
            return esc_attr($this->excerpt);
        } else {
            return esc_attr(substr(strip_tags($this->excerpt), 0, 149));
        }
    }

    public function getPermalink($auto_load = false)
    {
        if (intval($this->product_id) > 0) {


            if (intval($this->getStore()->store_id) == 0) {
                //get store from object cache
                $store = TppStoreModelStores::getInstance($this->store_id);
            } else {
                $store = $this->getStore($auto_load);
            }

            $url = get_bloginfo('url');

            if (substr($url, -1) == '/') {
                $url = substr($url, -1);
            }

            return get_bloginfo('url') . '/shop/' . $store->store_slug . '/product/' . $this->product_slug .'/';
        } else {
            return '';
        }
    }

    public function getStoreEmail()
    {
        if (intval($this->store_id) <= 0) {
            throw new Exception('Could not determine the store id in the product model method: getStoreEmail()');
        }

        global $wpdb;


        $this->store_email = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT paypal_email FROM " . TppStoreModelStore::getInstance()->getTable() . " WHERE store_id = %d",
                $this->store_id
            )
        );



        if (filter_var($this->store_email, FILTER_VALIDATE_EMAIL)) {
            return $this->store_email;
        } else {
            throw new Exception('Could not find the store email address');
        }

    }


    public function getAdminDownloadUrl($encrypted = true)
    {
        if (is_null($this->_product_download_model->product_id)) {
            $this->_product_download_model->setData(array(
                'product_id'    =>  $this->product_id,
                'file'          =>  $this->product_type_text
            ));
        }
        return $this->_product_download_model->getDownloadUrlAdmin($encrypted);
    }

    public function getDownloadUrl($encrypted = true, $product_edit = false)
    {
        if (is_null($this->_product_download_model->product_id)) {
            $this->_product_download_model->setData(array(
                'product_id'    =>  $this->product_id,
                'file'          =>  $this->product_type_text
            ));
        }
        return $this->_product_download_model->getDownloadUrl($encrypted, $product_edit);

    }

    public function downloadExists()
    {
        if (is_null($this->_product_download_model->product_id)) {
            $this->_product_download_model->setData(array(
                'product_id'    =>  $this->product_id,
                'file'          =>  $this->product_type_text
            ));
        }
        return $this->_product_download_model->downloadExists();
    }


    public function getProductById($enabled = 'all', $type = 0)
    {

        switch ($enabled) {
            case 'all':
                $where = "";
                break;

            case '0';
                $where = " AND enabled = 0 ";
                break;

            default:
                $where = " AND enabled = 1 ";
                break;
        }

        if (is_null($this->product_id) || intval($this->product_id) <= 0) {
            $this->reset();
        } else {
            global $wpdb;

            if ($type == 5) {
                //event
                $rows = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT p.*, s.currency, e.* FROM " . $this->getTable() . " AS p
                        LEFT JOIN " . TppStoreModelEvent::getInstance()->getEventTable() . " AS e ON e.product_id = p.product_id
                    INNER JOIN shop_product_stores AS s ON s.store_id = p.store_id
                    WHERE p.product_id = %d $where",
                        $this->product_id
                    ),
                    OBJECT_K
                );

            } else {
                $rows = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT p.*, s.currency FROM " . $this->getTable() . " AS p
                    INNER JOIN shop_product_stores AS s ON s.store_id = p.store_id
                    WHERE product_id = %d $where",
                        $this->product_id
                    ),
                    OBJECT_K
                );
            }

            if ($wpdb->num_rows == 1) {
                foreach ($rows as $row) {
                    $this->setData($row);
                }
            } else {
                $this->reset();
            }
        }
        return $this;
    }

    public function getOptions()
    {

        if (intval($this->product_id) <= 0) {
            return false;
        }

        if (!$this->_options) {
            $this->_options = $this->_product_options_model->setData(array('product_id'   =>  $this->product_id))->getOptions();
        }

        return $this->_options;
    }

    public function getMainImage($size = false)
    {
        return $this->_product_images_model->setData(array(
            'product_id'    =>  $this->product_id
        ))->getMainImage($size);
    }

    public function getPriceWithoutTax($order_quantity = 1)
    {

        if ($this->currency !== geo::getInstance()->getCurrency()) {
            if (false === ($price = geo::getInstance()->convertCurrency($this->price, $this->currency))) {
                $price = $this->price;
            }
        } else {
            $price = $this->price;
        }

        if (intval($this->price_includes_tax) == 1) {
            return $this->format($order_quantity * $this->format($price / (1 + $this->tax_rate/100)));
        } else {
            return $this->format($order_quantity * $price);
        }
    }

    public function getTax($order_quantity = 1)
    {

        return $this->getFormattedTax(false, true, $order_quantity);

    }


}