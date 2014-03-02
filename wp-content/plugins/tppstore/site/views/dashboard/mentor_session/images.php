<legend class="wrap">Images</legend>

<div class="form-group">
    <pre>Upload some images to help sell your session.

Images could be examples of your work or yourself/you at work.

The ideal image size is under 500KB.</pre>
</div>
<div class="form-group">

<div id="dropbox">
    <?php

    $product_images = $product->getImages(-1, true);

    $base_src = substr($_SESSION['images_store'][$store_id]['tmp_product'], strlen(WP_CONTENT_DIR . '/uploads'));


    if (!empty($product_images)):

        $x = 1;

        foreach ($product_images as $i => $image): ?>

            <div class="drop-wrap">
                <div class="photo-box">
                    <div class="handle"></div>
                    <div class="delete-icon"></div>
                    <div class="preview">
                        <?php $src = $image->getSrc(); ?>
                        <?php $new_src = (!empty($_SESSION['tpp_store']['tmp_new_product_images']) && $_SESSION['tpp_store']['tmp_new_product_images'][$x])?$_SESSION['tpp_store']['tmp_new_product_images'][$x]:false; ?>
                        <?php if ($src || $new_src): ?>
                            <img src="<?php echo $new_src?$base_src . $new_src:$src ?>">
                            <?php if(!$new_src): ?>
                                <input type="hidden" name="original_pic[]" value="<?php echo $image->image_id ?>">

                                <?php if (count($image->child_images) > 0): ?>
                                    <?php foreach ($image->child_images as $child_image): ?>
                                        <input type="hidden" name="original_pic[]" value="<?php echo $child_image->image_id ?>">
                                        <input type="hidden" class="child-image-ordering" name="child_image_ordering[<?php echo $child_image->image_id ?>]" value="<?php echo $child_image->ordering ?>">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <input type="hidden" class="image-ordering" name="image_ordering[<?php echo $new_src?$new_src:$image->image_id ?>]" value="<?php echo $image->ordering ?>">
                            <?php if ($new_src): ?>
                                <input type="hidden" name="uploaded_pic[]" value="<?php echo $new_src ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="message"></div>
            </div>
            <?php $x++; ?>
        <?php endforeach;

    endif;


    ?>

    <?php for ($x = count($product_images) + 1; $x <= 5; $x++): ?>
        <div class="drop-wrap">
            <div class="photo-box">
                <div class="handle"></div>
                <div class="delete-icon"></div>
                <div class="preview">
                    <?php if (!empty($_SESSION['tpp_store']['tmp_new_product_images']) && isset($_SESSION['tpp_store']['tmp_new_product_images'][$x])): ?>
                        <span class=imageHolder">
                                    <img src="<?php echo $base_src . $_SESSION['tpp_store']['tmp_new_product_images'][$x] ?>">
                                </span>
                        <input type="hidden" name="uploaded_pic[]" value="<?php echo $_SESSION['tpp_store']['tmp_new_product_images'][$x] ?>">
                        <input type="hidden" class="image-ordering" name="image_ordering[<?php echo $_SESSION['tpp_store']['tmp_new_product_images'][$x] ?>]" value="<?php echo $x; ?>">
                    <?php else: ?>
                        <div class="upload-icon"></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="message"></div>
        </div>
    <?php endfor; ?>
</div>
    </div>