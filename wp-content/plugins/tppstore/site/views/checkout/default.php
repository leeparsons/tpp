<?php get_header(); ?>

<article class="page-article">

    <header>
        <h1>Checkout</h1>
    </header>

    <div class="entry-content">

        <a class="align-right" target="_blank" href="https://www.paypal.com/uk/webapps/mpp/paypal-popup" title="How PayPal Works"><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/AM_SbyPP_mc_vs_ms_ae_UK.png" border="0" alt="PayPal Acceptance Mark"></a>

        <div class="align-left">
            <p>To continue with your checkout we just need some information from you to continue.</p>
            <p>If you are not yet ready to checkout, please <a href="/shop" class="btn btn-primary">continue shopping</a></p>
        </div>


    </div>

    <div class="wrap">

        <form method="post" action="/shop/checkout/payment">

            <fieldset>
                <legend>Payment Details</legend>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="fname" value="<?php echo $user->first_name ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="lname" value="<?php echo $user->last_name ?>">
                </div>

                <div class="form-group">
                    <label for="paypal_email">Paypal Email Address</label>
                    <input type="text" class="form-control" id="paypal_email" name="paypal_email" value="<?php $paypal_email ?>">
                </div>

                <input type="hidden" name="store" value="<?php echo $store_id ?>">

                <div class="form-group">
                    <input type="submit" value="proceed to payment" class="btn btn-primary">
                </div>

            </fieldset>

        </form>

    </div>

</article>

<?php get_footer();