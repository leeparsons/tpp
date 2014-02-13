<?php
/**
 * User: leeparsons
 * Date: 04/02/2014
 * Time: 12:57
 */
 
class TppStoreModelMessage extends TppStoreAbstractModelBase {

    public $id = null;
    public $message = null;
    public $from_user = null;
    public $to_user = null;
    public $parent = null;
    public $created_on = null;
    public $status = null;
    public $subject = null;


    protected $_allowed_types = array(
        'message'
    );

    protected $_allowed_statuses = array(
        'draft',
        'read',
        'unread',
        'trash'
    );

    public function getTitle()
    {
        return '';
    }

    public function getDescription()
    {
        return '';
    }

    public function getSafeMessage()
    {
        return esc_textarea($this->message);
    }


    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);

            return true;
        } else {
            return false;
        }
    }

    public function save()
    {

    }


    public function validate()
    {

    }
}