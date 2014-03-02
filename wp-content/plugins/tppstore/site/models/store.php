<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:18
 */
 
class TppStoreModelStore extends TppStoreModelCurrency {

    public $store_id = null;
    public $store_name = null;
    public $store_slug = null;
    public $enabled = null;
    public $user_id = null;
    public $src = null;
    public $description = null;
    public $paypal_email = null;
    public $country = null;
    public $city = null;
    public $approved = 0;
    //can be -1 = unapproved
    //0 - waiting approval
    //1 approved

    public $currency = 'GBP';

    protected $_user = null;
    protected $_pages = null;

    protected $_table = 'shop_product_stores';

    public function __construct()
    {
        $this->_user = new TppStoreModelUser();
    }

    public function getSeoTitle()
    {
        return $this->store_name;
    }

    public function getTitle()
    {
        return $this->getSeoTitle();
    }

    public function getSafeTitle()
    {
        return esc_textarea($this->store_name);
    }

    public function getSeoDescription()
    {
        return esc_attr($this->description);
    }

    public function getDescription()
    {
        return $this->getSeoDescription();
    }

    /*
     * sets up the store pages model
     */
    public function getPages($auto_load = true)
    {
        if (is_null($this->_pages)) {
            $this->_pages = new TppStoreModelStorePages();
            $this->_pages->setData(array(
                'store_id'  =>  $this->store_id
            ));

            if (true === $auto_load) {
                $this->_pages->getPages();
            }
        }

        return $this->_pages;
    }

    public function readFromPost($application_submission = false)
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pic = filter_input(INPUT_POST, 'uploaded_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            $original_pic = filter_input(INPUT_POST, 'original_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            if (is_array($pic) && !empty($pic[0])) {
                $this->src = $pic[0];
            } elseif (is_array($original_pic) && !empty($original_pic[0])) {
                $this->src = $original_pic[0];
            } else {
                $this->src = null;
            }

            $this->paypal_email = filter_input(INPUT_POST, 'paypal_email', FILTER_SANITIZE_STRING);

            $this->description = filter_input(INPUT_POST, 'store_description', FILTER_SANITIZE_STRING);

            $this->store_name = filter_input(INPUT_POST, 'store_name', FILTER_SANITIZE_STRING);

            $this->store_slug = filter_input(INPUT_POST, 'store_slug', FILTER_SANITIZE_STRING);

            $this->enabled = filter_input(INPUT_POST, 'store_enabled', FILTER_SANITIZE_NUMBER_INT);

            $this->currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_STRING);

