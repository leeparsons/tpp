<?php
//determine if we are searching the shop?

$sf = intval(filter_input(INPUT_GET, 'sf', FILTER_SANITIZE_NUMBER_INT));

switch ($sf) {
    case 2:
        //blog
get_header('blog'); ?>

<header>
    <h1>Search Results</h1>
</header>

        <?php if (have_posts()):
            while (have_posts()):

                the_post();

                the_title();

            endwhile;
        endif;

        break;

    default:
        //default everything to shop

get_header();

?><header>
    <h1>Search Results</h1>
</header><?php

        TppStoreControllerProduct::getInstance()->search();



        break;
}

get_footer();
