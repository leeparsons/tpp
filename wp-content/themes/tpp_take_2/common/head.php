<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/assets/css/fonts.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css?v=2">
    <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
        <link rel="stylesheet" type="text/css" href="/assets/css/mobile.css?v=2">
    <?php elseif (tpp_is_ipad()): ?>
        <link rel="stylesheet" type="text/css" href="/assets/css/ipad.css?v=2">
    <?php endif; ?>
    <?php wp_head(); ?>
    <!--[if gte IE 9]><style type="text/css">.gradient{filter: none;}</style><![endif]-->
    <title><?php wp_title(''); ?></title>
    <?php TppStoreHelperHtml::getInstance()->renderOgImages() ?>
    <meta name="description" content="<?php echo tpp_meta_description()?:get_bloginfo('description') ?>">
    <?php TppStorehelperhtml::getInstance()->robots(); ?>
</head>
<body>
<div class="wrap">
    <header class="head">

        <hgroup class="head-title">
            <?php if(is_home()): ?>
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
    </header>
</div><?php flush();