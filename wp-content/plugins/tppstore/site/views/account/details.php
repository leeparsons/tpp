<?php

get_header(); ?>

<?php include TPP_STORE_PLUGIN_DIR . 'site/views/account/sidebar.php'; ?>

<aside class="aside-75">
    <header>
        <h1>My Details</h1>
    </header>

    <div class="entry">
        <form method="post">
            <fieldset>
                <legend>My <?php echo $user->user_type == 'buyer'?'Buyer':'Store Owner' ?> Profile</legend>
                <div class="form-group">
                    <label class="assistive-text" for="first_name">First Name:</label>
                    <input type="text" name="f_name" id="first_name" value="<?php echo $user->first_name ?>" class="form-control" placeholder="First Name">
                </div>
                <div class="form-group">
                    <label class="assistive-text" for="last_name">Last Name:</label>
                    <input type="text" name="l_name" id="last_name" value="<?php echo $user->last_name ?>" class="form-control" placeholder="Last Name">
                </div>

                <div class="form-group">
                    <label class="assistive-text" for="email">Email Address:</label>
                    <input type="text" name="e_mail" id="email" value="<?php echo $user->email ?>" class="form-control" placeholder="Email Address">
                </div>

                <div class="form-group">
                    <label class="assistive-text" for="address">Delivery Address:</label>
                    <textarea name="address" id="address" class="form-control" placeholder="Address"><?php echo $user->address ?></textarea>
                </div>



            </fieldset>
        </form>

        <?php print_r($user); ?>

    </div>
</aside>



<?php get_footer();