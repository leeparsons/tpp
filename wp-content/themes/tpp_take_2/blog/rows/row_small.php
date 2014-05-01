<article class="small_row">
    <a class="align-left" href="<?php the_permalink(); ?>">
        <?php the_post_thumbnail('blog_square_two'); ?>
        <h4 class="align-left wrap"><?php the_title(); ?></h4>
        <span class="wrap"></span>
            <time class="align-left" datetime="<?php echo get_the_date('Y-m-d') ?>"><?php echo get_the_date('F j. Y'); ?></time>
            <span class="align-right"><?php


            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
        </span>
    </a>
</article>