<?php
/**
 * User: leeparsons
 * Date: 02/12/2013
 * Time: 14:40
 */
 
class TppStoreModelCategory extends TppStoreAbstractModelBase {

    public $category_id = null;
    public $category_name = null;
    public $category_slug = null;
    public $description = null;
    public $enabled = 0;
    public $featured = 0;
    public $image = null;

    public $children = array();

    //related closure table properties
    public $level = 0;
    public $parent_id = 0;
    public $ordering = 0;
    public $product_count = 0;


    protected $new_image = array();
    protected $_table = 'shop_product_categories';
    protected $_p2c_model = null;

    public function __construct()
    {
        $this->_p2c_model = new TppStoreModelP2c();
    }

    public function getSeoTitle()
    {
        return $this->category_name;
    }

    public function getSeoDescription()
    {
        if (trim($this->description) == '') {
            return $this->getSeoTitle();
        }
        return esc_attr($this->description);
    }

    protected function getClosureTable()
    {
        return 'shop_product_category_closures';
    }


    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $this->enabled = filter_input(INPUT_POST, 'enabled', FILTER_SANITIZE_NUMBER_INT);

        $new_image = $_FILES['category_image'];

        if ($new_image['error'] == 0) {
            $this->new_image = $new_image;
        } elseif ($new_image['name'] != '') {
            TppStoreMessages::getInstance()->addMessage('category', array('error'   =>  'Unable to retrieve your new image. It may be corrupted'));
        }

        $this->description = filter_input(INPUT_POST, 'category_description', FILTER_SANITIZE_STRING);

        $this->category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

