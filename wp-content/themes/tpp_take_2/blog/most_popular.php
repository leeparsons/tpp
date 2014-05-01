<?php

query_posts('posts_per_page=4');

if (have_posts()): ?>

    <section class="half-half">
        <?php

        while (have_posts()):
            the_post();

            get_template_part('blog/squares/square_two');

        endwhile; ?>

    </section>
<?php endif;
