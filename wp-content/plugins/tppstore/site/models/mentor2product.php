<?php
/**
 * User: leeparsons
 * Date: 13/02/2014
 * Time: 23:28
 */
 

class TppStoreModelMentor2product extends TppStoreAbstractModelResource {

    public $product_id = null;
    public $mentor_id = null;

    protected $_table = 'shop_p2m';

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->mentor_id = filter_input(INPUT_POST, 'mentor', FILTER_SANITIZE_NUMBER_INT);
            $this->product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
            return true;
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

        //delete from this table where product id is this product!

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->getTable() . " WHERE product_id = %d",
                $this->product_id
            )
        );

        $wpdb->replace(
            $this->getTable(),
            array(
                'product_id'    =>  $this->product_id,
                'mentor_id'     =>  $this->mentor_id
            ),
            array(
                '%d',
                '%d'
            )
        );

        if ($wpdb->result === false) {
            TppStoreMessages::getInstance()->addMessage('error', $wpdb->last_error);
        }

        return $wpdb->result;

    }

    public function validate($mentor_only = false)
    {

        $error = false;

        if ($mentor_only === false) {
            if (intval($this->product_id) == 0) {
                TppStoreMessages::getInstance()->addMessage('error', 'Unable to determine your product id on the mentor association');
                $error = true;
            }
        }

        if (intval($this->mentor_id) == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Please select a mentor');
            $error = true;
        }

        return !$error;
    }


}