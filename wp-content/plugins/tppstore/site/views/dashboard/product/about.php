<legend class="wrap">About your product</legend>

<div class="form-group">
    <label for="product_title">Product Title</label>
    <pre>A short summary title, people will see this first and should be able to determine what you are selling from it. We recommend keeping it short and snappy, about 50 characters or 10 words as a rule of thumb. The maximum number of letters and spaces allowed is 140.</pre>
    <input type="text" class="form-control" id="product_title" name="product_title" placeholder="Product Title" value="<?php echo $product->product_title ?>">

</div>

<div class="form-group">
    <label for="product_quantity">Quantity Available</label>

    <p>If you have an unlimited number of items to sell, tick the box below. Otherwise, enter the number available in the box below.</p>

    <label class="wrap"><input type="checkbox" name="unlimited" id="unlimited" <?php echo (intval($product->unlimited) == 1)?'checked="checked"':'' ?> value="1">Unlimited</label>
    <input type="text" class="form-control" id="product_quantity" name="product_quantity" placeholder="quantity" value="<?php echo $product->quantity_available ?>" <?php echo (intval($product->unlimited) == 1)?'style="visibility:hidden"':'' ?>>
</div>




<div class="form-group">
    <label id="product_type_label" <?php if($product->product_type == 2 || $product_category_1 == 3) { echo 'style="display:none"'; } ?>>What type is your product?</label>
    <select name="product_type" id="product_type" class="form-control" <?php echo $product_category_1 == 3?'style="display:none"':'' ?>>
        <option value="2" <?php if(is_null($product->product_type) || $product->product_type == 2) {echo 'selected="selected"';} ?>>Services (E.g.: mentor sessions, the client books the service and contacts you for a date)</option>
        <option value="1" <?php if($product->product_type == 1) {echo 'selected="selected"';} ?>>Downloadable</option>
        <option value="3" <?php if($product->product_type == 3) {echo 'selected="selected"';} ?>>Physical Product</option>
    </select>
</div>

<?php

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/product/download.php';

?>




<div class="form-group product-type-group <?php if (intval($product->product_type) != 2 || $product_category_1 == 3) {echo 'hidden';} ?>" id="services_group">
    <!--        <label>Enter the name of your service</label>-->
    <!--        <input name="service" type="text" class="form-control" value="--><?php //if (intval($product->product_type) == 2) {echo $product->product_type_text;} ?><!--">-->
</div>

<!--<div class="form-group product-type-group --><?php //if (intval($product->product_type) != 3) {echo 'hidden';} ?><!--" id="hosted_group">-->
<!--    <label>Enter the url to your product</label>-->
<!--    <input name="hosted" type="text" class="form-control" value="--><?php //if (intval($product->product_type) == 3) {echo $product->product_type_text;} ?><!--">-->
<!--</div>-->


<div class="form-group" >
    <label>Enter the full description for your product</label>
    <pre>Tell your potential customers what your product is all about and its benefits. Include any answers to questions or details you will need from the customer to complete the sale.</pre>
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
