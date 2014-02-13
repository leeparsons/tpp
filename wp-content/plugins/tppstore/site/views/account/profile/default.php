<?php get_header(); ?>

<article class="page-article">
    <header>
        <h1><?php echo ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) ?></h1>
    </header>

    <div class="entry-content">
        <p>
            <?php if (false !== ($src = $user->getProfilePic())): ?>
                <img src="<?php echo $src ?>" alt="" class="align-left">
            <?php endif; ?>
            <span>Member Since: <?php echo date('dS F, Y ', strtotime($user->date_activated)) ?></span>
            <?php if (intval($store->store_id) > 0): ?>
                <span>View <?php echo ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) ?>'s Store: <a href="<?php echo $store->getPermalink() ?>"><?php echo $store->store_name ?></a></span>
            <?php endif; ?>
        </p>
    </div>
</article>

<?php get_footer(); ?>


