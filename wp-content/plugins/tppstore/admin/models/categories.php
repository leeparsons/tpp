<?php
/**
 * User: leeparsons
 * Date: 15/03/2014
 * Time: 20:12
 */
 
class TppStoreModelAdminCategories extends TppStoreAbstractModelResource {

    public $category_id = 0;

    public $product_ids = array();

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->category_id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

            $this->product_ids = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);

            return true;
        } else {
            return false;
        }
    }

    public function save()
    {
        if (false === $this->validate()) {
            return false;
        }

        global $wpdb;

        if (empty($this->product_ids)) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM shop_favourites WHERE related_parent_id = %d AND position = 'category'",
                    $this->category_id
                )
            );

            if (false === $wpdb->result) {
                return false;
            }

        } else {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM shop_favourites WHERE product_id NOT IN (" . implode(',', $this->product_ids) . ") AND related_parent_id = %d AND position = 'category'",
                    $this->category_id
                )
            );

            if (false === $wpdb->result) {
                return false;
            }

            $sql = array();

            $ordering = 1;

            foreach ($this->product_ids as $id) {
                $sql[] = "(" . intval($this->category_id) .  ", 'category', $id, $ordering)";
                $ordering++;
            }

            $sql = "REPLACE INTO shop_favourites (related_parent_id, position, product_id, ordering) VALUES " . implode(',', $sql);

            $wpdb->query($sql);

            return $wpdb->result;

        }

        return $wpdb->result;
    }

    private function validate()
    {
        return intval($this->category_id) > 0;
    }

    public function getFavouriteProducts()
    {
        $products = array();

        if (intval($this->category_id) > 0) {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT p.product_id, p.quantity_available, p.unlimited, f.product_id AS favourite, p.product_title, p.price, p.tax_rate, p.enabled, s.currency, s.store_name FROM " . TppStoreModelProduct::getInstance()->getTable()  . " AS p
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                    INNER JOIN " . TppStoreModelP2c::getInstance()->getTable() . " AS p2c ON p2c.product_id = p.product_id AND p2c.category_id = %d
                 LEFT JOIN shop_favourites AS f ON p.product_id = f.product_id AND f.position = 'category' AND f.related_parent_id = %d

                 ORDER BY f.position = 'category' DESC, f.ordering, p.product_id
                 ",
                array(
                    $this->category_id,
                    $this->category_id
                )
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                foreach ($wpdb->last_result as $row) {
                    $products[$row->product_id] = $row;
                }
            }

        }

        return $products;

    }

}