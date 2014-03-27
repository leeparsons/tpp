<?php
/**
 * User: leeparsons
 * Date: 15/03/2014
 * Time: 20:12
 */

class TppStoreModelAdminFavourites extends TppStoreAbstractModelResource {

    public $related_parent_id = 0;
    public $position = '';

    public $product_ids = array();

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->related_parent_id = filter_input(INPUT_POST, 'related_parent_id', FILTER_SANITIZE_NUMBER_INT);

            $this->position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);

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


        if (trim($this->position) != '') {

            if (intval($this->related_parent_id) > 0) {
                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM shop_favourites WHERE related_parent_id = %d AND position = %s",
                        array(
                            $this->related_parent_id,
                            $this->position
                        )
                    )
                );

            } else {
                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM shop_favourites WHERE position = %s",
                        $this->position
                    )
                );

            }

        }

        if (false === $wpdb->result) {
            return false;
        }


        $return = false;




        if (!is_null($this->product_ids)) {
            $sql = array();

            $ordering = 1;



            if (intval($this->related_parent_id) > 0) {
                foreach ($this->product_ids as $id) {
                    $sql[] = "(" . intval($this->related_parent_id) .  ", '" . esc_sql($this->position) . "', $id, $ordering)";
                    $ordering++;
                }

            } else {
                foreach ($this->product_ids as $id) {
                    $sql[] = "(null, '" . esc_sql($this->position) . "', $id, $ordering)";
                    $ordering++;
                }
            }


            $sql = "INSERT INTO shop_favourites (related_parent_id, position, product_id, ordering) VALUES " . implode(',', $sql);

            $wpdb->query($sql);



            $return = $wpdb->result;
        }

        $this->clearCache();

        return $return;

    }

    private function clearCache()
    {
        $c = new TppCacher();
        if (intval($this->related_parent_id) > 0) {
            $c->setCachePath('favourites/' . intval($this->related_parent_id) . '/' . $this->position);
        } else {
            $c->setCachePath('favourites/' . $this->position);
        }
        $c->deleteCache();
    }

    /*
     * gets only the selected favourites for this item/ position
     */
    public function getDirectFavouriteProducts($limit = 0)
    {

        global $wpdb;

        if (intval($limit) == 0) {
            $limit = "";
        } else {
            $limit = "LIMIT 0," . $limit;
        }

        if (intval($this->related_parent_id) > 0) {
            $wpdb->query(
                $wpdb->prepare(
                    "SELECT p.product_id, p.product_slug, f.position, p.quantity_available, p.unlimited, f.product_id AS favourite, p.product_title, p.price, p.tax_rate, p.enabled, s.currency, s.store_name, s.store_slug,
                     i.path, s.store_id, i.src, i.alt, i.filename, i.extension, i.size_alias

                     FROM
                    shop_favourites AS f
                     LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable()  . " AS p ON p.product_id = f.product_id AND f.position = %s AND f.related_parent_id = %d
                     INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i ON p.product_id = i.product_id

                     LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id

                    WHERE s.enabled = 1 AND p.enabled = 1
                    GROUP BY p.product_id
                    ORDER BY f.ordering ASC
                    $limit
                    ",
                    array(
                        $this->position,
                        $this->related_parent_id
                    )
                )
            );
        } else {
            $wpdb->query(
                $wpdb->prepare(
                    "SELECT p.product_id, p.product_slug, f.position, p.quantity_available, p.unlimited, f.product_id AS favourite, p.product_title, p.price, p.tax_rate, p.enabled, s.currency, s.store_name, s.store_slug,
                     i.path, s.store_id, i.src, i.alt, i.filename, i.extension, i.size_alias
                     FROM
                     shop_favourites AS f

                     LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable()  . " AS p ON p.product_id = f.product_id AND f.position = %s
                     INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i ON i.product_id = p.product_id

                     LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                     WHERE s.enabled = 1 AND p.enabled = 1
                     GROUP BY p.product_id
                     ORDER BY f.ordering ASC
                     $limit
                    ",
                    $this->position
                )
            );
        }


        $products = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $products[$row->product_id] = new TppStoreModelProduct();
                $products[$row->product_id]->setData($row);
                $products[$row->product_id]->getProductImage()->setData( array(
                    'src'           =>  $row->src,
                    'product_id'    =>  $row->product_id,
                    'alt'           =>  $row->alt,
                    'filename'      =>  $row->filename,
                    'extension'     =>  $row->extension,
                    'size_alias'    =>  $row->size_alias,
                    'path'          =>  $row->path
                ));
                $products[$row->product_id]->getStore()->setData(array(
                    'store_name'    =>  $row->store_name,
                    'store_slug'    =>  $row->store_slug
                ));
            }
        }

        return $products;
    }


    /*
     * gets the favourites among other products for admin listings only
     */
    public function getFavouriteProducts($sort = 'p.product_id', $direction = 'asc')
    {
        $products = array();

        if ($sort == 'best_selling') {
            $select = ", COUNT(o.product_id) AS sold";
            $join = " LEFT JOIN " . TppStoreModelOrderItem::getInstance()->getTable() . " AS o ON o.product_id = p.product_id";
            $group = "GROUP BY p.product_id";
            $sort = "sold";
        } else {
            $join = "";
            $group = "";
            $select = "";
        }



        if ( trim($this->position) != '' ) {
            global $wpdb;
            if (intval($this->related_parent_id) > 0) {
                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT p.product_id, p.product_slug, p.quantity_available, p.unlimited, f.product_id AS favourite, p.product_title, p.price, p.tax_rate, p.enabled, s.currency, s.store_name, f.position,
                        i.path, s.store_id, i.src, i.alt, i.filename, i.extension, i.size_alias
                    $select

                    FROM " . TppStoreModelProduct::getInstance()->getTable()  . " AS p
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                    $join
                    LEFT JOIN shop_favourites AS f ON p.product_id = f.product_id AND f.position = %s AND f.related_parent_id = %d
                    INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i ON i.product_id = p.product_id
                    $group
                    ORDER BY f.position = %s DESC, f.ordering, $sort $direction
                 ",
                        array(
                            $this->position,
                            $this->related_parent_id,
                            $this->position,
                            $this->related_parent_id
                        )
                    ),
                    OBJECT_K
                );
            } else {
                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT p.product_id, p.product_slug, p.quantity_available, p.unlimited, f.product_id AS favourite, p.product_title, p.price, p.tax_rate, p.enabled, s.currency, s.store_name, f.position,
                        i.path, s.store_id, i.src, i.alt, i.filename, i.extension, i.size_alias
                    $select

                    FROM " . TppStoreModelProduct::getInstance()->getTable()  . " AS p
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                    $join
                    LEFT JOIN shop_favourites AS f ON p.product_id = f.product_id AND f.position = %s
                    INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i ON i.product_id = p.product_id
                    $group
                    ORDER BY f.position = %s DESC, f.ordering, $sort $direction
                 ",
                        array(
                            $this->position,
                            $this->position,
                        )
                    ),
                    OBJECT_K
                );
            }

            if ($wpdb->num_rows > 0) {
                foreach ($wpdb->last_result as $row) {
                    $products[$row->product_id] = new TppStoreModelProduct();
                    $products[$row->product_id]->setData($row);
                    $products[$row->product_id]->getProductImage()->setData( array(
                        'src'           =>  $row->src,
                        'product_id'    =>  $row->product_id,
                        'alt'           =>  $row->alt,
                        'filename'      =>  $row->filename,
                        'extension'     =>  $row->extension,
                        'size_alias'    =>  $row->size_alias,
                        'path'          =>  $row->path
                    ));
                    $products[$row->product_id]->getStore()->setData(array(
                        'store_name'    =>  $row->store_name,
                        'store_slug'    =>  $row->store_slug
                    ));
                }
            }



        }

        return $products;

    }

    public function validate()
    {
        return trim($this->position) != '';
    }

}