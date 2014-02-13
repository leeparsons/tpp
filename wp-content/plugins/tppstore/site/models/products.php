<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 22:44
 */

class TppStoreModelProducts extends TppStoreAbstractModelResource {


    public $store_id = null;
    public $user_id = null;
    public $products = array();

    protected static $_ids = array();
    protected $_table = 'shop_products';

    public function getProductCountByUser()
    {
        if (is_null($this->user_id) || intval($this->user_id) <= 0) {
            return 0;
        }

        global $wpdb;

        $res = $wpdb->get_row($wpdb->prepare(
                "SELECT COUNT(p.product_id) AS c FROM shop_products AS p

                INNER JOIN shop_product_stores AS s ON s.store_id = p.store_id AND s.enabled = 1

                WHERE user_id = %d",
                $this->user_id
            ),
            OBJECT);

        if ($wpdb->num_rows == 1) {
            return $res->c;
        }


        return 0;
    }

    /*
     * @param mentors = determines whether or not to get mentor sessions only, otherwise gets other types of product only
     */
    public function getProductsByStore($start = 0, $limit = 20, $enabled = 'all', $mentors = false)
    {

        if (is_null($this->store_id) || intval($this->store_id) <= 0) {
            $this->reset();
        } else {

            switch ($enabled) {
                case 'all':
                    $where = "";
                    break;

                default:
                    $where = " AND p.enabled = " . intval($enabled) . " AND s.enabled = " . intval($enabled) . " ";
                    break;
            }

            if (true === $mentors) {
                $where .= " AND product_type = 4 ";
            } else {
                $where .= " AND product_type <> 4 ";
            }


            global $wpdb;

            $sql = $wpdb->prepare(
                "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.size_alias, s.currency FROM " . $this->getTable() . " AS p
                     LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                     LEFT JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i USING(product_id)
                     WHERE p.store_id = %d $where
                     GROUP BY p.product_id
                     LIMIT %d, %d",
                array(
                    $this->store_id,
                    $start,
                    $limit
                )
            );


            $res = $wpdb->get_results(
                $sql,
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {

                foreach ($res as $row) {
                    $this->products[$row->product_id] = new TppStoreModelProduct();
                    $this->products[$row->product_id]->setData($row);
                    $this->products[$row->product_id]->getProductImage()->setData(
                        array(
                            'src'           =>  $row->src,
                            'product_id'    =>  $row->product_id,
                            'alt'           =>  $row->alt,
                            'filename'      =>  $row->filename,
                            'extension'     =>  $row->extension,
                            'size_alias'    =>  $row->size_alias,
                            'path'          =>  $row->path
                        )
                    );

                }
            } else {
                $this->reset();
            }
        }

        return $this->products;

    }

    /*
     * @param mentors = determines whether or not to get mentors. false for just products, true for just mentors.
     * @param store_enabled = 1, 0 or 'all'
     */
    public function getProductCountByStore($mentors = false, $store_enabled = 1)
    {
        if (is_null($this->store_id) || intval($this->store_id) <= 0) {
            return 0;
        }

        global $wpdb;

        if (true === $mentors) {
            $where = " AND product_type = 4 ";
        } else {
            $where = " AND product_type <> 4 ";
        }

        switch ($store_enabled) {
            case '1':
                $join = "LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1 ";

                break;

            case '0':
                $join = "LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 0 ";
                break;
            default:
                //all
                //do nothing
                $join = "";
                break;
        }

        $res = $wpdb->get_row($wpdb->prepare(
                "SELECT COUNT(p.product_id) AS c FROM " . $this->getTable() . " AS p
                $join
                WHERE p.store_id = %d $where",
                $this->store_id
            ),
            OBJECT);

        if ($wpdb->num_rows == 1) {
            return $res->c;
        }


        return 0;

    }


    public function getTopProducts($size = 'thumb')
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.size_alias, s.currency,s.store_slug, s.store_name FROM " . $this->getTable() . " AS p
                INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1
                INNER JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images WHERE size_alias = %s ORDER BY ordering ASC) AS i USING(product_id)
                WHERE p.enabled = 1
                GROUP BY p.product_id
                ORDER BY RAND() LIMIT 8",
                $size
            ),
            OBJECT_K
        );

        $return = array();

        if ($wpdb->num_rows > 0) {

            foreach ($rows as $row) {
                $return[$row->product_id] = new TppStoreModelProduct();
                $return[$row->product_id]->setData($row);
                $return[$row->product_id]->getProductImage()->setData(
                    array(
                        'src'           =>  $row->src,
                        'product_id'    =>  $row->product_id,
                        'alt'           =>  $row->alt,
                        'filename'      =>  $row->filename,
                        'extension'     =>  $row->extension,
                        'size_alias'    =>  $row->size_alias,
                        'path'          =>  $row->path
                    )
                );

                $return[$row->product_id]->getSTore()->setData(array(
                    'store_id'      =>  $row->store_id,
                    'store_slug'    =>  $row->store_slug,
                    'store_name'    =>  $row->store_name
                ));
            }
        }

        return $return;
    }

    public function getLatestProducts($limit = 5)
    {

        global $wpdb;


        $rows = $wpdb->get_results(
            "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.image_id, i.size_alias, s.currency FROM " . $this->getTable() . " AS p
                INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1
                INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images GROUP BY product_id ORDER BY ordering ASC) AS i USING(product_id)
                WHERE p.enabled = 1
                GROUP BY p.product_id
                ORDER BY p.created_on DESC LIMIT 5"
        );

        $return = array();
        if ($wpdb->num_rows > 0) {
            foreach ($rows as $row) {
                $return[$row->product_id] = new TppStoreModelProduct();
                $return[$row->product_id]->setData($row);
                $return[$row->product_id]->getProductImage()->setData(
                    array(
                        'src'           =>  $row->src,
                        'product_id'    =>  $row->product_id,
                        'alt'           =>  $row->alt,
                        'filename'      =>  $row->filename,
                        'extension'     =>  $row->extension,
                        'size_alias'    =>  $row->size_alias,
                        'path'          =>  $row->path
                    )
                );
            }
        }

        return $return;


    }

    public function getProductsByIDs($ids = array(), $with_images = true)
    {


        //add caching to improve things
        $str = implode(':', $ids);

        $str .= ':' . (true === $with_images?'with_images':'no_images');

        if (!isset($this->_products[md5($str)])) {

            $return = array();

            if (is_array($ids) && !empty($ids)) {
                global $wpdb;

                if (true === $with_images) {
                    $join = "
                    INNER JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM " . TppStoreModelProductImage::getInstance()->getTable() . " GROUP BY product_id ORDER BY ordering ASC) AS i ON (i.product_id = p.product_id)";
                } else {
                    $join = "";
                }

                $rows = $wpdb->get_results(
                    "SELECT
                    p.product_id,
                    p.price_includes_tax,
                    product_type,
                    product_type_text,
                    unlimited,
                    tax_rate,
                    price,
                    product_title,
                    product_slug,
                    p.store_id,
                    quantity_available,
                    s.currency,
                    s.store_name,
                    s.src FROM " . $this->getTable() . " AS p
                    $join
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                    WHERE p.product_id IN (" . implode(',', $ids) . ") AND p.enabled = 1 AND s.enabled = 1 ",
                    OBJECT_K
                );

                if ($wpdb->num_rows > 0) {
                    foreach ($rows as $row) {
                        $return[$row->product_id] = new TppStoreModelProduct();
                        $return[$row->product_id]->setData($row);
                        $return[$row->product_id]->getStore()->setData(array(
                            'store_id'      =>  $row->store_id,
                            'store_name'    =>  $row->store_name,
                            'src'           =>  $row->src
                        ));
                    }
                }

            }

            $this->_products[md5($str)] = $return;
        }

        return $this->_products[md5($str)];


    }

    /*
     * properties contains an array of nvp database columns to update
     * where contains the where clauses in nvp format
     */
    public function bulkUpdate($properties = array(), $where = array())
    {

        if (!is_array($where) || empty($where) || !is_array($properties) || empty($properties)) {
            return false;
        }

        global $wpdb;

        $update = array();
        $values = array();

        foreach ($properties as $column => $value) {
            $update[$column] = $value;
            $values[] = "%s";
        }


        $where_format = "%d";

        $wpdb->update(
            $this->getTable(),
            $update,
            $where,
            $values,
            $where_format
        );


        return $wpdb->result;
    }

    public function search($s = '', $page = 1, $count = false)
    {
        global $wpdb;

        if (false === $count) {

            $start = (($page-1) * 20) - $page + 1;

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.size_alias, s.currency, s.store_name, s.store_slug FROM " . $this->getTable() . " AS p
                INNER JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1
                INNER JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i USING(product_id)
                WHERE (product_title LIKE '%%%s%%' OR product_description LIKE '%%%s%%' OR product_slug LIKE '%%%s%%')
                AND p.enabled = 1
                GROUP BY p.product_id
                LIMIT $start, 20
                ",
                    array(
                        $s,
                        $s,
                        $s
                    )
                )
            );



        } else {



            $c = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(product_id) AS c FROM " . $this->getTable() . " AS p
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1
                WHERE (product_title LIKE '%%%s%%' OR product_description LIKE '%%%s%%' OR product_slug LIKE '%%%s%%')
                AND p.enabled = 1
                ",
                    array(
                        $s,
                        $s,
                        $s
                    )
                )
            );

            return $c;

        }

        if ($wpdb->num_rows > 0) {
            $return = array();
            foreach ($rows as $row) {
                $return[$row->product_id] = new TppStoreModelProduct();
                $return[$row->product_id]->setData($row);
                $return[$row->product_id]->getProductImage()->setData(array(
                    'product_id'    =>  $row->product_id,
                    'store_id'      =>  $row->store_id,
                    'src'           =>  $row->src,
                    'path'          =>  $row->path,
                    'filename'      =>  $row->filename,
                    'extension'     =>  $row->extension
                ));
                $return[$row->product_id]->getStore()->setData(array(
                    'store_id'      =>  $row->store_id,
                    'store_name'    =>  $row->store_name,
                    'store_slug'    =>  $row->store_slug
                ));
            }
            return $return;
        } else {
            return array();
        }
    }

}