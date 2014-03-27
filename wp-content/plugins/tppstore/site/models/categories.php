<?php
/**
 * User: leeparsons
 * Date: 02/12/2013
 * Time: 14:40
 */

class TppStoreModelCategories extends TppStoreAbstractModelResource {

    public $categories = array();

    protected $_table = 'shop_product_categories';

    protected function getClosureTable()
    {
        return 'shop_product_category_closures';
    }

    public function getCategories($args = array())
    {

        global $wpdb;

        $select = '';
        $join = '';
        $where = '';
        $order = '';
        $group = '';

        $from = " FROM " . $this->getTable() . " AS c ";


        if (isset($args['heirarchical']) && $args['heirarchical'] === true) {


            if (isset($args['product_count']) && $args['product_count'] == true) {

                $select .= "SELECT c.*, COUNT(p2c.product_id) AS product_count, cc.* ";
                $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
                $join .= "LEFT JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c ON p2c.category_id = c.category_id ";

            } else {
                $select = "SELECT * ";
                $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
            }

            if (isset($args['parent'])) {
                $where .= " WHERE (cc.parent_id = " . $args['parent'] . ") ";
            } else {
                $where .= " WHERE (cc.parent_id IN (SELECT category_id FROM shop_product_categories WHERE enabled = 1)) ";
            }

            if (isset($args['parent'])) {
                $where .= " AND c.enabled = 1 ";
            } else {
                $where .= " AND c.enabled = 1 OR cc.parent_id = 0 ";
            }

            $order .= " ORDER BY cc.parent_id, cc.ordering, cc.child_id ";


        } elseif (isset($args['featured']) && $args['featured'] === true) {

            if (isset($args['product_count']) && $args['product_count'] == true) {

                $select = "SELECT c.*, COUNT(p2c.product_id) AS product_count ";
                $join .= "LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
                $join .= "LEFT JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c ON p2c.category_id = c.category_id ";
                $join .= " LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = p2c.product_id";
                $join .= " LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id ";
                $where .= "WHERE featured = 1 AND s.enabled = 1 AND p.enabled = 1 ";
                $group = "GROUP BY c.category_id ORDER BY c.category_name";

            } else {
                $select = "SELECT * ";
                $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id";
                $where = " WHERE featured = 1";
                $order = "ORDER BY category_name";
            }

        } elseif (isset($args['parent']) && intval($args['parent']) > 0) {
            if (!is_array($args['parent'])) {
                $args['parent'] = array($args['parent']);
            }


            if (isset($args['product_count']) && $args['product_count'] == true) {

                $select .= "SELECT c.*, COUNT(p2c.product_id) AS product_count ";
                //$join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
                $join .= "LEFT JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c ON p2c.category_id = c.category_id ";


                $join .= " LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = p2c.product_id";
                $join .= " LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id ";

                $where .= " s.enabled = 1 AND p.enabled = 1 AND ";

                $group = "GROUP BY c.category_id";

            } else {
                $select = "SELECT * ";

            }

            $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
            $where .= " cc.parent_id IN (" . implode(',', $args['parent']) . ") ";
            $where .= " AND c.enabled = 1 OR cc.parent_id = 0 ";
            $order .= " ORDER BY cc.parent_id, cc.ordering, cc.child_id ";

        } else {

            if (isset($args['product_count']) && $args['product_count'] == true) {

                $select .= "SELECT c.*, COUNT(p2c.product_id) AS product_count ";
                $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
                $join .= "LEFT JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c ON p2c.category_id = c.category_id ";

                $join .= " LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = p2c.product_id";
                $join .= " LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id ";

                $where .= " AND s.enabled = 1 AND p.enabled = 1 AND ";

            } else {
                $select = "SELECT * ";
                $join .= " LEFT JOIN " . $this->getClosureTable() . " AS cc ON cc.child_id = c.category_id ";
            }


            $where .= " (cc.parent_id IN (SELECT category_id FROM shop_product_categories WHERE enabled = 1)) ";
            $where .= " AND c.enabled = 1 OR cc.parent_id = 0 ";
            $order .= " ORDER BY cc.parent_id, cc.ordering, cc.child_id ";

        }

        if (isset($args['product_count']) && $args['product_count'] == true) {
            $group = "GROUP BY c.category_id";
        }

        if (isset($args['exclude'])) {
            $where .= " AND c.category_id NOT IN (" . implode(',', $args['exclude']) . ") ";
        }

        if (isset($args['category_id'])) {
            $where .= " AND category_id IN (" . implode(',', $args['category_id']) . ") ";
        }


        if (stripos($where, 'WHERE') === false) {
            $where = " WHERE " . $where;
        }




        $rows = $wpdb->get_results(
            $select . ' ' . $from . ' ' . $join . ' ' . $where . ' ' . $group . ' ' . $order,
            ARRAY_A
        );

        if ($wpdb->num_rows > 0) {

            if (isset($args['heirarchical']) && $args['heirarchical'] === true) {
                $data = $this->organiseDataHeirarchically($rows, $args);
            } else {//(isset($args['featured']) && $args['featured'] === true) {
                $data = $this->organiseDataIntoRowset($rows);
            }

            $this->categories = $data;
        }

    }

