<?php
/**
 * User: leeparsons
 * Date: 04/01/2014
 * Time: 21:46
 */

class TppStoreModelUserDiscount extends TppStoreAbstractModelResource {


    public $message = '';

    public $product_id = null;
    public $user_id = null;
    public $max_uses = 0;
    public $uses = 0;

    protected $_table = 'shop_user_discounts';

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);

            return true;

        } else {
            return false;
        }
    }

    /*
     * This gets all the discounts for this user that are not expired/maxed out
     * return an array of discounts against products with the discount value as a percentage
     */
    public function getDiscounts()
    {

        $discounts = array();

        if (true === $this->getUser()) {


            global $wpdb;

            $wpdb->query(
                "SELECT d.product_id, d.discount_value FROM " . $this->getTable() . " AS d

                INNER JOIN " . TppStoreModelProductDiscount::getInstance()->getTable() . " AS pd ON pd.product_id = d.product_id

                WHERE user_id = " . $this->user_id . "
                AND max_uses > uses
                ",
                OBJECT_K
            );


            if ($wpdb->num_rows > 0) {
                foreach ($wpdb->last_result as $row) {
                    $discounts[$row->product_id] = $row->discount_value / 100;
                }
            }

        }

        return $discounts;


    }

    public function getDiscountUseByUserAndProduct()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;


        $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d AND user_id = %d",
                array(
                    $this->product_id,
                    $this->user_id
                )
            ),
            OBJECT_K
        );

        if ($wpdb->num_rows == 1) {
            $this->setData($wpdb->last_result[0]);
            return true;
        } else {
            return false;
        }
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;


        //determine if a discount has already been applied for this product?
        if (true === $this->getDiscountUseByUserAndProduct()) {

            if ($this->max_uses > $this->uses) {
                $wpdb->update(
                    $this->getTable(),
                    array(
                        'uses'          =>  $this->uses
                    ),
                    array(
                        'product_id'    =>  $this->product_id,
                        'user_id'       =>  $this->user_id
                    ),
                    array(
                        '%d'
                    ),
                    array(
                        '%d',
                        '%d'
                    )
                );

                return $wpdb->result;
            } else {
                $this->message = 'You have already had a discount applied for this product.';
                return false;
            }



        } else {
            //assume new!

            $wpdb->insert(
                $this->getTable(),
                array(
                    'product_id'    =>  $this->product_id,
                    'user_id'       =>  $this->user_id,
                    'max_uses'      =>  $this->max_uses,
                    'uses'          =>  $this->uses
                ),
                array(
                    '%d',
                    '%d',
                    '%d',
                    '%d'
                )
            );

            return $wpdb->result;
        }



    }


    private function getUser()
    {
        $user = TppStoreControllerUser::getInstance()->loadUserFromSession();

        if (false !== $user) {
            $this->user_id = $user->user_id;
            return true;
        } else {
            return false;
        }
    }


    private function validate()
    {
        if (intval($this->product_id) < 1) {
            return false;
        }

        if (intval($this->user_id) < 1) {
            return $this->getUser();
        }

        return true;

    }

}