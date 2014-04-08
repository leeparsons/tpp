<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css?v=2.6">
    <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
        <link rel="stylesheet" type="text/css" href="/assets/css/mobile.css?v=2.1">
    <?php elseif (tpp_is_ipad()): ?>
        <link rel="stylesheet" type="text/css" href="/assets/css/ipad.css?v=2.1">
    <?php endif; ?>
    <!--[if gte IE 9]><style type="text/css">.gradient{filter: none;}</style><![endif]-->
    <title><?php

            wp_title('');

        ?></title>
    <meta property="og:title" content="<?php echo wp_title('') ?>">
    <meta property="og:description" content="<?php echo get_bloginfo('description')?>">
    <meta property="fb:app_id" content="270470249767149">
    <meta property="og:type" content="website" />
    <meta name="description" content="<?php
        echo get_bloginfo('description');
    ?>">
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
    </header>
</div><?php flush();