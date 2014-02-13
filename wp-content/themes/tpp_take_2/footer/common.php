<div id="fb-root"></div>
<?php if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())): ?>
    <input type="hidden" id="logged_in">
<?php endif; ?>
<?php wp_enqueue_script('jquery') ?>
<script>(function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) {return;}js = d.createElement(s); js.id = id;js.src = "https://connect.facebook.net/en_US/all.js";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
<?php wp_footer(); ?>
<script src="<?php echo TPP_STORE_PLUGIN_URL ?>/site/assets/js/fb-login-ck.js"></script>
<script>
    (function(d, t) {
        var g = d.createElement(t),
            s = d.getElementsByTagName(t)[0];
        g.src = '/assets/js/script.min.js';
        s.parentNode.insertBefore(g, s);
    }(document, 'script'));
</script>
<script>
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
    window.___gcfg = {lang: 'en-GB'};
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/platform.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script><?php

if (isset($_COOKIE['accept_cookies'])):
    ?><script>var expiration_date = new Date();var cookie_string = '';expiration_date.setFullYear(expiration_date.getFullYear() + 1);cookie_string = "accept_cookies=true; path=/; expires=" + expiration_date.toGMTString();document.cookie = cookie_string;</script><?php
else: ?><script>(function(d, t) {
        var g = d.createElement(t),
            s = d.getElementsByTagName(t)[0];
        g.src = '/assets/js/cookie.min.js';
        s.parentNode.insertBefore(g, s);
    }(document, 'script'));</script><?php

endif;