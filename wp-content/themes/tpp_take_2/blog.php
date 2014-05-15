<?php
/*
 * Template Name: blog
 */

get_header('blog'); ?>
    <div class="blog-wrap">
        <section class="blog-main">
            <?php get_template_part('blog/slideshow'); ?>
        </section>

        <div class="aside-40">
            <?php get_template_part('blog/sidebar_top_boxes'); ?>
        </div>

        <section class="blog-main">
            <div class="blog-divider-top"></div>
            <h2>Most Recent Interviews</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/most_recent_interviews'); ?>
        </section>

        <aside class="aside-40 blog-social-counts">
            <a href="" class="social-counter fb-counter"></a>
            <a href="" class="social-counter tw-counter"></a>
            <a href="" class="social-counter li-counter"></a>
            <a href="" class="social-counter ig-counter"></a>
            <a href="" class="social-counter yt-counter"></a>
            <a href="" class="social-counter gp-counter"></a>

            <?php get_template_part('blog/sidebar/newsletter'); ?>

            <?php get_template_part('blog/sidebar/products') ?>
        </aside>


        <section class="blog-main community-challenges">

            <div class="blog-divider-top"></div>
            <h2>Community Challenges</h2>
            <div class="wrap text-center">
                sponsor logos
            </div>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/most_recent_community_challenges'); ?>

        </section>



        <section class="blog-main my-first-time">

            <div class="blog-divider-top"></div>
            <h2>My First Time</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/my_first_time'); ?>

        </section>


        <?php

        $q = new WP_Query(array(
            'post_status'   =>  'publish'
        ));

        ?>
        <aside class="aside-40">
            <div class="widget posts-widget">
                <h3>Recent Articles</h3>
                <div class="blog-divider-top"></div>
                <?php if ($q->have_posts()): ?>
                    <?php while ($q->have_posts()): $q->the_post(); ?>
                        <a class="wrap" href="<?php the_permalink() ?>">
                    <span class="strong"><?php echo get_the_post_thumbnail(get_the_ID(), 'store_related') ?><?php


                        echo tpp_limit_content(get_the_title(), 90) ?><span><?php

                            echo tpp_limit_content(get_the_excerpt(), 110);

                            ?></span></span>
                        </a>

                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </aside>

<!--        <aside class="aside-40">-->
<!--            <h4>Popular Categories</h4>-->
<!--            <div class="blog-divider-top"></div>-->
<!--            --><?php //get_template_part('blog/sidebar/popular_categories'); ?>
<!--        </aside>-->


        <section class="blog-main behind-the-scenes">

            <div class="blog-divider-top"></div>
            <h2>Behind The Scenes</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/behind_the_scenes'); ?>

        </section>

        <?php

        /*
         * TODO: get the most popular posts!
         *

        <section class="blog-main most-popular">

            <div class="blog-divider-top"></div>
            <h2>Most Popular Posts</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/most_popular'); ?>

        </section>

        *
        * end TODO
        */

        ?>
    </div>
<?php get_footer();


