<legend class="wrap">About your event</legend>

<div class="form-group">
    <label for="product_quantity">Places available</label>

    <pre>If you have an unlimited number of places available, tick the box below. Otherwise, enter the number available in the box below.</pre>

    <label class="wrap"><input type="checkbox" name="unlimited" id="unlimited" <?php echo (intval($product->unlimited) == 1)?'checked="checked"':'' ?> value="1">Unlimited</label>
    <input type="text" placeholder="Places Available" class="form-control" id="product_quantity" name="product_quantity" value="<?php echo $product->quantity_available ?>" <?php echo (intval($product->unlimited) == 1)?'style="visibility:hidden"':'' ?>>
</div>

<div class="form-group" >
    <label>Describe what your event is about</label>

    <pre style="clear:left;">Tell people about:

        The event / course / workshop leaders
        Why the course or event is being held
        Who is it suitable for

        Include:

        The venue address
        Timings
        Course syllabus / structure if applicable
        Any other important information like travel details to the venue and car parking
    </pre>
    <textarea rows="15" name="full_description" id="full_description" class="form-control"><?php echo esc_textarea(strip_tags($product->product_description)) ?></textarea>
    <span id="description_count" class="<?php $len = mb_strlen(strip_tags($product->product_description), 'UTF-8'); echo $len <= 21000?'wp-message':'wp-error' ?>"><?php echo 21000 - $len ?> characters remaining</span>
</div>

