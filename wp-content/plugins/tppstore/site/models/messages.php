<?php
/**
 * User: leeparsons
 * Date: 04/02/2014
 * Time: 12:57
 */
 
class TppStoreModelMessages extends TppStoreAbstractModelResource {

    public $message_id = null;
    public $sender = null;
    public $receiver = null;
    public $parent_id = null;
    public $created_on = null;
    public $status = null;



    protected $_allowed_types = array(
        'message'
    );

    protected $_allowed_statuses = array(
        'unread',
        'read',
        'deleted'
    );

    protected $_table = 'shop_messages';





    public function getUnreadMessages($page = 1, $count = false)
    {

        if (intval($this->receiver) == 0) {
            return $count === true?0:false;
        }

        global $wpdb;

        if ($count === true) {
            $c = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(receiver) AS c FROM " . $this->getTable() . " WHERE receiver = %d AND status = 'unread'",
                    $this->receiver
                )
            );

            return $c;
        }

        if ($page == 0) {
            $page = 1;
        }

        $start = $page * 20;



        $wpdb->query(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE receiver = %d AND status = 'unread'",
                $this->receiver
            )
        );

        $rows = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $rows[$row->message_id] = new TppStoreModelMessage();
                $rows[$row->message_id]->setData($row);
            }
        }


        return $rows;
    }


    public function getMessageHistory()
    {
        if (trim($this->parent_id) == '') {
            return array();
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT m.*, u.first_name, u.last_name, u2.first_name AS receiver_first_name, u2.last_name AS receiver_last_name FROM " . $this->getTable() . " AS m
                LEFT JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u ON u.user_id = m.sender
                LEFT JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u2 ON u2.user_id = m.receiver
                WHERE parent_id = %s
                ORDER BY created_on ASC",
                $this->parent_id
            ),
            OBJECT_K
        );

        $messages = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $messages[$row->message_id] = new TppStoreModelMessage();
                $messages[$row->message_id]->setData($row);
                $messages[$row->message_id]->getSender(false)->setData(array(
                    'first_name'    =>  $row->first_name,
                    'last_name'     =>  $row->last_name,
                    'user_id'       =>  $row->sender
                ));
                $messages[$row->message_id]->getReceiver(false)->setData(array(
                    'first_name'    =>  $row->receiver_first_name,
                    'last_name'     =>  $row->receiver_last_name,
                    'user_id'       =>  $row->receiver
                ));
            }
        }

        return $messages;

    }



    public function getMessages($page = 1, $count = false)
    {

        if (intval($this->receiver) < 1) {
            return array();
        }

        global $wpdb;

        if ($count === true) {
            $c = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT count(receiver) AS c FROM " . $this->getTable() . " AS m
                WHERE receiver = %d",
                    $this->receiver
                )
            );

            return $c;
        }


        if ($page < 1) {
            $page = 1;
        }

        $start = ($page-1) * 20;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " AS m
                LEFT JOIN " . TppStoreModelUser::getInstance()->getTable() .  " AS u ON u.user_id = m.sender
                WHERE receiver = %d
                GROUP BY parent_id
                ORDER BY created_on DESC

                LIMIT $start, 20
                ",
                $this->receiver
            )
        );

        $messages = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $messages[$row->message_id] = new TppStoreModelMessage();
                $messages[$row->message_id]->setData($row);
                $messages[$row->message_id]->sender_user = new TppStoreModelUser();
                $messages[$row->message_id]->sender_user->setData(array(
                    'user_id'       =>  $row->user_id,
                    'first_name'    =>  $row->first_name,
                    'last_name'     =>  $row->last_name
                ));
            }
        }

        return $messages;
    }


}