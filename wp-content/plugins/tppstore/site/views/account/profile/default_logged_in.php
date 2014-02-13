<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>
    <article class="page-article">
        <header>
            <h1><?php echo ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) ?></h1>
        </header>

        <?php if (false !== $user->can_edit): ?>

            <a href="/shop/myaccount/profile/edit" class="btn btn-primary">Edit</a>
            <a href="/shop/dashboard" class="btn btn-default">Cancel</a>

            <br>

            <br>
        <?php endif; ?>

        <div class="entry-content">
            <p>
                <?php if (false !== ($src = $user->getProfilePic())): ?>
                    <img src="<?php echo $src ?>" alt="" class="align-left">
                <?php endif; ?>

                <span>Member Since: <?php echo date('dS F, Y ', strtotime($user->date_activated)) ?></span>
                <?php if (intval($store->store_id) > 0): ?>
                    <br>
                    <br>
                    <span>View <?php echo ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) ?>'s Store: <a href="<?php echo $store->getPermalink() ?>"><?php echo $store->store_name ?></a></span>
                <?php endif; ?>
            </p>




        </div>





    </article>
<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';