<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="/assets/css/blog.css?v=1.3" rel="stylesheet" type="text/css">
    <?php if (tpp_is_ipad()): ?>
        <link href="/assets/css/blog_ipad.css?v=1" rel="stylesheet" type="text/css">
    <?php endif; ?>
    <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
        <link href="/assets/css/blog_mobile.css?v=1" rel="stylesheet" type="text/css">
    <?php endif; ?>
    <title><?php

            wp_title('');

        ?></title>
    <?php /*
    <meta property="og:title" content="<?php echo wp_title('') ?>">
    <meta property="og:description" content="<?php echo get_bloginfo('description')?>">
    <meta property="fb:app_id" content="270470249767149">
    <meta property="og:type" content="website" />
    <meta name="description" content="<?php
        echo get_bloginfo('description');
    ?>">
    */ ?>
    <?php wp_head(); ?>
</head>
<body>
<div class="wrap">
    <?php get_template_part('common/top_bar') ?>
    <header class="head">

        <div class="head-title">
            <a href="/">The Photography Parlour</a>
        </div>

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