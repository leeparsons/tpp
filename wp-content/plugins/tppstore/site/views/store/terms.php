<?php get_header(); ?>

    <article class="page-article">
        <header>
            <h1><?php echo $store->store_name ?> Terms and Conditions</h1>
        </header>

        <div class="wrap">
            <a href="<?php echo $store->getPermalink() ?>" class="btn btn-primary">Back to store</a>
            <br>
            <br>
        </div>

        <div class="wrap">
            <?php echo $store->getPages()->getTerms(); ?>
        </div>

    </article>

<?php get_footer(); ?>