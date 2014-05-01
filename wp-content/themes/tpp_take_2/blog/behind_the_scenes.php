<?php


query_posts('post_type=tpp_interview&posts_per_page=5');

if (have_posts()): ?>

    <section id="behind_the_scenes_articles" class="half-half">

<?php

    $i = 1;

    while (have_posts()):
        the_post();

        if ($i > 1) {
            get_template_part('blog/squares/square_small');
        } else {
            get_template_part('blog/squares/square_half');
        }


        $i++;

    endwhile; ?>
    </section>
<?php endif;

rewind_posts();

wp_reset_query();





