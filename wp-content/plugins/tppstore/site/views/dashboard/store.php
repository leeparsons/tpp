<?php
/**
 * User: leeparsons
 * Date: 21/12/2013
 * Time: 09:31
 */

wp_enqueue_script('store-menu', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/store-menu-ck.js', 'jquery', 1, true);

get_header();

if (!isset($myaccount_page) || intval($store->approved) == 1) {
    $myaccount_page = false;

}
?>
<div class="aside-25 sidemenu dashboard">
    <a href="/shop/dashboard" class="btn btn-primary">&lt; &lt; Back to dashboard</a>
    <ul class="product-togglers">
    <?php if (false === $myaccount_page): ?>
        <li class="first active">Ecommerce</li>
        <li>About</li>
        <li>Location</li>
        <li>Images</li>
        <li class="last">Save</li>
    <?php else: ?>
        <li class="first active">About</li>
        <li>Location</li>
        <li>Save</li>
    <?php endif; ?>
    </ul>
</div>

<article class="aside-75 mystore dashboard">

<?php TppStoreMessages::getInstance()->render() ?>

        <?php if (intval($store->store_id) > 0): ?>
            <?php if (intval($store->approved) == 0): ?>
                <p class="inactive wp-error">Your store is currently pending approval. You can update your store information in the mean time below.</p>
            <?php elseif (intval($store->approved) == -1): ?>
                <p class="inactive wp-error">Your store has not been approved. To resubmit your application, fill in the form below and resubmit it.</p>
            <?php else:  ?>
                <?php if (intval($store->enabled) == 0): ?>
                    <p class="inactive wp-error">Your store is not yet live!</p>
                <?php else: ?>
                    <p class="active pull-left wp-message">Your store is live!</p>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <p class="inactive pull-left wp-error">Fill out the form field below and apply to sell with us!</p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="store_form" action="/shop/dashboard/store">

            <?php if ($myaccount_page === true): ?>
            <input type="hidden" name="sid" value="<?php echo $store->store_id ?>">
            <?php endif; ?>

            <h1>My Store</h1>

            <input type="hidden" id="upload_destination" value="/shop/dashboard/store/upload">

            <?php if ($myaccount_page === false): ?>

                <fieldset id="ecommerce">

                    <legend>Ecommerce Settings</legend>

                    <div class="form-group">
                        <label for="currency">Select the currency for your store:</label>
                        <select name="currency" id="currency" class="form-control" style="width:220px">
                            <option value="GBP" <?php echo $store->currency == 'GBP' || is_null($store_currency)?'selected':'' ?>>&pound; - pounds sterling (GBP)</option>
                            <option value="USD" <?php echo $store->currency == 'USD'?'selected':'' ?>>&dollar; - US Dollars (USD)</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <?php if (!filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)): ?>
                            <p>Enter your paypal email address to take payments:</p>

                            <label for="paypal_email">Paypal Email</label>
                            <input type="text" name="paypal_email" id="paypal_email" class="form-control" value="<?php echo $store->paypal_email ?>">
                        <?php else: ?>
                            <input type="hidden" name="paypal_email" id="paypal_email" value="<?php echo $store->paypal_email ?>">
                            <p>Your paypal email address is: <?php echo $store->paypal_email ?></p>
                            <pre>If you need to change your paypal email address please contact us on: rosie@thephotographyparlour.com.</pre>
                        <?php endif; ?>

                    </div>

                </fieldset>

            <?php endif; ?>

            <fieldset>
                <legend>About</legend>
                <div class="form-group">
                    <label for="store_name">Name: <span class="required">*</span></label>
                    <input type="text" class="form-control" name="store_name" id="store_name" value="<?php echo $store->store_name ?>">
                </div>

                <?php if ($myaccount_page === false): ?>
                <div class="form-group">
                    <?php if (intval($store->store_id) > 0 && intval($store->enabled) == 1 && (!is_null($store->store_slug) && !empty($store->store_slug) )): ?>
                        <p>Your store URL: <a target="_blank" href="<?php echo $store->getPermalink(false, true) ?>"><?php echo $store->getPermalink(false, true) ?></a></p>
                        <input type="hidden" name="store_slug" value="<?php echo $store->store_slug ?>">
                    <?php else: ?>
                        <label for="store_slug">Your store URL:</label>
                    <input type="text" class="form-control" name="store_slug" id="store_slug" value="<?php echo $store->store_slug?:sanitize_title_with_dashes($store->store_name) ?>">
                        <script>
                            document.getElementById('store_name').onkeyup = function() {
                                document.getElementById('store_slug').value = this.value.replace(/[^a-zA-Z0-9\s]/g, '') // Remove non alphanum except whitespace
                                    .replace(/^\s+|\s+$/, '')
                                    .replace(/\s+/g, '-')
                                    .toLowerCase();
                            }
                        </script>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="store_description">Description</label>
                    <?php

                    wp_editor(
                        $store->description,
                        'store_description',
                        array(
                            'media_buttons' =>  false,
                            'teeny'         =>  true
                        )
                    );
                    ?>
                    <!--textarea name="store_description" class="form-control" rows="5" id="store_description"><?php echo $store->description ?></textarea-->
                </div>

            </fieldset>

            <fieldset>
                <legend>Location</legend>
                <div class="form-group">

                    <label for="city">Nearest City:</label>

                    <input type="text" name="city" id="city" value="<?php echo $store->city ?>" class="form-control" placeholder="your store location">

                </div>

                <div class="form-group">

                    <label for="country">Country:</label>

                    <?php

                    $select_name = 'country';
                    $select_id = 'country';
                    $selected_value = $store->country;

                    flush(); include TPP_STORE_PLUGIN_DIR . 'templates/countries.php';flush();

                    unset($selected_value);
                    unset($select_id);
                    unset($select_name);

                    ?>



                </div>

            </fieldset>




            <?php if ($myaccount_page === false): ?>


            <fieldset>
                <legend>Store Image</legend>
                <p class="wp-message">Upload an image for your store. The ideal image size is 250 pixels by 250 pixels but we will resize your image if it's larger.</p>
                <div class="form-group">
                    <div id="dropbox" class="store-dropbox">
                        <div class="drop-wrap">
                            <div class="photo-box">
                                <div class="handle" style="background:none"></div>
                                <div class="delete-icon"></div>
                                <div class="preview">
                                    <?php if (intval($store->store_id) > 0 && $store->src): ?>
                                        <img src="<?php echo $store->getSrc() ?>">
                                        <input type="hidden" name="original_pic[]" value="<?php echo $store->src ?>">
                                    <?php else: ?>
                                        <div class="upload-icon"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <?php endif; ?>



            <?php if (intval($store->approved) > 0): ?>
            <div class="hidden">
