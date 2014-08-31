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
    public $tax = 0;
    public $discount = 0;
    public $commission = 0;
    public $user_id = 0;
    public $exchange_rates = null;
    public $ref = '';
    public $message = null;

    public $order_type = 'default';

    public $currency = 'GBP';

    protected $_payment_model = null;

    protected $_table = 'shop_orders';

    protected $order_info_model = null;

    public function __construct()
    {

        $this->_payment_model = TppStoreModelPayment::getInstance();
    }


    public function getOrderInfo()
    {
        if (is_null($this->order_info_model)) {
            $this->order_info_model = new TppStoreModelOrderInfo();
            $this->order_info_model->getOrderInfoByOrder($this->order_id);
        }

        return $this->order_info_model;
    }



//    public function getData()
//    {
//        if (is_serialized($this->data)) {
//            return unserialize($this->data);
//        } else {
//            return $this->data;
//        }
//    }

    public function serializeExchangeRates()
    {
        if (!is_serialized($this->exchange_rates)) {
            $this->exchange_rates = serialize($this->exchange_rates);
        }
    }

    public function unserializeExchangeRates()
    {
        if (is_serialized($this->exchange_rates)) {
            $this->exchange_rates = unserialize($this->exchange_rates);
        }
    }

    public function getSeoTitle()
    {
        return 'Order';
    }

    public function getSeoDescription()
    {
        return 'Order';
    }

    public function getPaymentModel()
    {
        return $this->_payment_model;
    }


    public function getFormattedTotal($total = false, $override_currency = false)
    {

        if (false === $total) {
            $total = $this->total;
        }
        return $this->getFormattedCurrency(true, $override_currency) . number_format($total, 2);


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

    public function getOrdersReceivedByStore($page = 1, $count = false, $status = 'complete')
    {

        $return = array();

        if (intval($this->store_id) > 0) {


            global $wpdb;


            if (true === $count) {
                $c = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(order_id) AS c FROM " . $this->getTable() . " WHERE store_id = %d AND status = %s",
                        array(
                            $this->store_id,
                            $status
                        )
                    )
                );

                return $c;
            }

            if ($page == 0) {
                $page = 1;
            }

            $start = (($page-1) * 20);

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT o.*, COUNT(h.order_id) AS count_items FROM " . $this->getTable() . " AS o
                LEFT JOIN " . TppStoreModelOrderItems::getInstance()->getTable() . " AS h ON h.order_id = o.order_id
                WHERE o.store_id = %d
                AND o.status = %s
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT $start, 20
                ",
                    $this->store_id,
                    $status
                ),
                OBJECT_K
            );



            if ($wpdb->num_rows > 0) {


                foreach ($wpdb->last_result as $row) {
                    $return[$row->order_id] = new TppStoreModelOrder();
                    $return[$row->order_id]->setData($row);
                }
            }

        }


        return $return;
    }

    public function getOrdersByUser($page = 1, $count = false, $status = 'complete')
    {

        if (intval($this->user_id) == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Please login to view your orders');
            return false;
        }

        global $wpdb;

        if (true === $count) {
            $c = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(order_id) AS c FROM " . $this->getTable() . " WHERE user_id = %d AND status = %s",
                    array(
                        $this->user_id,
                        $status
                    )
                )
            );

            return $c;
        }

        if ($page == 0) {
            $page = 1;
        }

        $start = (($page-1) * 20);

        $wpdb->query(
            $wpdb->prepare(
                "SELECT o.*, s.store_name, SUM(h.quantity) AS count_items FROM " . $this->getTable() . " AS o

                LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = o.store_id
                LEFT JOIN " . TppStoreModelOrderItems::getInstance()->getTable() . " AS h ON h.order_id = o.order_id
                WHERE o.user_id = %d
                AND o.status = %s
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT $start, 20
                ",
                array(
                    $this->user_id,
                    $status
                )

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


    public function getOrderByRef()
    {

        if (is_null($this->ref)) {
            $this->reset();
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE ref = %s",
                $this->ref
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
                    'tax'           =>  $this->tax,
                    'status'        =>  $this->status,
                    'store_id'      =>  $this->store_id,
                    'order_date'    =>  $this->order_date,
                    //'data'          =>  is_serialized($this->data)?$this->data:serialize($this->data),
                    'user_id'       =>  $this->user_id,
                    'ref'           =>  trim($this->ref) == ''?$this->generateRef():$this->ref,
                    'currency'      =>  $this->currency,
                    'exchange_rates'=>  $this->exchange_rates,
                    'message'       =>  $this->message,
                    'order_type'    =>  $this->order_type
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
                    "%s",
                    "%s",
                    "%s",
                    "%s",
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
                    'tax'           =>  $this->tax,
                    'status'        =>  $this->status,
                    'store_id'      =>  $this->store_id,
                    'order_date'    =>  $this->order_date,
                    //'data'          =>  is_serialized($this->data)?$this->data:serialize($this->data),
                    'user_id'       =>  $this->user_id,
                    'ref'           =>  trim($this->ref) == ''?$this->generateRef():$this->ref,
                    'currency'      =>  $this->currency,
                    'exchange_rates'=>  $this->exchange_rates,
                    'message'       =>  $this->message,
                    'order_type'    =>  $this->order_type
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
                    "%s",
                    "%s",
                    "%s",
                    "%s",
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

        if (is_null($this->status) || $this->status == '') {
            $this->status = 'pending';
        }

        if (trim($this->ref) == '') {
            $this->ref = $this->generateRef();
        }

        if (!is_null($this->exchange_rates)) {
            //$this->serializeExchangeRates();
        } else {
            $this->exchange_rates = 1;
        }

        return !$error;
    }


    public function hasExpired() {

        if ($this->order_type == 'guest_checkout' && $this->order_date < strtotime('-7days')) {
            return true;
        } else {
            return false;
        }
    }

    private function generateRef()
    {
        return uniqid('odr_');
    }
}