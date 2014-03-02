<?php wp_enqueue_script('jquery-ui-sortable'); ?>
<?php wp_enqueue_script('file_uploads', '/assets/js/file_upload-ck.js', array('jquery'), '1', true) ?>
<?php wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', array('jquery'), '1', true) ?>
<?php wp_enqueue_script('tpp_product_dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/product-ck.js', array('jquery'), 3.5, true); ?>
<?php wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css'); ?>
<script>var currency = '<?php echo $store->getFormattedCurrency() ?>';</script>
<form method="post" enctype="multipart/form-data" id="product_form">

    <input type="hidden" id="upload_destination" value="/shop/dashboard/product/upload">

    <div class="aside-25">

        <a href="/shop/dashboard" class="btn btn-primary">&lt; &lt; Back to dashboard</a>

        <ul class="product-togglers">
            <li class="first active">Category</li>
            <li>About</li>
<!--            <li>Variations (optional)</li>-->
            <li>Pricing</li>
            <li>Images</li>
            <li>SEO &amp; Meta</li>
            <li class="last">Save &amp; Preview</li>
        </ul>
    </div>

    <div class="aside-75">
        <?php TppStoreMessages::getInstance()->render(); ?>
<!--        <fieldset class="notoggle">-->
<!---->
<!--            <input type="submit" class="btn-primary btn" value="Save">-->
<!---->
<!---->
<!--        </fieldset>-->

        <fieldset>

            <?php include 'product/category.php'; ?>

            <div class="switch-control form-group">
                <h4>Next Step</h4>
                <a href="#1" data-step="1" class="step btn-primary btn">Go to About</a>
            </div>

        </fieldset>

        <fieldset>
            <?php include 'product/about.php'; ?>


            <div class="switch-control form-group">
                <h4>Next Step</h4>
<!--                <p>Not everyone will want to upload product options. To skip the optional product variations step, click on "Go to images"</p>-->
<!--                <a href="#2" data-step="2" class="step btn-primary btn">Go to Product Variations (optional)</a>-->
                <a href="#2" data-step="2" class="step btn-primary btn">Go to Pricing</a>
                <a href="#0" data-step="0" class="step btn-primary btn">Go back to Category</a>

            </div>



        </fieldset>




<!--        <fieldset>-->
<!---->
<!--            --><?php //include 'product/variations.php'; ?>
<!---->
<!---->
<!---->
<!--            <div class="form-group wrap">-->
<!--                <h4>Next Step</h4>-->
<!--                <a href="#3" data-step="3" class="step btn-primary btn">Go to Discounts</a>-->
<!--            </div>-->
<!---->
<!--        </fieldset>-->

        <fieldset>
            <?php include 'product/prices.php'; ?>


            <div class="switch-control form-group">
                <h4>Next Step</h4>
                <a href="#3" data-step="3" class="step btn-primary btn">Go to Images</a>
                <a href="#1" data-step="1" class="step btn-primary btn">Go back to About</a>

            </div>

        </fieldset>

        <fieldset>

            <?php include 'product/images.php'; ?>

            <div class="form-group wrap">
                <h4>Next Step</h4>
                <a href="#4" data-step="4" class="step btn-primary btn">Go to SEO &amp; Meta</a>
                <a href="#2" data-step="2" class="step btn-primary btn">Go back to Pricing</a>

            </div>

        </fieldset>

        <fieldset>

            <?php include 'product/seo.php'; ?>

            <div class="form-group wrap">
                <h4>Next Step</h4>
                <a href="#5" data-step="5" class="step btn-primary btn">Go to Save &amp; Preview</a>
                <a href="#3" data-step="3" class="step btn-primary btn">Go back to Images</a>

            </div>

        </fieldset>


        <fieldset>
            <legend class="wrap">Save &amp; Preview</legend>

            <div class="form-group" style="display:none">
                <label><input type="radio" name="product_enabled" id="enabled_yes" value="1" <?php echo $product->enabled == 1 || is_null($product->enabled)?'checked':'' ?>> Enabled</label>
                <label><input type="radio" name="product_enabled" id="enabled_no" value="0" <?php echo $product->enabled == 0 && !is_null($product->enabled)?'checked':'' ?>> Not Enabled</label>
            </div>

            <div class="switch-control form-group">

                <?php if (intval($product->enabled) == 1): ?>
                    <input type="submit" class="btn-primary btn" value="Save">
                    <input type="submit" class="btn-danger btn unpublish" value="Save &amp; Go offline">
                <?php else: ?>
                    <input type="submit" class="btn-primary btn unpublish<?php

                    //if the product has never been created, then it's published by default. If they only want to save then force it unpublished

                    ?>" value="Save">
                    <input type="submit" class="btn-go btn publish" value="Save &amp; Go live">
                <?php endif; ?>


                <input type="submit" value="Preview" class="btn btn-info preview" >
                <a href="#4" data-step="4" class="step btn-primary btn">Go back to SEO &amp; Meta</a>

                <a href="/shop/dashboard" class="btn btn-default">Cancel</a>
            </div>

        </fieldset>





    </div>

    <input type="hidden" id="p" name="<?php echo md5('product_id' . NONCE_KEY) ?>" value="<?php echo $product->product_id ?>">
    <input type="hidden" name="sid" value="<?php echo $store_id ?>">
    <input type="hidden" id="senabled" value="<?php echo intval($store->enabled) ?>">
</form>