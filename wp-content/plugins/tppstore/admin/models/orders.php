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

}