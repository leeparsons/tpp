<?php
/**
 * User: leeparsons
 * Date: 27/09/2013
 * Time: 13:51
 */
 
class TppStoreMessages {

    protected static $instance = false;
    protected $errors = array();

    protected function __construct()
    {
        $this->errors = new WP_Error();

        if (!session_id()) {
            session_start();
        }

        $this->loadFromSession();
    }


    public static function getInstance()
    {

        if (!self::$instance) {
            self::$instance = new TppStoreMessages();
        }
        return self::$instance;

    }

    public function addMessage($code = null, $message = null, $data = false)
    {

        if (empty($code) || empty($message)) {
            return false;
        }




        if (is_array($message)) {
            $errors = $this->getMessages();

            if (!empty($errors)) {
                $keys = array_keys($message);
                if (isset($errors[$keys[0]])) {
                    return false;
                }
            }
        }

        $this->errors->add($code, $message, $data);

    }

    public function getMessages($type = 'error')
    {

        $messages = $this->errors->get_error_messages();


        if (!empty($messages)) {

            $return = array();
            foreach ($messages as $key => $message) {

                if (is_array($message)) {
                    foreach ($message as $message_key => $value) {
                        $return[$message_key] = $value;
                    }
                } else {
                    $return[$key] = $message;
                }
            }
        }

        return $return;
    }


    public function renderAdmin()
    {
        $html = array();

        if ($this->getTotal() > 0) {

            $messages_array = $this->errors->errors;

            foreach ($messages_array as $type => $messages) {
                if ($type == 'message') {
                    $class = 'updated ';
                } else {
                    $class = 'error';
                }

                if (is_array($messages)) {


                    foreach ($messages as $id => $message) {
                        if (is_array($message)) {

                            foreach ($message as $m_id => $v) {



                                $html[] = array('<div id="message" class="' . $class . '">');
                                $html[] = '<div class="' . $class . '" id="' . $class . '-' . $id . '">' . $v . '</div>';
                                $html[] = '</div>';

                            }
                        } else {
                            $html[] = '<div class="' . $class . '" id="error-' . $id . '">' .  $message . '</div>';
                        }
                    }

                } else {
                    $html[] = '<div class="' . $class . '">' . $messages . '</div>';
                }

            }


        }


        echo implode('', $html);


    }

    public function render($echo = true, $error_key = false)
    {

        $html = array();

        if ($this->getTotal() > 0) {

            $messages_array = $this->errors->errors;

            $html = array('<div id="message" class="error">');

            foreach ($messages_array as $type => $messages) {

                if (is_array($messages)) {
                    foreach ($messages as $id => $message) {
                        if (is_array($message)) {
                            foreach ($message as $m_id => $v) {
                                if (false === $error_key) {
                                    $html[] = '<div class="wp-' . $type . '" id="error-' . $id . '">' . $v . '</div>';
                                } elseif ($error_key == $m_id || stripos($m_id, $error_key) !== false) {
                                    $html[] = '<div class="wp-' . $type . '" id="error-' . $id . '">' . $v . '</div>';
                                }
                            }
                        } else {
                            $html[] = '<div class="wp-' . $type . '" id="error-' . $id . '">' .  $message . '</div>';
                        }
                    }

                } else {
                    $html[] = '<div class="wp-error">' . $messages . '</div>';
                }

            }

            $html[] = '</div>';

        }

        if ($echo) {
            echo implode('', $html);
        } else {
            return implode('', $html);
        }

    }

    public function saveToSession()
    {
        $_SESSION['tpp_store_messages'] = $this->errors;

    }

    protected function loadFromSession()
    {
        if (!empty($_SESSION['tpp_store_messages'])) {
            $this->errors = clone $_SESSION['tpp_store_messages'];
            $_SESSION['tpp_store_messages']->errors = null;
            unset($_SESSION['tpp_store_messages']);
        }

    }

    public function getTotal()
    {
        return count($this->errors->errors);
    }
}