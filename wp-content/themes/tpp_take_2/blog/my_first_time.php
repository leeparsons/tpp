<?php

query_posts('posts_per_page=3');

if (have_posts()) :



    while (have_posts()):
        the_post();

        get_template_part('blog/my_first_time/square_small');



    endwhile;

endif;
?>