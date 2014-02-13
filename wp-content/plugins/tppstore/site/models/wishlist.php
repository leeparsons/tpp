<?php
/**
 * User: leeparsons
 * Date: 07/01/2014
 * Time: 12:31
 */
 
class TppStoreModelWishlist extends TppStoreAbstractModelBase {


    public $user_id = null;
    public $wish_id = null;

    /*
     * Should always be a serialized string.
     * use getUnserializedItems() to get them unserialized
     */
    public $items = null;

    protected $_table = 'shop_wishlists';

    protected $_unserialized_items = null;
    protected $product_id = null;

    public function getTitle()
    {
        return 'Wish list';
    }

    public function getDescription()
    {
        return 'Wish List';
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

    public function addItem()
    {
        $this->getUnserializedItems();

        if (!isset($this->_unserialized_items[$this->product_id])) {
            $this->_unserialized_items[$this->product_id] = $this->product_id;
        }

        $this->items = serialize($this->_unserialized_items);

    }

    public function getUnserializedItems()
    {

        if (is_null($this->_unserialized_items)) {
            if (is_null($this->items) || empty($this->items)) {
                $this->_unserialized_items = array();
            } else {
                $this->_unserialized_items = unserialize($this->items);
            }
        }

        return $this->_unserialized_items;
    }

    public function getSerializedItems()
    {
        if (is_null($this->items) || empty($this->items)) {
            return false;
        } elseif (is_serialized($this->items)) {
            return $this->items;
        } else {
            $this->items = serialize($this->items);
        }

        return $this->items;
    }

    public function getTotalItems()
    {
        if (intval($this->wish_id) < 1) {
            $this->getWishListByUserId();
        }

        return count($this->getUnserializedItems());

    }

    public function getWishListByUserId()
    {
        if (intval($this->user_id) > 0 && intval($this->wish_id) == 0) {

            global $wpdb;

            $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE user_id = %d",
                    intval($this->user_id)
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows == 1) {
                $this->setData($wpdb->last_result[0]);
            }

        } else {
            $this->wish_id = null;
        }

        return $this;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        if (intval($this->wish_id) == 0) {
            //determine if a wish list exists by this user id?

            $this->getWishListByUserId();

            //add this wishlist record to the existing record!
            $this->addItem($this->product_id);


            if (intval($this->wish_id) > 0) {

                //add the items!


                $wpdb->update(
                    $this->getTable(),
                    array(
                        'items' =>  $this->getSerializedItems(),
                    ),
                    array(
                        'wish_id'   =>  $this->wish_id
                    ),
                    array(
                        '%s'
                    ),
                    array(
                        '%d'
                    )
                );


                if ($wpdb->result === false) {
                    TppStoreMessages::getInstance()->addMessage('error', 'Unable to add your product to your wish list: ' . $wpdb->last_error);
                    return false;
                }

                return true;

            } else {
                //we could not find any wish, so it's totally new!
                return $this->saveNew();
            }

        } else {
            return $this->saveNew();
        }


    }

    /*
     * only called from this->save if the data is completely new!
     */
    private function saveNew()
    {

        global $wpdb;

        $wpdb->insert(
            $this->getTable(),
            array(
                'items'     =>  $this->getSerializedItems(),
                'user_id'   =>  $this->user_id
            ),
            array(
                '%s',
                '%d'
            )
        );


        if ($wpdb->result === true) {
            $this->wish_id = $wpdb->insert_id;
            return true;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to create your wish list');
            return false;
        }


    }

    public function validate()
    {

        if (intval($this->product_id) < 1) {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to add this product to your wish list');
        }


        if (intval($this->user_id) < 1) {

            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                TppStoreMessages::getInstance()->addMessage('error', 'Please login to add this product to your wish list');
            } else {
                $this->user_id = $user->user_id;
            }
        }


        return TppStoreMessages::getInstance()->getTotal() == 0;

    }

}