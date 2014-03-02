<?php
/**
 * User: leeparsons
 * Date: 12/01/2014
 * Time: 17:51
 */


class TppStoreModelMentors extends TppStoreAbstractModelResource {

    protected $_table = 'shop_product_mentors';

    public $store_id = null;


    private $mentors = array();

    public function getMentorSessionsByMentor($mentor_id = 0, $page = 1, $limit = 20, $enabled = 'all', $exclude = false)
    {
        if (intval($mentor_id) == 0) {
            return array();
        }

        switch ($enabled) {
            case 'all':
                $where = "";
                break;

            default:
                $where = " AND p.enabled = " . intval($enabled) . " AND s.enabled = " . intval($enabled) . " ";
                break;
        }



        $where .= " AND product_type = 4 ";

        if (intval($exclude) > 0) {
            $where .= " AND p.product_id <> " . intval($exclude);
        }

        if ($page == 0) {
            $page = 1;
        }
        $start = (($page-1) * 20);


        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT p.*, i.path, i.src, i.alt, i.filename, i.extension, i.size_alias, s.currency FROM " . TppStoreModelProducts::getInstance()->getTable() . " AS p
                     LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                     LEFT JOIN (SELECT product_id, path, src, alt, filename, extension, size_alias FROM shop_product_images ORDER BY ordering ASC) AS i ON p.product_id = i.product_id
                     LEFT JOIN " . TppStoreModelMentor2product::getInstance()->getTable() . " AS p2m ON p2m.product_id = p.product_id
                     WHERE p.store_id = %d $where
                     AND p2m.mentor_id = %d
                     GROUP BY p.product_id
                     LIMIT %d, %d",
            array(
                $this->store_id,
                $mentor_id,
                $start,
                $limit
            )
        );

        $wpdb->query(
            $sql,
            OBJECT_K
        );

        $mentors = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                foreach ($wpdb->last_result as $row) {
                    $mentors[$row->product_id] = new TppStoreModelProduct();
                    $mentors[$row->product_id]->setData($row);
                    $mentors[$row->product_id]->getProductImage()->setData(
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

                    $mentors[$row->product_id]->getStore()->setData(
                        array(
                            'store_id'      =>  $row->store_id,
                            'store_name'    =>  $row->store_name
                        )
                    );

                }
            }
        }

        return $mentors;
    }

    public function getMentorSessionCountByMentor($mentor_id = 0, $enabled = 'all')
    {
        if (intval($mentor_id) == 0) {
            return 0;
        }

        switch ($enabled) {
            case 'all':
                $where = "";
                break;

            default:
                $where = " AND p.enabled = " . intval($enabled) . " AND s.enabled = " . intval($enabled) . " ";
                break;
        }

        $where .= " AND product_type = 4 ";


        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT COUNT(p.product_id) AS c FROM " . TppStoreModelProducts::getInstance()->getTable() . " AS p
                     LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id
                     LEFT JOIN " . TppStoreModelMentor2product::getInstance()->getTable() . " AS p2m ON p2m.product_id = p.product_id
                     WHERE p.store_id = %d $where
                     AND p2m.mentor_id = %d
                     GROUP BY p.product_id",
            array(
                $this->store_id,
                $mentor_id
            )
        );

        $c = $wpdb->get_var($sql);

        return intval($c);

    }

    public function getMentors($page = 1, $order = 'm.mentor_name', $sort = 'ASC', $category_id = 0)
    {
        if ($page == 0) {
            $page = 1;
        }
        $start = (($page-1) * 20);

        global $wpdb;


        $select = "m.*, COUNT(p2m.mentor_id) AS sessions";

        switch ($order)
        {
            case 'rating':
                $select .= ", AVG(r.rating) AS rating";
                $join = "LEFT JOIN " . TppStoreModelRatings::getInstance()->getTable() . " AS r ON r.product_id = p2m.product_id ";
                break;
            default:
                $join = "";



                break;
        }

        if (intval($category_id) > 0) {
            $join .= "
            LEFT JOIN shop_p2c AS p2c ON p.product_id = p2c.product_id
            ";
            $where = " AND p2c.category_id = " . intval($category_id);
        } else {
            $where = "";
        }


        $wpdb->query(
            "SELECT $select FROM " . $this->getTable() . " AS m
            LEFT JOIN " . TppStoreModelMentor2product::getInstance()->getTable() . " AS p2m ON p2m.mentor_id = m.mentor_id
            LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = m.store_id
            LEFT JOIN " . TppStoreModelProducts::getInstance()->getTable() . " AS p ON p.product_id = p2m.product_id
            $join
            WHERE s.enabled = 1 AND p.enabled = 1
            $where
            GROUP BY m.mentor_id
            HAVING sessions > 0
            ORDER BY $order $sort"
        );

        $mentors = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $mentors[$row->mentor_id] = new TppStoreModelMentor();
                $mentors[$row->mentor_id]->setData($row);
            }
        }

        return $mentors;

    }

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
                s.store_slug,
                s.currency,
                AVG(r.rating) AS rating
                FROM " . $this->getTable() . " AS m
                LEFT JOIN " . TppStoreModelMentor2product::getInstance()->getTable() . " AS p2m ON p2m.mentor_id = m.mentor_id
                LEFT JOIN " . TppStoreModelProduct::getInstance()->getTable() . " AS p ON p.product_id = p2m.product_id
                LEFT JOIN " . TppStoreModelRating::getInstance()->getTable() . " AS r ON r.product_id = p2m.product_id
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

    public function getMentorCountByStore()
    {
        if (intval($this->store_id) == 0) {
            return 0;
        }

        global $wpdb;

        $c = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(mentor_id) FROM " . $this->getTable() . " WHERE store_id = %d",
            $this->store_id
        ));

        return intval($c);

    }

    public function getMentorsByStore($start = 0, $limit = 20)
    {
        if (is_null($this->store_id) || intval($this->store_id) <= 0) {
            $this->reset();
        } else {

            global $wpdb;

            if ($limit == 0) {
                $sql = $wpdb->prepare(
                    "SELECT m.*, COUNT(p2m.product_id) AS sessions FROM " . $this->getTable() . " AS m

              LEFT JOIN shop_p2m AS p2m ON p2m.mentor_id = m.mentor_id

                WHERE m.store_id = %d

                GROUP BY m.mentor_id",

                $this->store_id
                );

            } else {
                $sql = $wpdb->prepare(
                        "SELECT m.*, COUNT(p2m.product_id) AS sessions FROM " . $this->getTable() . " AS m

              LEFT JOIN shop_p2m AS p2m ON p2m.mentor_id = m.mentor_id

                WHERE m.store_id = %d

                GROUP BY m.mentor_id
                     LIMIT %d, %d",
                    array(
                        $this->store_id,
                        $start,
                        $limit
                    )
                );

            }


            $res = $wpdb->get_results(
                $sql,
                OBJECT_K
            );


            if ($wpdb->num_rows > 0) {

                foreach ($res as $row) {
                    $this->mentors[$row->mentor_id] = new TppStoreModelMentor();
                    $this->mentors[$row->mentor_id]->setData($row);

                }
            } else {
                $this->reset();
            }
        }

        return $this->mentors;
    }

}