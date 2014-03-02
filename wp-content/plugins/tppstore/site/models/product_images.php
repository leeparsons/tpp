<?php
/**
 * User: leeparsons
 * Date: 10/12/2013
 * Time: 21:00
 */
 
class TppStoreModelProductImages extends TppStoreAbstractModelResource {


    public $product_id = null;
    public $store_id = null;
    public $image_id = null;
    public $size_alias = null;

    public $image_ids = array();

    public $images = array();


    protected $_table = 'shop_product_images';

    private static $_sizes = array(
        'full'  =>  array(
//            'width'     =>  550,
//            'height'    =>  410,
            'width'     =>  660,
            'height'    =>  400,
            'crop'      =>  true
        ),
        'thumb' =>  array(
            'width'     =>  250,
            'height'    =>  250,
            'crop'      =>  true
        ),
        'slideshow_thumb'   =>  array(
            'width'     =>  50,
            'height'    =>  50,
            'crop'      =>  true
        ),
        'cart_thumb'    =>  array(
            'width'     =>  200,
            'height'    =>  200,
            'crop'      =>  true
        ),
        'store_thumb'   =>  array(
            'width'     =>  380,
            'height'    =>  380,
            'crop'      =>  true
        ),
        'store_related' =>  array(
            'width'     =>  110,
            'height'    =>  110,
            'crop'      =>  true
        )
    );


    public static function getSizes()
    {
        return self::$_sizes;
    }

    public static function getSize($size = 'full')
    {
        if (isset(self::$_sizes[$size])) {
            return self::$_sizes[$size];
        } else {
            return false;
        }


    }



    public function getImagesBySize($size = 'main')
    {

        $return = array();

        if (intval($this->product_id) > 0) {

            global $wpdb;

            if (!is_null($this->size_alias)) {
                $size = $this->size_alias;
            }

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d AND size_alias = %s ORDER BY ordering, filename ASC",
                    array(
                        $this->product_id,
                        $size
                    )
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                foreach ($rows as $row) {
                    $return[$row->image_id] = new TppStoreModelProductImage();
                    $return[$row->image_id]->setData($row);
                }
            }

        }

