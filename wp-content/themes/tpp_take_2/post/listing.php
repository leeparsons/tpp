<article class="post align-left">
    <header><h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3></header>

    <div class="hentry excerpt">
        <?php the_post_thumbnail('blog_post_thumb', array('class'   =>  'align-left')); ?>
        <?php the_excerpt() ?>
        <div class="author">
            <a href="<?php echo get_author_posts_url($post->post_author) ?>"><?php the_author() ?></a>
            <span class="align-right"><?php echo get_the_date('jS F, Y') ?></span>
        </div>
    </div>

</article>
<?php flush();