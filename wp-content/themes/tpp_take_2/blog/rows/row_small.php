<article class="small-row wrap hentry">

    <a class="align-left small-row-img" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('blog_square_x_small'); ?></a>

    <div class="align-left small-row-text">
        <h4 class="align-left wrap entry-title"><a class="align-left" href="<?php the_permalink(); ?>"><?php echo tpp_limit_content(get_the_title(), 60); ?></a></h4>
        <div class="abs">
            <a class="align-left post-meta" href="<?php the_permalink(); ?>">
                <span class="wrap"></span>
                <time class="align-left published" datetime="<?php echo get_the_date('Y-m-d') ?>"><?php echo get_the_date('F j. Y'); ?></time>
            </a>
            <a class="align-right post-meta" href="<?php the_permalink(); ?>">
                <span class="align-left"><?php


                    $comment_counts = get_comment_count(get_the_ID());

                    echo $comment_counts['approved'];

                    ?> comment<?php echo $comment_counts['approved'] == 1?'':'s' ?></span>
                </span>
            </a>
        </div>
    </div>
</article>