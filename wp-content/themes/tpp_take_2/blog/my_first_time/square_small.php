<article class="small-square-3 align-left <?php echo $i < 3?'small-square-3-mr':'' ?>">
    <a class="align-left img-wrap" href="<?php the_permalink(); ?>">
        <?php the_post_thumbnail('blog_small_my_first_time'); ?>
    </a>
    <h4 class="align-left wrap"><a class="align-left" href="<?php the_permalink(); ?>"><?php echo tpp_limit_content(get_the_title(), 60); ?></a></h4>

        <span class="wrap"></span>

            <a class="wrap post-meta" href="<?php the_permalink(); ?>"><time class="align-left published" datetime="<?php echo get_the_date('Y-m-d') ?>"><?php echo get_the_date('F j. Y'); ?></time>
            <span class="align-right"><?php


            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
        </span></a>
</article>