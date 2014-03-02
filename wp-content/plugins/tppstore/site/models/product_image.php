<?php
/**
 * User: leeparsons
 * Date: 10/12/2013
 * Time: 21:59
 */
 
class TppStoreModelProductImage extends TppStoreAbstractModelResource {


    public $product_id = null;
    public $store_id = null;
    public $alt = null;
    public $src = null;
    public $path = null;
    public $image_id = null;
    public $parent_id = null;
    public $filename = null;
    public $size_alias = null;
    public $extension = null;
    public $ordering = 0;
    public $actual_width = null;
    public $actual_height = null;


    public $child_images = array();

    protected $_table = 'shop_product_images';

    public function addChild($data)
    {
        $this->child_images[] = new TppStoreModelProductImage();
        $this->child_images[count($this->child_images) - 1]->setData($data);
    }

    public function getImageSize($size = 'full')
    {
        return TppStoreModelProductImages::getSize($size);
    }

    public function getWidth($with_attrib = false)
    {

        $return = 0;

        if ($this->actual_width > 0) {
            list($int, $dec) = explode('.', $this->actual_width);

            if ($dec > 0) {
                $return = $this->actual_width;
            } else {
                $return = $int;
            }

        }

        if (true === $with_attrib && $return > 0) {
            return " width=\"$return\" ";
        } else {
            return '';
        }

    }

    public function getHeight($with_attrib = false)
    {
        $return = 0;

        if ($this->actual_height > 0) {
            list($int, $dec) = explode('.', $this->actual_height);

            if ($dec > 0) {
                $return = $this->actual_height;
            } else {
                $return = $int;
            }
        }

        if (true === $with_attrib && $return > 0) {
            return " height=\"$return\" ";
        } else {
            return '';
        }
    }

    public function getSrc($size = null, $html = false, $attribs = array())
    {


        $src = '';

        if (!is_null($this->src) && !is_null($this->path)) {

            if (is_null($size) || false === $size) {
                $src = substr($this->path, strlen(WP_CONTENT_DIR . '/uploads')) . $this->src;
                $base_file_exists = file_exists($this->path . $this->src);

            } else {

                $size_info = $this->getImageSize($size);

                $sub = substr($this->path, strlen(WP_CONTENT_DIR . '/uploads'));

                if (substr($sub, -1) != '/') {
                    $sub .= '/';
                }

                $src = $sub . $this->filename . '_' . $size_info['width'] . '_' . $size_info['height'] . '.' . $this->extension;


                $base_file_exists = file_exists($this->path . $this->src);


            }



            if (is_null($size) || false === $size) {
                $resized_file_exists = true;
            } else {
                $lib = new TppStoreLibraryFileImage();
                $lib->setFile($this->path . $this->filename . '.' . $this->extension);

                $base_name = $lib->getBaseName();

                if (strpos($base_name, $this->path) !== false) {
                    $resized_file_exists = file_exists($base_name . '_' . $size_info['width'] . '_' . $size_info['height'] . '.' . $lib->getExtension());
                } else {
                    $resized_file_exists = file_exists($this->path . $base_name . '_' . $size_info['width'] . '_' . $size_info['height'] . '.' . $lib->getExtension());
                }



            }



            if (true === $base_file_exists && false === $resized_file_exists && false !== $size) {

                //try and make the image?

                $lib = new TppStoreLibraryFileImage();
                $lib->setFile($this->path . $this->filename . '.' . $this->extension);

                $sizes = TppStoreModelProductImages::getSize($size);

                $lib->resize($sizes);

                if (!file_exists($this->path . $this->filename . '_' . $sizes['width'] . '_' . $sizes['height'] . '.' . $this->extension)) {
                    return false;
                }

            } elseif (false === $base_file_exists) {

                TppStoreLibraryLogger::getInstance()->add(0, 'view product image', 'product_id: ' . $this->product_id, array($base_file_exists, $this->path . $this->filename));


                return false;
            }
        } else {
            TppStoreLibraryLogger::getInstance()->add(0, 'view product image', 'product_id: ' . $this->product_id, array($this->path . $this->filename));

            return false;
        }
        if (true === $html) {
            $str = '';
            if (is_array($attribs) && !empty($attribs)) {

                foreach ( $attribs as $attrib => $value) {
                    $str .= $attrib .'="' .$value . '"';
                }
            }

            return '<img src="' . $src . '" alt="' . $this->alt . '" ' . $str . '>';
        } else {
            return $src;
        }

    }


}