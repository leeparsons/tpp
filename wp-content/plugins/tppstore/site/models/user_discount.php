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
    public $used = 0;

    public $product_ids = array();

    protected $_table = 'shop_user_discounts';


    //increment the number of social share discount uses per product/user
    public function incrementUses()
    {

        if (intval($this->user_id) < 1 || empty($this->product_ids)) {
            return false;
        }

        global $wpdb;

        $sql = "UPDATE " . $this->getTable() . " set uses = uses + 1, used = 1 WHERE user_id = " . intval($this->user_id) . " AND product_id IN (" . implode(',', $this->product_ids) . ") AND used = 0";

        $wpdb->query($sql);

    }


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
                AND (max_uses > uses OR max_uses = 0) AND used = 0
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
                "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d AND user_id = %d AND used = 0",
                array(
                    $this->product_id,
                    $this->user_id
                )
            )
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

            if ($this->max_uses == 0 || $this->max_uses > $this->uses) {
                $wpdb->update(
                    $this->getTable(),
                    array(
                        'uses'          =>  $this->uses,
                        'used'          =>  $this->used
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