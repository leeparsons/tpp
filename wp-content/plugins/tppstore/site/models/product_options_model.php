<?php
/**
 * User: leeparsons
 * Date: 06/12/2013
 * Time: 22:09
 */
 

class TppStoreModelProductOptions extends TppStoreAbstractModelResource {

    public $product_id = null;
    public $options = array();
    public $new_options = array();

    protected $_table = 'shop_product_options';

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

           $this->options = filter_input(INPUT_POST, 'product_option', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            if (!$this->options) {
                $this->options = array();
            } else {
                foreach ($this->options as &$option) {
                    $option['name'] = urldecode($option['name']);
                }
            }

           $this->new_options = filter_input(INPUT_POST, 'product_option_new', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            if (!$this->new_options) {
                $this->new_options = array();
            } else {
                foreach ($this->new_options as &$option) {
                    $option['name'] = urldecode($option['name']);
                }
            }


        } else {
            return false;
        }
    }

    public function getOptions()
    {
        if (!$this->validate()) {
            return false;
        }

        //get the options saved for this product!

        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE product_id = " . $this->product_id . "
                ORDER BY option_price ASC",
                OBJECT_K
            )
        );

        if ($wpdb->num_rows > 0) {
            return $rows;
        }

        return false;

    }

    /*
     * custom function to get a multi array for product options in a list to determine the smallest price
     */
    public function getOptionsByProductIds($product_ids = array()) {

    }

    public function getOptionById()
    {
        if (intval($this->option_id) <= 0) {
            $this->reset();
        } else {
            global $wpdb;
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE option_id = %d",
                    $this->option_id
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

    public function save()
    {

        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        $error = false;

        if (!empty($this->options)) {
            $option_ids = array();
            $sql_array = array();
            $sql_string = '';
            foreach ($this->options as $option) {
                $option_ids[] = $option['option_id'];
                $sql_array[] = $option['option_id'];
                $sql_array[] = $option['name'];
                $sql_array[] = $option['price'];
                $sql_array[] = $option['availability'];
                $sql_array[] = $this->product_id;

                $sql_string .= $sql_string == ''?'(%d, %s, %s, %s, %d)':',(%d, %s, %s, %s, %d)';
            }


            //firstly delete any records that are not being saved!
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM " . $this->getTable() . " WHERE product_id = %d AND option_id NOT IN (%s)",
                    array(
                        $this->product_id,
                        implode(',', $option_ids)
                    )
                )
            );



            //now update the table!
            $wpdb->query(
                $wpdb->prepare(
                    "REPlACE INTO " . $this->getTable() . " (option_id, option_name, option_price, option_quantity_available, product_id) VALUES " . $sql_string,
                    $sql_array
                )
            );


            if ($wpdb->rows_affected == 0) {
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', array('product_options'    =>  'There was an error saving your product options.'));
            }

        }

        if (!empty($this->new_options)) {
            $sql_array = array();
            $sql_string = '';
            foreach ($this->new_options as $option) {
                $sql_array[] = $option['name'];
                $sql_array[] = $option['price'];
                $sql_array[] = $option['availability'];
                $sql_array[] = $this->product_id;
                $sql_string .= $sql_string == ''?'(%s,%s,%s, %d)':',(%s,%s,%s, %d)';
            }

            //now insert into the table!
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO " . $this->getTable() . " (option_name, option_price, option_quantity_available, product_id) VALUES " . $sql_string,
                    $sql_array
                )
            );

            if ($wpdb->rows_affected == 0) {
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', array('product_options'    =>  'There was an error saving your product options.'));
            }

        }

        return !$error;


    }

    public function validate()
    {

        if (intval($this->product_id) <= 0 || !is_array($this->new_options) || !is_array($this->options)) {
            TppStoreMessages::getInstance()->addMessage('error', array('product_options'    =>  'Unable to save your product options'));
            return false;
        }

        return true;

    }

}