<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 16:41
 */
 
class TppStoreModelP2c extends TppStoreAbstractModelResource {


    protected $_table = 'shop_p2c';

    public $categories = array();

    public $product_id = null;
    public $category_id = null;


    public function getProducts($with_main_image = true, $params = array())
    {
        if (intval($this->category_id) <= 0) {
            return array();
        }

        $limit = '';
        if (isset($params['start'])) {
            $limit = $params['start'];
        }

        if (isset($params['limit'])) {
            $limit .= ($limit === ''?'0,':',') . $params['limit'];
        }

        if ($limit !== '') {
            $limit = "LIMIT " . $limit;
        }


        if (isset($params['enabled'])) {
            switch ($params['enabled']) {
                case '0':
                    $where = " p.enabled = 0 ";
                    break;

                case 'all':
                    $where = "";
                    break;

                default:
                    $where = " p.enabled = 1 ";
                    break;
            }
        } else {
            $where = " p.enabled = 1 ";
        }



        $select = "SELECT ";

        if (isset($params['count'])) {
            $select .= "COUNT(DISTINCT(p.product_id)) AS c";
        } else {
            if ($with_main_image === true) {
                $select .= "i.src, i.image_id, i.alt, i.filename, i.path, i.extension,s.currency,s.store_name,s.store_slug,";
            }
            $select .= "p.*";
        }


        global $wpdb;

        if (strlen($where) > 0) {
             $where = " WHERE $where";
        }

        if ($with_main_image === true) {
            $wpdb->get_results(
                $wpdb->prepare(
//                    $select .
//                    " FROM " . TppStoreModelProduct::getInstance()->getTable() . " AS p
//                    INNER JOIN " . $this->getTable() . " AS p2c ON p2c.product_id = p.product_id AND p2c.category_id = %d AND p.enabled = 1
//                    LEFT JOIN " . TppStoreModelCategory::getInstance()->getTable() . " AS c ON c.category_id = p2c.category_id AND c.enabled = 1
//                    LEFT JOIN " . TppStoreModelProductImage::getInstance()->getTable() . " AS i ON i.product_id = p.product_id AND i.ordering = 1 AND i.size_alias = 'thumb' " .
//                    $where .
//                    $limit,
//                    $this->category_id
                $select .
                 " FROM shop_products AS p
                 INNER JOIN shop_product_stores AS s ON s.store_id = p.store_id AND s.enabled = 1

                    LEFT JOIN (SELECT src, extension, image_id, filename, path, alt, product_id FROM shop_product_images GROUP BY product_id, ordering )
                     AS i ON i.product_id = p.product_id
                    INNER JOIN " . $this->getTable() . " AS p2c ON p2c.product_id = p.product_id AND p2c.category_id = %d
                     LEFT JOIN " . TppStoreModelCategory::getInstance()->getTable() . " AS c ON c.category_id = p2c.category_id AND c.enabled = 1 " .
                    $where .
                    " GROUP BY p.product_id " .
                    $limit,
                    $this->category_id
                ),
                OBJECT_K
            );

        } else {
            $wpdb->get_results(
                $wpdb->prepare(
                    $select .
                    " FROM " . TppStoreModelProduct::getInstance()->getTable() . " AS p
                    LEFT JOIN " . TppStoreModelStore::getInstance()->getTable() . " AS s ON s.store_id = p.store_id AND s.enabled = 1
                    INNER JOIN " . $this->getTable() . " AS p2c ON p2c.product_id = p.product_id AND p2c.category_id = %d
                    LEFT JOIN " . TppStoreModelCategory::getInstance()->getTable() . " AS c ON c.category_id = p2c.category_id AND c.enabled = 1 " .
                    $where .
                    $limit,
                    $this->category_id
                ),
                OBJECT_K
            );

        }



        if (isset($params['count'])) {
            return $wpdb->last_result[0]->c;
        }

        $return = array();

        if ($wpdb->num_rows > 0) {

            if ($with_main_image === true) {
                foreach ($wpdb->last_result as $row) {
                    $return[$row->product_id] = new TppStoreModelProduct();
                    $return[$row->product_id]->setData($row);

                    $return[$row->product_id]->getProductImage()->setData(array(
                        'image_id'      =>  $row->image_id,
                        'product_id'    =>  $row->product_id,
                        'src'           =>  $row->src,
                        'path'          =>  $row->path,
                        'alt'           =>  $row->alt,
                        'filename'      =>  $row->filename,
                        'extension'     =>  $row->extension,
                        'store_id'      =>  $row->store_id
                    ));

                    $return[$row->product_id]->getStore()->setData(array(
                        'store_id'      =>  $row->store_id,
                        'store_name'    =>  $row->store_name,
                        'store_slug'    =>  $row->store_slug
                    ));


                }

            } else {
                foreach ($wpdb->last_result as $row) {
                    $return[$row->product_id] = new TppStoreModelProduct();
                    $return[$row->product_id]->setData($row);
                }
            }
        }

        return $return;

    }



    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->categories = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);





            if (false === $this->categories || is_null($this->categories)) {
                //determine if just one is sent up?
                $this->categories = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

                if (false !== $this->categories && !is_null($this->categories)) {
                    $this->categories = array($this->categories);
                }

            }

            if (is_null($this->product_id)) {
                $this->product_id = filter_input(INPUT_POST, md5('product_id' . NONCE_KEY), FILTER_SANITIZE_NUMBER_INT);
            }

        }



    }

    public function getCategories()
    {
        if (!$this->validate()) {
            return null;
        }

        global $wpdb;


        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT c.* FROM " . TppStoreModelCategories::getInstance()->getTable() . " AS c "  .
                " LEFT JOIN " . $this->getTable() . " AS p2c USING(category_id) " .
                " WHERE product_id = %d",
                $this->product_id
            ),
            OBJECT_K
        );


        $return = array();

        if ($wpdb->num_rows > 0) {
            foreach ($rows as $row) {
                $return[$row->category_id] = clone TppStoreModelCategory::getInstance();
                $return[$row->category_id]->setData($row);
            }
        }


        return $return;
    }

    public function save()
    {



        if (!$this->validate()) {
            return false;
        }


        $error = false;

        //if the categories are an empty array then delete all assignments to the product!
        $this->delete();

        global $wpdb;
        //now insert the categories selected!
        foreach ($this->categories as $category) {

            if (intval($category) == 0) {
                continue;
            }

            $wpdb->replace(
                $this->getTable(),
                array(
                    'product_id'    =>  $this->product_id,
                    'category_id'   =>  $category
                ),
                array(
                    '%d',
                    '%d'
                )
            );


            if ($wpdb->rows_affected == 0) {
                TppStoreMessages::getInstance()->addMessage('error', array('product_category'   =>  'Unabel to allocate your selected categories'));
                $error = true;
            }




        }


        return !$error;

    }

    public function delete()
    {

        if (!$this->validate()) {
            return false;
        }

        global $wpdb;
        $error = false;
        if (empty($this->categories)) {
            //assume no categories allocated
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM " . $this->getTable() . " WHERE product_id = %d",
                    $this->product_id
                )
            );

        } else {
            //only delete the ones which are not reallocated during this save!
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM " . $this->getTable() . " WHERE product_id = %d AND category_id NOT IN (%s)",
                    array(
                        $this->product_id,
                        implode(',', $this->categories)
                    )
                )
            );
        }

        return !$error;

    }

    public function validate()
    {

        $error = false;

        if (!is_array($this->categories)) {
            //assume hack!
            TppStoreMessages::getInstance()->addMessage('error',    array('product_categories'  =>  'Unable to allocate your product to the selected categories'));
            $error = true;
        }

        //determine if  product id is set
        if (is_null($this->product_id) || intval($this->product_id) <= 0) {
            TppStoreMessages::getInstance()->addMessage('error',    array('product_categories'  =>  'Unable to allocate your product to the selected categories'));
            $error = true;
        }

        return !$error;
    }

}