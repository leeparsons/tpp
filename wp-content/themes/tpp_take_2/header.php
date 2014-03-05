<?php get_template_part('common/head');flush(); ?>
<?php get_template_part('menus/main_menu') ?>
<?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
    <?php if (is_home() && get_query_var('tpp_pagename') == ''): ?>
    <div class="wrap">
        <section class="header-slideshow" id="header_slideshow">
            <ul>
                <li><a href="/shop/taylor-barnes-photography/product/illuminated-masterclass-in-fine-art-photography-and-styling/" class="img" style="background-image:url(/assets/images/homepage/homepage-illuminated-masterclass.jpg)"></a></li>
                <li style="display:none"><a href="/?sf=1&s=debs+ivelja" class="img" style="background-image:url(/assets/images/homepage/homepage-banner-debs-ivelja.jpg)"></a></li>
                <li style="display:none"><a href="/shop/category/marketing/" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-marketing.jpg)"></a></li>
                <li style="display:none"><a href="/shop/category/mentors/" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-mentors.jpg)"></a></li>
                <?php /*
 <li><a href="/?sf=1&s=dasha+caffrey" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-dasha-caffrey-3.jpg)"></a></li>
 */ ?>
                <li style="display:none"><a href="/?sf=1&s=taylor%20barnes" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-ashlee.jpg)"></a></li>
            </ul>
        </section>
    </div>
    <?php flush(); wp_enqueue_script('home_slides', '/assets/js/homeslideshow.min.js', array('jquery'), 1, true) ?>
    <div id="header_newsletter" class="wrap">
        <form action="http://thephotographyparlour.us3.list-manage.com/subscribe/post?u=c83dc78a82a2e856668eb3087&amp;id=3608e4a665" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank" novalidate class="innerwrap newsletter">
            <!--p>Grab your free 8 step guide to starting a photography business!</p-->
            <p>Get all our latest news : sign up to our mailing list!</p>
            <div>
                <input type="text" value="" placeholder="Name" name="FNAME" class="input-sm" id="mce-FNAME">
                <input type="email" placeholder="Email" value="" name="EMAIL" class="email input-sm" id="mce-EMAIL">
            </div>
            <div style="position: absolute; left: -5000px;"><input type="text" name="b_c83dc78a82a2e856668eb3087_3608e4a665" value=""></div>
            <input type="submit" value="go!" name="subscribe" id="mc-embedded-subscribe" class="button">
            <input type="hidden" value="newsletter" name="SOURCE" id="mce-SOURCE">
        </form>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php get_template_part('common/sponsors') ?>
    <div class="wrap">
        <section class="innerwrap"><?php flush();