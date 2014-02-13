<?php
/**
 * User: leeparsons
 * Date: 10/12/2013
 * Time: 20:22
 */

class TppStoreLibraryFileImage extends TppStoreLibraryAbstractFile {


    public function getDimensions()
    {

        if (!file_exists($this->_file)) {
            return false;
        }

        $dimensions = array();

        list($dimensions['width'], $dimensions['height']) = getimagesize($this->_file);


        if (empty($dimensions)) {
            return false;
        }

        return $dimensions;
    }

    public function resize($sizes = array())
    {

        if (!file_exists($this->_file)) {
            return false;
        }

        $extension = substr($this->_file, strrpos($this->_file, '.') + 1);

        $base_name =  substr($this->_file, 0, strrpos($this->_file, '.'));


        if (!empty($sizes)) {
            if (is_array($sizes)) {

                switch ($extension) {
                    case 'jpeg':
                    case 'jpg':
                    case 'png':
                        $image = getimagesize($this->_file);

                        break;
                    default:
                        return false;
                        break;
                }

                $image = wp_get_image_editor($this->_file);
                if ($image instanceof WP_Error) {
                 return false;
                } else {

                    $tmp = $sizes;

                    if (!is_array(array_shift($tmp))) {
                        $tmp = $sizes;
                        unset($sizes);
                        $sizes[] = $tmp;
                    }

                    unset($tmp);

                    foreach ($sizes as $name => $size) {
                        $image->resize($size['width'], $size['height'], (isset($size['crop'])?$size['crop']:false));
                        $image->save($this->path . $base_name . '_' . $size['width'] . '_' . $size['height'] . '.' . $extension);
                    }
                }
            }
        }

    }

    public function validateUploadedFile($allowed_mime_type = null)
    {
        if (empty($this->_uploaded_file)) {
            $this->error = 'Could not detected uploaded file';
            return false;
        }

        if ($this->_uploaded_file['error'] != 0) {
            $this->error = $this->codeToMessage($this->_uploaded_file['error']);
            return false;
        }

        if (!is_null($allowed_mime_type)) {
            //determine if the type is correct?

            if ($allowed_mime_type == $this::$image_mime_type) {
                $mime_type = getimagesize($this->_uploaded_file['tmp_name']);



                if (is_array($mime_type) && in_array($mime_type['mime'], $this::$image_mime_type)) {
                    return true;
                } elseif ($this->_uploaded_file['type'] == 'application/octet-stream') {

                    //determine if the extension is within the allowed extensions?

                    $extension = substr($this->_uploaded_file['name'], strrpos($this->_uploaded_file['name'], '.') + 1);

                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                        case 'png':


                            $imageMime = getimagesize($this->_uploaded_file['tmp_name']);
                        return true;
                            break;
                        default:
                            $this->error = 'Please upload an image';
                            return false;
                            break;
                    }




                } else {
                    $this->error = 'Please upload an image';
                    return false;
                }

            } else {
                $file_info = new finfo(FILEINFO_MIME);
                $mime_type = $file_info->buffer(file_get_contents($this->_uploaded_file['tmp_name']));


                //not yet used
            }



        } else {
            $this->error = 'File type not allowed';
            return false;
        }

    }


    /*
     * resource is a file resource obtained from 'php://input'
     */
    public function createImageFromInput($save_path = '')
    {
        $file_name = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

        if (false == $file_name) {
            return false;
        }

        $file = file_get_contents('php://input');

        if (false === $file) {
            return false;
        }

        $info = getimagesize('php://input');

        if (empty($info) || empty($info['mime'])) {
            return false;
        }

        //is the mime type in the allowed mime types?

        if (!in_array($info['mime'], $this->getAllowedMimeTypes())) {
            return false;
        }

        $img = imagecreatefromstring($file);
        unset($file);
        //make the image into a temp file:

        if (false === $img) {
            return false;
        }

        if ($save_path == '') {
            $save_path = WP_CONTENT_DIR . '/uploads/store/tmp';
        } else {
            if (substr($save_path, -1) == '/') {
                $save_path = substr($save_path, 0, -1);
            }
        }

        $directory = new  TppStoreDirectoryLibrary();

        $directory->setDirectory($save_path);

        $directory->createDirectory();

        //determine a unique file name!

        //get the base of the file name

        $this->_file = $file_name;

        $base_name = $this->getBaseName();

        $extension = $this->getExtension();

        $this->_file = '';

        $file_name = $base_name . '.' . $extension;

        $file_name = wp_unique_filename($save_path, $file_name);

        switch ($info['mime']) {
            case 'image/jpg':
            case 'image/jpeg':
                imagejpeg($img, $save_path . '/' . $file_name, 100);
                break;
            case 'image/png':
                imagepng($img, $save_path . '/' . $file_name, 9);
                break;
            default:
                return false;
                break;
        }

        imagedestroy($img);

        chmod($save_path . '/' . $file_name, 0777);

        $this->_uploaded_file = (array(
            'name'      =>  $file_name,
            'error'     =>  0,
            'tmp_name'  =>  $save_path . '/' . $file_name,
            'type'      =>  $info['mime'],
            'size'      =>  $_SERVER['CONTENT_LENGTH']
        ));

        return true;

    }

}