<?php
/**
 * User: leeparsons
 * Date: 29/12/2013
 * Time: 11:31
 */
 
class TppStoreModelOrder extends TppStoreModelCurrency {



    public $order_id = null;
    public $order_date = null;
    public $status = 'failed';

    public $gateway = null;
    public $store_id = null;
    public $total = 0;
    public $tax_rate = 0;
    public $discount = 0;
    public $commission = 0;
    public $user_id = 0;

    public $ref = '';

    protected $_payment_model = null;

    protected $_table = 'shop_orders';

    public function __construct()
    {

        $this->_payment_model = TppStoreModelPayment::getInstance();
    }


//    public function getData()
//    {
//        if (is_serialized($this->data)) {
//            return unserialize($this->data);
//        } else {
//            return $this->data;
//        }
//    }

    public function getTitle()
    {
        return 'Order';
    }

    public function getDescription()
    {
        return 'Order';
    }

    public function getPaymentModel()
    {
        return $this->_payment_model;
    }


    public function getFormattedTotal($total = false)
    {

        if (false === $total) {
            $total = $this->total;
        }
        return $this->getFormattedCurrency() . number_format($total, 2);


    }

    public function getOrderDate($with_time = false)
    {


        if (!is_null($this->order_date)) {

            if (true === $with_time) {
                return date('jS F, Y @ H:i:s', $this->order_date);
            }

            return date('jS F', $this->order_date);
        } else {
            return '';
        }
    }

    public function getOrdersByUser($page = 1, $count = false)
    {

        if (intval($this->user_id) == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Please login to view your orders');
            return false;
        }

        global $wpdb;

        if (true === $count) {
            $c = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(order_id) AS c FROM " . $this->getTable() . " WHERE user_id = %d",
                    $this->user_id
                )
            );

            return $c;
        }

        $start = (($page-1) * 20) - $page + 1;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT o.*, s.store_name, COUNT(h.order_id) AS count_items FROM " . $this->getTable() . " AS o

                LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = o.store_id
                LEFT JOIN " . TppStoreModelOrderItems::getInstance()->getTable() . " AS h ON h.order_id = o.order_id
                WHERE o.user_id = %d
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT $start, 20
                ",
                $this->user_id
            ),
            OBJECT_K
        );



        if ($wpdb->num_rows > 0) {

            $return = array();

            foreach ($wpdb->last_result as $row) {
                $return[$row->order_id] = new TppStoreModelOrder();
                $return[$row->order_id]->setData($row);
            }

            return $return;
        } else {
            return array();
        }

    }

    public function getOrderById()
    {

        if (intval($this->order_id) < 1) {
            $this->reset();
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE order_id = %d",
                $this->order_id
            ),
            OBJECT_K
        );

        if ($wpdb->num_rows == 1) {
            $this->setData($wpdb->last_result[0]);
        } else {
            $this->reset();
        }
        return $this;
    }

    public function save()
    {

        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        if (intval($this->order_id) > 0) {
            $wpdb->update(
                $this->getTable(),
                array(
                    'total'         =>  $this->total,
                    'commission'    =>  $this->commission,
                    'discount'      =>  $this->discount,
                    'tax_rate'      =>  $this->tax_rate,
                    'status'        =>  $this->status,
                    'store_id'      =>  $this->store_id,
                    'order_date'    =>  $this->order_date,
                    //'data'          =>  is_serialized($this->data)?$this->data:serialize($this->data),
                    'user_id'       =>  $this->user_id,
                    'ref'           =>  trim($this->ref) == ''?$this->generateRef():$this->ref
                ),
                array(
                    'order_id'  =>  $this->order_id
                ),
                array(
                    "%f",
                    "%f",
                    "%f",
                    "%f",
                    "%s",
                    "%d",
                    "%d",
                    //"%s",
                    "%d",
                    "%s"
                ),
                array(
                    '%d'
                )
            );

        } else {
            $wpdb->insert(
                $this->getTable(),
                array(
                    'total'         =>  $this->total,
                    'commission'    =>  $this->commission,
                    'discount'      =>  $this->discount,
                    'tax_rate'      =>  $this->tax_rate,
                    'status'        =>  $this->status,
                    'store_id'      =>  $this->store_id,
                    'order_date'    =>  $this->order_date,
                    //'data'          =>  is_serialized($this->data)?$this->data:serialize($this->data),
                    'user_id'       =>  $this->user_id,
                    'ref'           =>  trim($this->ref) == ''?$this->generateRef():$this->ref
                ),
                array(
                    "%f",
                    "%f",
                    "%f",
                    "%f",
                    "%s",
                    "%d",
                    "%d",
                    //"%s",
                    "%d",
                    "%s"
                )
            );

            if ($wpdb->result && $wpdb->insert_id > 0) {
                $this->order_id = $wpdb->insert_id;
            }
        }

        if ($wpdb->result) {
            return true;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', array('Could not create your order: ' . $wpdb->last_error));
            return false;
        }


    }

    public function validatePurchaseByUser($user_id = 0)
    {

        if (intval($user_id) < 1 || intval($this->product_id) < 1) {
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT COUNT(o.order_id) AS c FROM " . $this->getTable() . " AS o
                INNER JOIN " . TppStoreModelOrderItems::getInstance()->getTable() . " AS oi ON o.order_id = oi.order_id
                WHERE o.user_id = %d AND oi.product_id = %d",
                array(
                    $user_id,
                    $this->product_id
                )
            ),
            OBJECT_K
        );

        if ($wpdb->result === false || $wpdb->last_result[0]->c == 0) {
            return false;
        } else {
            return $this;
        }


    }

    public function validate() {


        $error = false;

        if (is_null($this->order_date)) {
            $this->order_date = time();
        }

        if (intval($this->store_id) <= 0) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('order_store'    =>  'Could not find the store from which you are purchasing from'));
        }

        if (intval($this->user_id) == 0) {
            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                TppStoreMessages::getInstance()->addMessage('error', array('order_user' =>  'Please login to place this order'));
            } else {
                $this->user_id = $user->user_id;
            }
        }


        if (trim($this->ref) == '') {
            $this->ref = $this->generateRef();
        }

        return !$error;
    }


    private function generateRef()
    {
        return uniqid('odr_');
    }
}