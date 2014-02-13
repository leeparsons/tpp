<?php
/**
 * User: leeparsons
 * Date: 20/01/2014
 * Time: 13:07
 */

get_header('blog'); ?>

<article class="aside-75">
    <header>
        <h1><?php echo trim(get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')) ?></h1>
        <div class="author-meta">
            <?php get_avatar(get_the_author_meta('ID'), 100); ?>
            <p><?php the_author_meta('description') ?></p>
        </div>
    </header>

    <?php if (have_posts): ?>
        <div class="posts">
            <h3>Posts by this author</h3>

            <?php while (have_posts()): the_post(); ?>
                <?php get_template_part('post/listing') ?>
            <?php endwhile; ?>
        </div>

    <?php endif; ?>

</article>



<?php get_sidebar();

get_footer();