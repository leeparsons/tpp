<?php
/**
 * User: leeparsons
 * Date: 15/01/2014
 * Time: 13:11
 */


get_header('shop_category'); ?>

    <article class="page-article">

    <header>
        <h1>Apply to sell with us</h1>
    </header>

        <?php TppStoreMessages::getInstance()->render() ?>

    <form method="post" action="/shop/sell-with-us/" id="application_form" class="wrap">

        <pre>Thanks for thinking of applying to sell with The Photography Parlour! We are looking for fresh and relevant businesses to partner with who will help photographers create exciting and successful photographic careers.

Whether you're a wedding photographer offering mentor sessions, a small PR firm who can create press releases or a lawyer ready to be on hand for advice when things go wrong - as long as you love photographers and have something to make their lives and businesses better then we want to hear from you!</pre>
        <br>
        <br>
        <?php

            $d1 = new DateTime();

            $d2 = new DateTime('2014-02-28 23:23:59');

            $diff = date_diff($d1, $d2);

        if ($diff->invert == 0):

            ?><h5 class="red" style="font-size:14px;">**Exclusive Offer**</h5>

            <pre><em style="font-size:14px;width:100%;display:block;">For our specially invited store owners and their friends (that's you!) we are running a special pre launch commission rate of 10% (+ 2.5% to cover our processing fees) on all stores created before the 28th February 2014. This offer ends on 28 February 2014.</em></pre>

            <br>
            <br>
        <?php

        endif;


 ?>


        <fieldset>
            <legend class="active">About Your Business</legend>

            <div class="slider">

                <div class="form-group">
                    <label for="store_name">Company/ Business Name (This will also be your store name)</label>
                    <input name="store_name" id="store_name" class="form-control" type="text" value="<?php echo $store->store_name ?>" placeholder="Company/ Business Name">
                </div>

                <div class="form-group">
                    <label for="store_country">Country</label>
                    <?php

                    $select_name = 'country';
                    $select_id = 'store_country';

                    $selected_value = $store->country;

                    flush(); include TPP_STORE_PLUGIN_DIR . 'templates/countries.php';flush();

                    unset($select_id);
                    unset($select_name);

                    ?>
                </div>

                <div class="form-group">
                    <label for="store_description">About your business</label>
                    <pre>Tell us about your business and the products or services you'd like to sell through The Photography Parlour. This will also become your store bio on the site when approved.</pre>
                    <textarea name="store_description" id="store_description" rows="10" class="form-control"><?php echo esc_textarea($store->description) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="store_website">Website Url</label>
                    <input name="store_website" id="store_website" value="<?php echo $store->url; ?>" type="text" placeholder="http://example.com" class="form-control">
                </div>

            </div>
        </fieldset>

        <?php if (intval($user->user_id) < 1): ?>

            <fieldset>
                <legend>About You</legend>

                <div class="slider">
                    <div class="form-group">
                        <label for="u_title">Title</label>
                        <select name="u_title" id="u_title" class="form-control">
                            <option value="">-- select --</option>
                            <option <?php echo $user->title == 'Mr'?'selected="selected"':'' ?> value="Mr">Mr</option>
                            <option <?php echo $user->title == 'Mrs'?'selected="selected"':'' ?> value="Mrs">Mrs</option>
                            <option <?php echo $user->title == 'Miss'?'selected="selected"':'' ?> value="Miss">Miss</option>
                            <option <?php echo $user->title == 'Ms'?'selected="selected"':'' ?> value="Ms">Ms</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="f_name">First Name</label>
                        <input name="fname" id="f_name" class="form-control" type="text" value="<?php echo $user->first_name ?>" placeholder="First name">
                    </div>
                    <div class="form-group">
                        <label for="l_name">Last Name</label>
                        <input name="lname" id="l_name" class="form-control" type="text" value="<?php echo $user->last_name ?>" placeholder="Last Name">
                    </div>
                </div>

            </fieldset>

            <fieldset>
                <legend>Create a user account</legend>
                <div class="slider">

                    <div class="wrap">
                        <div class="half-left">
                            <h3>Sign in with Facebook</h3>
                            <div class="wrap">
                                <pre>We recommend signing in with Facebook so buyers will see any mutual friends you have in common as this will improve your store's trust.</pre>
                            </div>
                            <div class="inner" id="facebook_register_form">
                                <a id="fb_connect" class="fb-login-button-a"></a>
                            </div>

                        </div>

                        <div class="half-right">
                            <div class="abs-or">
                                <span>OR</span>
                            </div>
                            <h3>Create an Account</h3>
                            <div class="form-group">
                                <label for="user_email">Email Address (will also be your login)</label>
                                <input name="email" id="user_email" class="form-control" type="text" value="<?php echo $user->email ?>" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="u_password">Password</label>
                                <input name="pswd" id="u_password" type="password" placeholder="Password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="u_password_confirm">Confirm Password</label>
                                <input name="cpswd" id="u_password_confirm" type="password" placeholder="Confirm Password" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

        <?php else: ?>

            <fieldset>
                <legend class="active">About you</legend>
                <div class="slider">

                    <div class="form-group">
                        <span>Name: <?php echo $user->getName(true) ?></span>
                    </div>

                    <div class="form-group">
                        <span>Email: <?php echo $user->email ?></span>
                    </div>

                </div>
            </fieldset>


        <?php endif; ?>

        <fieldset>
            <legend>Confirm and Apply</legend>

            <div class="slider">
                <div class="form-group">

                    <label for="how">How did you hear about us?</label>
                    <select name="apply_how" id="how" class="form-control">
                        <option value="Invite from The Photography Parlour">Invite from The Photography Parlour</option>
                        <option value="Another seller">Another seller</option>
                        <option value="A friend">A friend</option>
                        <option value="Twitter">Twitter</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Google search">Google search</option>
                        <option value="Another website">Another website</option>
                    </select>
                </div>

                <div class="form-group">
                    <pre>We will never sell your personal details to third parties. By applying to become a registered seller you agree to our <a href="/terms-and-conditions">terms and conditions</a> and <a href="/privacy-policy">privacy policy</a>.</pre>

                    <label for="terms"><input type="checkbox" value="1" name="apply_agree" id="terms" class="align-left">
                        <span>Agree to our terms and conditions</span>
                    </label>

                </div>

                <div class="form-group">
                    <label for="comms" class="comms">
                        <input type="checkbox" checked="checked" name="apply_comms" id="comms" value="1" class="align-left">
                        <span>Send me the latest news and updates (by checking this you agree to sign up to our newsletter).</span>
                    </label>

                </div>

                <div style="position:absolute;height:0;width:0;left:-100000px;overflow:hidden;">
                    <input type="text" name="mc_name" value="" id="mc_name">
                </div>

                <div class="form-group">
                    <input type="submit" value="Submit Application" class="btn btn-primary align-left">
                </div>

                <div class="form-group">
                    <br><br>
                    <a target="_blank" href="/terms-and-conditions">Terms &amp; Conditions</a>
                    <span> | </span>
                    <a target="_blank" href="/privacy-policy">Privacy Policy</a>
                </div>
            </div>
        </fieldset>
    </form>

    </article>
<?php wp_enqueue_script('tpp-application', TPP_STORE_PLUGIN_URL . '/site/assets/js/apply-ck.js', array('jquery'), null, true) ?>
<?php flush(); get_footer();