<?php
/**
 * User: lee
 * Date: 14/11/2013
 * Time: 20:52
 */

get_header('blog');

?>
<article class="single-article aside-75">

<?php if (have_posts()): the_post(); ?>

    <?php

    TppCacher::getInstance()->setCacheName(get_the_ID());
    TppCacher::getInstance()->setCachePath('blog/posts/');
    if (false === ($content = TppCacher::getInstance()->readCache())):

        ob_start();

    ?>

    <header>
        <h1><?php the_title(); ?></h1>
    </header>

    <div class="author-meta align-left wrap">

        <?php echo get_avatar( get_the_author_meta('ID') , 100 ); ?>
        <a href="<?php echo get_author_posts_url($post->post_author) ?>">By: <?php echo trim(get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')) ?></a>
        <p><?php the_author_meta('description') ?></p>

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
        <div class="comments">
        <?php comments_template() ?>
    </div>

<?php endif; ?>

</article>

<?php

flush();

get_sidebar();

get_footer();

