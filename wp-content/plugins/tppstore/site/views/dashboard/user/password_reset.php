<?php
/**
 * User: leeparsons
 * Date: 26/02/2014
 * Time: 07:55
 */
 
get_header(); ?>

<article class="article-page">
    <header class="wrap">
        <h1>Forgotten password</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render(); ?>

    <div class="hentry wrap">
        <pre>If you have forgotten your password, enter your email address for your account below and we will send you your new password.<br><br>If you logged in with Facebook, then your email address will be the same as your Facebook email address.</pre>
    </div>

    <form method="post" action="/shop/password_reset/">
        <div class="form-group">
            <label for="email">Enter your email</label>
            <input class="form-control" type="text" name="email" id="email" value="<?php $user->email ?>" placeholder="Email">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Reset Password">
        </div>
    </form>

</article>


<?php get_footer();