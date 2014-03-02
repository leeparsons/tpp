<?php
/**
 * User: leeparsons
 * Date: 21/02/2014
 * Time: 11:28
 */
 
class TppContactUs {

    public $body = '';
    public $name = '';
    public $email = '';

    private $messages = array(
        'error'     =>  array(),
        'message'   =>  array()
    );

    protected static $_self = false;

    protected function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$_self === false) {
            self::$_self = new TppContactUs();
        }

        return self::$_self;
    }


    public function addMessage($type = 'error', $message)
    {
        $this->messages[$type][] = $message;
    }

    public function renderMessages()
    {
        if ($this->countErrors() > 0) {
            foreach ($this->messages['error'] as $error) {
                echo '<p class="wp-error">' . $error . '</p>';
            }
        }

        if ($this->countMessages() > 0) {
            foreach ($this->messages['message'] as $message) {
                echo '<p class="wp-message">' . $message . '</p>';
            }
        }

    }

    public function countErrors()
    {
        return count($this->messages['error']);
    }

    public function countMessages()
    {
        return count($this->messages['message']);
    }


    public function renderContactForm()
    {
        include get_template_directory() . '/contact-us/form.php';
    }


    public function actionPost()
    {

        global $wp_query;


        $wp_query->is_404 = false;

        if (filter_input(INPUT_POST, 'cu_url', FILTER_SANITIZE_STRING) != '') {
            $this->addMessage('error', 'Sorry, we seem to think you area  spam bot.');
        }

        $nonce = filter_input(INPUT_POST, 'contact_submission', FILTER_SANITIZE_STRING);

        if (!wp_verify_nonce($nonce, 'contact_submission')) {

            $this->addMessage('Could not verify you');

        } else {
            $this->name = filter_input(INPUT_POST, 'cu_name', FILTER_SANITIZE_STRING);
            $this->email = filter_input(INPUT_POST, 'cu_email', FILTER_SANITIZE_STRING);

            $this->body = filter_input(INPUT_POST, 'cu_message', FILTER_UNSAFE_RAW);



            if (trim($this->name) == '') {
                $this->addMessage('error', 'Please enter your name');
            }


            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->addMessage('error', 'Please enter a valid email');
            }

            if (trim($this->body) == '') {
                $this->addMessage('error', 'Please enter your message');
            }



            if ($this->countErrors() == 0) {

                $to_email = get_option('admin_email');

                $headers = "From: Rosie Parsons <no-reply@thephotographyparlour.com>" . "\r\n";
                $headers .= "Reply-to: no-reply@thephotographyparlour.com" . "\r\n";
                $headers .= "Return-Path: no-reply@thephotographyparlour.com" . "\r\n";

                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
                $headers .= "X-Priority: 3" . "\r\n";
                $headers .= "X-Mailer: PHP". phpversion() . "\r\n";

                $html = array();

                $html[] = "<p>You have received an email from The Photography Parlour.</p>";
                $html[] =  "<p>From: " . $this->name . "</p>";
                $html[] =  "<p>Email: " . $this->email . "</p>";
                $html[] =  "<p>Message: " . nl2br($this->body) . "</p>";

                $html = implode("", $html);

                mail(
                    $to_email,
                    'Contact form submission from The Photography Parlour ' . date('dS M, Y H:is'),
                    $html,
                    $headers,
                    '-f no-reply@thephotographyparlour.com'
                );


                $this->addMessage('message', 'Thank you for your message. We will be in touch soon.');

            }
        }


    }

}