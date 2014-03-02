<?php
/**
 * User: leeparsons
 * Date: 24/02/2014
 * Time: 07:52
 */

class TppStoreControllerMessages extends TppStoreAbstractBase {


    public function actionCreate()
    {

        $to = filter_input(INPUT_POST, 'receiver', FILTER_SANITIZE_NUMBER_INT);

        if (intval($to) == 0) {
            TppStoreMessages::getInstance()->addMessage('error', 'Please specify a recipient');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToReferer();
        }

        if (false === ($from = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You need to login to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin();
        }

        //validate the message

        $message = $this->getMessageModel();

        if (false === $message->readFromPost()) {
            TppStoreMessages::getInstance()->addMessage('error', 'You need to login to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToReferer();
        }





        if (trim($message->subject) == '') {
            $message->setData(array(
                'subject'   =>  'Question from: ' . $from->first_name
            ));
        }

        if (false !== $message->save()) {
            TppStoreMessages::getInstance()->addMessage('message', 'Your message has been sent.');

            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/private_message_received.php';

            $body = ob_get_contents();

            ob_end_clean();

            $this->sendMail($message->getReceiver(true)->email, 'You have received a private message on your account', $body);

        } else {
            TppStoreMessages::getInstance()->addMessage('message', 'Your message could not be sent.');
        }
        TppStoreMessages::getInstance()->saveToSession();
        $this->redirectToReferer();



    }

    public function actionReply()
    {

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        $message_id = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_STRING);
        $nonce = filter_input(INPUT_POST, 'respond_nonce', FILTER_SANITIZE_STRING);
        $response = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);



        if ($nonce != md5(AUTH_SALT . $message_id . 'respond')) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        $message = $this->getMessageModel()->setData(array(
            'message_id'    =>  $message_id
        ))->getMessageById();

        if ($message_id != $message->message_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        if (trim($response) == '') {
            wp_enqueue_style('dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/dashboard.css');
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            include TPP_STORE_PLUGIN_DIR . 'site/views/account/messages/default.php';
            exit;
        }

        $subject = $message->subject;

        if (substr($subject, 0, 3) != 'RE:') {
            $subject = 'RE:' . $subject;
        }

        $reply = $this->getMessageModel()->setData(array(
            'receiver'      =>  $message->sender,
            'sender'        =>  $user->user_id,
            'parent_id'     =>  trim($message->parent_id) == ''?$message_id:$message->parent_id,
            'status'        =>  'unread',
            'message'       =>  $response,
            'subject'       =>  $subject
        ));



        if (false === $reply->save()) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        } else {

            $message->setData(array(
                'responded_on'  =>  date('Y-m-d H:i:s')
            ))->save();

            $message = $reply;

            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/private_message_received.php';

            $body = ob_get_contents();

            ob_end_clean();

            $this->sendMail($message->getReceiver(true)->email, 'You have received a private message on your account', $body);


            TppStoreMessages::getInstance()->addMessage('message', 'Your response has been sent.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }


    }

    public function actionDelete()
    {
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        $message_id = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_STRING);
        $nonce = filter_input(INPUT_POST, 'delete_nonce', FILTER_SANITIZE_STRING);

        if ($nonce != md5(AUTH_SALT . $message_id . 'delete')) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        //get the message by the id, and determine if this user can actually delete this message

        $message = $this->getMessageModel()->setData(array(
            'message_id'    =>  $message_id
        ))->getMessageById();

        if ($message->receiver != $user->user_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to complete this action');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        if (false === $message->setData(array(
                    'status'    =>  'deleted'
                ))->delete()) {
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('message/' . $message_id);
        } else {
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

    }


    public function renderMessageList()
    {
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }


        $page = get_query_var('page');

        $total_messages = $this->getMessagesModel()->setData(array(
            'receiver'   =>  $user->user_id
        ))->getMessages(null, true);

        if ($total_messages > 0) {
            $messages = $this->getMessagesModel()->setData(array(
                'receiver'   =>  $user->user_id
            ))->getMessages($page);
        } else {
            $messages = array();
        }

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $store = $this->getStoreModel();
        }

        include TPP_STORE_PLUGIN_DIR . 'site/views/account/messages/list.php';

    }

    public function renderMessage()
    {

        $message_id = get_query_var('args');

        if ($message_id == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'You are unauthorised to view this message');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }


        $message = $this->getMessageModel()->setData(array(
            'message_id'   =>   $message_id
        ))->getMessageById();

        if (trim($message->message_id) == '' || $message->receiver !== $user->user_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are unauthorised to view this message');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount('messages/');
        }

        if ($message->status == 'unread') {
            $message->setData(array(
                'status'    =>  'read'
            ))->save();
        }


        $message_history = $this->getMessagesModel()->setData(array(
            'parent_id'    =>  $message->parent_id
        ))->getMessageHistory();

        $store = TppStoreControllerStore::getInstance()->loadStoreFromSession();


        include TPP_STORE_PLUGIN_DIR . 'site/views/account/messages/default.php';


    }

}