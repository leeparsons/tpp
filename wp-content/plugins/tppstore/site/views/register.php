<?php
/**
 * User: leeparsons
 * Date: 01/12/2013
 * Time: 09:56
 */
wp_enqueue_style('tpp-login', TPP_STORE_PLUGIN_URL . '/site/assets/css/login.css');


get_header();

?>
    <div class="inner">
        <?php TppStoreMessages::getInstance()->render(); ?>
    </div>

    <div class="half-right">
        <div class="abs-or">
            <span>OR</span>
        </div>
        <header class="inner">
            <h2>Register {create an account}</h2>
        </header>

        <form method="post" id="register_form">

            <div class="form-group">
                <label for="your_first_name">Your First Name</label>
                <input type="text" name="your_first_name" id="your_first_name" value="<?php echo $fname ?>" class="form-control <?php echo (empty($error['first_name'])?'':'alert-danger') ?>">
            </div>

            <div class="form-group">
                <label for="your_last_name">Your Last Name</label>
                <input type="text" name="your_last_name" id="your_last_name" value="<?php echo $lname ?>" class="form-control <?php echo (empty($error['last_name'])?'':'alert-danger') ?>">
            </div>

            <div class="form-group">
                <label for="your_email">Your Email</label>
                <input type="text" name="your_email" id="your_email" value="<?php echo $email ?>" class="form-control <?php echo (empty($error['email'])?'':'alert-danger') ?>">
            </div>

            <div class="form-group">
                <label for="your_name">Your Password</label>
                <input type="password" name="your_password" id="your_password" value="<?php echo $password ?>" class="form-control <?php echo (empty($error['password'])?'':'alert-danger') ?>">
            </div>


            <div class="form-group">
                <label for="your_password_confirm">Confirm Your Password</label>
                <input type="password" name="your_password_confirm" id="your_password_confirm" value="<?php echo $confirm_password; ?>" class="form-control <?php echo (empty($error['password'])?'':'alert-danger') ?>">
            </div>


            <div class="form-group">
                <p>Tell us you are a human, answer the question below:</p>
                <label for="answer" id="question"></label>
                <input type="text" name="answer" id="answer" value="" class="form-control <?php if (!empty($error['answer'])) { ?>alert-danger<?php } ?>">
            </div>

            <script>
                document.getElementById('question').innerHTML = '<?php echo $question['question']; ?>';
                document.getElementById('answer').value = '';
                document.getElementById('answer').onblur = function() {
                    if (this.value == '') {
                        this.setAttribute('class', 'form-control alert-danger')
                    } else {
                        this.setAttribute('class', 'form-control')
                    }
                };
            </script>

            <div class="form-group">

                <label for="newsletter_agree">
                    <input type="checkbox" value="1" name="newsletter_agree" id="newsletter_agree">
                    Sign up to our newsletter to receive latest news and product information.
                </label>

            </div>

            <div class="form-group">
                <input type="submit" value="Register" class="btn btn-primary align-right">
            </div>
        </form>
    </div>
    <div class="half-left">
        <hgroup class="inner">
            <h2>Register using Facebook</h2>
        </hgroup>

        <div class="inner" id="facebook_register_form">
            <a class="fb-login-button-a"></a>
        </div>

    </div>

<?php


get_footer();