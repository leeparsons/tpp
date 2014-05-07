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

            $this->categories = $this->_p2c_model->setData(array(
                'product_id'   =>  $this->product_id
            ))->getCategories();

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

            $this->price_includes_tax = filter_input(INPUT_POST, 'price_includes_tax', FILTER_SANITIZE_STRING);

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
                case 5:

                    break;

                case 4:
                default:

                    if ('' != ($download_elsewhere = filter_input(INPUT_POST, 'download_elsewhere', FILTER_SANITIZE_STRING))) {
                        $this->product_type_text = $download_elsewhere;
                    } elseif (isset($_FILES['download']) && $_FILES['download']['name'] !== '') {
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

    public function getImages($parent_id = 0, $heirarchical = false, $count = false)
    {

        return $this->_product_images_model->setData(array(
            'product_id'    =>  $this->product_id
        ))->getImages($parent_id, $heirarchical, $count);

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

        $this->clearCache();

    }

    /*
     * Save images in a session temporarily incase the save fails
     */
    protected function saveImagesTemporarily()
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

        TppStoreLibraryLogger::getInstance()->add(0, 'saving images to session', 'see if they get saved?', print_r($images_to_save_in_session, true));

        return $saved_already;
    }

    public function deleteTemporaryImages()
    {
        if (intval(filter_input(INPUT_POST, 'preview', FILTER_SANITIZE_NUMBER_INT)) != 1) {
            $_SESSION['tpp_store']['tmp_new_product_images'] = null;
            unset($_SESSION['tpp_store']['tmp_new_product_images']);
        }
    }

    /*
    *   @param $validate_records - determines whether or not to validate the unique fields, like title or slug against current items in the database owned by this user?
    *  product full urls are determined by the store owner so can have duplicate product slugs that are not in this store
    */
    public function save($validate_records = true)
    {


        $saved_already = $this->saveImagesTemporarily();

        if (!$this->validate($validate_records)) {
            if (intval($this->product_type) == 4) {
                if (true === $this->getMentor2Product()->readFromPost()) {
                    if (false === $this->getMentor2Product()->validate(true)) {
                        TppStoreMessages::getInstance()->addMessage('error', array('mentor' =>  'Please select a mentor'));
                    }
                }

            }
            return false;
        }

        global $wpdb;



        if (intval($this->product_id) <= 0) {


            if (intval($this->enabled) == 1) {
                $this->published_date = date('Y-m-d h:i:s');
                $this->notify_live = true;
            } else {
                $this->published_date = null;
            }

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
                    'price_includes_tax'    =>  $this->price_includes_tax,
                    'published_date'        =>  $this->published_date
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%f',
                    '%d',
                    '%f',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%s',
                    '%s'
                )
            );

            if ($wpdb->rows_affected == 1) {
                $this->product_id = $wpdb->insert_id;
            } else {
                TppStoreMessages::getInstance()->addMessage('error',    array('product' =>  $wpdb->last_error));
                return false;
            }

        } else {


            if (intval($this->enabled) == 1 && trim($this->published_date) == '' || $this->published_date == '0000-00-00 00:00:00') {
                $this->published_date = date('Y-m-d h:i:s');
                $this->notify_live = true;
            } elseif (intval($this->enabled) == 0) {
                $this->published_date = null;
            }

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
                    'price_includes_tax'    =>  $this->price_includes_tax,
                    'published_date'        =>  $this->published_date
                ),
                array(
                    'product_id'    =>  $this->product_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%f',
                    '%d',
                    '%f',
                    '%s',
                    '%s',
                    //'%d',
                    '%d',
                    '%s',
                    '%d',
                    '%s',
                    '%s'
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

//        if ($this->product_type == 1 && isset($_FILES['download']) && $_FILES['download']['name'] !== '') {
//            $product_download_continue = $this->_product_download_model->setData(
//                array(
//                    'product_id'  =>  $this->product_id,
//                    'upload_file'   =>  $_FILES['download']
//                ))->save();
//        }

        if ($this->product_type == 1 || $this->product_type == 4) {


            if ( true === $this->_product_download_model->readFromPost()) {
                $product_download_continue = $this->_product_download_model->setData(
                    array(
                        'product_id'        =>  $this->product_id
                    )
                )->save($this->product_type == 1);

                if (true === $product_download_continue) {
                    $this->product_type_text = $this->_product_download_model->getPlainLink();
                    $wpdb->update(
                        $this->getTable(),
                        array(
                            'product_type_text' =>  $this->product_type_text
                        ),
                        array(
                            'product_id'        =>  $this->product_id
                        ),
                        array(
                            "%s"
                        ),
                        array(
                            "%d"
                        )
                    );
                }

            } else {
                $product_download_continue = false;
            }

//              setData(
//                array(
//                    'product_id'        =>  $this->product_id,
//                    'upload_file'       =>  $path,
//                    'download_override' =>  trim($path) != '',
//                    'file'              =>  TppStoreControllerProduct::getInstance()->getProductUploadFileFromSession()
//                ))->save();


        }

        //need to be able to save categories
        $this->_p2c_model->setData(array('product_id'   =>  $this->product_id))->readFromPost();

        $product_categories_continue = $this->_p2c_model->save();

        //save the product options!
        $this->_product_options_model->setData(array('product_id'   =>  $this->product_id))->readFromPost();

        $product_options_continue = $this->_product_options_model->save();

        if ($product_categories_continue === true) {
            if ($saved_already == 0) {

                $this->_product_images_model->setData(array(
                    'product_id'    =>  $this->product_id,
                    'store_id'      =>  $this->store_id
                ))->retrieveUsingSession();

                $product_images_continue = $this->_product_images_model->save();
            } else {
                $product_images_continue = true;
            }
            $this->deleteTemporaryImages();

        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Warning: your new images have not yet been saved. Please fix the errors before saving.');
        }

        //determine if there are any images that have been saved?
        $product_image_count = $this->getImages(0, false, true);

        $product_discounts_continue = false;

        $discount = $this->getDiscount(false);

        if ($discount->readFromPost()) {

            $discount->setData(array(
                'product_id'    =>  $this->product_id
            ));

            $product_discounts_continue = $discount->save();
        }

        if (intval($this->enabled) == 1) {

            if ($product_image_count <= 0) {

                $this->setOffline();


                switch ($this->product_type) {
                    case '4':
                        $product_type = 'mentor session';
                        break;
                    case '5':
                        $product_type = 'event/ workshop';
                        break;
                    default:
                        $product_type = 'product';
                        break;
                }

                TppStoreMessages::getInstance()->addMessage('error', 'No images were saved - your ' . $product_type . ' has been set to offline and will not appear on the website listings. Please upload some images to be able to make this ' . $product_type . ' go live.');
                $product_images_continue = false;
            }

            if (trim($this->product_type_text) == '' && $this->product_type == 1) {
                $this->setOffline();
                TppStoreMessages::getInstance()->addMessage('error', 'Please fill out the download options before you can make this product go live');
                $product_discounts_continue = false;
            }
        }

        $product_mentor_continue = true;

        if ($this->product_type == 4) {

            if (true === $this->getMentor2Product()->readFromPost()) {
                if (false === $this->getMentor2Product()->validate(true)) {
                    TppStoreMessages::getInstance()->addMessage('error', array('mentor' =>  'Please select a mentor'));
                    $product_mentor_continue = false;
                } else {

                    $product_mentor_continue = $this->_mentor_2_product->setData(array(
                        'product_id'    =>  $this->product_id
                    ))->save();
                }
            }
        }

        if ($this->product_id == 1 && ($this->product_type_text) == '') {
            $this->setOffline();
        }

        //clean out the product cache!
        $this->clearCache();

        $continue =
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

        if ($continue === false) {
            $this->notify_live = false;
        }

        return $continue;

    }

    public function clearCache()
    {
        $c = new TppCacher();
        $c->setCachePath('product/' . $this->product_id . '/');
        $c->deleteCache();
        $c->setCachePath('homepage/products/top/');
        $c->deleteCache();
        $c->setCachePath('latest/products/');
        $c->deleteCache();
        $c->setCachePath('homepage/categories/featured/');
        $c->deleteCache();
        if ($this->product_type == 4) {
            $c->setCachePath('mentor/' . $this->getMentor()->mentor_id);
            $c->deleteCache();
        }
    }

    public function setOffline()
    {
        if (intval($this->product_id) > 0 && intval($this->enabled) != 0) {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE " . $this->getTable() . " SET enabled = 0 WHERE product_id = %d",
                    $this->product_id
                )
            );
        }

        $this->enabled = 0;

        $this->clearCache();
    }

    public function validate($validate_records = true)
    {

        if (intval($this->product_type) === 4) {
            $product_string = 'mentor session';
        } elseif (intval($this->product_type) === 5) {
            $product_string = 'event';
        } else {
            $product_string = 'product';
        }

        $error = false;

        if (is_null($this->store_id) || intval($this->store_id) <= 0) {

            if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_store'  =>  'We could not determine your store.'));

                TppStoreLibraryLogger::getInstance()->add(null, 'We could not determine the users store', 'product upload', array(
                    'product_id'    =>  $this->product_id
                ), 'error');
                return false;

            }

            $this->store_id = $store->store_id;



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


            case 5:
                //event!

                break;
            case 1:
                //download

                $type = filter_input(INPUT_POST, 'download_location', FILTER_SANITIZE_NUMBER_INT);

                switch ($type)
                {
                    case '2':

                        //hosted with us
                        $file = TppStoreControllerProduct::getInstance()->getProductUploadFileFromSession();

                        $path = TppStoreControllerProduct::getInstance()->getProductUploadDirectoryFromSession() . $file;

                        if (!file_exists($path) && ('' == (filter_input(INPUT_POST, 'original_download', FILTER_SANITIZE_STRING)))) {
                            TppStoreMessages::getInstance()->addMessage('error', 'Please select a file for download.');
                        }
                        break;

                    default:
                        //hosted else where

                        if ('' != ($download = filter_input(INPUT_POST, 'download_elsewhere', FILTER_SANITIZE_STRING))) {

                            $this->product_type_text = $download;

                            if (!$e = preg_match('_^(?:(?:https?|http|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', $download)) {

                                if (intval($this->enabled) == 1) {
                                    $this->setOffline();
                                    TppStoreMessages::getInstance()->addMessage('error', 'The url you provided for the download: ' . $download . ' is not a valid url. Please fix this before your product can go live.');
                                } else {
                                    TppStoreMessages::getInstance()->addMessage('error', 'The url you provided for the download: ' . $download . ' is not a valid url. The download will probably not work.');
                                }
                            }

                        }
                        break;

                }

                break;
            case 4:

                //mentor session

                //download file
               // if (trim($this->product_type_text) == '' || is_null($this->product_type_text)) {
                    //don't stop on the file upload, so don't raise an error on this point!

                    //TppStoreMessages::getInstance()->addMessage('error', array('product_type'    =>  'Please choose a file to be downloaded.'));
                //} else
                if (
                    isset($_FILES['download']) && $_FILES['download']['name'] !== ''
                    &&
                    (!is_null($this->product_type_text) && $this->product_type_text != '')) {
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

        if ($this->price_includes_tax != 'na' && floatval($this->tax_rate) <= 0) {
            //$error = true;
            $this->tax_rate = 0.00;
            //TppStoreMessages::getInstance()->addMessage('error', array('product_tax' => 'Please enter a tax rate so we can determine the tax to display on invoices'));
        } elseif ($this->price_includes_tax == 'na') {
            $this->tax_rate = 0;
        }


        if (mb_strlen($this->product_description, 'UTF-8') > 21000) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('product_description' => 'Your description is over the limit of 21000 characters. Please make it a little shorter.'));
        }

        $this->product_description = strip_tags($this->product_description);

        return !$error;
    }


    public function delete()
    {
        if (intval($this->product_id) < 1 || intval($this->store_id) < 1) {
            return false;
        }


        //delete the files and directory

        $dir = new TppStoreDirectoryLibrary();

        $dir->setDirectory(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id);

        if (false === $dir->deleteDirectory()) {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to delete your directory: ' . '/uploads/store/' . $this->store_id . '/' . $this->product_id . ' <br><br> Please contact us!');
            return false;
        } elseif (false === $dir->directoryExists()) {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM " . $this->getTable() . " WHERE product_id = %d",
                    $this->product_id
                )
            );

            if ($wpdb->result === false) {
                TppStoreMessages::getInstance()->addMessage('error', 'Unable to delete your product: ' . $wpdb->last_error);
            } else {
                $this->clearCache();
            }
            return $wpdb->result;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to delete your directory: ' . '/uploads/store/' . $this->store_id . '/' . $this->product_id . ' <br><br> Please contact us!');
            return false;
        }




    }


}