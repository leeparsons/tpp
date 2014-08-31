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
    TppStoreControllerEvents::getInstance()->applyRewriteRules();

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
    TppStoreControllerLanding::getInstance()->applyRewriteRules();
    add_rewrite_rule('shop/exchange-rates/?', 'index.php?tpp_pagename=tpp-generate-exchange-rates', 'top');

    //uncomment this every time something new is added in
    flush_rewrite_rules(true);


});

add_action( 'template_redirect', function() {

    TppStoreControllerDashboard::getInstance()->templateRedirect();
    TppStoreControllerProduct::getInstance()->templateRedirect();
    TppStoreControllerMentors::getInstance()->templateRedirect();
    TppStoreControllerEvents::getInstance()->templateRedirect();

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
    TppStoreControllerLanding::getInstance()->templateRedirect();
} );

function tpp_meta_description()
{
    if (is_feed()) {
        return;
    }

    $description = TppStoreAbstractBase::$_meta_description;

    $description = trim(strip_tags(str_replace('"', '', stripcslashes($description))));

    // "internal whitespace trim"
    $description = preg_replace("/\s\s+/", " ", $description);


    if (strlen($description) > 150) {
        $description = substr($description, 0, 160) . ' ...';
    }

    return $description;
}


function tpp_get_meta_title()
{
    return TppStoreAbstractBase::$_meta_title?:'';
}

function tpp_meta_title($title, $sep)
{
    return TppStoreAbstractBase::$_meta_title?:trim($title);
}

add_filter('wp_title', 'tpp_meta_title', 10, 2);

//include get_template_directory() . '/classes/geoip/geo.php';


include get_template_directory() . '/classes/ip2location/locator.php';


function tpp_geo_locate() {

    $testing = getenv('ENVIRONMENT') == 'local' || strtolower($_SERVER['HTTP_HOST']) == 'dev.thephotographyparlour.com';
        if (geo::getInstance()->code === false) {


            $test_ip = '31.185.167.16';//uk
            //$test_ip = '196.255.255.255';//us


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

function tpp_rel_canonical()
{
    if ( tpp_on_shop() === true ) {

        if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
            echo "<link rel='canonical' href='" . esc_url( get_site_url() . substr($_SERVER['REQUEST_URI'], 0, $pos)) . "' />\n";
        } else {
            echo "<link rel='canonical' href='" . esc_url( get_site_url() . $_SERVER['REQUEST_URI']) . "' />\n";
        }

    } else {
        rel_canonical();
    }

}

add_action( 'wp_head', 'tpp_rel_canonical' );

add_action('init', 'tpp_geo_locate');


//add_action('tpp_store_products', function() {TppStoreControllerProduct::getInstance()->listProducts();});
