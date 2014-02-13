<?php


add_action('init', function() {


    TppStoreAbstractBase::checkRobot();


        add_filter('query_vars', function($vars) {
            $vars[] = 'path';
            $vars[] = 'product_id';
            $vars[] = 'method';
            $vars[] = 'tpp_pagename';
            $vars[] = 'args';
            $vars[] = 'product_slug';
            $vars[] = 'store_slug';
            $vars[] = 'shop_args';
            $vars[] = 'tpp_checkout_method';
            $vars[] = 'mentor_id';

            return $vars;
        } );


    TppStoreControllerDashboard::getInstance()->applyRewriteRules();
    TppStoreControllerProduct::getInstance()->applyRewriteRules();
    TppStoreControllerMentors::getInstance()->applyRewriteRules();
    TppStoreControllerRegister::getInstance()->applyRewriteRules();
    TppStoreControllerUser::getInstance()->applyRewriteRules();
    //TppStoreControllerUser::getInstance()->registerActions();
    TppStoreControllerCategory::getInstance()->applyRewriteRules();
    TppStoreControllerStore::getInstance()->applyRewriteRules();
    TppStoreControllerAccount::getInstance()->applyRewriteRules();
    TppStoreControllerCart::getInstance()->applyRewriteRules();
    TppStoreControllerCheckout::getInstance()->applyRewriteRules();
    TppStoreControllerDiscount::getInstance()->applyRewriteRules();
    TppStoreControllerReview::getInstance()->applyRewriteRules();
    TppStoreControllerWishlist::getInstance()->applyRewriteRules();

    add_rewrite_rule('shop/exchange-rates/?', 'index.php?tpp_pagename=tpp-generate-exchange-rates', 'top');

    //uncomment this every time something news is added in
    flush_rewrite_rules(true);


});

add_action( 'template_redirect', function() {

    TppStoreControllerDashboard::getInstance()->templateRedirect();
    TppStoreControllerProduct::getInstance()->templateRedirect();
    TppStoreControllerMentors::getInstance()->templateRedirect();

    TppStoreControllerRegister::getInstance()->templateRedirect();
    TppStoreControllerUser::getInstance()->templateRedirect();
    TppStoreControllerCategory::getInstance()->templateRedirect();
    TppStoreControllerStore::getInstance()->templateRedirect();
    TppStoreControllerAccount::getInstance()->templateRedirect();
    TppStoreControllerCart::getInstance()->templateRedirect();
    TppStoreControllerCheckout::getInstance()->templateRedirect();
    TppStoreControllerDiscount::getInstance()->templateRedirect();
    TppStoreControllerReview::getInstance()->templateRedirect();
    TppStoreControllerWishlist::getInstance()->templateRedirect();
} );

function tpp_meta_description()
{
    if (is_feed()) {
        return;
    }

    $description = TppStoreAbstractBase::$_meta_description;

    if (trim($description) == '') {
        if (substr($_SERVER['REQUEST_URI'], 0, 5) !== '/shop/') {
            global $wp_query;
            $post = $wp_query->get_queried_object();

            if (is_single() || is_page()) {

                $description = $post->post_excerpt?:$post->post_content;

            } elseif (is_author()) {
                $description = get_the_author_meta('description', $post->ID);
            }

        }
    }

    $description = trim(strip_tags(str_replace('"', '', stripcslashes($description))));

    // "internal whitespace trim"
    $description = preg_replace("/\s\s+/", " ", $description);


    if (strlen($description) > 150) {
        $description = substr($description, 0, 160) . ' ...';
    }

    return $description;
}



function tpp_meta_title($title, $sep)
{
    return TppStoreAbstractBase::$_meta_title?:trim($title);
}

add_filter('wp_title', 'tpp_meta_title', 10, 2);

//include get_template_directory() . '/classes/geoip/geo.php';


include get_template_directory() . '/classes/ip2location/locator.php';


function tpp_geo_locate() {

    $testing = true;

    $test_ip = '31.185.167.16';

        if (geo::getInstance()->code === false) {


//            include get_template_directory() . '/classes/geoip/geoip.inc';

            $ip = ($testing === false)?$_SERVER['REMOTE_ADDR']:$test_ip;
            $result = geo::getInstance()->lookup($ip);
            geo::getInstance()->setData($result);




            //$gi = geoip_open(get_template_directory() . '/classes/geoip/database.bin', GEOIP_STANDARD);

//            $code = geoip_country_code_by_addr($gi, $ip);

//            $country = geoip_country_name_by_addr($gi, $ip);

//            geoip_close($gi);

//            if ($code == '') {
//                //assume UK!
//                geo::getInstance()->code = 'GB';
//                geo::getInstance()->country = 'United Kingdom';
//            } else {
//                $code = 'US';
//                geo::getInstance()->code = $code;
//                geo::getInstance()->country = $country;
//            }

            geo::getInstance()->setCurrency();

        }



}

//add_action('tpp_store_products', function() {TppStoreControllerProduct::getInstance()->listProducts();});
