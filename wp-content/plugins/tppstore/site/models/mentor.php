<?php
/**
 * User: leeparsons
 * Date: 11/01/2014
 * Time: 14:03
 */
 
class TppStoreModelMentor extends TppStoreAbstractModelResource {

    public $mentor_name = null;
    public $mentor_company = null;
    public $mentor_specialities = array();
    public $product_id = null;
    public $mentor_id = null;
    public $mentor_city = null;
    public $mentor_country = null;
    public $src = null;
    public $store_id = null;
    public $slug = '';
    public $mentor_bio = null;

    //number of sessions
    public $sessions = 0;

    private $path = null;

    public $rating = 0;

    protected $_table = 'shop_product_mentors';

    protected $_specialism_model = null;

    public function getSessionCount()
    {
        return $this->sessions;
    }

    public function getBio()
    {
        return esc_textarea($this->mentor_bio);
    }

    public function getLocation()
    {
        if (!empty($this->mentor_city) && !empty($this->mentor_country)) {
            return $this->mentor_city . ', ' . $this->mentor_country;
        } elseif (!empty($this->mentor_city)) {
            return $this->mentor_city;
        } else {
            return $this->mentor_country;
        }

    }

    public function getSeoTitle()
    {
        return $this->mentor_name;
    }

    public function getSeoDescription()
    {
        return esc_textarea(substr(strip_tags($this->mentor_bio), 0, 149));
    }

    public function getPermalink()
    {
        return get_site_url() . '/shop/mentor/' . $this->slug . '/';
    }

    public function getImageDirectory()
    {
        if (is_null($this->path)) {
            $this->path = WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/mentor/' . $this->mentor_id . '/';
        }
        return $this->path;
    }

    public function getSrc($size = null, $html = false, $attribs = array())
    {
        $src = '';

        if (is_null($this->path) && intval($this->mentor_id) > 0) {
            $this->getImageDirectory();
        }

        if (!is_null($this->src) && !is_null($this->path)) {

            if (is_null($size) || false === $size) {
                $src = substr($this->path, strlen(WP_CONTENT_DIR . '/uploads')) . $this->src;
                $base_file_exists = file_exists($this->path . $this->src);

            } else {

                $lib = new TppStoreLibraryFileImage();

                $lib->setFile($this->path . $this->src);

                $this->filename = $lib->getBaseName();
                $this->extension = $lib->getExtension();

                $size_info = TppStoreModelProductImage::getInstance()->getImageSize($size);

                $src = substr($this->filename, strlen(WP_CONTENT_DIR . '/uploads')) . '_' . $size_info['width'] . '_' . $size_info['height'] . '.' . $this->extension;


                $base_file_exists = file_exists($this->path . $this->src);


            }



            if (is_null($size) || false === $size) {
                $resized_file_exists = true;
            } else {
                $lib = new TppStoreLibraryFileImage();
                $lib->setFile($this->filename . '.' . $this->extension);

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
                $lib->setFile($this->filename . '.' . $this->extension);

                $sizes = TppStoreModelProductImages::getSize($size);

                $lib->resize($sizes);

                if (strpos($this->filename, $this->path) === 0) {
                    if (!file_exists($this->filename . '_' . $sizes['width'] . '_' . $sizes['height'] . '.' . $this->extension)) {
                        return false;
                    }
                } else {
                    if (!file_exists($this->path . $this->filename . '_' . $sizes['width'] . '_' . $sizes['height'] . '.' . $this->extension)) {
                        return false;
                    }
                }


            } elseif (false === $base_file_exists) {
                return false;
            }
        } else {
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

    public function getSpecialism($auto_load = true)
    {
        if (is_null($this->_specialism_model)) {
            $this->_specialism_model = new TppStoreModelMentorSpecialisms();
            $this->_specialism_model->setData(
                array(
                    'mentor_id' =>  $this->mentor_id
                )
            );
            if (true === $auto_load) {
                $this->_specialism_model->getSpecialisms();
            }
        }

        return $this->_specialism_model;

    }

    public function setRating($rating = 0)
    {
        $this->rating = $rating;
    }

    public function getRating()
    {
        return number_format($this->rating, 1);
    }

    public function getMentorBySlug($slug = '')
    {
        $slug = trim($slug);

        if ($slug == '') {
            $this->reset();
        } else {

            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT m.* FROM " . $this->getTable() . " AS m
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = m.store_id
                    WHERE m.slug = %s AND s.enabled = 1",
                    array(
                        $slug
                    )
                )
            );

            if ($wpdb->num_rows == 1) {
                $this->setData($wpdb->last_result[0]);
            } else {
                $this->reset();
            }

        }

        return $this;
    }


