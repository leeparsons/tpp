<legend class="wrap">About your session</legend>

<div class="form-group">
    <label for="product_title">Session Title</label>
    <pre>You can create many different mentor sessions on a range of topics, eg "Business Blitz", "Wedding Portfolio Review" or "Help with Your Online Marketing Plan". Each one will need a separate listing. Here choose the title for this session - it should explain the key area you will cover. The maximum number of letters and spaces allowed is 140.</pre>
    <input type="text" class="form-control" placeholder="session title" id="product_title" name="product_title" value="<?php echo $product->product_title ?>">
</div>

<div class="form-group">
    <label for="product_quantity">Places available per month</label>

    <pre>If you have an unlimited number of places available, tick the box below. Otherwise, enter the number available in the box below.</pre>

    <label class="wrap"><input type="checkbox" name="unlimited" id="unlimited" <?php echo (intval($product->unlimited) == 1)?'checked="checked"':'' ?> value="1">Unlimited</label>
    <input type="text" placeholder="Places Available" class="form-control" id="product_quantity" name="product_quantity" value="<?php echo $product->quantity_available ?>" <?php echo (intval($product->unlimited) == 1)?'style="visibility:hidden"':'' ?>>
</div>

<input type="hidden" name="product_type" id="product_type" value="4">

<div class="form-group product-type-group" id="download_group">
    <label>Upload file for download</label>
    <pre>This is optional, and could be information about your session or otherwise. This will be sent out to people who purchase a session with you.</pre>
    <input name="download" type="file" class="form-control">

        <input type="hidden" name="original_download" value="<?php echo $product->product_type_text; ?>">
        <?php if($product->product_type_text != ''): ?>
            <p><br>
                <strong>Current Download File:</strong>
                <a href="<?php echo $product->getDownloadUrl(false) ?>" target="_blank"><?php echo $product->product_type_text ?></a>
                <input type="hidden" name="original_download" value="<?php echo $product->product_type_text?>">
            </p>

        <?php endif; ?>

</div>



<div class="form-group" >
    <label>Describe what your session is about</label>
    <?php
/*
    wp_editor($product->product_description, 'full_description', array(
        'media_buttons' =>  false,
        'teeny'         =>  true
    ));
*/
    ?>
    <textarea rows="15" name="full_description" id="full_description" class="form-control"><?php echo esc_textarea(strip_tags($product->product_description)) ?></textarea>
    <span id="description_count" class="<?php $len = mb_strlen(strip_tags($product->product_description), 'UTF-8'); echo $len <= 21000?'wp-message':'wp-error' ?>"><?php echo 21000 - $len ?> characters remaining</span>
</div>