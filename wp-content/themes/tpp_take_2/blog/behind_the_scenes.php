<?php


query_posts('posts_per_page=5&cat=111');

if (have_posts()): $i = 1; ?>

    <section id="behind_the_scenes_articles" class="half-half artillery">
        <?php

        while (have_posts()):
        the_post();

        if ($i == 1): ?>
        <div class="align-left half-wrap">
            <?php get_template_part('blog/squares/square_half') ?>
        </div>
        <div class="align-right half-wrap">
            <?php else: ?>
                <?php get_template_part('blog/rows/row_small'); ?>
                <div class="blog-divider-grey"></div>
            <?php endif;


            $i++;

            endwhile; ?>
        </div>
    </section>
<?php endif;

rewind_posts();

wp_reset_query();





