<?php
/**
 * User: leeparsons
 * Date: 14/03/2014
 * Time: 17:34
 */


class TppStoreModelAdminProducts extends TppStoreAbstractModelResource {


    protected $_table = 'shop_products';

    public function getSidebarFavouritesList()
    {
        global $wpdb;

        $wpdb->query(
            "SELECT f.position, product_title, p.product_id, store_name, quantity_available, unlimited, price FROM " . $this->getTable() . " AS p
            LEFT JOIN shop_favourites AS f ON f.product_id = p.product_id
            LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id

            ORDER BY f.position = 'sidebar' DESC, f.product_id
            ",
            OBJECT_K
        );

        return $wpdb->last_result;

    }


    public function getHomepageFavouritesList()
    {
        global $wpdb;

        $wpdb->query(
            "SELECT f.position, product_title, p.product_id, store_name, quantity_available, unlimited, price FROM " . $this->getTable() . " AS p
            LEFT JOIN shop_favourites AS f ON f.product_id = p.product_id
            LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id

            ORDER BY f.position = 'homepage' DESC, f.product_id
            ",
            OBJECT_K
        );

        return $wpdb->last_result;
    }

    /*
     * used by blog 2 products relations admin searching only
     */
    public function search($s = '')
    {
        if (trim($s) == '') {
            return array();
        }

        global $wpdb;
        $products = array();
        $wpdb->query(

                "SELECT p.enabled, s.enabled, product_id, product_title, s.store_name, i.path, s.store_id, i.src, i.alt, i.filename, i.extension, i.size_alias FROM " . $this->getTable() . " AS p

                INNER JOIN (SELECT src, product_id, image_id, path, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i USING(product_id)

                LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                LEFT JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u ON u.user_id = s.user_id
                WHERE p.product_title LIKE '%" . esc_sql($s) . "%' OR p.product_description LIKE '%" . esc_sql($s) . "%'
                OR s.store_name LIKE '%" . esc_sql($s) . "%'
                OR u.first_name LIKE '%" . esc_sql($s) . "%'
                OR u.last_name LIKE '%" . esc_sql($s) . "%'
                GROUP BY p.product_id
                ",
            OBJECT_K
        );

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $product = new TppStoreModelProduct();
                $product->setData($row);
                $product->getProductImage()->setData(
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

                $src = $product->getProductImage()->getSrc('store_related');
                if (false === $src) {
                    $src = '/store/' . $row->store_id . '/' . $row->product_id . '/' . $row->src;
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
                        $src = false;
                    }
                }
                $row->image = $src;
                $products[] = $row;
            }
        }

        return $products;
    }

}