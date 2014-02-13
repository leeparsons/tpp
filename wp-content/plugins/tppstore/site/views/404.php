<?php
/**
 * User: leeparsons
 * Date: 15/12/2013
 * Time: 15:05
 */

if (!isset($message)) {
    if (TppStoreMessages::getInstance()->getTotal() > 0) {
        $message = TppStoreMessages::getInstance()->render(false);
    } else {
        $message = 'Sorry, we could not find what you were looking for.';
    }
}

if (!isset($title)) {
    $title = 'Page not found!';
}

get_header(); ?>

<article class="page">
    <header>
        <h1><?php echo $title; ?></h1>
    </header>
    <div class="hentry">
        <?php echo $message; ?>
    </div>
</article>

<?php get_footer();