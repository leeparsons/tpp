<?php

class TppStoreModelAdminOrders extends TppStoreAbstractModelResource {

    protected $_table = 'shop_orders';

    public function getYears()
    {

        global $wpdb;

        $years = $wpdb->get_col(
            "SELECT YEAR(FROM_UNIXTIME(order_date)) AS year FROM " . $this->getTable() . "
            GROUP BY year"
        );

        return $years;

    }

    public function getIncomeByYear($year = false)
    {

        $year = intval($year);

        if ($year == 0) {
            $year = date('Y');
        }

        global $wpdb;

        $wpdb->query(
            "SELECT
            commission AS commission,
            total AS total,
            currency,
            order_date,
            exchange_rates
            FROM " . TppStoreModelOrder::getInstance()->getTable() . "
            WHERE
            status = 'complete' AND
            YEAR(FROM_UNIXTIME(order_date)) = $year
            ORDER BY order_date DESC
            "
        );

        return $wpdb->last_result;
    }

    public function getBestSellers()
    {
        global $wpdb;

        $wpdb->query(
            "SELECT
            oi.quantity,
            product_id,
            o.order_id,
            oi.product_name AS product_title,
            oi.product_type,
            commission / exchange_rates AS commission,
            total / exchange_rates AS total,
            currency,
            order_date
            FROM " . TppStoreModelOrder::getInstance()->getTable() . " AS o
            LEFT JOIN shop_order_items AS oi ON o.order_id = oi.order_id
            WHERE
            status = 'complete'

            ORDER BY total DESC, o.commission DESC
            "
        );


        //convert all the currencies into one base currency if I can!

        $products = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {

                if ($row->currency !== 'GBP') {
                    $rates = $row->exchange_rates;

                        $row->alternate_currency = $row->currency;
                        $row->commission = $row->commission;// / $rates[$row->currency];
                        $row->alternate_currency_total = $row->total;
                        $row->alternate_currency_commission = $row->total;
                        $row->total = $row->total;// / $rates[$row->currency];
                        $row->currency = 'GBP';


                } else {
                    $row->alternate_currency = $row->currency;
                    $row->alternate_currency_total = 0;
                    $row->alternate_currency_commission = 0;
                }

                if (!isset($products)) {
                    $products = array();
                }

                if (!isset($products[$row->product_id])) {
                    $products[$row->product_id] = $row;
                } else {
                    $products[$row->product_id]->quantity += $row->quantity;
                    $products[$row->product_id]->commission += $row->commission;
                    $products[$row->product_id]->total += $row->total;
                    $products[$row->product_id]->alternate_currency_total += $row->alternate_currency_total;
                    $products[$row->product_id]->alternate_currency_commission += $row->alternate_currency_commission;
                }


            }
        }

        //$return = array();


usort($products, function($a, $b) {
    return $a->total < $b->total;
});
   //     krsort($return);

        return $products;
    }

    public function loyaltyReport($count = false)
    {
        global $wpdb;

        $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

        if (trim($status) != '') {
            $where = " WHERE status = '" . esc_sql($status) . "'";
        } else {
            $where = '';
        }

        if ($count === true) {

            $c = $wpdb->get_var(
                "SELECT COUNT(order_id) FROM shop_orders $where
                LEFT JOIN shop_users AS U ON U.user_id = O.user_id

                GROUP BY U.user_id, currency, status
"
            );

            return $c;

        }

        $page = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT);

        if (intval($page) == 0) {
            $page = 1;
        }


        $limit = (($page - 1) * 20) . ', 20';


        //divide by exchange rates to convert into GBP
        $wpdb->query(
            "SELECT

              O.order_id,

              COUNT(O.order_id) AS orders,

              AVG(total / exchange_rates) AS gbp_avg,
              currency, SUM(total / exchange_rates) AS gbp_total,

              AVG(total) AS avg,
              SUM(total) AS total,

              CONCAT(U.first_name, ' ', U.last_name) AS name, status, U.user_id

              FROM shop_orders AS O

LEFT JOIN shop_users AS U ON U.user_id = O.user_id

$where

GROUP BY U.user_id, currency, status

ORDER BY gbp_total DESC, status

LIMIT $limit
",
            OBJECT_K
        );



        $rows = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $rows[] = $row;
            }
        }

        return $rows;

    }


    public function getLineItemsByOrders($order_ids = array())
    {

        $line_items = array();

        if (!empty($order_ids)) {
            global $wpdb;


            $wpdb->query(
                "SELECT * FROM shop_order_items WHERE order_id IN (" . implode(',', $order_ids) . ") ORDER BY order_id",
                OBJECT_K
            );


            if ($wpdb->num_rows > 0) {
                foreach ($wpdb->last_result as $row) {
                    if (!isset($line_items[$row->order_id])) {
                        $line_items[$row->order_id] = array();
                    }
                    $line_items[$row->order_id][] = $row;
                }
            }

        }


        return $line_items;
    }


}
