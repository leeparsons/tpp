<?php

include_once TPP_INTERVIEWS_PLUGIN_DIR . 'models/interview.php';


$interview = new TppInterviewModel(get_the_ID());

$interview->load();

?><article class="square-large">
    <div class="featured-interview-wrap" id="interview_<?php echo get_the_ID() ?>">
        <h2 id="interview_title-<?php echo get_the_ID() ?>"><a href="" class="interview-title"><?php


                if ($interview->isLive()) {
                    echo 'Live Now!<br>';
                    the_title();
                } elseif (!$interview->hasHappened()) {
                    echo 'Next interview<br>';
                    the_title();
                    echo '<br><br>';
                    echo '<time datetime="' . $interview->getDate() . '">' . $interview->getStartDatetime('F, j. Y H:i') . ' GMT</time>';
                } else {
                    the_title();
                }



                ?></a></h2>
        <?php


        if (false === $interview->hasVideo()): ?>
            <?php if (has_post_thumbnail()): ?>
                <a href="<?php the_permalink() ?>">
                <?php the_post_thumbnail('blog_large_square') ?>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <?php echo $interview->getVideoEmbedCode(false, 475); ?>
        <?php endif; ?>
    </div>
    <div class="interview-text wrap">
        <a class="wrap" href="<?php the_permalink(); ?>"><strong class="wrap"><?php the_title(); ?></strong></a>
        <div class="wrap post-meta">
            <time class="align-left" datetime="<?php echo $interview->getDate()?:get_the_date('Y-m-d'); ?>"><?php echo $interview->getDate('F, j. Y')?:get_the_date('F, j. Y'); ?></time>
            <span class="align-left"> / </span>
            <span class="align-left"><?php

            $comment_counts = get_comment_count(get_the_ID());

            echo $comment_counts['approved'];

            ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
        </div>
        <div class="hentry wrap">
            <?php the_excerpt(); ?>
            <a class="read-more" href="<?php the_permalink(); ?>">&ndash; read more</a>
        </div>
    </div>
</article>
<script><?php include TPP_INTERVIEWS_PLUGIN_DIR . 'assets/js/video_click_detect.min.js'; ?></script>