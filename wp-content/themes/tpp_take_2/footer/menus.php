<footer class="wrap" id="footer">
    <section class="innerwrap">
        <nav class="aside-25">
            <h5><a href="/information">Information</a></h5>
            <?php

            wp_nav_menu(array(
                'theme_location'    =>  'footer_pages',
                'menu'              =>  'footer',
                'menu_class'        =>  'footer-menu'
            ));

            ?>
        </nav>

        <nav class="aside-25" >
            <h5><a href="/contact">Contact Us</a></h5>
            <?php

            wp_nav_menu(array(
                'theme_location'    =>  'footer_contact',
                'menu'              =>  'contact us',
                'menu_class'        =>  'footer-menu'
            ));

            ?>
        </nav>

        <nav class="aside-25">
            <h5>Newsletter</h5>
            <ul class="footer-menu">
                <li><p class="a">Join our mailing list</p></li>
            </ul>
            <ul class="footer-menu">
                <li>
                    <form action="http://thephotographyparlour.us3.list-manage.com/subscribe/post?u=c83dc78a82a2e856668eb3087&amp;id=3608e4a665" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank" novalidate class="newsletter">
                        <div id="mce-responses" class="clear">
                            <div class="response" id="mce-error-response" style="display:none"></div>
                            <div class="response" id="mce-success-response" style="display:none"></div>
                        </div>
                        <input type="text" value="" placeholder="Name" name="FNAME" class="form-control" id="mce-FNAME">
                        <br>
                        <input type="email" placeholder="Email" value="" name="EMAIL" class="form-control" id="mce-EMAIL">
                        <div style="position: absolute; left: -5000px;"><input type="text" name="b_c83dc78a82a2e856668eb3087_3608e4a665" value=""></div>
                        <br>
                        <input type="submit" value="go!" name="subscribe" id="mc-embedded-subscribe" class="btn btn-primary">
                        <input type="hidden" value="newsletter" name="SOURCE" id="mce-SOURCE">
                    </form>
                </li>
            </ul>
        </nav>

        <nav class="aside-25 social">
            <h5>Follow Us</h5>
            <a href="https://twitter.com/ThePhotoParlour" target="_blank" class="fb-icon">Facebook</a>
            <a href="https://www.facebook.com/thephotographyparlour" target="_blank" class="tw-icon">Twitter</a>
        </nav>

        <span id="copyright" class="wrap">&copy; The Photography parlour 2013 &ndash; <?php echo date('Y'); ?></span>
    </section>
</footer>
