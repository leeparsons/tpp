<?php
/**
 * User: leeparsons
 * Date: 17/02/2014
 * Time: 21:48
 */
 
class TppStoreModelModelEvents extends TppStoreAbstractModelResource {

    public $store_id = 0;

    protected $_table = 'shop_product_events';

    public function getEventCountByStore($enabled = 'all')
    {

        if (intval($this->store_id) == 0) {
            return 0;
        }

        switch ($enabled) {
            default:
                $where = "";
                break;
            case '1':
                $where = " AND p.enabled = 1 AND s.enabled = 1 AND e.listing_expire > NOW() ";
                break;
            case '0':
                $where = " AND (p.enabled = 0 OR s.enabled = 0 OR e.listing_expire < NOW() )";
                break;
        }


        global $wpdb;

        $c = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(p.product_id) AS c FROM " . $this->getTable() . " As e
                INNER JOIN " . TppStoreModelProducts::getInstance()->getTable() . " AS p ON p.product_id = e.product_id
                WHERE store_id = %d $where AND product_type = 5",
                $this->store_id
            )
        );



        return $c;
    }


    public function getEvents($page = 1, $order = 'rating', $sort = 'DESC')
    {

        $start = (($page-1) * 20);

        global $wpdb;

        $wpdb->get_results(
            "SELECT
                e.*,
                p.*,
                i.path,
                i.src,
                i.alt,
                i.filename,
                i.extension,
                i.size_alias,
                s.store_name,
                s.store_slug,
                s.currency,
                AVG(r.rating) AS rating
                FROM " . $this->getTable() . " AS e
                LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = e.product_id
                LEFT JOIN " . TppStoreModelRating::getInstance()->getTable() . " AS r ON r.product_id = p.product_id
                LEFT JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images WHERE size_alias = 'main' ORDER BY ordering ASC) AS i ON i.product_id = p.product_id
                LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                LEFT JOIN " . TppStoreModelProductOptions::getInstance()->getTable() . " AS o ON o.product_id = p.product_id
                WHERE p.enabled = 1 AND s.enabled = 1
                AND e.listing_expire > NOW()
                GROUP BY p.product_id
                ORDER BY $order $sort
                LIMIT $start, 20
             ",
            OBJECT_K
        );

        if ($wpdb->num_rows > 0) {
            $return = array();

            foreach ($wpdb->last_result as $row) {
                $return[$row->product_id] = new TppStoreModelEvent();
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


                $return[$row->product_id]->getStore()->setData(
                    array(
                        'store_id'      =>  $row->store_id,
                        'store_name'    =>  $row->store_name,
                        'store_slug'    =>  $row->store_slug
                    )
                );

            }

            return $return;


        } else {
            return array();
        }

    }

}