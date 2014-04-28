<article class="square-large">
    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink() ?>">
            <?php the_post_thumbnail('blog_large_community_challenge') ?>
        </a>
    <?php endif; ?>
    <div class="community-challenge-text wrap">
        <h3><a class="wrap" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="wrap interview-meta">
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
<script><?php include TPP_INTERVIEWS_PLUGIN_DIR . 'assets/js/video_click_detect.min.js'; ?></script>