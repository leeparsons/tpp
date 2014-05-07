<?php
/**
 * User: lee
 * Date: 14/11/2013
 * Time: 20:52
 */

get_header('blog');

?>
<article class="single-article aside-60">

<?php if (have_posts()): the_post(); ?>

    <?php

    TppCacher::getInstance()->setCacheName(get_the_ID());
    TppCacher::getInstance()->setCachePath('blog/posts/' . get_the_ID() . '/');
    if (false === ($content = TppCacher::getInstance()->readCache())):

        ob_start();

    ?>

    <header>
        <h1><?php the_title(); ?></h1>
    </header>

    <div class="author-meta align-left wrap">

        <?php echo get_avatar( get_the_author_meta('ID') , 100 ); ?>
        <a class="author" href="<?php echo get_author_posts_url($post->post_author) ?>">By: <?php echo trim(get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')) ?></a>
        <p><?php the_author_meta('description') ?></p>
        <?php

        $url = get_the_author_meta('url');
        $twitter = get_the_author_meta('twitter');
        $facebook = get_the_author_meta('facebook');
        $gplus = get_the_author_meta('gplus');
        $blog = get_the_author_meta('blog');
        $twitter_url = get_the_author_meta('twitter_url');

        if ($url || $twitter_url || $facebook || $gplus || $blog):



        ?>
        <div class="author-links">
            <?php if ($url): ?>
            <a href="<?php echo $url ?>" target="_blank">website</a>
            <?php endif; ?>
            <?php if ($blog): ?>
                <a href="<?php echo $blog ?>" target="_blank">blog</a>
            <?php endif; ?>
            <?php if ($twitter_url): ?>
                <a href="<?php echo $twitter_url ?>" target="_blank">twitter</a>
            <?php endif; ?>
            <?php if ($facebook): ?>
                <a href="<?php echo $facebook ?>" target="_blank">facebook</a>
            <?php endif; ?>
            <?php if ($gplus): ?>
                <a href="<?php echo $gplus ?>" target="_blank">Google +</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
        <script>
            document.getElementById('autor_avatar');
            document.getElementById('author_links');
        </script>

    <div class="post-share">
        <div class="social-buttons">
            <h3>Share this</h3>
            <div class="align-left">
                <div class="fb-share-button" data-href="<?php the_permalink() ?>" data-type="button_count"></div>
            </div>
            <div class="align-left">
                <script type="IN/Share" data-url="<?php the_permalink() ?>" data-counter="right"></script>
            </div>

            <div class="align-left">
                <a href="https://twitter.com/share" data-href="<?php the_permalink() ?>" class="twitter-share-button">Tweet</a>
            </div>
            <?php if ($shared === false): ?>
                <script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
            <?php endif; ?>
            <div class="align-left">
                <div class="g-plusone" data-href="<?php the_permalink() ?>"></div>
            </div>

            <a href="#reply-title"><?php comments_number( 'Be the first to comment!', '1 comment > add a comment', '% comments > add a comment'); ?></a>
        </div>
    </div>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

        <?php

    $content = ob_get_contents();


    ob_end_clean();

    TppCacher::getInstance()->saveCache($content);

        endif; //end cache

        echo $content;

        ?>
    <div class="post-share">
        <div class="social-buttons">
            <h3>Share this</h3>
            <div class="align-left">
                <div class="fb-share-button" data-href="<?php the_permalink() ?>" data-type="button_count"></div>
            </div>
            <div class="align-left">
                <script type="IN/Share" data-url="<?php the_permalink() ?>" data-counter="right"></script>
            </div>

            <div class="align-left">
                <a href="https://twitter.com/share" data-href="<?php the_permalink() ?>" class="twitter-share-button">Tweet</a>
            </div>
            <?php if ($shared === false): ?>
                <script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
            <?php endif; ?>
            <div class="align-left">
                <div class="g-plusone" data-href="<?php the_permalink() ?>"></div>
            </div>
        </div>
    </div>

    <?php get_template_part('post/related_products') ?>
        <div class="comments">
        <?php comments_template() ?>
    </div>

<?php endif; ?>

</article>

<?php

flush();

get_template_part('sidebars/post');

get_footer();

