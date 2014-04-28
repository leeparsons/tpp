<?php


query_posts('post_type=tpp_interview&posts_per_page=4');

if (have_posts()): ?>

    <section id="interview_articles">

<?php

    $i = 1;

    while (have_posts()):
        the_post();

        if ($i > 1) {
            get_template_part('blog/interviews/square_small');
        } else {
            get_template_part('blog/interviews/square_large');
        }


        $i++;

    endwhile; ?>
    </section>
<?php endif;

rewind_posts();

wp_reset_query();