    public function organiseDataIntoRowset($rows = array())
    {



        $data = array();

        foreach ($rows as $row) {
            $data[$row['category_id']] = new TppStoreModelCategory();
            $data[$row['category_id']]->setData($row);
        }
        return $data;
    }

    public function organiseDataHeirarchically($rows = array(), $args = array())
    {

        $data = array();

        foreach ($rows as $row) {


            if ($row['parent_id'] == 0 && !isset($data[$row['category_id']])) {
                $data[$row['category_id']] = $row;
                $data[$row['category_id']]['children'] = array();
            } else {

                switch ($row['level']) {
                    case 2:
                        if (isset($data[$row['parent_id']])) {
                            $data[$row['parent_id']]['children'][$row['child_id']] = $row;
                            $data[$row['parent_id']]['children'][$row['child_id']]['category_slug'] = $data[$row['parent_id']]['category_slug'] . '/' . $data[$row['parent_id']]['children'][$row['child_id']]['category_slug'];
                            //$data[$row['parent_id']]['product_count'] += $row['product_count'];

                        }
                        break;
                    case 3:

                        foreach ($data as &$grand_parent_row) {


                            if (!empty($grand_parent_row['children'])) {



                                foreach ($grand_parent_row['children'] as &$parent_row) {

                                    if ($parent_row['category_id'] == $row['parent_id']) {
                                        $parent_row['children'][$row['child_id']] = $row;
                                        $parent_row['children'][$row['child_id']]['category_slug'] = $parent_row['category_slug'] . '/' . $parent_row['children'][$row['child_id']]['category_slug'];
                                        //$parent_row['children'][$row['child_id']]['product_count']+=$row['product_count'];
                                    }

                                }

                            }
                        }

                        break;
                    default:
                        //can't find parent so don't place it in! Code should never come in here anyway...
                        break;
                }

            }
        }
        return $data;
    }


    public function getProductCounts($category_ids = array())
    {
        if (!is_array($category_ids) || empty($category_ids)) {
            return array();
        }

        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT category_id, COUNT(p.product_id) AS c FROM " . TppStoreModelProduct::getInstance()->getTable() . " AS p

                INNER JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c USING(product_id)

                WHERE category_id IN (" . implode(',', $category_ids) . ")

                GROUP BY category_id"
            ),
            OBJECT_K
        );

        if ($wpdb->num_rows == 0) {
            return array();
        }

        $tmp = array();
        foreach ($rows as $row) {
            $tmp[$row->category_id] = $row->c;
        }

        return $tmp;

    }




}