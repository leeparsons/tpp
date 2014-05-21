<?php

query_posts('posts_per_page=3');

if (have_posts()) :

    $i = 0;

    while (have_posts()):
        the_post();

        $i++;

        include get_template_directory() . '/blog/my_first_time/square_small.php';

    endwhile;

endif;
?>