<!--                <legend>Publish your store</legend>-->

                <div class="form-group">
                    <label for="enabled_yes"><input <?php echo intval($store->enabled) == 1?'checked':'' ?> type="radio" id="enabled_yes" name="store_enabled" value="1"> Publish</label>
                    <label for="enabled_no"><input <?php echo intval($store->enabled) == 0?'checked':'' ?> type="radio" id="enabled_no" name="store_enabled" value="0"> Unpublish</label>
                </div>

            </div>
            <?php endif; ?>

            <fieldset>
                <legend>Save</legend>
                <?php if (intval($store->approved) == -1): ?>
                    <input type="submit" class="btn-primary btn" value="Resubmit Application">
                <?php elseif (intval($store->store_id) > 0): ?>
                    <input type="submit" class="btn-primary btn" value="Save">
                    <?php if (false === $myaccount_page): ?>
                        <?php if (intval($store->enabled) == 0): ?>
                            <input type="submit" class="publish btn-primary btn" value="Go Live">
                        <?php else: ?>
                            <input type="submit" class="unpublish btn-danger btn" value="Go Offline">
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <input type="submit" class="btn btn-primary" value="Submit Application">
                <?php endif; ?>


                <a href="/shop/dashboard" class="btn btn-default">Cancel</a>

                <?php if (intval($store->enabled) == 1): ?>
                    <a href="<?php echo $store->getPermalink() ?>" class="btn btn-info" target="_blank">View Store</a>
                <?php endif; ?>

           </fieldset>

        </form>

    </article>
<?php include 'footer.php';