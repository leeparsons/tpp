<?php
/**
 * User: leeparsons
 * Date: 23/03/2014
 * Time: 11:09
 */
 
class TppAntiSpam extends TppStoreAbstractInstantiable {


    public static function init()
    {
        add_filter('preprocess_comment', function() {
            TppAntiSpam::getInstance()->preCommentPost();
            }, 1);

        add_action( 'comment_form_after_fields', function() {

            TppAntiSpam::getInstance()->addCommentCaptcha();

        }, 1);

    }


    public function addCommentCaptcha()
    {


        $question = $this->generateResultSet();

        $this->saveCaptchaResultSet($question);

        ?><div class="form-group">
            <pre>Answer the question to prove you are human:</pre>
            <label for="tpp_comment_answer"><?php echo $question['question']; ?></label>
            <input type="text" id="tpp_comment_answer" aria-required="true" class="form-control" name="tpp_comment_answer">
        </div>
        <?php


    }

    public function preCommentPost($comment)
    {

        // skip the captcha if user is logged in and the settings allow
        if (is_user_logged_in()) {
            // skip the CAPTCHA display if the minimum capability is met
            return $comment;
        }

        // skip captcha for comment replies from admin menu
        if ( isset($_POST['action']) && $_POST['action'] == 'replyto-comment' &&
            ( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false )) ) {
            // skip capthca
            return $comment;
        }

        // Skip captcha for trackback or pingback
        if ( $comment['comment_type'] != '' && $comment['comment_type'] != 'comment' ) {
            // skip capthca
            return $comment;
        }

        $validate_result = $this->validateCaptcha();

        if (false === $validate_result) {
            wp_die( "<strong>Sorry please fill out the captcha answer</strong>" );
        }

        return($comment);


    }


    private function generateResultSet()
    {
        $results = array(
            array(
                'question'  =>  'What colour is the sky?',
                'answer'    =>  'blue'
            ),
            array(
                'question'  =>  'What colour is the yolk of an egg?',
                'answer'    =>  'yellow'
            ),
            array(
                'question'  =>  'What colour is grass?',
                'answer'    =>  'green'
            ),
            array(
                'question'  =>  'What is 4 + 5?',
                'answer'    =>  9
            ),
            array(
                'question'  =>  'What colour is snow?',
                'answer'    =>  'white'
            ),
            array(
                'question'  =>  'In which City is Manhattan located?',
                'answer'    =>  'new york'
            ),
            array(
                'question'  =>  'In which country is Tokyo?',
                'answer'    =>  'japan'
            )
        );

        return $results[rand(0, count($results) - 1)];

    }

    private function validateCaptcha()
    {

        if (false !== ($result_set = $this->loadCaptchaResultSet())) {

            $answer = filter_input(INPUT_POST, 'tpp_comment_answer', FILTER_SANITIZE_STRING);

            if (strtolower($result_set['answer']) == strtolower($answer)) {
                return true;
            }
        }

        return false;
    }

    private function loadCaptchaResultSet()
    {
        $this->startSession();

        if (isset($_SESSION['tpp_comment_captcha'])) {
            return $_SESSION['tpp_comment_captcha'];
        } else {
            return false;
        }

    }

    private function saveCaptchaResultSet($result_set)
    {
        $this->startSession();
        $_SESSION['tpp_comment_captcha'] = $result_set;

    }

    private function destroyCaptchaResultSet()
    {
        $this->startSession();
        if (isset($_SESSION['tpp_comment_captcha'])) {
            $_SESSION['tpp_comment_captcha'] = null;
            unset($_SESSION['tpp_comment_captcha']);
        }
    }

}

