<?php

get_header();

if (have_posts()):
    while (have_posts()):

        the_post(); ?>

        <article class="page-article aside-60">

            <header>
                <h1><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>

                <?php TppContactUs::getInstance()->renderContactForm() ?>

            </div>

        </article>

        <?php get_template_part('sidebars/page'); ?>

    <?php endwhile;
endif;

get_footer();