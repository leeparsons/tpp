<?php

class TppStoreAdminModelOrders extends TppStoreAbstractModelResource {

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
            commission AS commission,
            total AS total,
            currency,
            order_date,
            exchange_rates
            FROM " . TppStoreModelOrder::getInstance()->getTable() . " AS o
            LEFT JOIN shop_order_items AS oi ON o.order_id = oi.order_id
            WHERE
            status = 'complete'

            ORDER BY o.currency, o.commission DESC
            "
        );

        //convert all the currencies into one base currency if I can!

        $products = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {

                if ($row->currency !== 'GBP') {
                    if (false !== ($rates = unserialize($row->exchange_rates)) && isset($rates[$row->currency])) {
                        $row->alternate_currency = $row->currency;
                        $row->commission = $row->commission / $rates[$row->currency];
                        $row->alternate_currency_total = $row->total;
                        $row->alternate_currency_commission = $row->total;
                        $row->total = $row->total / $rates[$row->currency];
                        $row->currency = 'GBP';

                    } else {
                        $row->alternate_currency = $row->currency;
                        $row->alternate_currency_total = 0;
                        $row->alternate_currency_commission = 0;
                    }
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

        $return = array();

        foreach ($products as $product) {
            $return[$product->quantity] = $product;
        }

        krsort($return);

        return $return;
    }



}