        $this->category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);

        $this->featured = filter_input(INPUT_POST, 'featured', FILTER_SANITIZE_NUMBER_INT);

        return true;

    }

    public function getCategoryById($id = 0)
    {

        if (intval($id) < 1) {
            $this->reset();

        } else {

            global $wpdb;

            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE category_id = %d",
                    intval($id)
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows == 1) {

                $this->setData($wpdb->last_result[0]);

            } else {
                $this->reset();

            }
        }


        return $this;

    }

    public function getCategoryBySlug($slug = '', $level = 1, $parent_slug = '')
    {
        $slug = trim($slug);

        if ($slug == '') {
            return $this;
        }

        global $wpdb;

        if (trim($parent_slug) != '' && $level > 1) {
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT c.* FROM " . $this->getTable() . " AS c
                LEFT JOIN " . $this->getClosureTable() . " AS p2c ON p2c.child_id = c.category_id
                LEFT JOIN " . $this->getTable() . " AS c2 ON c2.category_id = p2c.parent_id
                WHERE c.category_slug = %s AND level = %d
                AND c2.category_slug = %s
                ",
                    array(
                        $slug,
                        $level,
                        $parent_slug
                    )
                ),
                OBJECT_K
            );

        } else {
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT c.* FROM " . $this->getTable() . " AS c
                LEFT JOIN " . $this->getClosureTable() . " AS p2c ON p2c.child_id = c.category_id
                WHERE category_slug = %s AND level = %d
                ",
                    array(
                        $slug,
                        $level
                    )
                ),
                OBJECT_K
            );
        }


        if ($wpdb->num_rows == 1) {

            $this->setData($wpdb->last_result[0]);

        } else {
            $this->reset();

        }

        return $this;

    }

    public function getChildren($product_count = true, $heirarchical = true)
    {
        if (intval($this->category_id) <= 0) {
            return $this;
        }
        $cats = TppStoreModelCategories::getInstance();
        $cats->getCategories(array(
            'parent'        =>  $this->category_id,
            'product_count' =>  $product_count,
            'heirarchical'  =>  $heirarchical
        ));
        $this->children = $cats->categories;
    }

    /*
     * Gets the parents right up to the top level tree
     */
    public function getParents($category_id = 0)
    {
        global $wpdb;

        $wpdb->query(
            "SELECT c2.category_slug AS parent_slug, c2.category_name AS parent_name, c3.category_slug AS grand_parent_slug, c3.category_name AS grand_parent_name
FROM shop_product_categories AS c

LEFT JOIN shop_product_category_closures AS cc ON cc.child_id = c.category_id

LEFT JOIN shop_product_categories AS c2 ON c2.category_id = cc.parent_id

LEFT JOIN shop_product_category_closures AS cc2 ON cc2.child_id = c2.category_id

LEFT JOIN shop_product_categories AS c3 ON c3.category_id = cc2.parent_id

/*LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1*/

WHERE c.category_id = $category_id

",
        OBJECT_K);


        if ($wpdb->num_rows > 0) {
            return $wpdb->last_result[0];
        } else {
            return array();
        }


    }

    /*
     * @param $params = array() - should contain either: start, end for limit, count = boolean
     */
    public function getProducts($with_main_image = true, $params = array())
    {
        return $this->_p2c_model->
            setData(array('category_id'  =>  $this->category_id))->
            getProducts(
                $with_main_image,
                $params
        );
    }

    public function getPermalink()
    {
        if (!$this->validate()) {
            return '#';
        } else {


            switch ($this->level) {

                case '2':

                    //get the category by parent_id!

                    global $wpdb;

                    $category_slug = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT category_slug FROM " . $this->getTable() . " WHERE category_id = %d ",
                            $this->parent_id
                        )
                    );

                    if ($wpdb->num_rows == 1) {
                        return get_bloginfo('url') . '/shop/category/' . $category_slug . '/' . $this->category_slug;
                    } else {
                        return get_bloginfo('url') . '/shop/category/' . $this->category_slug;
                    }

                    break;

                case '3':
                    global $wpdb;


                    $category_slug = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT CONCAT(c3.category_slug, '/', c2.category_slug, '/', c.category_slug) AS category_slug FROM " .
                            $this->getTable() . " AS c
                            LEFT JOIN shop_product_category_closures AS cc ON cc.child_id = c.category_id
                            LEFT JOIN shop_product_categories AS c2 ON c2.category_id = cc.parent_id
                            LEFT JOIN shop_product_category_closures AS cc2 ON cc2.child_id = c2.category_id
                            LEFT JOIN shop_product_categories AS c3 ON c3.category_id = cc2.parent_id
                            WHERE c.category_id = %d",
                            $this->category_id
                        )
                    );

                    if ($wpdb->num_rows == 1) {
                        return get_bloginfo('url') . '/shop/category/' . $category_slug;
                    } else {
                        return get_bloginfo('url') . '/shop/category/' . $this->category_slug;
                    }

                    break;
                default:
                    return get_bloginfo('url') . '/shop/category/' . $this->category_slug;
                    break;
            }


        }


    }

    public function getImageSrc($size = array(250, 250), $crop = true)
    {
        if (!$this->validate() || $this->image == null) {
            //some sort of place holder!
            return '';
        }

        $dir = wp_upload_dir();

        if (!file_exists($dir['basedir'] . '/store/categories/' . $this->category_id . '/' . $this->image)) {
            return '';
        }

        return $dir['baseurl'] . '/store/categories/' . $this->category_id . '/' . $this->image;

    }

    public function save()
    {

        if (!$this->validate()) {
            return false;
        }

        global $wpdb;


        if (intval($this->category_id) > 0) {

            $wpdb->update(
                $this->getTable(),
                array(
                    'description'   =>  $this->description,
                    'category_name' =>  $this->category_name,
                    'category_slug' =>  $this->category_slug,
                    'enabled'       =>  $this->enabled,
                    'featured'      =>  $this->featured
                ),
                array(
                    'category_id'   =>  $this->category_id
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d'
                ),
                array(
                    '%d'
                )
            );

        } else {
            $wpdb->insert(
                $this->getTable(),
                array(
                    'description'   =>  $this->description,
                    'category_name' =>  $this->category_name,
                    'category_slug' =>  $this->category_slug,
                    'enabled'       =>  $this->enabled,
                    'featured'      =>  $this->featured
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d'
                )
            );

        }


        if ($wpdb->result === false) {
            TppStoreMessages::getInstance()->addMessage('error', array('error'  =>  'An error occurred saving your category to the database: ' . $wpdb->last_error));
            return false;
        }

        if (intval($this->category_id) < 1) {
            $this->category_id = $wpdb->insert_id;
        }


        //see if we need to upload an image?

        if (!empty($this->new_image)) {

            $directory = new TppStoreDirectoryLibrary();
            $save_path = WP_CONTENT_DIR . '/uploads/store/categories/' . $this->category_id . '/';

            $directory->setDirectory($save_path);
            if (false === $directory->createDirectory()) {
                TppStoreMessages::getInstance()->addMessage('error', array('error'  =>  'An error occurred: could not create the category directory at: ' . $save_path));
                return false;
            }

            $lib = new TppStoreLibraryFileImage();
            $lib->setUploadedFile($this->new_image);

            if (false === $lib->validateUploadedFile($lib::$image_mime_type)) {
                TppStoreMessages::getInstance()->addMessage('error', array('error'  =>  'An error occurred: could not move the uploaded new image for your category at: ' . $save_path));
                return false;
            } elseif (false === $lib->moveUploadedFile($save_path, $lib->getUploadedName())) {
                TppStoreMessages::getInstance()->addMessage('error', array('error'  =>  'An error occurred: could not move the uploaded new image for your category at: ' . $save_path));
                return false;
            }

            $current_image = $this->image;
            $this->image = $lib->getUploadedName();

            $wpdb->update(
                $this->getTable(),
                array(
                    'image'      =>  $this->image
                ),
                array(
                    'category_id'   =>  $this->category_id
                ),
                array(
                    '%s'
                ),
                array(
                    '%d'
                )
            );

            $files = $directory->getFiles();

            if ($wpdb->result === false) {

                $this->image = $current_image;
                //delete the uploaded file!

                if (count($files) > 0) {
                    foreach ($files as $file) {
                        if ($file != $save_path . $this->image) {
                            @unlink($file);
                        }
                    }
                }


                TppStoreMessages::getInstance()->addMessage('error', array('error'  =>  'An error occurred saving your category to the database: ' . $wpdb->last_error));
                return false;
            } else {
                if (count($files) > 0) {
                    foreach ($files as $file) {
                        if ($file != $save_path . $this->image) {
                            @unlink($file);
                        }
                    }
                }
            }




        }

        return true;

    }

    public function validate()
    {

        if (intval($this->category_id) <= 0) {
            return false;
        }

        if (trim($this->category_name) == '' || is_null($this->category_name)) {
            return false;
        }


        if (trim($this->category_slug) == '' || is_null($this->category_slug)) {
            return false;
        }

        return true;
    }

}