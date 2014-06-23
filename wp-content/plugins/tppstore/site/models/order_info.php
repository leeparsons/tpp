<?php
/**
 * User: leeparsons
 * Date: 06/06/2014
 * Time: 21:27
 */
 
class TppStoreModelOrderInfo extends TppStoreAbstractModelResource {


    public $order_info_id = 0;
    public $order_id = 0;
    public $data = null;

    protected $_table = 'shop_order_info';

    public function getData()
    {
        if (is_serialized($this->data)) {
            $this->data = unserialize($this->data);

            if (is_array($this->data)) {
                $this->data = (object)$this->data;
            }
        }

        return $this->data;
    }

    public function getOrderInfoByOrder($order_id = 0)
    {

        if (intval($order_id) < 0) {
            $this->reset();
        } else {
            global $wpdb;


            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE order_id = %d",
                    array(
                        $order_id
                    )
                )
            );

            if ($wpdb->num_rows == 1) {
                $this->setData($wpdb->last_result[0]);
            }

        }

        return $this;


    }

    public function save()
    {
        if ($this->validate()) {
            global $wpdb;

            if (intval($this->order_info_id) > 0) {
                $wpdb->replace(
                    $this->getTable(),
                    array(
                        'order_id'      =>  $this->order_id,
                        'order_info_id' =>  $this->order_info_id,
                        'data'          =>  $this->data
                    ),
                    array(
                        '%d',
                        '%d',
                        '%s'
                    )
                );
            } else {
                $wpdb->insert(
                    $this->getTable(),
                    array(
                        'order_id'      =>  $this->order_id,
                        'data'          =>  $this->data
                    ),
                    array(
                        '%d',
                        '%s'
                    )
                );

                if ($wpdb->last_result === true) {
                    $this->order_info_id = $wpdb->insert_id;
                }

            }




        }
    }


    public function validate()
    {
        if (intval($this->order_id) > 0 && !is_null($this->data)) {

            if (!is_serialized($this->data)) {
                $this->data = serialize($this->data);
            }

            return true;
        }

        return false;
    }

}