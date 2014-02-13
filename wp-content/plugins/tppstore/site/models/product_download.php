<?php
/**
 * User: leeparsons
 * Date: 03/12/2013
 * Time: 16:36
 */
 
class TppStoreModelProductDownload extends TppStoreAbstractModelResource {

    public $file = null;
    public $upload_file = null;
    public $product_id = null;

    protected $upload_path = null;

    public function __construct()
    {
        $this->upload_path = WP_CONTENT_DIR . '/uploads/tpp_products/downloads/';

        if (!file_exists($this->upload_path)) {
            if (!@mkdir($this->upload_path, 0777, true)) {
                throw new Exception('Could not create the product document upload path');
            }
        }
    }

    public function setData($data = array())
    {
        parent::setData($data);

        if (is_array($this->upload_file) && isset($this->upload_file['name'])) {
            $ext = substr($this->upload_file['name'], strrpos($this->upload_file['name'], '.') + 1);
            $name = substr($this->upload_file['name'], 0, strrpos($this->upload_file['name'], '.'));
            $this->upload_file['name'] = sanitize_title_with_dashes($name) . '.' . $ext;
        }

        return $this;
    }

    public function getDownloadUrl($encrypted = true)
    {


        if (false === $encrypted) {
            $path =  substr($this->upload_path, strlen(WP_CONTENT_DIR . '/uploads')) . $this->product_id . '/' . $this->file;
            return $path;
        } else {

            if (!class_exists('TppStoreLibraryEncryption')) {
                include TPP_STORE_PLUGIN_DIR . 'libraries/encryption.php';
            }

            return '/shop/download/' . $this->product_id . '/' . TppStoreLibraryEncryption::encrypt($this->file);
        }

    }

    public function canUpload($raise_error = true)
    {


        if (
            is_null($this->upload_file) ||
            !is_array($this->upload_file) ||
            !isset($this->upload_file['tmp_name']) ||
            !file_exists($this->upload_file['tmp_name'])
        ) {
            if ($raise_error === true) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'We could not detect the file you uploaded. Please note we will need to manually upload files above 10MB for you so please get in touch!'));

                TppStoreLibraryLogger::getInstance()->add(null, 'The uploaded file could not be detected.', 'product download file upload', array(
                    'product_id'    =>  $this->product_id
                ));


            }
            return false;
        }


        $error = false;

        switch (substr($this->upload_file['name'], strrpos($this->upload_file['name'], '.') + 1)) {
            case 'php':
            case 'js':
            case 'aspx':
            case 'asp':
            case 'vb':
            case 'vbs':
                if ($raise_error === true) {
                    TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'The file type you tried uploading is not allowed.'));
                }
                $error = true;
                break;
            default:

                if ($this->upload_file['error'] > 0) {
                    if ($raise_error === true) {
                        TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'There was an error detected with your file. Please try uploading it again.'));
                    }
                    $error = true;
                } elseif (!is_null($this->product_id) && intval($this->product_id) > 0) {
                    if (!file_exists($this->upload_path . $this->product_id)) {
                        if (!@mkdir($this->upload_path . $this->product_id)) {
                            $error = true;
                            if ($raise_error === true) {
                                TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'We could not upload your file. Please try again. (Error code: p0)'));
                            }
                        }
                    }

                }
                break;
        }

        return !$error;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        //move the uploaded file!

        //$this->upload_file['name'] = sanitize_title_with_dashes($this->upload_file['name']);

        if (!@move_uploaded_file($this->upload_file['tmp_name'], $this->upload_path . $this->product_id . '/' . $this->upload_file['name'])) {
            TppStoreMessages::getInstance()->addMessage('error', array('product_download'   =>  'Could not move your uploaded file.'));
            return false;
        } else {

            $files = scandir($this->upload_path . $this->product_id);

            foreach ($files as $file) {
                if ($file == '.' || $file == '..' || $file == $this->upload_file['name']) {
                    continue;
                }
                @unlink($this->upload_path . $this->product_id . '/' . $file);
            }

        }

        return true;
    }

    public function validate($validate_upload = true)
    {
        if (true === $validate_upload) {
            return $this->canUpload();
        } else {
            if (is_null($this->product_id) || intval($this->product_id) <= 0) {
                return false;
            }
        }
        return true;
    }

}