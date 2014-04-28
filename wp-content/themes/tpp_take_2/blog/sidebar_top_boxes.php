<?php foreach (array(286, 296) as $cat_id): ?>
    <div class="side-box">
        <?php

        //get the latest post's featured image in this category

        $q = new WP_Query('cat=' . $cat_id . '&posts_per_page=1');

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post(); ?>
            <article class="blog-sidebar-box" onclick="window.location.href='<?php the_permalink(); ?>';">
                <?php the_post_thumbnail('blog_sidebar'); ?>

                <div class="links-wrap">
                    <a href="" class="cat-title"><?php echo get_the_category_by_ID($cat_id); ?></a>
                    <a class="post-title" href="<?php the_permalink(); ?>"><?php echo tpp_limit_content(get_the_title()) ?></a>
                </div>
                </article><?php
            }

        }


        rewind_posts();

        ?>


    </div>
<?php endforeach; ?>