            $this->city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);

            $this->country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);

            return $this->validate(true, $application_submission);
        } else {
            return false;
        }


    }

    public function getLocation()
    {
        if (!empty($this->city) && !empty($this->country)) {
            return $this->city . ', ' . $this->country;
        } elseif (!empty($this->city)) {
            return $this->city;
        } else {
            return $this->country;
        }


    }

    public function getPermalink($preview = false, $full_url = false)
    {

        return ($full_url === true?'http://www.' . $_SERVER['HTTP_HOST']:'') . '/shop/store/' . $this->store_slug . ($preview === true?'?preview=true':'');
    }

    public function getOwner()
    {



        if (intval($this->_user->user_id) < 1) {
            $this->_user->setData(array(
                'user_id'   =>  $this->user_id
            ))->getUserByID();
        }

        return ucfirst($this->_user->first_name) . ' ' . ucfirst($this->_user->last_name);
    }

    public function getFacebookId()
    {
        if (intval($this->_user->user_id) < 1) {
            $this->_user->setData(array(
                'user_id'   =>  $this->user_id
            ))->getUserByID();
        }

        return $this->_user->facebook_user_id;
    }

    public function getUser($load = false)
    {
        if (true === $load && is_null($this->_user) || ($this->_user instanceof TppStoreModelUser && intval($this->_user->user_id) < 1)) {
            if (!($this->_user instanceof TppStoreModelUser)) {
                $this->_user = new TppStoreModelUser();
            }
            $this->_user->setData(array(
                'user_id'   =>  $this->user_id
            ))->getUserByID();
        }
        return $this->_user;
    }

    public function getStoreBySlug($slug = '', $enabled = 1)
    {

        $slug = trim($slug);

        if ($slug !== '') {

            $slug = strtolower(htmlspecialchars_decode($slug, ENT_QUOTES));

            switch ($enabled) {
                case '1':
                    $where = " AND enabled = 1 AND approved = '1'";
                    break;

                case '0':
                    $where = " AND enabled = 0 ";
                    break;

                default:
                    $where = " ";
                    break;
            }

            global $wpdb;

            $res = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM " . $this->getTable() . " WHERE store_slug = %s $where", $slug)
            );


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

    public function getStoreByID()
    {
        if (intval($this->store_id) <= 0) {
            $store_id = $this->store_id;
            $this->reset();
            $this->store_id = $store_id;
        } else {
            global $wpdb;
            $rows = $wpdb->get_results(
                $wpdb->prepare("SELECT *, enabled as enabled FROM " . $this->getTable() . " WHERE store_id = %d",
                $this->store_id
                )
            );

            if ($wpdb->num_rows == 1) {
                $this->setData($rows[0]);
            }
        }

        return $this;
    }

    public function getStoreByTitle($title = '')
    {

        if ($title !== '') {

            $title = strtolower(htmlspecialchars_decode($title, ENT_QUOTES));
            global $wpdb;

            $res = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM " . $this->getTable() . " WHERE store_name = %s", $title)
            );


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

    public function getStoreByUserID($enabled = 'all')
    {

        global $wpdb;

        switch ($enabled) {
            case '1':
                $where = " AND enabled = 1 AND approved = '1'";
                break;

            case '0':
                $where = " AND enabled = 0";
                break;

            default:
                $where = "";
                break;
        }

        $res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE user_id = %d $where",
                $this->user_id
            ),
            OBJECT_K
        );



        if ($wpdb->num_rows == 1) {
            foreach ($res as $row) {
                $this->setData($row);
            }
        }

        return $this;

    }

    public function getImageDirectory($create = false)
    {
        if (!class_exists('TppStoreDirectoryLibrary')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/directory.php';
        }

        //determine the save path
        $directory = new TppStoreDirectoryLibrary();

        $directory->setDirectory(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/store_image/');

        if (false === $directory->directoryExists()) {

            if (false === $create) {
                return false;
            } else {
                if (true === $directory->createDirectory()) {
                    return WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/store_image/';
                } else {
                    return false;
                }
            }
        } else {
            return WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/store_image/';
        }
    }

    public function getSrc($html = false, $size = false)
    {
        if (is_null($this->src) || empty($this->src) || intval($this->store_id) <= 0) {
            return false;
        }

        if (false === ($directory = $this->getImageDirectory())) {
            return false;
        }


        if (false !== $size) {
            $size = TppStoreModelProductImages::getSize($size);

            $lib = new TppStoreLibraryFileImage();
            $lib->setFile($directory . $this->src);
            $file = $lib->getBaseName() . '_' . $size['width'] . '_' . $size['height'] . '.' . $lib->getExtension();

            if (!file_exists($file)) {
                if (false === $lib->resize($size)) {
                    return false;
                }
            }

            $this->src = substr($lib->getBaseName() . '_' . $size['width'] . '_' . $size['height'] . '.' . $lib->getExtension(), strlen($this->getImageDirectory()));
        }

        $relative_path = substr($directory, strlen(WP_CONTENT_DIR . '/uploads'));

        if (true === $html) {
            return '<img src="' . $relative_path . $this->src . '" alt="">';

        } else {
            return $relative_path . $this->src;
        }

    }

    public function getStores($enabled = 1)
    {

        global $wpdb;

        switch ($enabled) {
            case 'all':
                $where = "";
                break;

            case '0':
                $where = " WHERE s.enabled = 0 ";
                break;

            default:
                $where = " WHERE s.enabled = 1 AND s.approved = '1' ";
                break;
        }

        $rows = $wpdb->get_results(
            "SELECT * FROM " . $this->getTable() . " AS s
            LEFT JOIN " . $this->_user->getTable() . " AS u ON u.user_id = s.user_id $where",
            OBJECT_K
        );

        $return = array();

        if ($wpdb->num_rows > 0) {
            foreach ($rows as $row) {
                $return[$row->store_id] = new TppStoreModelStore();
                $return[$row->store_id]->setData($row);
                $return[$row->store_id]->_user->setData($row);
            }
        }

        return $return;
    }

    public function getProducts($enabled = 1, $page = 1, $count = false, $limit = 20, $product_not_in = false)
    {

        $res = array();

        if ($this->enabled == 1 && intval($this->store_id) > 0) {
            global $wpdb;


            switch ($enabled) {



                case 'all':
                    $where = "";
                    break;

                default:
                    $where = " AND p.enabled = $enabled AND s.enabled = 1";
                    break;


            }

            if (is_array($product_not_in)) {
                $where .= " AND p.product_id NOT IN('" . implode(',', $product_not_in) . "') ";
            } elseif (is_string($product_not_in) && trim($product_not_in) != '') {
                $where .= " AND p.product_id <> " . $product_not_in;
            }

            if ($count === false) {
                if ($page == 0) {
                    $page = 1;
                }

                $start = (($page-1) * 20);

                $rows = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.size_alias, s.currency, s.store_slug, s.store_name FROM " . TppStoreModelProduct::getInstance()->getTable() . " AS p
                INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1

                INNER JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC)
                     AS i ON i.product_id = p.product_id

                WHERE s.store_id = %s
                $where
                GROUP BY p.product_id
                ORDER BY p.product_type IN (4,5) DESC, p.product_type
                LIMIT $start, $limit
                ",
                        array(
                            $this->store_id
                        )
                    ),
                    OBJECT_K
                );
            } else {
                $c = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(p.product_id) AS c FROM " . TppStoreModelProduct::getInstance()->getTable() . " AS p
                        INNER JOIN " . $this->getTable() . " AS s ON s.store_id = p.store_id
                        WHERE s.store_id = %s
                        $where
                        GROUP BY p.product_id
                        ",
                        $this->store_id
                    )
                );

                return $c;
            }

            //mail('parsolee@gmail.com', 'test store', print_r($wpdb, true));


            if ($wpdb->num_rows > 0) {
                $return = array();
                foreach ($rows as $row) {
                    $return[$row->product_id] = new TppStoreModelProduct();
                    $return[$row->product_id]->setData($row);
                    $return[$row->product_id]->getProductImage()->setData(array(
                        'product_id'    =>  $row->product_id,
                        'store_id'      =>  $row->store_id,
                        'src'           =>  $row->src,
                        'path'          =>  $row->path,
                        'filename'      =>  $row->filename,
                        'extension'     =>  $row->extension
                    ));
                    $return[$row->product_id]->getStore()->setData(array(
                        'store_id'      =>  $row->store_id,
                        'store_name'    =>  $row->store_name,
                        'store_slug'    =>  $row->store_slug
                    ));
                }
                return $return;
            } else {
                return array();
            }





        }

        return $res;
    }

    /*
     * @param $validate_records - validate new data against database entries to see if a clash exists?
     */
    public function save($validate_records = true, $application_submission = false)
    {
        if (!$this->validate($validate_records, $application_submission)) {
            return false;
        }

        global $wpdb;

        if (intval($this->store_id) <= 0) {
            $wpdb->insert(
                $this->getTable(),
                array(
                    'store_name'    =>  $this->store_name,
                    'store_slug'    =>  $this->store_slug,
                    'enabled'       =>  $this->enabled,
                    'user_id'       =>  $this->user_id,
                    'src'           =>  $this->src,
                    'currency'      =>  $this->currency,
                    'description'   =>  $this->description,
                    'paypal_email'  =>  $this->paypal_email,
                    'country'       =>  $this->country,
                    'city'          =>  $this->city,
                    'approved'      =>  $this->approved
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );
        } else {
            $wpdb->update(
                $this->getTable(),
                array(
                    'store_name'    =>  $this->store_name,
                    'store_slug'    =>  $this->store_slug,
                    'enabled'       =>  $this->enabled,
                    'user_id'       =>  $this->user_id,
                    'src'           =>  $this->src,
                    'currency'      =>  $this->currency,
                    'description'   =>  $this->description,
                    'paypal_email'  =>  $this->paypal_email,
                    'country'       =>  $this->country,
                    'city'          =>  $this->city,
                    'approved'      =>  $this->approved
                ),
                array(
                    'store_id'      =>  $this->store_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array(
                    '%d'
                )
            );
        }

        $save_path = false;

        if ($wpdb->rows_affected <= 1 && $wpdb->last_error == '') {

            //not affected if nothing to update. Still is a valid result

            if (intval($this->store_id) == 0) {
                $this->store_id = $wpdb->insert_id;

                //move the temporary file across!

                if (!is_null($this->src) && !empty($this->src) && false !== ($temp_path = TppStoreControllerStore::getInstance()->loadStoreTempPath())) {

                    $base_path = substr($temp_path, 0, strpos($temp_path, 'new_store_'));

                    $save_path = $base_path . $this->store_id . '/store_image/';

                    $directory = new TppStoreDirectoryLibrary();

                    if (false === $directory->createDirectory($save_path)) {
                        TppStoreMessages::getInstance()->addMessage('error', 'Unable to create the store directory: ' . $save_path);
                        return false;
                    }

                    //move the images across!
                    $directory->setDirectory($temp_path);

                    $files = $directory->getFiles(false);

                    if (!empty($files)) {

                        $tmp = TppStoreModelProductImages::getSize('thumb');
                        $images_to_keep = array(
                            $this->src
                        );

                        $image = new TppStoreLibraryFileImage();

                        $image->setFile($this->src);
                        $extension = $image->getExtension();
                        $base_name = $image->getBaseName();

                        if (is_array($tmp) && !isset($tmp['width'])) {
                            foreach ($tmp as $size) {
                                if (is_array($size)) {
                                    $images_to_keep[] = $base_name . '_' . $size['width'] . '_' .  $size['height'] . '.' . $extension;
                                }
                            }
                        } else {
                            $images_to_keep[] = $base_name . '_' . $tmp['width'] . '_' .  $tmp['height'] . '.' . $extension;
                        }


                        foreach ($files as $file_name) {
                            if (!in_array($file_name, $images_to_keep)) {
                                @unlink($temp_path . $file_name);
                            } elseif (!@rename($temp_path . $file_name, $save_path . $file_name)) {
                                    TppStoreMessages::getInstance()->addMessage('error', 'Unable to move the uploaded image: ' . $temp_path . $file_name . ' to ' . $save_path . $file_name);
                                    return false;
                            }
                        }
                    }

                    //remove the old temp directory!
                    $directory->deleteDirectory();

                }

            } else {
                if (intval($this->enabled) == 1) {
                    //update all the products:
                    TppStoreModelProducts::getInstance()->bulkUpdate(
                        array(
                            'enabled'   =>  $this->enabled
                        ),
                        array(
                            'store_id'  =>  $this->store_id
                        )
                    );
                }
            }

            if (!is_null($this->src) && !empty($this->src) && false === $save_path && false !== ($save_path = $this->getImageDirectory(true))) {

                //now we need to delete all files that do not match this file!
                $directory= new TppStoreDirectoryLibrary();

                $directory->setDirectory(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/store_image');

                $files = $directory->getFiles(false);

                $images_to_keep = array();

                if (!is_null($this->src)) {
                    $sizes = TppStoreModelProductImages::getSize('thumb');
                    $images_to_keep = array(
                        $this->src
                    );

                    $image = new TppStoreLibraryFileImage();

                    $image->setFile($this->src);
                    $extension = $image->getExtension();
                    $base_name = $image->getBaseName();


                    $tmp_sizes = $sizes;

                    $tmp = array_shift($tmp_sizes);

                    if (is_array($tmp)) {
                        foreach ($sizes as $size) {
                            $images_to_keep[] = $base_name . '_' . $size['width'] . '_' .  $size['height'] . '.' . $extension;
                        }
                    } else {
                        $images_to_keep[] = $base_name . '_' . $sizes['width'] . '_' .  $sizes['height'] . '.' . $extension;
                    }
                }

                if (count($files) > 0) {
                    foreach ($files as $file) {
                        if (!in_array($file, $images_to_keep)) {
                            @unlink($save_path . $file);
                        }
                    }
                }
            }

            return true;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', array('store_save' =>  $wpdb->last_error));
            return false;
        }


    }

    /*
     * If the application form has been submitted then we need to run a low level validation as not all fields will be filled out.
     */
    public function validate($validate_records = true, $application_submission = false)
    {
        global $wpdb;

        $errors = false;

        if (is_null($this->enabled) || $this->enabled == '') {
            $this->enabled = 0;
        }

        if (is_null($this->approved) || $this->approved == '') {
            $this->approved = 0;
        }

        if ($this->approved < 1) {
            $this->enabled = 0;
        }



        $this->store_name = htmlspecialchars_decode($this->store_name, ENT_QUOTES);
        $this->store_slug = strtolower(htmlspecialchars_decode($this->store_slug, ENT_QUOTES));

        if (true === $application_submission && is_null($this->store_name)) {
            $this->store_name = 'New Store';
        }

        if (intval($this->user_id) < 1) {
            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                $errors = true;
                if (false === $application_submission) {
                    TppStoreMessages::getInstance()->addMessage('error', array('user_account' =>  'Could not validate your user account.'));
                }

            } else {
                $this->user_id = $user->user_id;
            }
        }



        if (true === $application_submission) {

            if (is_null($this->store_slug) || trim($this->store_slug) == '') {
//                $errors = true;
//                TppStoreMessages::getInstance()->addMessage('error', array('store_slug' =>  'Please enter a store url'));
                //create a store slug for them!
                $this->store_slug = sanitize_title_with_dashes($this->store_name);


//            if (true === $validate_records && !is_null($this->store_slug) && $this->store_slug !== '') {
                $c = $wpdb->get_var(
                    "SELECT COUNT(store_id) AS c FROM " . $this->getTable() . " WHERE user_id <> " . intval($this->user_id) . " AND store_slug LIKE '%" . $wpdb->escape($this->store_slug) . "%'"
                );

                if ($c > 0) {
                    $this->store_slug .= '-' . $c;
                }
  //          }

            }

        } else {
            if (!filter_var($this->paypal_email, FILTER_VALIDATE_EMAIL)) {
                $errors = true;
                TppStoreMessages::getInstance()->addMessage('error', array('paypal_email' =>  'Could not validate your paypal email address. Please make sure it is formatted correctly. Your store will not be able to take payments without a valid linked paypal account.'));
                //force disable of this store!
                $this->enabled = 0;
            }
        }



        if (is_null($this->store_name) || trim($this->store_name) == '') {
            //determine if this store name already exists?
            $errors = true;
            if (true === $application_submission) {
                TppStoreMessages::getInstance()->addMessage('error', array('store_name' =>  'Please enter your business name'));
            } else {
                TppStoreMessages::getInstance()->addMessage('error', array('store_name' =>  'Please enter a store name'));
            }

        } elseif (true === $validate_records) {
            $wpdb->query(
                $wpdb->prepare("SELECT store_id FROM " . $this->getTable() . " WHERE user_id <> %d AND store_name = %s",
                    array(
                        $this->user_id,
                        $this->store_name
                    )
                )
            );

            if ($wpdb->num_rows > 0) {
                $errors = true;
                if (false === $application_submission) {
                    TppStoreMessages::getInstance()->addMessage('error', array('store_name' =>  'A store with this name already exists. Please enter a new name'));
                } else {
                    TppStoreMessages::getInstance()->addMessage('error', array('store_name' =>  'A store with your business name already exists. If you believe this is an error, please contact us.'));
                }

            }
        }

        switch ($this->currency) {
            case 'GBP':
            case 'USD':
                break;
            default:
                $this->currency = 'GBP';
                break;
        }

        if (!is_null($this->city) && !empty($this->city)) {
            $this->city = trim($this->city);
        } else {
            $this->city = null;
        }

        if (!is_null($this->country) && !empty($this->country)) {
            $this->country = trim($this->country);
        } else {
            $this->country = null;
        }

        return false === $errors;
    }

}