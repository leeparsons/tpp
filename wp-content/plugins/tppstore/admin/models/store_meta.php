<?php
/**
 * User: leeparsons
 * Date: 16/01/2014
 * Time: 21:47
 */
 
class TppStoreAdminModelStoreMeta extends TppStoreAbstractModelResource {

    public $store_id = null;
    public $newsletter = 0;
    public $how = null;
    public $website = null;

    public $created_on = null;

    protected $_table = 'shop_store_applications';

    public function getApplicationByStore()
    {

        if (intval($this->store_id) < 1) {
            return false;
        }

        global $wpdb;

        $wpdb->query(
            "SELECT a.*, COUNT(p.product_id) AS product_count

            FROM " . $this->getTable() . " AS a
            INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = a.store_id
            INNER JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u ON u.user_id = s.user_id
            LEFT JOIN " . TppStoreModelProducts::getInstance()->getTable() . " AS p ON p.store_id = a.store_id
            WHERE s.store_id = " . intval($this->store_id) . "
            ",
            OBJECT_K
        );

        if ($wpdb->num_rows == 1) {
            return $wpdb->last_result[0];
        } else {
            return false;
        }

    }

    public function getApplications(){

        global $wpdb;

        $wpdb->query(
            "SELECT a.*, COUNT(p.product_id) AS product_count, CONCAT(u.first_name, ' ', u.last_name) AS owner, u.email, s.approved, s.store_name

            FROM " . $this->getTable() . " AS a
            INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = a.store_id
            INNER JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u ON u.user_id = s.user_id
            LEFT JOIN " . TppStoreModelProducts::getInstance()->getTable() . " AS p ON p.store_id = a.store_id
            GROUP BY s.store_id
            ORDER BY a.created_on DESC
            ",
            OBJECT_K
        );


        $res = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $res[] = $row;
            }
        }


        return $res;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        $wpdb->replace(
            $this->getTable(),
            array(
                'store_id'      =>  $this->store_id,
                'newsletter'    =>  $this->newsletter,
                'how'           =>  $this->how,
                'website'       =>  $this->website
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s'
            )
        );

        return true;
    }

    public function validate()
    {
        //don't raise error messages, just return valid or not

        if (intval($this->store_id) < 1) {
            return false;
        }

        return true;
    }



}