    public function readFromPost()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->mentor_name = filter_input(INPUT_POST, 'mentor_name', FILTER_SANITIZE_STRING);
            $this->mentor_company = filter_input(INPUT_POST, 'mentor_company', FILTER_SANITIZE_STRING);
            $this->mentor_city = filter_input(INPUT_POST, 'mentor_city', FILTER_SANITIZE_STRING);
            $this->mentor_country = filter_input(INPUT_POST, 'mentor_country', FILTER_SANITIZE_STRING);

            $this->store_id = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_NUMBER_INT);

            $this->mentor_bio = filter_input(INPUT_POST, 'mentor_bio', FILTER_UNSAFE_RAW);

            $images = filter_input(INPUT_POST, 'uploaded_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            if (is_array($images) && isset($images[0])) {
                $this->src = $images[0];
            } else {
                $images = filter_input(INPUT_POST, 'original_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
                if (is_array($images) && isset($images[0])) {
                    $this->src = $images[0];
                }
            }

            return true;
        } else {
            return false;
        }

    }


    /*
     * @param test = false, whether or not to run a test. true = testing existence only. false = populate the model with the resulting data.
     * @param populate_mentor_id = false, determines whether or not to populate the mentor id from the result
     */
    public function getMentorByProduct($test = false, $populate_mentor_id = false)
    {

        if (intval($this->product_id) < 1) {
            if (false === $test) {
                $this->reset();
            } else {
                if (true === $populate_mentor_id) {
                    $this->mentor_id = null;
                }
                return false;
            }
        } else {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT m.* FROM shop_p2m AS p2m
                      LEFT JOIN shop_product_mentors AS m ON m.mentor_id = p2m.mentor_id
                      WHERE p2m.product_id = %d",
                    $this->product_id
                ),
                OBJECT_K
            );


            if ($wpdb->num_rows == 0) {
                if (false === $test) {
                    $this->reset();
                } else {
                    if (true === $populate_mentor_id) {
                        $this->mentor_id = null;
                    }
                    return false;
                }
            } else {
                if (false === $test) {
                    $this->setData($wpdb->last_result[0]);
                } else {
                    if (true === $populate_mentor_id) {
                        $this->mentor_id = $wpdb->last_result[0]->mentor_id;
                    }
                    return true;
                }
            }
        }

        return $this;
    }


    public function getMentorById()
    {
        if (intval($this->mentor_id) > 0) {
            global $wpdb;

            $wpdb->get_row(

                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE mentor_id = %d",
                    $this->mentor_id
                )


            );

            if ($wpdb->num_rows > 0) {
                $this->setData($wpdb->last_result[0]);
            }

        }

        return $this;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;




        if (intval($this->mentor_id) < 1) {// &&  false === $this->getMentorByProduct(true, true)) {
            //new

            $wpdb->insert(
                $this->getTable(),
                array(
                    'store_id'          =>  $this->store_id,
                    'mentor_name'       =>  $this->mentor_name,
                    'mentor_company'    =>  $this->mentor_company,
                    'mentor_city'       =>  $this->mentor_city,
                    'mentor_country'    =>  $this->mentor_country,
                    'src'               =>  $this->src,
                    'mentor_bio'        =>  $this->mentor_bio
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );



        } else {
            //existing
            $wpdb->update(
                $this->getTable(),
                array(
                    'mentor_name'       =>  $this->mentor_name,
                    'mentor_company'    =>  $this->mentor_company,
                    'mentor_city'       =>  $this->mentor_city,
                    'mentor_country'    =>  $this->mentor_country,
                    'src'               =>  $this->src,
                    'mentor_bio'        =>  $this->mentor_bio
                ),
                array(
                    'store_id'        =>  $this->store_id,
                    'mentor_id'         =>  $this->mentor_id
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array(
                    '%d',
                    '%d'
                )
            );

        }

        $move_temp_file = false;

        if ($wpdb->rows_affected == 1 && $wpdb->last_error == '') {
            if (intval($this->mentor_id) == 0) {
                $this->mentor_id = $wpdb->insert_id;
            }



            $slug = sanitize_title_with_dashes($wpdb->escape($this->mentor_name));

            $wpdb->query(
                "SELECT slug FROM " . $this->getTable() . "
                    WHERE slug LIKE '%" . $slug . "%'
                    AND mentor_id <> " . $this->mentor_id
            );


            if ($wpdb->num_rows > 0) {
                $slug .= '-' . $wpdb->num_rows;
            }

            $this->slug = $slug;

            $wpdb->update(
                $this->getTable(),
                array(
                    'slug'  =>  $this->slug
                ),
                array(
                    'mentor_id' =>  $this->mentor_id
                ),
                array(
                    '%s'
                ),
                array(
                    '%d'
                )
            );


            $move_temp_file = true;
        }

        /*
         * save mentor specialisms
         */
        $res = $this->getSpecialism()->readFromPost();

        if (false === $wpdb->result) {
            TppStoreMessages::getInstance()->addMessage('error', 'There was an error saving your mentor session: ' . $wpdb->last_error);
        } elseif ($res === true) {
            $this->getSpecialism()->save();
        }




        if ($move_temp_file === true) {

                //move the temporary file across!



            if (!is_null($this->src) && !empty($this->src) && false !== ($temp_path = TppStoreControllerMentors::getInstance()->loadMentorUploadSession())) {

                if (intval($temp_path['directory']) == 0) {

                    $base_path = substr($temp_path['path'], 0, strpos($temp_path['path'], 'new_mentor'));

                    $save_path = $base_path . $this->mentor_id .'/';

                    $directory = new TppStoreDirectoryLibrary();

                    if (false === $directory->createDirectory($save_path)) {
                        TppStoreMessages::getInstance()->addMessage('error', 'Unable to create the mentor directory: ' . $save_path);
                        return false;
                    }

                    $temp_path = $temp_path['path'] . '/';

                    //move the images across!
                    $directory->setDirectory($temp_path);

                    $files = $directory->getFiles(false);

                    if (!empty($files)) {

                        $tmp = TppStoreModelProductImages::getSize('thumb');
                        $images_to_keep = array(
                            $this->src
                        );

                        $image = new TppStoreLibraryFileImage();

                        $image->setFile($this->getImageDirectory() . $this->src);
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
                } else {

                    $base_path = $temp_path['path'];

                    $save_path = $base_path;

                    $directory = new TppStoreDirectoryLibrary();

                    if (false === $directory->createDirectory($save_path)) {
                        TppStoreMessages::getInstance()->addMessage('error', 'Unable to create the mentor directory: ' . $save_path);
                        return false;
                    }

                    $temp_path = $temp_path['path'] . '/';

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
                            }
                        }
                    }

                }




            }
        }

        $this->clearCache();

        return $wpdb->result;

    }


    private function clearCache()
    {
        $c = new TppCacher();
        $c->setCachePath('mentor/' . $this->getMentor()->mentor_id);
        $c->deleteCache();
    }

    private function validate()
    {

        $error = false;

        $this->mentor_bio = trim(strip_tags($this->mentor_bio));


        if (intval($this->store_id) <= 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to detect your session');
            $error = true;
        }

//        if (intval($this->product_id) <= 0) {
//            TppStoreMessages::getInstance()->addMessage('error', 'Unable to detect your session');
//            $error = true;
//        }

        $this->mentor_name = trim($this->mentor_name);

        if ($this->mentor_name == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please enter a mentor name');
            $error = true;
        }

        $this->mentor_company = trim($this->mentor_company);

        $this->mentor_city = trim($this->mentor_city);

        return !$error;
    }

    private function getMentorSessionIds()
    {
        if (intval($this->mentor_id) < 0) {
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT product_id FROM " . TppStoreModelMentor2product::getInstance()->getTable() . " WHERE mentor_id = %d",
                $this->mentor_id
            ),
            OBJECT_K
        );

        $return = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $return[] = $row->product_id;
            }
        }

        return $return;
    }

    public function delete()
    {
        if (intval($this->mentor_id) < 0) {
            return false;
        }

        global $wpdb;


        //get all the mentor sessions associated with this mentor and delete them

        $product_ids = $this->getMentorSessionIds();

        $safe_to_delete = true;

        foreach ($product_ids as $id) {
            $product = new TppStoreModelProduct();
            if ( false == $product->setData(array(
                'product_id'    =>  $id,
                'store_id'      =>  $this->store_id
            ))->delete()) {
                $safe_to_delete = false;
            }
        }

        //delete the files associated with this mentor
        if ($safe_to_delete === true) {
            $dir = new TppStoreDirectoryLibrary();

            $dir->setDirectory($this->getImageDirectory());

            if (false === $dir->deleteDirectory()) {
                TppStoreMessages::getInstance()->addMessage('error', 'Unable to remove the directory: ' . $this->path . '. Please contact us!');
            } else {
                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM " . $this->getTable() . " WHERE mentor_id = %d",
                        $this->mentor_id
                    )
                );

                if ($wpdb->result === false) {
                    TppStoreMessages::getInstance()->addMessage('error', 'Unable to delete your mentor: ' . $wpdb->last_error . '. Please contact us!');
                }

                return $wpdb->result;
            }

        }

        $this->clearCache();

        return false;

    }



}