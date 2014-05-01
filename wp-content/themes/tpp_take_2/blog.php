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
            subscribe
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


        <section class="blog-main behind-the-scenes">

            <div class="blog-divider-top"></div>
            <h2>Behind The Scenes</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/behind_the_scenes'); ?>

        </section>

        <section class="blog-main most-popular">

            <div class="blog-divider-top"></div>
            <h2>Most Popular Posts</h2>
            <div class="blog-divider-bottom"></div>

            <?php get_template_part('blog/most_popular'); ?>

        </section>



        <?php get_template_part('sidebars/general'); ?>
    </div>
<?php get_footer();


