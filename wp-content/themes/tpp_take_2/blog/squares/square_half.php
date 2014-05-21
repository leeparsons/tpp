<article class="square-half">
    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink() ?>">
            <?php the_post_thumbnail('blog_square_half') ?>
        </a>
    <?php endif; ?>
    <div class="square-half-text wrap hentry">
        <h3 class="wrap entry-title"><a class="wrap" href="<?php the_permalink(); ?>"><?php echo tpp_limit_content(get_the_title(), 70); ?></a></h3>
        <div class="wrap square-half-meta post-meta">
            <time class="align-left published" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('F, j. Y'); ?></time>

            <span class="align-right"><?php

            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
        </div>
        <div class="entry-excerpt wrap">
            <?php echo tpp_limit_content(strip_tags(get_the_content()), 300); ?>
        </div>
    </div>
</article>