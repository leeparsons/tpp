<?php
/**
 * User: leeparsons
 * Date: 29/12/2013
 * Time: 12:56
 */
 

class TppStoreModelPayment extends TppStoreModelCurrency {

    public $payment_id = null;
    public $amount = 0;
    public $order_id = 0;
    public $status = null;
    public $user_data = null;
    public $gateway_data = null;
    public $gateway = null;
    public $payment_date = null;

    public $message = null;

    protected $_table = 'shop_order_payments';

    public function getGatewayData()
    {
        if (is_serialized($this->gateway_data)) {
            return unserialize($this->gateway_data);
        } else {
            return $this->gateway_data;
        }
    }

    public function getPaymentDate()
    {
        if (!is_null($this->payment_date)) {
            return date('jS F, Y @ H:i:s', $this->payment_date);
        }

        return '';
    }

    public function getFormattedTotal($with_currency = true, $override_currency = false)
    {

        if (true === $with_currency) {
            return $this->getFormattedCurrency(true, $override_currency) . number_format($this->amount, 2);
        } else {
            return number_format($this->amount, 2);
        }

    }

    public function getPaymentById()
    {

        if (intval($this->payment_id) < 1) {
            $this->reset();
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE payment_id = %d",
                $this->payment_id
            ),
            OBJECT_K
        );

        if ($wpdb->num_rows == 1) {
            $this->setData($wpdb->last_result[0]);
        } else {
            $this->reset();
        }
    }


    public function getPaymentsByOrder()
    {
        $payments = array();

        if (intval($this->order_id) > 0) {


            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE order_id = %d",
                    $this->order_id
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                foreach ($wpdb->last_result as $row) {
                    $payments[$row->payment_id] = new TppStoreModelPayment();
                    $payments[$row->payment_id]->setData($row);
                }
            }

        }

        return $payments;

    }


    public function save()
    {

        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        if (intval($this->payment_id) > 0) {

            $wpdb->update(
                $this->getTable(),
                array(
                    'amount'        =>  $this->amount,
                    'order_id'      =>  $this->order_id,
                    'status'        =>  $this->status,
                    'user_data'     =>  is_serialized($this->user_data)?$this->user_data:serialize($this->user_data),
                    'gateway_data'  =>  is_serialized($this->gateway_data)?$this->gateway_data:serialize($this->gateway_data),
                    'payment_date'  =>  $this->payment_date,
                    'gateway'       =>  $this->gateway,
                    'message'       =>  $this->message
                ),
                array(
                    'payment_id'    =>  $this->payment_id
                ),
                array(
                    "%f",
                    "%d",
                    "%s",
                    "%s",
                    "%s",
                    "%s",
                    "%s",
                    "%s"
                ),
                array(
                    "%d"
                )
            );


        } else {

            $wpdb->insert(
                $this->getTable(),
                array(
                    'amount'        =>  $this->amount,
                    'order_id'      =>  $this->order_id,
                    'status'        =>  $this->status,
                    'user_data'     =>  is_serialized($this->user_data)?$this->user_data:serialize($this->user_data),
                    'gateway_data'  =>  is_serialized($this->gateway_data)?$this->gateway_data:serialize($this->gateway_data),
                    'payment_date'  =>  $this->payment_date,
                    'gateway'       =>  $this->gateway,
                    'message'       =>  $this->message
                ),
                array(
                    "%f",
                    "%d",
                    "%s",
                    "%s",
                    "%s",
                    "%s",
                    "%s",
                    "%s"
                )
            );

            if (true === $wpdb->result && $wpdb->rows_affected == 1) {
                $this->payment_id = $wpdb->insert_id;
            }

        }

        if (true === $wpdb->result) {

            return true;
        } else {
            TppStoreMessages::getInstance()->addMessage('error', array('payment_data'   =>  'There was an issue with your payment: ' . $wpdb->last_error));
        }
    }

    public function validate()
    {

        $error = false;

        if (is_null($this->gateway) || empty($this->gateway)) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('payment_gateway'    =>  'Could not determine a valid payment gateway'));
        }

        if (is_null($this->order_id) || intval($this->order_id) <= 0) {
            $error = true;
            TppStoreMessages::getInstance()->addMessage('error', array('payment_order'    =>  'Could not determine a valid order'));
        }

        if (is_null($this->status) || empty($this->status)) {
            $this->status = 'pending';
        }

        if (empty($this->payment_date) || is_null($this->payment_date)) {
            $this->payment_date = time();
        }

        return !$error;
    }

}