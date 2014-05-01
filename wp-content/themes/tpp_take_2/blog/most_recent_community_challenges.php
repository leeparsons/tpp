<?php

query_posts('posts_per_page=5');

if (have_posts()) :

    $i = 1; ?>

    <section class="community-challenges">
    <?php

    while (have_posts()):
        the_post();

        if ($i == 1) {
            get_template_part('blog/squares/square_half');
        } else {
            get_template_part('blog/rows/row_small');
        }


        $i++;

    endwhile; ?>

    </section>
<?php endif;
