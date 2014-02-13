<?php
/**
 * User: leeparsons
 * Date: 13/01/2014
 * Time: 08:07
 */
 

class TppStoreModelStorePages extends TppStoreAbstractModelResource {

    public $store_id = null;
    public $pages = array();

    //public $privacy = null;
    //public $refunds = null;
    public $terms = null;
    //terms, privacy, refunds

    protected $_table = 'shop_product_store_pages';


    public function getPageCount()
    {
        if (intval($this->store_id) > 0) {
            global $wpdb;

            $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(page) AS c FROM " . $this->getTable() . " WHERE store_id = %d AND content IS NOT NULL AND LENGTH(content) > 0",
                    $this->store_id
                ),
                OBJECT_K
            );

            return $wpdb->last_result[0]->c;

        } else {
            return 0;
        }
    }

    public function getPages()
    {
        if (intval($this->store_id) > 0) {
            global $wpdb;


            $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE store_id = %d",
                    $this->store_id
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {

                foreach ($wpdb->last_result as $row) {
                    $this->pages[$row->page] = new TppStoreModelStorePage();
                    $this->pages[$row->page]->setData(array(
                        'store_id'  =>  $this->store_id,
                        'content'   =>  $row->content,
                        'page'      =>  $row->page
                    ));
                }
            }
        }

        return $this;
    }


//    public function getPrivacy()
//    {
//        return $this->pages['privacy']->content;
//    }

    public function getTerms()
    {
        return isset($this->pages['terms']->content)?$this->pages['terms']->content:false;
    }
//
//    public function getRefunds()
//    {
//        return $this->pages['refunds']->content;
//    }

    public function readFromPost()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //$this->privacy = filter_input(INPUT_POST, 'privacy', FILTER_UNSAFE_RAW);
            //$this->refunds = filter_input(INPUT_POST, 'refunds', FILTER_UNSAFE_RAW);
            $this->terms = filter_input(INPUT_POST, 'terms', FILTER_UNSAFE_RAW);

            return true;
        } else {
            return false;
        }

    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }


        global $wpdb;

        $error = false;
        foreach (array('terms') as $page) {
            $wpdb->replace(
                $this->getTable(),
                array(
                    'page'      =>  $page,
                    'content'   =>  $this->$page,
                    'store_id'  =>  $this->store_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d'
                )
            );

            if (false === $wpdb->result) {
                $error = true;
                TppStoreMessages::getInstance()->addMessage('error', "Unable to save your $page, please try again: " . $wpdb->last_error);            return false;
            }

        }

        return !$error;

    }

    public function validate()
    {

        if (intval($this->store_id) < 1) {
            TppStoreMessages::getInstance()->addMessage('error', 'Could not detect your store');
            return false;
        }

        return true;
    }

}