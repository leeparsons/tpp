<?php

get_header('blog');


query_posts(array(
    'posts_per_page'    =>  10,
    'paged'             =>  $paged
));


if (have_posts()): ?>
<section class="aside-75 posts">
    <?php while (have_posts()): the_post(); ?>
        <article class="post align-left">
            <header><h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3></header>

            <div class="hentry excerpt">
                <?php the_post_thumbnail('blog_post_thumb', array('class'   =>  'align-left')); ?>
                <?php the_excerpt() ?>
                <div class="author">
                    <a href="<?php echo get_author_posts_url($post->post_author) ?>">By: <?php the_author() ?></a>
                    <span class="align-right"><?php echo get_the_date('jS F, Y') ?></span>
                </div>
            </div>

        </article>
    <?php endwhile; ?>
    <div class="wrap navigation">
        <?php posts_nav_link(' ', '<span class="align-right btn btn-primary">Recent Posts</span>', '<span class="align-left btn btn-primary">Previous Posts</span>'); ?>
    </div>
</section>
<?php endif;

get_sidebar();

get_footer();


 