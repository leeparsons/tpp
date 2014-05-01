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

    private $type = 1;
    private $original_download = null;

    public $download_override = null;

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

//        if ('' != ($download = filter_input(INPUT_POST, 'download_elsewhere', FILTER_SANITIZE_STRING))) {
//            $this->download_override = $download;
////        } elseif (is_array($this->upload_file) && isset($this->upload_file['name'])) {
////            $ext = substr($this->upload_file['name'], strrpos($this->upload_file['name'], '.') + 1);
////            $name = substr($this->upload_file['name'], 0, strrpos($this->upload_file['name'], '.'));
////            $this->upload_file['name'] = sanitize_title_with_dashes($name) . '.' . $ext;
////        }
//        }

        return $this;
    }

    public function getDownloadUrlAdmin($encrypted = true)
    {
        if ( false !== ($url = filter_var($this->file, FILTER_VALIDATE_URL))) {

            return $url;

        }


        if (!file_exists($this->upload_path . '/' . $this->product_id . '/' . $this->file)) {

            return 'File does not exist!';

        } else {
            if (false === $encrypted) {
                $path =  substr($this->upload_path, strlen(WP_CONTENT_DIR . '/uploads')) . $this->product_id . '/' . $this->file;
                return $path;
            } else {

                if (!class_exists('TppStoreLibraryEncryption')) {
                    include TPP_STORE_PLUGIN_DIR . 'libraries/encryption.php';
                }


                return get_site_url(null, '/shop/download/' . $this->product_id . '/' . TppStoreLibraryEncryption::encrypt('admin_logged_in=true&file=' . $this->file));
            }
        }
    }

    public function getDownloadUrl($encrypted = true, $product_edit = false)
    {

        if ( false !== ($url = filter_var($this->file, FILTER_VALIDATE_URL))) {

            return $url;

        }

        if (!file_exists($this->upload_path . '/' . $this->product_id . '/' . $this->file)) {
            if ($product_edit === false) {
                return "javascript:alert('The store owner has not uploaded a file. They will email you directly with your download. If you don\'t hear from them within 24 hours, you can contact them using the button below, or via their store page.')";
            } else {
                return $this->file;
            }
        }

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

        if (trim($this->download_override) != '') {
            return true;
        }

        if (
        (
            is_null($this->upload_file) ||
            //!is_array($this->upload_file) ||
            !file_exists($this->upload_file)
        ) && trim($this->original_download) == ''
            // !isset($this->upload_file['tmp_name']) ||
           // !file_exists($this->upload_file['tmp_name'])
        ) {

            if ($raise_error === true) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'We could not detect the file you uploaded. Please note we will need to manually upload files above 15MB for you so please get in touch!'));

                TppStoreLibraryLogger::getInstance()->add(null, 'The uploaded file could not be detected.', 'product download file upload', array(
                    'product_id'    =>  $this->product_id,
                    'file'          =>  $this->file,
                    'upload_path'   =>  $this->upload_path,
                    'upload_file'   =>  $this->upload_file,
                    'object:'       =>  'TppStoreModelProductDownload'
                ),
                    'error');
            }
            return false;
        }


        $error = false;

