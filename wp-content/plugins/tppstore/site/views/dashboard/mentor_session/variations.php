<legend class="wrap">Session Variations (optional)</legend>


<div class="form-group">

    <pre>Use this section to add purchase options for your session. For example, 1 hour session, 2 hour session, 3 hour session etc.

If someone purchases an option, the price of the option below will override the price of the product you set earlier.

The tax rate you set earlier will also apply to the prices you set here.</pre>

</div>
<div class="wrap">

    <div class="half-left">

        <div class="form-group">
            <label for="variation_name">Option Name:</label>
            <div class="wp-error hidden" id="option_name_error"></div>
            <input type="text" id="variation_name" class="form-control" placeholder="Option Name">
        </div>
        <div class="form-group">

            <label for="variation_cost">Option Price (<?php echo $store->getFormattedCurrency()  ?>):</label>
            <div class="wp-error hidden" id="option_cost_error"></div>
            <input type="text" id="variation_cost" class="form-control" placeholder="Price (<?php echo $store->getFormattedCurrency() ?>)">

        </div>


        <div class="form-group">

            <label for="variation_availability">Option Quantity Available:</label>
            <div class="wp-error hidden" id="option_availability_error"></div>
            <input type="text" id="variation_availability" class="form-control" placeholder="1">

        </div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" id="add_product_variation" value="Add Option">
        </div>




    </div>

    <div class="half-right">
        <ul id="product_options">
            <?php

            $product_options = $product->getoptions();

            if (is_array($product_options) && count($product_options) > 0):
                foreach ($product_options as $option): ?>
                    <li>
                        <span class="close">x</span>
                        <span class="option-name"><?php echo $option->option_name ?></span>
                        <span class="option-price"> <?php echo $store->getFormattedCurrency()  ?><?php echo number_format($option->option_price, 2) ?></span>
                        <span class="option-availability"><?php echo $option->option_quantity_available ?> available</span>
                        <input type="hidden" name="product_option[<?php echo $option->option_id ?>][name]" value="<?php echo urlencode($option->option_name) ?>">
                        <input type="hidden" name="product_option[<?php echo $option->option_id ?>][option_id]" value="<?php echo $option->option_id ?>">
                        <input type="hidden" name="product_option[<?php echo $option->option_id ?>][price]" value="<?php echo number_format($option->option_price, 2) ?>">
                        <input type="hidden" name="product_option[<?php echo $option->option_id ?>][availability]" value="<?php echo $option->option_quantity_available ?>">
                    </li>
                <?php endforeach;
            endif;

            ?>
        </ul>

    </div>
</div>
