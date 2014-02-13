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


    private $rating = 0;

    protected $_table = 'shop_product_mentors';

    protected $_specialism_model = null;

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

    public function readFromPost()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->mentor_name = filter_input(INPUT_POST, 'mentor_name', FILTER_SANITIZE_STRING);
            $this->mentor_company = filter_input(INPUT_POST, 'mentor_company', FILTER_SANITIZE_STRING);
            $this->mentor_city = filter_input(INPUT_POST, 'mentor_city', FILTER_SANITIZE_STRING);
            $this->mentor_country = filter_input(INPUT_POST, 'mentor_country', FILTER_SANITIZE_STRING);
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
                    "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d",
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

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;



        if (intval($this->mentor_id) < 1 &&  false === $this->getMentorByProduct(true, true)) {
            //new

            $wpdb->insert(
                $this->getTable(),
                array(
                    'product_id'        =>  $this->product_id,
                    'mentor_name'       =>  $this->mentor_name,
                    'mentor_company'    =>  $this->mentor_company,
                    'mentor_city'       =>  $this->mentor_city,
                    'mentor_country'    =>  $this->mentor_country
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );

            if (true === $wpdb->result) {
                $this->mentor_id = $wpdb->insert_id;
            }

        } else {
            //existing
            $wpdb->update(
                $this->getTable(),
                array(
                    'mentor_name'       =>  $this->mentor_name,
                    'mentor_company'    =>  $this->mentor_company,
                    'mentor_city'       =>  $this->mentor_city,
                    'mentor_country'    =>  $this->mentor_country
                ),
                array(
                    'product_id'        =>  $this->product_id,
                    'mentor_id'         =>  $this->mentor_id
                ),
                array(
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

        /*
         * save mentor specialisms - no longer required but keeping just in case
         $res = $this->getSpecialism()->readFromPost();

        if (false === $wpdb->result) {
            TppStoreMessages::getInstance()->addMessage('error', 'There was an error saving your mentor session: ' . $wpdb->last_error);
        } elseif ($res === true) {
            $this->getSpecialism()->save();
        }

        */

        return $wpdb->result;

    }



    private function validate()
    {

        $error = false;

        if (intval($this->product_id) <= 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to detect your session');
            $error = true;
        }

        if (trim($this->mentor_name) == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please enter a mentor name');
            $error = true;
        }

        if (trim($this->mentor_company) == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please enter a mentor company');
            $error = true;
        }


        return !$error;
    }
}