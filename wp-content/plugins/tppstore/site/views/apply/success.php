<?php

get_header(); ?>

    <article class="page">
        <header>
            <h1>Application Received!</h1>
        </header>
        <div class="wrap">
            <div class="innerwrap">
                <div class="social-buttons">
                    <h4>Keep in touch with our community of photographers!</h4>
                    <p>Join us by clicking on the social icons below</p>
                    <div class="fb-like" data-href="https://www.facebook.com/thephotographyparlour" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                    &nbsp;&nbsp;<a href="https://twitter.com/photoparlour" class="twitter-follow-button" data-show-count="false">Follow @photoparlour</a>
                    <div class="g-plusone" data-annotation="inline" data-width="300" data-href="http://www.thephotographyparlour.com"></div>
                </div>
            </div>
        </div>

        <div class="hentry">
            <p>Thank you <?php echo $user->getName() ?>!<br><br>We have received and are reviewing your application to sell with us.<br><br>You will receive an email within the next 24 hours or sooner to let you know if your application has been successful, in the mean time you can check your status by logging into your account. If you haven't received an email within 24 hours, please check your spam inbox.<br><br>If you have any questions please email <a href="mailto:rosie@thephotographyparlour.com">rosie@thephotographyparlour.com</a>.<br><br>We look forward to working with you!</p>
        </div>
    </article>
<?php get_footer('application');
