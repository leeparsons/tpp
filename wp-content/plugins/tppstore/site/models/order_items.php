<?php

class TppStoreModelOrderItems extends TppStoreAbstractModelResource {

    public $order_id = 0;
    public $products = array();
    private $line_items = array();


    private $_loaded = false;

    protected $_table = 'shop_order_items';



    public function getLineItems($load = false)
    {

        if (!empty($this->products)) {
            foreach ($this->products as $product) {
                $this->line_items[$product->product_id] = new TppStoreModelOrderItem();
                $this->line_items[$product->product_id]->setData(
                    array(
                        'product_id'    =>  $product->product_id,
                        'order'         =>  $this->order_id,
                        'data'          =>  serialize($product)
                    )
                );
            }
        } elseif (true === $load) {
            if (intval($this->order_id) > 0) {
                global $wpdb;

                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT product_id, data FROM " . $this->getTable() . " WHERE order_id = %d",
                        $this->order_id
                    ),
                    OBJECT_K
                );

                if ($wpdb->num_rows > 0) {
                    foreach ($wpdb->last_result as $row) {
                        $this->line_items[$row->product_id] = new TppStoreModelOrderItem();
                        $this->line_items[$row->product_id]->setData(array(
                            'product_id'    =>  $row->product_id,
                            'order'         =>  $this->order_id,
                            'data'          =>  $row->data
                        ))->setData(unserialize($row->data));
                    }
                }
            }
        }



        return $this->line_items;
    }

    public function save()
    {
        if (false === $this->validate()) {
            return false;
        }

        global $wpdb;

        $sql = "INSERT IGNORE INTO " . $this->getTable() . ' (order_id, product_id, store_id, product_type, product_name, quantity, data) VALUES ';

        $insert = array();

        $ids = array();

        foreach ($this->products as $product) {
            $ids[] = intval($product->product_id);
            $insert[] = "(" . intval($this->order_id) .
                    "," . intval($product->product_id) .
                    "," . intval($product->store_id) .
                    "," . intval($product->product_type) .
                    ",'" . esc_sql($product->product_title) . "'" .
                    "," . esc_sql($product->order_quantity) .
                    ",'" . esc_sql(serialize($product)) . "')";

        }

        $sql = $sql . implode(',', $insert);

        $wpdb->query("DELETE FROM " . $this->getTable() . " WHERE product_id NOT IN (" . implode(",", $ids) . ") AND order_id = " . intval($this->order_id));

        //delete from the order items currently existing that do not exist in this set!

        $wpdb->query($sql);

        if (false === $wpdb->result) {
            TppStoreMessages::getInstance()->addMessage('error', array('cart-success'  =>  'Unable to add your cart items to the database. Please contact us with the following: ' . $wpdb->last_error));
        }

        return $this;

    }



    public function validate()
    {
        if (intval($this->order_id) < 1) {
            return false;
        }
    }

}