<article class="small-square">
    <?php if ($i > 2): ?>
        <div class="blog-divider-grey"></div>
    <?php endif; ?>
    <a class="wrap text-center img-wrap" href="<?php the_permalink(); ?>">
        <?php the_post_thumbnail('blog_small_square'); ?>
    </a>
    <h4 class="align-left wrap"><a href="<?php the_permalink(); ?>"><?php echo tpp_limit_content(get_the_title(), 45) ?></a></h4>
    <a href="<?php the_permalink(); ?>" class="post-meta">
        <span class="wrap"></span>
            <time class="align-left" datetime="<?php echo get_the_date('Y-m-d') ?>"><?php echo get_the_date('F j. Y'); ?></time>
            <span class="align-right"><?php


            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
        </span>
    </a>
</article>