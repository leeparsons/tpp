<?php
/**
 * User: leeparsons
 * Date: 06/12/2013
 * Time: 17:38
 */

wp_enqueue_style('tpp-login', TPP_STORE_PLUGIN_URL . '/site/assets/css/login.css');

get_header();

?>

<article class="page-article">



    <div class="half-right">
        <div class="abs-or">
            <span>OR</span>
        </div>
        <header>
            <h2>Login</h2>
        </header>

        <?php TppStoreMessages::getInstance()->render() ?>


        <form method="POST" action="?action=tpp_login">

        <?php wp_nonce_field('store_login', 'login_nonce') ?>

            <?php if (!is_null($redirect)): ?>
            <input type="hidden" name="redirect" value="<?php echo $redirect ?>">
    <?php endif; ?>
        <input type="hidden" name="action" value="tpp_login">
        <div class="form-group">
            <label for="user_email">Email Address:</label>
            <input type="text" class="form-control" value="<?php echo $user->email; ?>" id="user_email" name="user_email" placeholder="Email Address">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" placeholder="Password" class="form-control" value="" id="password" name="tpp_password">
        </div>

        <div class="form-group">
            <input type="submit" class="form-control align-right btn-primary btn" value="Login">
        </div>

    </form>
    </div>

    <div class="half-left">
        <hgroup class="inner">
            <h2>Sign in with Facebook</h2>
        </hgroup>

        <div class="inner" id="facebook_register_form">
            <a class="fb-login-button-a" ></a>
        </div>

        <div class="wrap">
            <hr>

            <header class="wrap">
                <h2>Don't have an account?</h2>
                <a href="/shop/store_register" class="btn btn-primary">Register for an account</a>
            </header>


        </div>

    </div>

</article>

<?php get_footer();