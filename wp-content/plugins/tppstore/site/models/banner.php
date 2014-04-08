<?php
/**
 * User: leeparsons
 * Date: 04/04/2014
 * Time: 07:52
 */
 
class TppStoreModelBanner extends TppStoreAbstractModelBase {

    public $banner_id = 0;
    public $link = '';
    public $src = '';
    public $ordering = 1;
    public $title = '';
    public $enabled = 0;

    private $overwrite_banner = 0;
    private $file = '';

    private $banner_directory = '';
    private $banner_uri = '';
    protected $_table = 'shop_banners';

    public function __construct()
    {
        $this->banner_directory = WP_CONTENT_DIR . '/uploads/tpp-store-banners/';

        $upload_dir = wp_upload_dir();

        $this->banner_uri = $upload_dir['baseurl'] . '/tpp-store-banners/';
    }

    public function getSeoDescription()
    {
        return '';
    }

    public function getPermalink()
    {
        if (trim($this->link) == '') {
            return 'javascript:return false;';
        } else {
            return $this->link;
        }
    }

    public function getSeoTitle()
    {
        return '';
    }

    public function getSrc($full_path = false)
    {
        if (trim($this->src) != '') {

            if (file_exists($this->banner_directory . $this->src)) {
                if (true === $full_path) {
                    return get_site_url(null, $this->banner_uri . $this->src);
                } else {
                    return $this->banner_uri . $this->src;
                }
            }

        } else {
            return false;
        }
    }

    public function getBanner()
    {
        if (intval($this->banner_id) > 0) {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE banner_id = %d",
                    $this->banner_id
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows == 1) {
                $this->setData($wpdb->last_result[0]);
            }

        } else {
            $this->reset();
        }
    }

    public function readFromPost()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->banner_id = filter_input(INPUT_POST, 'banner_id', FILTER_SANITIZE_NUMBER_INT);

            $this->enabled = filter_input(INPUT_POST, 'banner_enabled', FILTER_SANITIZE_NUMBER_INT);

            $this->link = filter_input(INPUT_POST, 'banner_link', FILTER_SANITIZE_STRING);

            $this->title = filter_input(INPUT_POST, 'banner_title', FILTER_SANITIZE_STRING);

            $this->file = $_FILES['banner_image'];

            $this->overwrite_banner = filter_input(INPUT_POST, 'replace_banner', FILTER_SANITIZE_NUMBER_INT);

            $this->src = filter_input(INPUT_POST, 'banner_src', FILTER_SANITIZE_STRING);

        } else {
            return false;
        }

    }

    public function save()
    {

        if (false === $this->validate()) {
            return false;
        }

        global $wpdb;

        if (intval($this->banner_id) > 0) {
            $wpdb->update(
                $this->getTable(),
                array(
                    'title'     =>  $this->title,
                    'enabled'   =>  $this->enabled,
                    'ordering'  =>  $this->ordering,
                    'link'      =>  $this->link,
                    'src'       =>  $this->src
                ),
                array(
                    'banner_id' =>  $this->banner_id
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s'
                ),
                array(
                    '%d'
                )
            );


        } else {
            $wpdb->insert(
                $this->getTable(),
                array(
                    'title'     =>  $this->title,
                    'enabled'   =>  $this->enabled,
                    'ordering'  =>  $this->ordering,
                    'link'      =>  $this->link,
                    'src'       =>  $this->src
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s'
                )
            );
        }


        if ($wpdb->result === true) {

            $this->clearCache();

            if (intval($this->banner_id) == 0) {
                $this->banner_id = $wpdb->insert_id;
            }

            return true;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your banner: ' . $wpdb->last_error);
            return false;
        }

    }

    private function clearCache()
    {
        $cache = new TppCacher();
        $cache->setCachePath('homepage/banners');
        $cache->setCacheName('banners');
        $cache->deleteCache();
    }

    public function validate()
    {

        $error = false;
        if (intval($this->ordering) <= 0) {
            global $wpdb;

            $c = $wpdb->get_var(
                "SELECT COUNT(banner_id) AS c  FROM " . $this->getTable() . " WHERE enabled = 1 "
            );

            $this->ordering = intval($c) + 1;

        }

        if (trim($this->title) == '') {
            if (is_array($this->file)) {
                $this->title = $this->file->name;
            } else {
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', 'Please set a title for your reference');
            }
        }


        if (trim($this->src) == '' || intval($this->overwrite_banner) == 1) {
            //attempted to upload a new image but failed
            if (!is_array($this->file) || $this->file['error'] > 0) {
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', 'Please upload an image');
            } else {
                //upload the banner

                $directory = new TppStoreDirectoryLibrary();
                $directory->setDirectory($this->banner_directory);
                if (false === $directory->createDirectory()) {
                    $error = true;
                    TppStoreMessages::getInstance()->addMessage('error', 'Unable to create banners directory');
                } else {
                    $file = new TppStoreLibraryFileImage();
                    $file->setUploadedFile($this->file);

                    if (false === $file->moveUploadedFile($this->banner_directory)) {
                        $error = true;
                        TppStoreMessages::getInstance()->addMessage('error', 'Unable to upload the banner image');
                    } else {
                        $this->src = $file->getUploadedName();
                    }

                }



            }
        }

        return $error === false;

    }

}