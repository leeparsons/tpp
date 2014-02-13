<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:44
 */
 
class TppStoreModelProduct extends TppStoreAbstractModelBaseProduct {


    protected $option_min_price = null;

    protected $_ratings_model = null;

    public $categories = null;

    protected $_images = array();

    protected $_options = null;

    protected $_table = 'shop_products';


    public function __construct()
    {
        parent::__construct();
        $this->_ratings_model = new TppStoreModelRatings();
    }

    public function getCategories()
    {

        if (is_null($this->categories)) {



            if (is_null($this->product_id) || intval($this->product_id) <= 0) {
                return array();
            }

            $this->categories = $this->_p2c_model->setData(array('product_id'   =>  $this->product_id))->getCategories();

        }

        return $this->categories;

    }

    public function getProductBySlug($slug = '', $store_id = 0, $enabled = 1)
    {

        if (trim($slug) !== '') {

            global $wpdb;

            $join = " INNER JOIN shop_product_stores AS s ON s.store_id = p.store_id ";


            if (intval($store_id) > 0) {

                if ($enabled !== -1) {
                    $res = $wpdb->get_results(
                        $wpdb->prepare("SELECT p.*, s.currency FROM " . $this->getTable() . " AS p $join WHERE p.store_id = %d AND product_slug = %s AND p.enabled = %d",
                            array($store_id, $slug, $enabled)
                        ),
                        OBJECT_K
                    );
                } else {
                    $res = $wpdb->get_results(
                        $wpdb->prepare("SELECT p.*, s.currency FROM " . $this->getTable() . " AS p $join WHERE p.store_id = %d AND product_slug = %s",
                            array($store_id, $slug)
                        ),
                        OBJECT_K
                    );
                }

            } else {
                if ($enabled === -1 || $enabled == 'all') {
                    $res = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT p.*, currency FROM " . $this->getTable() . " AS p $join WHERE product_slug = %s",
                            $slug
                        ),
                        OBJECT_K
                    );
                } else {
                    $res = $wpdb->get_results(
                        $wpdb->prepare("SELECT p.*, currency FROM " . $this->getTable() . " AS p $join WHERE product_slug = %s AND p.enabled = %d",
                            array($slug, $enabled)
                        ),
                        OBJECT_K
                    );
                }
            }


