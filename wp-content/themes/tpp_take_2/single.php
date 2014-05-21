<?php
/**
 * User: lee
 * Date: 14/11/2013
 * Time: 20:52
 */

get_header('blog');

?><div class="blog-wrap single-post">
    <div class="blog-main">
        <article class="single-article wrap">

            <?php if (have_posts()): the_post(); ?>

                <?php

                TppCacher::getInstance()->setCacheName(get_the_ID());
                TppCacher::getInstance()->setCachePath('blog/posts/' . get_the_ID() . '/');
                if (false === ($content = TppCacher::getInstance()->readCache())):

                    ob_start();

                    ?>

                    <header>
                        <div class="blog-divider-top"></div>
                        <h1><?php the_title(); ?></h1>
                        <div class="blog-divider-bottom"></div>
                    </header>

                    <div class="author-meta align-left wrap">

                        <?php echo get_avatar( get_the_author_meta('ID') , 100 ); ?>
                        <a href="<?php echo get_author_posts_url($post->post_author) ?>">By: <?php echo trim(get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')) ?></a>
                        <p><?php the_author_meta('description') ?></p>

                    </div>

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
                <div class="comments wrap">
                    <?php comments_template() ?>
                </div>

            <?php endif; ?>

        </article>
    </div>
<?php

flush();

get_template_part('sidebars/post');
?></div>
<?php
get_footer();

