<?php
/**
 * User: leeparsons
 * Date: 09/01/2014
 * Time: 13:14
 */
 
class TppStoreModelProductDiscount extends TppStoreAbstractModelBase {



    public $discount_id = null;
    public $product_id = null;
    public $discount_type = null;
    public $discount_value = null;

    protected $_table = 'shop_product_discounts';

    public function getTitle()
    {
        return '';
    }

    public function getDescription()
    {
        return '';
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->discount_id = filter_input(INPUT_POST, 'discount_id', FILTER_SANITIZE_NUMBER_INT);
            $this->product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);
            $this->discount_type = filter_input(INPUT_POST, 'discount_type', FILTER_SANITIZE_STRING);
            $this->discount_value = filter_input(INPUT_POST, 'discount_value', FILTER_SANITIZE_NUMBER_FLOAT);
            return true;
        } else {
            return false;
        }
    }

    public function getDiscountIDByProduct()
    {
        if (intval($this->product_id) > 0) {
            global $wpdb;

            $wpdb->get_results(
                "SELECT discount_id FROM " . $this->getTable() . " WHERE product_id = " . intval($this->product_id),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                $this->setData(array('discount_id'  =>  $wpdb->last_result[0]->discount_id));
            }

        }

        return $this;
    }

    public function getDiscountByProduct($reset = true)
    {
        if (intval($this->product_id) > 0) {
            global $wpdb;

            $wpdb->get_row(
                "SELECT * FROM " . $this->getTable() . " WHERE product_id = " . intval($this->product_id),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                $this->setData($wpdb->last_result[0]);
            } else {
                if ($reset === true) {
                    $this->reset();
                }
            }

        } else {
            if ($reset === true) {
                $this->reset();
            }
        }

        return $this;
    }


    public function isSale()
    {
        return $this->discount_type == 'sale';
    }

    public function isFixed()
    {
        return $this->discount_type == 'fixed';
    }


    public function isSocialDiscount()
    {
        return $this->discount_type == 'social';
    }


    public function isDiscounted()
    {
        return !($this->discount_type == '' || is_null($this->discount_type));
    }

    public function getDiscountValue()
    {

        if ($this->discount_type == '') {
            return false;
        } else {
            $v = floatval($this->discount_value);
        }


        if ($v > 0) {
            return $v;
        } else {
            return false;
        }
    }

    public function save()
    {
        if (false === $this->validate()) {
            //delete from this discounts!
            return false;
        }

        global $wpdb;



        if (intval($this->discount_id) == 0) {
            $this->getDiscountIDByProduct(false);
        }

        if (intval($this->discount_id) > 0) {

            if ($this->discount_type == '' || is_null($this->discount_type)) {
                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM " . $this->getTable() . " WHERE discount_id = %d",
                        $this->discount_id
                    )
                );
            } else {
                $wpdb->update(
                    $this->getTable(),
                    array(
                        'discount_type'     =>  $this->discount_type,
                        'discount_value'    =>  $this->discount_value,
                        'product_id'    =>  $this->product_id
                    ),
                    array(
                        'discount_id'   =>  $this->discount_id,
                    ),
                    array(
                        '%s',
                        '%f',
                        '%d'
                    ),
                    '%d'
                );

                if (false === $wpdb->result) {
                    TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your discount. ' . $wpdb->last_error);
                }
            }



            return $wpdb->result;

        } else {

            $wpdb->insert(
                $this->getTable(),
                array(
                    'product_id'        =>  $this->product_id,
                    'discount_type'     =>  $this->discount_type,
                    'discount_value'    =>  $this->discount_value
                ),
                array(
                    '%d',
                    '%s',
                    '%f'
                )
            );

            if ( true === $wpdb->result ) {
                $this->discount_id = $wpdb->insert_id;
                return true;
            } else {
                TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your discount. ' . $wpdb->last_error);
                return false;
            }


        }
    }

    public function validate()
    {

        $error = false;

        if (intval($this->product_id) < 1) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your discount against a product');
        }

        if ($this->discount_type == '' || is_null($this->discount_type)) {
            $error = false;
        } elseif
            (($this->discount_type != '' && !is_null($this->discount_type))
            &&
            floatval($this->discount_value) == 0) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', 'Please enter a discount value.');
        } elseif ($this->discount_type !== 'fixed' && $this->discount_value > 100) {
            $this->discount_value = 100;
        }

        return !$error;

    }

}