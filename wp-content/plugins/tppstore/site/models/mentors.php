<?php
/**
 * User: leeparsons
 * Date: 12/01/2014
 * Time: 17:51
 */


class TppStoreModelMentors extends TppStoreAbstractModelResource {

    protected $_table = 'shop_product_mentors';

    public function getMentorSessionList($page = 1, $order = 'rating', $sort = 'DESC')
    {

        global $wpdb;

        $wpdb->get_results(
            "SELECT

                MIN(o.option_price) AS option_min_price,
                m.*,
                p.*,
                i.path,
                i.src,
                i.alt,
                i.filename,
                i.extension,
                i.size_alias,
                s.store_name,

                s.currency,
                AVG(r.rating) AS rating
                FROM " . $this->getTable() . " AS m
                LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = m.product_id
                LEFT JOIN " . TppStoreModelRating::getInstance()->getTable() . " AS r ON r.product_id = m.product_id
                LEFT JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images WHERE size_alias = 'main' ORDER BY ordering ASC) AS i ON i.product_id = p.product_id
                LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                LEFT JOIN " . TppStoreModelProductOptions::getInstance()->getTable() . " AS o ON o.product_id = p.product_id
                WHERE p.enabled = 1 AND s.enabled = 1
                GROUP BY p.product_id
                ORDER BY $order $sort
             ",
            OBJECT_K
        );


        if ($wpdb->num_rows > 0) {
            $return = array();

            foreach ($wpdb->last_result as $row) {
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

                $return[$row->product_id]->getMentor()->setData(
                    array(
                        'mentor_id'         =>  $row->mentor_id,
                        'mentor_name'       =>  $row->mentor_name,
                        'mentor_company'    =>  $row->mentor_company,
                        'mentor_country'    =>  $row->mentor_country,
                        'mentor_city'       =>  $row->mentor_city
                    )
                )->setRating($row->rating);

                $return[$row->product_id]->getStore()->setData(
                    array(
                        'store_id'      =>  $row->store_id,
                        'store_name'    =>  $row->store_name
                    )
                );

            }

            return $return;


        } else {
            return array();
        }

    }


}