            if ($wpdb->num_rows == 1) {
                foreach ($res as $row) {
                    foreach ($row as $k => $v) {
                        $this->$k = $v;
                    }
                }
            } else {
                $this->reset();
            }

        }


        return $this;
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->product_title = filter_input(INPUT_POST, 'product_title', FILTER_SANITIZE_STRING);
            $this->product_description = filter_input(INPUT_POST, 'full_description', FILTER_UNSAFE_RAW);
            $this->enabled = filter_input(INPUT_POST, 'product_enabled', FILTER_SANITIZE_NUMBER_INT);
            $this->quantity_available = filter_input(INPUT_POST, 'product_quantity', FILTER_SANITIZE_NUMBER_INT);
            $this->price = filter_input(INPUT_POST, 'product_price', FILTER_SANITIZE_STRING);
            $this->tax_rate = filter_input(INPUT_POST, 'product_tax_rate', FILTER_SANITIZE_STRING);
            $this->excerpt = filter_input(INPUT_POST, 'short_description', FILTER_SANITIZE_STRING);
            $this->product_type = filter_input(INPUT_POST, 'product_type', FILTER_SANITIZE_NUMBER_INT);
            $this->store_id = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_NUMBER_INT);
            $this->product_id = filter_input(INPUT_POST, md5('product_id' . NONCE_KEY), FILTER_SANITIZE_NUMBER_INT);
            $this->unlimited = filter_input(INPUT_POST, 'unlimited', FILTER_SANITIZE_NUMBER_INT);

            //validate the price:
            $this->price = (float)$this->price;

            $this->price_includes_tax = filter_input(INPUT_POST, 'price_includes_tax', FILTER_SANITIZE_NUMBER_INT);

            $this->tax_rate = (float)$this->tax_rate;

            //determine the product slug!

            $this->product_slug = sanitize_title_with_dashes($this->product_title);


            switch ($this->product_type)
            {
                case 2:
                    //services
                    $this->product_type_text = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
                    break;
                case 3:
                    //hosted
                    $this->product_type_text = filter_input(INPUT_POST, 'hosted', FILTER_SANITIZE_STRING);
                    break;

                case 4:
                default:

                    //download
                    if (isset($_FILES['download']) && $_FILES['download']['name'] !== '') {
                        $this->product_type_text = $_FILES['download']['name'];
                    } else {
                        $this->product_type_text = filter_input(INPUT_POST, 'original_download', FILTER_SANITIZE_STRING);
                    }
                    break;
            }

            return true;

        } else {
            return false;
        }


    }

    /*
     * $rating = -1 to get all ratings, 0, 1, 2, 3, 4, 5 to get individual ratings, or array of ratings
     */
    public function getAverageRating($rating = -1)
    {
        return $this->_ratings_model->setData(array(
            'product_id'    =>  $this->product_id
        ))->getAverageRating($rating);
    }

    /*
   * $rating = -1 to get all ratings, 0, 1, 2, 3, 4, 5 to get individual ratings, or array of ratings
   */
    public function getReviews($rating = -1)
    {
        return $this->_ratings_model->setData(array(
            'product_id'    =>  $this->product_id
        ))->getReviews();
    }

    public function getImagesBySize($size = 'main')
    {
        if (intval($this->product_id) <= 0) {
            return array();
        } else {
            return $this->_product_images_model->setData(array(
                'product_id'    =>  $this->product_id,
                'size_alias'    =>  $size
            ))->getImagesBySize();
        }
    }

    public function getImages($parent_id = 0, $heirarchical = false)
    {

        return $this->_product_images_model->setData(array(
            'product_id'    =>  $this->product_id
        ))->getImages($parent_id, $heirarchical);

    }




    public function updateQuantity()
    {
        if (intval($this->product_id) <= 0) {
            return false;
        }

        global $wpdb;


        if ($this->quantity_available < 0) {
            $this->quantity_available = 0;
        }

        $wpdb->update(
            $this->getTable(),
            array(
                'quantity_available'  =>  $this->quantity_available
            ),
            array(
                'product_id'    =>  $this->product_id
            ),
            array(
                "%d"
            ),
            array(
                "%d"
            )

        );


    }

    /*
    *   @param $validate_records - determines whether or not to validate the unique fields, like title or slug against current items in the database owned by this user?
    *  product full urls are determined by the store owner so can have duplicate product slugs that are not in this store
    */
    public function save($validate_records = true)
    {


        //if there are files that have been uploaded then save them into the session and retrieve them later
        $images_to_save_in_session = array();
        $new_images = array();
        $image_ordering = array();

        $saved_already = intval(filter_input(INPUT_POST, 'saved_preview', FILTER_SANITIZE_NUMBER_INT));

        if ($saved_already == 0) {
            $new_images = filter_input(INPUT_POST, 'uploaded_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            $image_ordering = filter_input(INPUT_POST, 'image_ordering', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);



            if (is_array($new_images) && !empty($new_images) && is_array($image_ordering) && !empty($image_ordering)) {

                $new_images = array_flip($new_images);

                //the keys of image ordering coincide with the image names. So if an ordering is sent up for an image that does not exist in the
                //new images sent up, then don't set the image for that ordering - assume it's being kept as the current image!

                foreach ($image_ordering as $image => $order) {
                    if (isset($new_images[$image])) {
                        $images_to_save_in_session[$order] = $image;
                    }
                }

            }

        }



        $_SESSION['tpp_store']['tmp_new_product_images'] = $images_to_save_in_session;
        unset($images_to_save_in_session);
        unset($image_ordering);
        unset($new_images);

        if (!$this->validate($validate_records)) {
            if (intval($this->product_type) == 4) {
                $this->getMentor()->readFromPost();
            }
            return false;
        }

        global $wpdb;



        if (intval($this->product_id) <= 0) {
            $wpdb->insert(
                $this->getTable(),
                array(
                    'product_title'         =>  $this->product_title,
                    'product_slug'          =>  sanitize_title_with_dashes($this->product_slug),
                    'quantity_available'    =>  $this->quantity_available,
                    'tax_rate'              =>  $this->tax_rate,
                    'enabled'               =>  $this->enabled,
                    'price'                 =>  $this->price,
                    'product_description'   =>  $this->product_description,
                    'excerpt'               =>  $this->excerpt,
                    'store_id'              =>  $this->store_id,
                    'product_type'          =>  $this->product_type,
                    'product_type_text'     =>  $this->product_type_text,
                    'unlimited'             =>  $this->unlimited,
                    'price_includes_tax'    =>  $this->price_includes_tax

                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%d'
                )
            );

            if ($wpdb->rows_affected == 1) {
                $this->product_id = $wpdb->insert_id;
            } else {
                TppStoreMessages::getInstance()->addMessage('error',    array('product' =>  $wpdb->last_error));
                return false;
            }

        } else {
            $wpdb->update(
                $this->getTable(),
                array(
                    'product_title'         =>  $this->product_title,
                    'product_slug'          =>  sanitize_title_with_dashes($this->product_slug),
                    'quantity_available'    =>  $this->quantity_available,
                    'tax_rate'              =>  $this->tax_rate,
                    'enabled'               =>  $this->enabled,
                    'price'                 =>  $this->price,
                    'product_description'   =>  $this->product_description,
                    'excerpt'               =>  $this->excerpt,
                    //'store_id'              =>  $this->store_id,
                    'product_type'          =>  $this->product_type,
                    'product_type_text'     =>  $this->product_type_text,
                    'unlimited'             =>  $this->unlimited,
                    'price_includes_tax'    =>  $this->price_includes_tax
                ),
                array(
                    'product_id'    =>  $this->product_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    //'%d',
                    '%d',
                    '%s',
                    '%d',
                    '%d'
                ),
                array(
                    '%d'
                )
            );

            if ($wpdb->rows_affected == 0 && $wpdb->last_error !== '') {
                //error is only empty if no rows were updated because the difference is none!
                TppStoreMessages::getInstance()->addMessage('error',    array('product' =>  $wpdb->last_error));
                return false;
            }


        }


        $product_download_continue = true;

        if ($this->product_type == 1 && isset($_FILES['download']) && $_FILES['download']['name'] !== '') {
            $product_download_continue = $this->_product_download_model->setData(
                array(
                    'product_id'  =>  $this->product_id,
                    'upload_file'   =>  $_FILES['download']
                ))->save();
        }

        //need to be able to save categories
        $this->_p2c_model->setData(array('product_id'   =>  $this->product_id))->readFromPost();

        $product_categories_continue = $this->_p2c_model->save();

        //save the product options!
        $this->_product_options_model->setData(array('product_id'   =>  $this->product_id))->readFromPost();

        $product_options_continue = $this->_product_options_model->save();

        if ($saved_already == 0) {
            $this->_product_images_model->setData(array(
                'product_id'    =>  $this->product_id,
                'store_id'      =>  $this->store_id
            ))->retrieveUsingSession();

            $product_images_continue = $this->_product_images_model->save();
        } else {
            $product_images_continue = true;
        }

        if (intval(filter_input(INPUT_POST, 'preview', FILTER_SANITIZE_NUMBER_INT)) != 1) {
            $_SESSION['tpp_store']['tmp_new_product_images'] = null;
            unset($_SESSION['tpp_store']['tmp_new_product_images']);
        }



        $product_discounts_continue = false;

        $discount = $this->getDiscount(false);

        if ($discount->readFromPost()) {

            $discount->setData(array(
                'product_id'    =>  $this->product_id
            ));

            $product_discounts_continue = $discount->save();
        }


        $product_mentor_continue = true;

        if ($this->product_type == 4) {

            if (true === $this->getMentor()->readFromPost()) {
                $this->_product_mentor_model->setData(array(
                    'product_id'    =>  $this->product_id
                ));
                $product_mentor_continue = $this->_product_mentor_model->save();
            } else {
                $product_mentor_continue = false;
            }


        }

        return
            $product_mentor_continue
            &&
            $product_images_continue
            &&
            $product_download_continue
            &&
            $product_categories_continue
            &&
            $product_options_continue
            &&
            $product_discounts_continue;
    }

    public function validate($validate_records = true)
    {

        if (intval($this->product_type) === 4) {
            $product_string = 'mentor session';
        } else {
            $product_string = 'product';
        }

        $error = false;

        if (is_null($this->store_id) || intval($this->store_id) <= 0) {
            TppStoreMessages::getInstance()->addMessage('error', array('product_store'  =>  'We could not determine your store.'));

            TppStoreLibraryLogger::getInstance()->add(null, 'We could not determine the users store', 'product upload', array(
                'product_id'    =>  $this->product_id
            ));

            return false;
        }

        global $wpdb;

        if (trim($this->product_title) == '' || is_null($this->product_title)) {
            TppStoreMessages::getInstance()->addMessage('error', array('product_title'  =>  'Please enter a title for your ' . $product_string));
            $error = true;
        } elseif (true === $validate_records) {
            if (!is_null($this->store_id) && intval($this->store_id) > 0) {
                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT product_id FROM " . $this->getTable() . " WHERE store_id = %d AND product_id <> %d AND product_title = %s",
                        array(
                            $this->store_id,
                            $this->product_id,
                            $this->product_title
                        )
                    )
                );

                if ($wpdb->num_rows > 0) {
                    $error = true;
                    TppStoreMessages::getInstance()->addMessage('error', array('product'    =>  'Another product or mentor session with this title exists in your store.'));
                }

            }
        }

        if (trim($this->product_slug) == '' || is_null($this->product_slug)) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('product_title'  =>  'Please enter a url for your ' . $product_string));
        } elseif (true === $validate_records) {
            if (!is_null($this->store_id) && intval($this->store_id) > 0) {
                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT product_id FROM " . $this->getTable() . " WHERE store_id = %d AND product_id <> %d AND product_slug = %s",
                        array(
                            $this->store_id,
                            $this->product_id,
                            $this->product_slug
                        )
                    )
                );

                if ($wpdb->num_rows > 0) {
                    $error = true;
                    TppStoreMessages::getInstance()->addMessage('error', array('product'    =>  'Another ' . $product_string . ' with this url exists in your store. Tip: change your product title to change the url.'));
                }

            }
        }

        switch ($this->product_type) {
            case 2:
                //service or url for hosted service

//                if (trim($this->product_type_text) == '' || is_null($this->product_type_text)) {
//                    $error = true;
//                    TppStoreMessages::getInstance()->addMessage('error', array('product_type'    =>  'Please enter the name of the service you offer.'));
//                }

                break;
            case 3:
                //physical product - no need for the hosted url
//                if (trim($this->product_type_text) == '' || is_null($this->product_type_text)) {
//                    $error = true;
//                    TppStoreMessages::getInstance()->addMessage('error', array('product_type'    =>  'Please enter the url of your hosted service.'));
//                }
                break;

            case 1:
            case 4:

                //download file
                if (trim($this->product_type_text) == '' || is_null($this->product_type_text)) {
                    //don't stop on the file upload, so don't raise an error on this point!

                    //TppStoreMessages::getInstance()->addMessage('error', array('product_type'    =>  'Please choose a file to be downloaded.'));
                } elseif (isset($_FILES['download']) && $_FILES['download']['name'] !== '' && (!is_null($this->product_type_text) && $this->product_type_text != '')) {
                    //determine if this file can be uploaded?
                    if (true === $this->_product_download_model->setData(array('upload_file'   =>  $_FILES['download']))->canUpload(false)) {
                        $this->product_type_text = $this->_product_download_model->upload_file['name'];
                        //don't stop on the file upload, so don't raise an error on this point!
                    }
                }
                break;
            default:
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', array('product_type'    =>  'The product type you selected is not recognised.'));
                break;
        }

        if (intval($this->price_includes_tax) == 1 && floatval($this->tax_rate) <= 0) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('product_tax' => 'Please enter a tax rate so we can determine the tax to display on invoices'));
        }


        if (mb_strlen($this->product_description, 'UTF-8') > 21000) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('product_description' => 'Your description is over the limit of 21000 characters. Please make it a little shorter.'));
        }

        $this->product_description = strip_tags($this->product_description);

        return !$error;
    }

}