<?php


get_header(); ?>

<header>
    <h1>Search Results</h1>
</header>
<?php
//determine if we are searching the shop?

$sf = intval(filter_input(INPUT_GET, 'sf', FILTER_SANITIZE_NUMBER_INT));

switch ($sf) {
    case 2:
        //blog

        if (have_posts()):
            while (have_posts()):

                the_post();

                the_title();

            endwhile;
        endif;

        break;

    default:
        //default everything to shop

        TppStoreControllerProduct::getInstance()->search();



        break;
}

get_footer();
