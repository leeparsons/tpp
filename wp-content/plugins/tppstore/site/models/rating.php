<?php
/**
 * User: leeparsons
 * Date: 05/01/2014
 * Time: 20:14
 */
 
class TppStoreModelRating extends TppStoreAbstractModelResource {


    protected $_table = 'shop_reviews';

    public $review_id = null;
    public $review_title = null;
    public $review_description = null;
    public $rating = 0;
    public $user_id = null;
    public $product_id = null;
    protected $_user = null;


    public function __construct()
    {
        $this->_user = new TppStoreModelUser();
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function readFromPost()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);

            $this->user_id = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_NUMBER_INT);

            $this->review_title = filter_input(INPUT_POST, 'review_title', FILTER_SANITIZE_STRING);

            $this->review_description = filter_input(INPUT_POST, 'review_description', FILTER_SANITIZE_STRING);

            $this->rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);


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

        if (intval($this->review_id) > 0) {

            $wpdb->update(
                $this->getTable(),
                array(
                    'review_title'          =>  $this->review_title,
                    'review_description'    =>  $this->review_description,
                    'rating'                =>  $this->rating,
                    'user_id'               =>  $this->user_id,
                    'product_id'            =>  $this->product_id
                ),
                array(
                    'review_id'             =>  $this->review_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d'
                ),
                array(
                    '%d'
                )
            );

        } else {
            //new review
            $wpdb->insert(
                $this->getTable(),
                array(
                    'review_title'          =>  $this->review_title,
                    'review_description'    =>  $this->review_description,
                    'rating'                =>  $this->rating,
                    'user_id'               =>  $this->user_id,
                    'product_id'            =>  $this->product_id
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d'
                )
            );

        }

        if ($wpdb->result === false) {
            TppStoreMessages::getInstance()->addMessage('error', array('review_insert'  =>  $wpdb->last_error?:'There was a problem adding your review, please contact us'));
            return false;
        }

        return true;

    }


    public function validate()
    {

        if (intval($this->user_id) == 0) {
            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                TppStoreMessages::getInstance()->addMessage('error', array('review_login' => 'You must login to review'));
            } else {
                $this->user_id = $user->user_id;
            }
        }

        if (intval($this->rating) < 0) {
            $this->rating = 0;
        } elseif (intval($this->rating > 5)) {
            $this->rating = 5;
        }

        if (intval($this->product_id) < 0) {
            TppStoreMessages::getInstance()->addMessage('error', array('review_product' => 'Please select a product to review'));
        }

        if (is_null($this->review_title) || trim($this->review_title) == '') {
            TppStoreMessages::getInstance()->addMessage('error', array('review_title' => 'Please enter a summary title for your review'));
        }


        if (TppStoreMessages::getInstance()->getTotal() > 0) {
            return false;
        }

        return true;

    }

}