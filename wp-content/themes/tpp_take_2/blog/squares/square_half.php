<article class="square-half">
    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink() ?>">
            <?php the_post_thumbnail('blog_square_half') ?>
        </a>
    <?php endif; ?>
    <div class="square-half-text wrap">
        <h3><a class="wrap" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="wrap square-half-meta">
            <time class="align-left" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('F, j. Y'); ?></time>
            <span class="align-left"> / </span>
            <span class="align-left"><?php

            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
            <span class="align-left"> / </span>
        </div>
        <div class="hentry wrap">
            <?php the_excerpt(); ?>
        </div>
    </div>
</article>