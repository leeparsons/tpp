<?php

query_posts('posts_per_page=5');

if (have_posts()) :

    $i = 1;

    while (have_posts()):
        the_post();

        if ($i == 1) {
            get_template_part('blog/community_challenges/square_large');
        } else {
            get_template_part('blog/community_challenges/square_small');
        }


        $i++;

    endwhile;

endif;
?>