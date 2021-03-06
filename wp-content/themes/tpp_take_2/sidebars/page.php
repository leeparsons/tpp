<section class="aside-40 sidebar post-aside">
    <?php

    $q = new WP_Query(array(
        'post_status'   =>  'publish'
    ));

    ?>
    <div class="widget posts-widget">
        <h4>Recent Articles</h4>
        <?php if ($q->have_posts()): ?>
            <?php while ($q->have_posts()): $q->the_post(); ?>
                <a class="wrap" href="<?php the_permalink() ?>">
                    <span class="strong"><?php echo get_the_post_thumbnail(get_the_ID(), 'store_related') ?><?php the_title() ?></span>
                </a>

            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <?php TppStoreControllerProduct::getInstance()->renderLatestProductsSideBar(); ?>

    <div class="widget">
        <h4>Blog Categories</h4>
        <ul class="blog-categories">
            <?php wp_list_categories(array(
                'title_li'  =>  ''
            )) ?>
        </ul>
    </div>

</section>