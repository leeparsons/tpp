<?php
/**
 * User: leeparsons
 * Date: 24/02/2014
 * Time: 08:01
 */
 
class TppStoreModelMessage extends TppStoreAbstractModelBase {

    public $message_id = null;
    public $message = null;
    public $sender = null;
    public $receiver = null;
    public $parent_id = null;
    public $created_on = null;
    public $status = null;
    public $subject = null;
    public $responded_on = null;

    public $sender_user = null;
    public $receiver_user = null;

    protected $_allowed_types = array(
        'message'
    );

    protected $_allowed_statuses = array(
        'unread',
        'read',
        'deleted'
    );

    protected $_table = 'shop_messages';

    public function getSeoTitle()
    {
        return '';
    }

    public function getSeoDescription()
    {
        return '';
    }

    public function getSender($auto_load = true)
    {
        if (is_null($this->sender_user)) {
            $this->sender_user = new TppStoreModelUser();
            if (true === $auto_load) {
                $this->sender_user->setData(array(
                    'user_id'   =>  $this->sender
                ))->getUserByID();
            }
        }

        return $this->sender_user;
    }

    public function getReceiver($auto_load = true)
    {
        if (is_null($this->receiver_user)) {
            $this->receiver_user = new TppStoreModelUser();
            if (true === $auto_load) {
                $this->receiver_user->setData(array(
                    'user_id'   =>  $this->receiver
                ))->getUserByID();
            }
        }

        return $this->receiver_user;
    }


    public function getRespondedDate()
    {
        return date('jS F, Y H:i:s', strtotime($this->responded_on));
    }

    public function getReceivedLapseTime()
    {

        $date1 = new DateTime($this->created_on, new DateTimeZone('Europe/London'));

        $date2 = new DateTime('now', new DateTimeZone('Europe/London'));

        $diff = $date1->diff($date2);

        if ($diff->y > 0) {
            return $date1->format('F Y');
        } elseif ($diff->m > 0) {
            if ($date2->format('Y') < $date1->format('Y')) {
                return $date1->format('jS F');
            } else {
                return $date1->format('F Y');
            }
        } elseif ($diff->d > 0) {
            return $diff->d . ' days';
        } elseif ($diff->h < 1) {
                return $diff->i . ' minutes';
        } else {
                return $date1->format('H:i');
        }



    }

    public function getReceivedDate()
    {
        return date('jS F, Y H:i:s', strtotime($this->created_on));
    }


    public function getHtmlMessage()
    {
        return nl2br($this->message);
    }

    public function getSafeMessage($limit = 0)
    {
        if ($limit > 0) {
            return esc_textarea(substr(strip_tags($this->message), 0, $limit));
        } else {
            return esc_textarea($this->message);
        }

    }


    public function getMessageById()
    {
        if (trim($this->message_id) == '') {
            $this->reset();
        } else {
            global $wpdb;
            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE message_id = %s",
                    $this->message_id
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                $this->setData($wpdb->last_result[0]);
            } else {
                $this->reset();
            }
        }

        return $this;
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);

            $receiver = filter_input(INPUT_POST, 'receiver', FILTER_SANITIZE_NUMBER_INT);

            $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);

            if (trim($subject) != '') {
                $this->subject = $subject;
            }

            if (intval($receiver) > 0) {
                $this->receiver = $receiver;
            }

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


        if (trim($this->message_id) == '') {
            $message_id = uniqid('ms_');
        } else {
            $message_id = $this->message_id;
        }

        $wpdb->replace(
            $this->getTable(),
            array(
                'message_id'    =>  $message_id,
                'parent_id'     =>  trim($this->parent_id) == ''?$message_id:$this->parent_id,
                'status'        =>  $this->status,
                'subject'       =>  $this->subject,
                'sender'        =>  $this->sender,
                'receiver'      =>  $this->receiver,
                'message'       =>  $this->message,
                'responded_on'  =>  $this->responded_on
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s'
            )
        );

        if ($wpdb->result === false || $wpdb->rows_affected == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to send your message. ' . $wpdb->last_error);
        } else {
            $this->message_id = $message_id;
        }

        return $wpdb->result;

    }

    public function delete()
    {
        if (trim($this->message_id) == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'No message id detected, could not delete your message');
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . $this->getTable() . " SET status = 'deleted' WHERE message_id = %s OR (parent_id = %s AND created_on < %s) AND receiver = %d",
                array(
                    $this->message_id,
                    $this->parent_id,
                    $this->created_on,
                    $this->receiver
                )
            )
        );

        if ($wpdb->result === false) {
            TppStoreMessages::getInstance()->addMessage('error', 'There was an error deleting your message: ' . $wpdb->last_error);
            return false;
        } else {
            TppStoreMessages::getInstance()->addMessage('message', 'Message deleted');
        }

        return true;

    }

    public function validate()
    {

        $error = false;

        if (intval($this->sender) == 0) {
            if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                TppStoreMessages::getInstance()->addMessage('error', 'Please login to ask a question');
                $error = true;
            } else {
                $this->sender = $user->user_id;
            }
        }

        if (trim($this->message) == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please write your message');
            $error = true;
        }

        if (intval($this->receiver) == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Please select the receiver');
            $error = true;
        }

        if (trim($this->subject) == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please enter the subject of the message');
            $error = true;
        }

        if (is_null($this->status)) {
            $this->status = $this->_allowed_statuses[0];
        }

        return !$error;

    }
}