//        switch (substr($this->upload_file['name'], strrpos($this->upload_file['name'], '.') + 1)) {
//            case 'php':
//            case 'js':
//            case 'aspx':
//            case 'asp':
//            case 'vb':
//            case 'vbs':
//                if ($raise_error === true) {
//                    TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'The file type you tried uploading is not allowed.'));
//                }
//                $error = true;
//                break;
//            default:
//
//                if ($this->upload_file['error'] > 0) {
//                    if ($raise_error === true) {
//                        TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'There was an error detected with your file. Please try uploading it again.'));
//                    }
//                    $error = true;
//                } elseif (!is_null($this->product_id) && intval($this->product_id) > 0) {
//                    if (!file_exists($this->upload_path . $this->product_id)) {
//                        if (!@mkdir($this->upload_path . $this->product_id)) {
//                            $error = true;
//                            if ($raise_error === true) {
//                                TppStoreMessages::getInstance()->addMessage('error', array('product_download' =>  'We could not upload your file. Please try again. (Error code: p0)'));
//                            }
//                        }
//                    }
//
//                }
//                break;
//        }

        return !$error;
    }

    public function downloadExists()
    {
        if (trim($this->file) != '') {
            return file_exists($this->upload_path . $this->product_id . '/' . $this->file);
        }

        return false;
    }


    public function getPlainLink()
    {
        if (trim($this->download_override) == '') {
            if (trim($this->file) == '') {
                return $this->original_download;
            }
            return $this->file;
        } else {
            return trim($this->download_override);
        }


    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->type = filter_input(INPUT_POST, 'download_location', FILTER_SANITIZE_NUMBER_INT);

            switch ($this->type)
            {
                case '2':
                    //hosted with us
                    $file = TppStoreControllerProduct::getInstance()->getProductUploadFileFromSession();

                    if (trim($file) != '') {
                        $path = TppStoreControllerProduct::getInstance()->getProductUploadDirectoryFromSession() . $file;

                        $this->upload_file = $path;

                        $this->file = $file;
                    }


                    $this->download_override = '';

                    $this->original_download = filter_input(INPUT_POST, 'original_download', FILTER_SANITIZE_STRING);

                    break;

                default:
                    //hosted else where

                    $this->download_override = filter_input(INPUT_POST, 'download_elsewhere', FILTER_SANITIZE_STRING);

                    break;

            }
            return true;
        } else {
            return false;
        }
    }

    public function save($raise_error = true)
    {
        if (!$this->validate($raise_error)) {
            return false;
        }

        //move the uploaded file!

        //$this->upload_file['name'] = sanitize_title_with_dashes($this->upload_file['name']);

        if (trim($this->download_override) == '' && $this->type == 2) {

            if (trim($this->upload_file) == '' && trim($this->original_download) != '') {
                return true;
            }

            if (!file_exists($this->upload_file)) {


                if ($raise_error === true) {
                    TppStoreMessages::getInstance()->addMessage('error', array('product_download'   =>  'Could not find your uploaded file.'));
                    return false;
                }
            } else {
                $dir = new TppStoreDirectoryLibrary();
                $dir->createDirectory($this->upload_path . $this->product_id);


                //TODO: figure out why this is not moving the uploaded file.

                $moved = @rename($this->upload_file, $this->upload_path . $this->product_id . '/' . $this->file);
                TppStoreControllerProduct::getInstance()->deleteProductUploadDirectoryFromSession();

                return $moved !== false;
            }


//            $files = scandir($this->upload_path . $this->product_id);
//
//            foreach ($files as $file) {
//                if ($file == '.' || $file == '..' || $file == $this->upload_file['name']) {
//                    continue;
//                }
//                @unlink($this->upload_path . $this->product_id . '/' . $file);
//            }


//        } else if (!@move_uploaded_file($this->upload_file['tmp_name'], $this->upload_path . $this->product_id . '/' . $this->upload_file['name'])) {
//            TppStoreMessages::getInstance()->addMessage('error', array('product_download'   =>  'Could not move your uploaded file.'));
//            return false;
//        } else {
//
//            $files = scandir($this->upload_path . $this->product_id);
//
//            foreach ($files as $file) {
//                if ($file == '.' || $file == '..' || $file == $this->upload_file['name']) {
//                    continue;
//                }
//                @unlink($this->upload_path . $this->product_id . '/' . $file);
//            }

        } elseif ($this->type == 1 && trim($this->download_override) == '') {
            if ($raise_error === true) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_download'   =>  'No url set for the external file. Please set one on your download options.'));
                return false;
            }
        }

        return true;
    }

    public function validate($validate_upload = true, $raise_error = true)
    {
        if (true === $validate_upload) {
            if ($this->type == 2) {
                return $this->canUpload($raise_error);
            } else {
                return true;
            }
        } else {
            if (is_null($this->product_id) || intval($this->product_id) <= 0) {
                return false;
            }
        }
        return true;
    }

}