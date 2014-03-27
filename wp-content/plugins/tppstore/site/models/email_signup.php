<?php
/**
 * User: leeparsons
 * Date: 15/03/2014
 * Time: 19:09
 */
 
class TppStoreModelEmailSignup extends TppStoreAbstractModelResource {

    public $email = null;
    public $source = null;

    public $first_name = null;
    public $last_name = null;
    public $user_id = null;

    protected $_table = 'shop_email_signups';

    public function save()
    {

        if ('' != filter_var($this->email, FILTER_VALIDATE_EMAIL) && trim($this->source) != '') {
            global $wpdb;
            $wpdb->insert(
                $this->getTable(),
                array(
                    'email'         =>  $this->email,
                    'source'        =>  $this->source,
                    'user_id'       =>  $this->user_id,
                    'first_name'    =>  $this->first_name,
                    'last_name'     =>  $this->last_name
                ),
                array(
                    "%s",
                    "%s",
                    "%d",
                    "%s",
                    "%s"
                )
            );
        }

    }


}