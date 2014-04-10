<!DOCTYPE html>
<html lang="en">
<head>
    <?php get_template_part('common/css') ?>
    <title><?php

        if (tpp_on_shop() === true) {
            echo tpp_get_meta_title();
        } else {
            wp_title('');
        }

        ?></title>
    <?php TppStoreHelperHtml::getInstance()->renderOgImages() ?>
    <?php if (tpp_on_shop() === true): ?>
        <meta property="og:description" content="<?php echo tpp_get_meta_title() ?>">
        <meta property="og:title" content="<?php echo tpp_meta_description() ?>">
        <meta property="og:url" content="<?php echo get_site_url() . $_SERVER['REQUEST_URI'] ?>">
    <?php else: ?>
        <meta property="og:title" content="<?php echo wp_title('') ?>">
        <meta property="og:description" content="<?php echo get_bloginfo('description')?>">
    <?php endif; ?>
    <meta property="fb:app_id" content="270470249767149">
    <meta property="og:type" content="website" />
    <meta name="description" content="<?php

    if (tpp_on_shop() === true) {
        echo tpp_meta_description();
    } else {
        echo get_bloginfo('description');
    }

    ?>">
    <?php wp_head(); ?>
    <?php TppStorehelperhtml::getInstance()->robots(); ?>
</head>
<body>
<div class="wrap">
    <?php get_template_part('common/top_bar') ?>
    <header class="head">

        <hgroup class="head-title">
            <?php if(is_home() && TppStoreControllerDashboard::getInstance()->isDashboard() === false): ?>
                <h1><a href="/">The Photography Parlour</a></h1>
            <?php else: ?>
                <a href="/">The Photography Parlour</a>
            <?php endif; ?>
        </hgroup>

        <form method="get" id="header_search_form" action="/">
                <ul id="search_filters">
                    <li><input type="radio" name="sf" value="1" checked>Shop</li>
                    <li><input type="radio" name="sf" value="2">Blog</li>
                </ul>
            <input type="text" class="input" placeholder="what are you looking for?" id="search_all" name="s" value="<?php echo filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING) ?>">
            <input type="submit" class="button button-default" value="Search">
            <button type="submit" id="search_button">Search</button>
        </form>
        <?php TppStoreControllerUser::getInstance()->renderMenuButtons(); ?>
        <?php if (!wp_is_mobile()): ?>
        <div class="header-social-buttons">
            <div class="wrap">
            <a class="rss-icon" href="<?php bloginfo('rss2_url'); ?>" target="_blank">Rss</a>
            <div class="fb-like" data-href="https://www.facebook.com/thephotographyparlour" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
            &nbsp;&nbsp;<a href="https://twitter.com/photoparlour" class="twitter-follow-button" data-show-count="false">Follow @photoparlour</a>
                <div class="g-plusone" data-size="medium" data-href="http://www.thephotographyparlour.com"></div>
            </div>
        </div>
        <?php endif; ?>
    </header>
</div><?php flush();