        return $return;

    }

    public function getMainImage($size = false)
    {
        $return = array();

        if (intval($this->product_id) > 0) {
            global $wpdb;

            if ($size === false || $size == '') {
                $rows = $wpdb->get_results(
                    "SELECT i.size_alias, i.filename, i.path, i.alt, i.src, i.extension FROM " . $this->getTable() . " AS i INNER JOIN ( " .
                    "SELECT image_id FROM " . $this->getTable() . " WHERE product_id = " . intval($this->product_id) . " GROUP BY ordering LIMIT 1" .
                    ") AS i2 ON i2.image_id = i.image_id OR i.parent_id = i2.image_id",
                    OBJECT_K
                );
            } elseif ($size === 'all') {
                $rows = $wpdb->get_results(
                    "SELECT i.size_alias, i.filename, i.path, i.alt, i.src, i.extension FROM " . $this->getTable() . " AS i INNER JOIN ( " .
                    "SELECT image_id FROM " . $this->getTable() . " WHERE product_id = " . intval($this->product_id) . " LIMIT 1" .
                    ") AS i2 ON i2.image_id = i.image_id OR i.parent_id = i2.image_id",
                    OBJECT_K
                );
            } else {
                $rows = $wpdb->get_results(
                    "SELECT size_alias, filename, path, alt, src, extension FROM " . $this->getTable() . " WHERE size_alias = '" . $size . "' AND product_id = " . intval($this->product_id) . " ORDER BY ordering LIMIT 1",
                    OBJECT_K
                );
            }



            if ($wpdb->num_rows > 0) {

                foreach ($rows as $row) {
                    $return[$row->size_alias] = new TppStoreModelProductImage();
                    $return[$row->size_alias]->setData($row);
                }
            }

        }

        return $return;

    }

    public function getImages($parent_id = 0, $heriarchical = false, $count = false)
    {


        if (intval($this->product_id) > 0) {

            global $wpdb;

            if ($count === true) {
                $c = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(product_id) AS c FROM " . $this->getTable() . " WHERE product_id = %d AND parent_id = 0",
                        $this->product_id
                    )
                );

                return $c;
            }


            if ($parent_id == -1) {
                $sql = $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d AND parent_id = 0 ORDER BY ordering, filename ASC",
                    array(
                        $this->product_id
                    )
                );
            } else {
                $sql = $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d AND parent_id = %d",
                    array(
                        $this->product_id,
                        $parent_id
                    )
                );
            }



            $rows = $wpdb->get_results(
                $sql,
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {

                if (false === $heriarchical) {
                    foreach ($rows as $row) {
                        $this->images[$row->image_id] = new TppStoreModelProductImage();
                        $this->images[$row->image_id]->setData($row);
                    }
                } else {
                    foreach ($rows as $row) {

                        if ($row->parent_id == 0) {
                            $this->images[$row->image_id] = new TppStoreModelProductImage();
                            $this->images[$row->image_id]->setData($row);
                        } else {
//                            if (isset($this->images[$row->parent_id])) {
//                                $this->images[$row->parent_id]->addChild($row);
//                            }
                        }

                    }

                }

            }
        }

        return $this->images;

    }



    /*
     * This retreives the uploaded files for this product based on the session with the image savepath stored according to the
     * store controller load session
     */
    public function retrieveUsingSession($preview = false)
    {

        if ( false === ($save_directory = TppStoreControllerDashboard::getInstance()->loadTempStorePathSession($this->store_id))) {

            TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to save your images'));

            return false;
        }

        //find all the files in the saved directory

        $directory = new TppStoreDirectoryLibrary();

        $directory->setDirectory($save_directory);

        if ($directory->directoryExists()) {
            $existing_images = $directory->getFiles(false);
        } else {
            $existing_images = array();
        }

        $new_images = filter_input(INPUT_POST, 'uploaded_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $original_images = filter_input(INPUT_POST, 'original_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

        $image_ordering = filter_input(INPUT_POST, 'image_ordering', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        //image ordering foes not workl
        //$image_ordering = array();
        $child_image_ordering = filter_input(INPUT_POST, 'child_image_ordering', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

        if (is_null($new_images)) {
            $new_images = array();
        }

        if (is_null($image_ordering)) {
            $image_ordering = array();
        }

        if (is_null($child_image_ordering)) {
            $child_image_ordering = array();
        }

        if (substr($save_directory, -1) == '/') {
            $save_directory = substr($save_directory, 0, -1);
        }


        $tmp_product_id = substr($save_directory, strrpos($save_directory, '/', 1) + 1);

        if ($preview === true) {
            $this->product_id = $tmp_product_id;
        }


        $store_path = substr($save_directory, 0, strrpos($save_directory, '/', 1) + 1);


        if (false === $preview && false === $directory->createDirectory($store_path . $this->product_id)) {
            TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to create store directory'));
            return false;
        }

        $error = false;

        //get the original images that are being kept from the data abase!
        $this->image_ids = $original_images;

        $tmp = $this->getImagesFromIds();

        $original_images_to_keep = array();
        $original_images_to_keep_multiplex = array();
        if (false !== $tmp) {
            foreach ($tmp as $tmp_image) {
                $original_images_to_keep[$tmp_image->image_id] = $tmp_image->src;
                $original_images_to_keep_multiplex[$tmp_image->src] = $tmp_image;
            }
        }



        //an array containing images to force delete from the database in case we don't hit the delete method
        $force_delete = $this->filterImages($new_images, $existing_images, $original_images_to_keep, $save_directory, $tmp_product_id, $store_path, $original_images_to_keep_multiplex, $image_ordering, $child_image_ordering, $preview);

        $tmp = substr($save_directory, strrpos($save_directory, '/') + 1);

        if ($preview === false && intval($tmp) == 0) {
            $directory->setDirectory($save_directory);
            $directory->deleteDirectory();
        }

        if (!empty($original_images_to_keep)) {
            //reset the image ids
            $this->image_ids = array();
            ////delete images which are not in this array - assume the others got deleted :-0
            foreach ($original_images_to_keep as $image_id => $image) {
                $this->image_ids[] = $image_id;
            }

            //delete from the database and file system any images which we are not keeping!
            if ($preview === false) {
                $deleted = $this->deleteExcludingIds();
            }


            if ($error === true) {
                $error = $deleted;
            }

            return $error;

        } elseif (empty($this->images)) {
            //delete any images saved against this product as assume they all got deleted :-0
            if ($preview === false) {
                $this->delete();
            }
        } else {
            //images have been uploaded, so don't delete these - keep them!
            //$this->deleteExcludingImageNames(false, $this->images, $store_path . $this->product_id);

            if ($preview === false) {
                $this->deleteExcludingIds();
            } else {
                $tmp = $this->images;
                $this->images = array();
                foreach ($tmp as $img) {
                    if (!in_array($img->filename, $force_delete)) {
                        $this->images[] = $img;
                    }
                }
            }

//            $this->deleteUsingNames($force_delete);
        }

        if (!empty($this->images)) {
            //set up the array to save $this->images into the database!

            $tmp_images = array();

            foreach ($this->images as $image) {
                if (is_object($image)) {
                    $tmp_images[] = $image;
                } elseif (is_array($image)) {
                    $tmp = new TppStoreModelProductImage();
                    $tmp_images[] = $tmp->setData($image);
                } else {
                    $tmp = new TppStoreModelProductImage();
                    $tmp->setData(array(
                        'filename'  =>  substr($image, strrpos($image, '/') + 1),
                        'path'      =>  substr($image, 0, strrpos($image, '/') + 1),
                        'alt'       =>  '',
                        'new'       =>  true
                    ));
                    $tmp_images[] = $tmp;
                }
            }

            $this->images = $tmp_images;
        }


        return !$error;





    }


    private function filterImages($new_images = array(), $existing_images = array(), $original_images_to_keep = array(), $save_directory = '', $tmp_product_id = -1, $store_path = '', $original_images_to_keep_multiplex = array(), $image_ordering = array(), $child_image_ordering = array(), $preview = false)
    {

        /*
         * TODO: if we are in preview mode, we need to force delete images from the image array to make sure we only have the needed images for the preview!
         * only worry about parent ids... so don't keep the children if in preview mode!
         */

        $force_delete = array();

        //move the new images if necessary - and save them into the database!
        if (!empty($existing_images)) {

            foreach ($existing_images as $index => $image) {

                if ($preview === true && $original_images_to_keep_multiplex[$image]->parent_id > 0) {
                    continue;
                }

                if (!in_array($image, $new_images) && !in_array($image, $original_images_to_keep)) {

                    if (false === $preview) {
                        @unlink($save_directory . '/' . $image);
                    }

                    $force_delete[] = $image;

                    //file detected in the system that is not to be kept and is not new so remove it
                } elseif ($tmp_product_id != $this->product_id && !is_numeric($tmp_product_id))  {
                    //come in here if all totally new images
                    //move the uploaded file into the correct directory if it's not already there!
                    //assume uniqid was used!

                    if (!@rename($save_directory . '/' . $image, $store_path . $this->product_id . '/' . $image)) {
                        TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'unable to copy your file: ' . $image));
                        $error = true;
                    } else {

                        $ordering = isset($image_ordering[$image])?$image_ordering[$image]:$image_ordering[$index];

                        //totally new image - product never been saved even!
                        $tmp = new TppStoreModelProductImage();
                        $tmp->setData(array(
                            'path'          =>  $store_path . $this->product_id . '/',
                            'filename'  =>  $image,
                            'alt'       =>  null,
                            'new'       =>  true,
                            'ordering'  =>  $ordering
                        ));

                        if ($preview === true) {
                            $tmp->setData(array(
                                'src'       =>  $image,
                                'filename'  =>  substr($image, 0, strrpos($image, '.')),
                                'extension' =>  substr($image, strrpos($image, '.') + 1),
                            ));
                        }

                        //this is a new product and therefore new images!
                        $this->images[] = $tmp;
                    }
                } elseif (!in_array($image, $original_images_to_keep)) {
                    //assume the save path contains the product_id
                    //this is also a new image!

                    $ordering = isset($image_ordering[$image])?$image_ordering[$image]:$image_ordering[$index];

                    $tmp = new TppStoreModelProductImage();
                    $tmp->setData(array(
                        'path'          =>  $store_path . $this->product_id . '/',
                        'filename'  =>  $image,
                        'alt'       =>  null,
                        'new'       =>  true,
                        'ordering'  =>  $ordering
                    ));

                    if ($preview === true) {
                        $tmp->setData(array(
                            'src'       =>  $image,
                            'filename'  =>  substr($image, 0, strrpos($image, '.')),
                            'extension' =>  substr($image, strrpos($image, '.') + 1),
                        ));
                    }

                    $this->images[] = $tmp;

                } else {
                    //add it as a non new image!

                    if (isset($original_images_to_keep_multiplex[$image->image_id])) {
                        //determine the image_ordering by the parent...

                    }

                    if (isset($child_image_ordering[$original_images_to_keep_multiplex[$image]->image_id])) {
                        $ordering = $child_image_ordering[$original_images_to_keep_multiplex[$image]->image_id];
                    } elseif (isset($image_ordering[$original_images_to_keep_multiplex[$image]->image_id])) {
                        $ordering = $image_ordering[$original_images_to_keep_multiplex[$image]->image_id];
                    } else {

                        $ordering = $image_ordering[$index];

                        $ordering = isset($image_ordering[$image])?$image_ordering[$image]:$image_ordering[$index];

                    }


                    $tmp = new TppStoreModelProductImage();
                    $tmp->setData(array(
                        'image_id'      =>  $original_images_to_keep_multiplex[$image]->image_id,
                        'path'          =>  $store_path . $this->product_id,
                        'filename'      =>  $image,
                        'alt'           =>  $original_images_to_keep_multiplex[$image]->alt,
                        'new'           =>  false,
                        'ordering'      =>  $ordering,
                        'parent_id'     =>  $original_images_to_keep_multiplex[$image]->parent_id,
                        'product_id'    =>  $original_images_to_keep_multiplex[$image]->product_id,
                        'extension'     =>  $original_images_to_keep_multiplex[$image]->extension,
                        'src'           =>  $original_images_to_keep_multiplex[$image]->src,
                        'store_id'      =>  $original_images_to_keep_multiplex[$image]->store_id,
                        'dimensions'    =>  $original_images_to_keep_multiplex[$image]->dimensions
                    ));

                    if ($preview === true) {
                        $tmp->setData(array(
                            'src'       =>  $image,
                            'filename'  =>  substr($image, 0, strrpos($image, '.')),
                            'extension' =>  substr($image, strrpos($image, '.') + 1),
                            'path'      =>  $tmp->path . '/'
                        ));
                    }

                    $this->images[] = $tmp;
                }
            }
        }

        return $force_delete;
    }



    /*
     * use an array of image names to force delete from the database!
     */
    public function deleteUsingNames($names = array())
    {

        if (!empty($names)) {
            global $wpdb;

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT image_id FROM " . $this->getTable() . " WHERE product_id = %d AND src IN ('" . implode("','", $names) . "')",
                    array(
                        $this->product_id
                    )
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                $ids = array();
                foreach ($rows as $row) {
                    $ids[] = $row->image_id;
                }

                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM " . $this->getTable() . " WHERE product_id = %d AND (parent_id IN (" . implode(',', $ids) . ") OR image_id IN (" . implode(',', $ids) . "))",
                        $this->product_id
                    )
                );

            }

        }

    }

    public function deleteExcludingImageNames($validate_images = true, $images = array())
    {

        if (!$this->validate($validate_images)) {
            return false;
        }

        if (!is_array($images)) {
            return false;
        }

        $path = WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id . '/';

        $files = scandir($path);

        //images contains the files already in the system uploaded
        //files are the images already stored

        $this->unlinkFiles($images, $files, $path);




    }


    /*
     * unlinks files that do not exist in the passed array, or ones that do not match the files being saved in teh passed array
     * You must pass in all the files that exist and all the ones being saved, otherwise you may lose data!
     * @param directory - the save path where existing files can be found
    */
    private function unlinkFiles($new_files = array(), $existing_files = array(), $directory = '')
    {

        $files_to_remove_from_database = array();

        if (strpos($directory, WP_CONTENT_DIR) === false) {
            return $files_to_remove_from_database;
        }


        $tmp_new_files = array();

        foreach ($new_files as $file) {
            if ($file instanceof TppStoreModelProductImage) {
                $tmp_new_files[] = $file->filename;
            } else {
                $tmp_new_files[] = $file;
            }

        }


        foreach ($existing_files as $existing_file) {
            if ($existing_file == '.' || $existing_file == '..') {
                continue;
            }


            if (!in_array($existing_file, $tmp_new_files)) {
                @unlink($directory . $existing_file);
                //return it and then delete from database!
                $files_to_remove_from_database[] = $existing_file;
            }

        }

        return $files_to_remove_from_database;
    }

    /*
     * this method assumes that the image ids saved in this->image_ids are to be saved. If it is empty, everything gets deleted for this product
     */
    protected function deleteExcludingIds()
    {

        if (!$this->validate(false)) {
            return false;
        }

        global $wpdb;
        $error = false;

        $return = array();


        if (empty($this->image_ids)) {

            $this->delete();

            $return = true;

        } else {
            //delete the images according to the images not in the images array of ids

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT src, path FROM " . $this->getTable() . " WHERE (image_id NOT IN (" . implode(',', $this->image_ids) . ") AND parent_id NOT IN (" . implode(',' , $this->image_ids) .  ")) AND product_id = %d",
                    $this->product_id
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {


                foreach ($rows as $row) {
                    //delete!
                    @unlink($row->path . $row->src);
                }

                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM " . $this->getTable() . " WHERE (image_id NOT IN (" . implode(',', $this->image_ids) . ") OR parent_id NOT IN (0, " . implode(',', $this->image_ids) . ")) AND product_id = %d",
                        $this->product_id
                    )
                );

                if ($wpdb->last_error != '') {
                    TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to delete your images: 418'));
                    $error = true;
                }

            } elseif ($wpdb->last_error != '') {
                TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to delete your images: 423'));
                $error = true;
            }



        }

        if ($error === true) {
            return false;
        }

        return true;

    }

    /*
     * Deletes all images for this product_id
     */
    protected function delete($validate_images_exist = false)
    {

        if (!$this->validate($validate_images_exist)) {
            return false;
        }

        $error = false;
        //get all the images for this product, and delete them from the file system!

        $this->getImages();

        global $wpdb;

        $file_system_delete = $this->deleteImagesFromFileSystem($validate_images_exist);

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->getTable() . " WHERE product_id = %d",
                array($this->product_id)
            )
        );

        if ($wpdb->last_error != '') {
            TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to delete your images: 464'));
            $error = true;
        }

        if ($error === false) {
            $error = $file_system_delete;
        }

        return !$error;

    }

    protected function deleteImagesFromFileSystem($validate_images_exist = true)
    {
        if (!$this->validate($validate_images_exist)) {
            return false;
        }

        $error = false;

        if (!empty($this->images)) {
            foreach ($this->images as &$image) {


                if (is_array($image)) {

                    $tmp = new TppStoreModelProductImage();
                    $tmp->setData($image);
                    $image = $tmp;
                }
                if (!@unlink($image->path . $image->src)) {
                    if ($validate_images_exist) {
                        TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Unable to delete your images: error code: 487<br><br>File: ' . $image->path . $image->src));
                        $error = true;
                    }
                }
            }
        }

        return !$error;
    }

    protected function getImagesFromIds()
    {
        if (!$this->validate(false) || empty($this->image_ids)) {
            return false;
        }

        global $wpdb;

        $rows = $wpdb->get_results(
            "SELECT * FROM " . $this->getTable() . " WHERE image_id IN (" . implode(',', $this->image_ids) . ")",
            OBJECT_K
        );

        if ($wpdb->num_rows > 0) {

            $return = array();

            foreach ($rows as $row) {
                $return[] = $row;
            }

            return $return;
        } else {
            return false;
        }
    }

    public function save()
    {
        if (!$this->validate(false)) {
            return false;
        }

        if (empty($this->images)) {
            //do not throw an error, but continue
            return true;
        }

        global $wpdb;




        foreach ($this->images as $image) {
            $tmp_image = new TppStoreLibraryFileImage();

//            $sql_str = "(%s, %s, %s,%s, %s, %d, %s, %d)";
//
//            $sql_array = array();
//
//            $sql_array[] = $image['src'];
//            $sql_array[] = $image['path'];
//            $sql_array[] = $image['alt'];
//            //extension
//            $sql_array[] = substr($image['src'], strrpos($image['src'], '.') + 1);
//            //base name
//            $sql_array[] = substr($image['src'], 0, strrpos($image['src'], '.'));
//
//            //parent_id
//            $sql_array[] = 0;
//            //image_size_alias
//            $sql_array[] = 'main';
//
//            $sql_array[] = $this->product_id;


            if (true === $image->new) {

                //now determine the extension!
                if (is_null($image->extension)) {

                    //only come in here for new images as current images will be populated with this informaiton
                    $extension = substr($image->filename, strrpos($image->filename, '.') + 1);
                    $base_name = substr($image->filename, 0, strrpos($image->filename, '.'));
                    $image->filename = $base_name;
                    //$name = $image->path, sanitize_title_with_dashes($name) . $extension);
                    $image->src = $base_name . '.' . $extension;
                    $image->extension = $extension;
                }


                $tmp_image->setFile($image->path . $image->src);

                if (false !== ($dimensions = $tmp_image->getDimensions())) {
                    $width = $dimensions['width'];
                    $height = $dimensions['height'];
                } else {
                    $width = null;
                    $height = null;
                }

                $wpdb->insert(
                    $this->getTable(),
                    array(
                        'src'           =>  $image->src,
                        'path'          =>  $image->path,
                        'alt'           =>  $image->alt,
                        'filename'      =>  $image->filename,
                        'extension'     =>  $image->extension,
                        'product_id'    =>  $this->product_id,
                        'size_alias'    =>  'main',
                        'parent_id'     =>  0,
                        'ordering'      =>  $image->ordering,//handles children images with no ordering!
                        'actual_width'  =>  $width,
                        'actual_height' =>  $height
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%s',
                        '%s'
                    )
                );


                /*
                 *
                 * Commenting out saving children in database as not needed
                 *
                 */
//
//                if ($wpdb->row_affected == 1) {
//                    $parent_id = $wpdb->insert_id;
//                } else {
//                    //this product image already exists in the database, so get its id!
//                    $parent_id = $wpdb->get_var(
//                        $wpdb->prepare(
//                            "SELECT image_id AS parent_id FROM " . $this->getTable() . " WHERE
//                          product_id = %d AND filename = %s",
//                            array(
//                                $this->product_id,
//                                $image->filename
//                            )
//                        )
//                    );
//
//
//                }
//
//                if (isset($image->new) && true === $image->new) {
//                    //determine if the image is a new image, if so then manipulate it to create the cache versions
//
//                    $tmp_image->setFile($image->path . $image->src);
//
//
//
//                    //a list of sizes, save each database entry for these are children of the main image
//                    $sizes = self::$_sizes;
//
//                    $tmp_image->resize($sizes);
//                    $sql_array = array();
//                    $sql_str = '';
//
//
//
//                    foreach ($sizes as $size_key => $size) {
//
//                        $sql_str .= $sql_str == ''?"(%s, %s, %s,%s, %s, %d, %s, %d, %d, %s, %s)":",(%s, %s, %s, %s, %s, %d, %s, %d, %d, %s, %s)";
//                        $sql_array[] = $image->filename . '_' . $size['width'] . '_' . $size['height'] . '.' . $image->extension;
//                        $sql_array[] = $image->path;
//                        $sql_array[] = $image->alt;
//                        //extension
//                        $sql_array[] = $image->extension;
//                        //base name
//                        $sql_array[] = $image->filename . '_' . $size['width'] . '_' . $size['height'];
//
//                        //parent_id
//                        $sql_array[] = $parent_id;
//                        //image_size_alias
//                        $sql_array[] = $size_key;
//
//                        $sql_array[] = $this->product_id;
//                        $sql_array[] = $image->ordering;
//
//                        //get the real dimensions and save them:
//                        $tmp_image->setFile($image->path . $image->filename . '_' . $size['width'] . '_' . $size['height'] . '.' . $image->extension);
//
//                        if (false !== ($dimensions = $tmp_image->getDimensions($size))) {
//                            $sql_array[] = $dimensions['width'];
//                            $sql_array[] = $dimensions['height'];
//                        } else {
//                            $sql_array[] = null;
//                            $sql_array[] = null;
//                        }
//
//
//                    }
//
//
//
//                    $wpdb->query(
//                        $wpdb->prepare(
//                            "REPlACE INTO " . $this->getTable() . " (src, path, alt, extension, filename, parent_id, size_alias, product_id, ordering, actual_width, actual_height) VALUES " . $sql_str,
//                            $sql_array
//                        )
//                    );
//
//                }

                /*
                 *
                 *
                 * end commenting out children in database
                 *
                 *
                 */
            } else {
                //just update this image with the new ordering and alt!
                $wpdb->update(
                    $this->getTable(),
                    array(
                        'alt'       =>  $image->alt,
                        'ordering'  =>  $image->ordering
                    ),
                    array(
                        'image_id'  =>  $image->image_id
                    ),
                    array(
                        '%s',
                        '%d'
                    ),
                    array(
                        '%d'
                    )
                );

                //just update this image with the new ordering and alt!
//                $wpdb->update(
//                    $this->getTable(),
//                    array(
//                        'alt'       =>  $image->alt,
//                        'ordering'  =>  $image->ordering
//                    ),
//                    array(
//                        'parent_id'  =>  $image->image_id
//                    ),
//                    array(
//                        '%s',
//                        '%d'
//                    ),
//                    array(
//                        '%d'
//                    )
//                );
            }
            $previous_ordering = $image->ordering;

        }



        if ($wpdb->last_error != '') {
            TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  $wpdb->last_error));
            return false;
        }

        return true;

    }

    public function validate($validate_images = true, $raise_error = true)
    {

        if (is_null($this->product_id) || intval($this->product_id) <= 0) {
            if (true === $raise_error) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'No Product selected'));
            }
            return false;
        }

        if (
            (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession()))
            ||
            is_null($this->store_id) || intval($this->store_id) <= 0) {
            if (true === $raise_error) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'No Store selected'));
            }
            return false;
        }

        //validate against the store record
        if ($store->store_id != $this->store_id) {
            if (true === $raise_error) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Could not authorise your request to save images into this store'));
            }
            return false;
        }

        if (true === $validate_images && (!is_array($this->images) || empty($this->images))) {
            if (true === $raise_error) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_images' =>  'Could not detect any images to save'));
            }
            return false;
        }

        return true;
    }

}