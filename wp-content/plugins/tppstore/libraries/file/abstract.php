<?php
/**
 * User: leeparsons
 * Date: 10/12/2013
 * Time: 20:21
 */
 
Abstract class TppStoreLibraryAbstractFile {

    protected $error = '';

    protected $_file = null;

    protected $_uploaded_file = null;

    public static $image_mime_type = array(
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif'
    );

    public function getAllowedMimeTypes()
    {
        return TppStoreLibraryAbstractFile::$image_mime_type;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setFile($file = null)
    {
        $this->_file = $file;
    }



    public function getUploadedName()
    {
        return $this->_uploaded_file['name'];
    }

    public function setUploadedFile(Array $file = array())
    {
        $this->_uploaded_file = $file;
    }

    public function getBaseName()
    {
        $file = is_null($this->_file)?$this->getUploadedName():$this->_file;

        if ($file == '') {
            return false;
        }

        if (false === ($pos = strrpos($file, '.'))) {
            return false;
        }

        return substr($file, 0, $pos);
    }

    public function getExtension()
    {

        $file = is_null($this->_file)?$this->getUploadedName():$this->_file;

        if ($file == '') {
            return false;
        }

        if (false === ($pos = strpos($file, '.'))) {
            return false;
        }

        return substr($file, $pos + 1);
    }

    public function moveUploadedFile($destination = '', $name = '')
    {
        if (strpos($destination, WP_CONTENT_DIR) === false) {
            $this->error = 'Unable to move the uploaded file';
            return false;
        }

        $args = func_get_args();

        if (count($args) == 2 && $name == '') {
            $this->error = 'Unable to rename the uploaded file';
            return false;
        } elseif (count($args) == 1) {

            if (substr($destination, -1) !== '/') {
                $destination .= '/';
            }


            $extension = substr($this->_uploaded_file['name'], strrpos($this->_uploaded_file['name'], '.'));
            $name = substr($this->_uploaded_file['name'], 0, strrpos($this->_uploaded_file['name'], '.'));
            $name = wp_unique_filename( $destination, sanitize_title_with_dashes($name) . $extension);
            $this->_uploaded_file['name'] = $name;
        }




        //move the uploaded file!
        try {

            if (substr($destination, -1) !== '/') {
                $destination .= '/';
            }

            if (!@move_uploaded_file($this->_uploaded_file['tmp_name'], $destination . $name)) {
                $this->error = 'Unable to move the uploaded file';
                return false;
            } else {
                @chmod($destination . $name, 0777);
            }
        } catch (Exception $e) {
            throw new Exception($e);
        }

        return true;

    }

    public function streamFile($file_name = 'download')
    {



        if (file_exists($this->_file)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file_name);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($this->_file));
            ob_clean();
            flush();
            readfile($this->_file);
            exit;
        } else {
            return false;
        }
    }


    protected function codeToMessage($code = 0)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    protected function sanitizeFileName($name = '')
    {
        return sanitize_title_with_dashes($name);
    }

    Abstract public function validateUploadedFile();


}