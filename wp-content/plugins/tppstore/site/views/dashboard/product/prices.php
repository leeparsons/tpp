<legend>Price</legend>

<div class="form-group">
    <label for="product_price">Price (<?php echo $store->getFormattedCurrency(true, $store->currency)  ?>)</label>
    <input type="text" placeholder="Price (<?php echo $store->getFormattedCurrency(true, $store->currency)  ?>)" class="form-control" id="product_price" name="product_price" value="<?php echo $product->price ?>">
</div>

<pre>If you charge tax on this item, add the tax rate below. (Tax will be calculated based on your price above)</pre>
<!---->
<!--<div class="form-group">-->
<!--    <span>Price includes tax?</span>-->
<!--</div>-->

<!--<div class="form-group">
    <label for="include_tax_yes"><input type="radio" name="price_includes_tax" id="include_tax_yes" value="1" <?php //echo intval($product->price_includes_tax) == 1?'checked="checked"':'' ?> Yes</label>
    <label for="include_tax_no"><input type="radio" name="price_includes_tax" id="include_tax_no" value="0" <?php //echo $product->price_includes_tax == '0'?'checked="checked"':'' ?> No</label>
    <label for="include_tax_na"><input type="radio" name="price_includes_tax" id="include_tax_na" value="na" <?php //echo $product->price_includes_tax != '0' && $product->price_includes_tax != '1'?'checked="checked"':'' ?> N/A</label>
</div>-->

<input name="price_includes_tax" value="0" type="hidden" id="include_tax_no">

<?php /* <div class="form-group" style="<?php echo $product->price_includes_tax != '1' && $product->price_includes_tax != '0'?'display:none':'' ?>" id="tax_group"> */ ?>
<div class="form-group">
    <label for="tax_rate">Tax Rate (%) (optional)</label>
    <pre>You do not need to fill this out if you do not charge your customers tax (VAT)</pre>
    <input type="text" class="form-control" id="tax_rate" name="product_tax_rate" placeholder="Tax Rate (%)" value="<?php echo $product->tax_rate ?>">
</div>

<div class="form-group">
    <span class="form-control-static">This is a preview of the final price of the item, including any tax you added: </span>
    <span class="form-control-static"><span><?php echo $product->getFormattedCurrency(true, $store->currency); ?></span><span id="preview_price"><?php

            echo $product->getFormattedPrice(false, false);

            ?></span></span>
</div>

<br><br>



<h4>Discounts</h4>

<div class="form-group">

<!--    <pre>There are three different types of discount you can add to your product - a social discount, a percentage discount or a fixed value discount.-->
<!---->
<!--The percentage discount calculates the percentage to remove from your pre tax price of your product, and automatically updates the cart with the discounted price.-->
<!---->
<!--The value discount works in the same way as the percentage discount except it calculates the discount as a fixed cost according to teh value you enter below.-->


    <label for="discount_type"><input type="checkbox" <?php echo $product->getDiscount()->isSocialDiscount()?'checked="checked"':'' ?> value="social" name="discount_type" id="discount_type">Enable Social Discounts</label>

    <pre>Offer your customers a 5% discount for sharing products on Facebook! Our system can automatically detect when a product is shared from our website and can apply a discount in the user's shopping cart for this product.

The discount will apply for the total quantity of this product purchased at checkout.</pre>

<!--    <select name="discount_type">-->
<!--        <option --><?php //echo true === $product->getDiscount()->isSocialDiscount()?'selected="selected"':'' ?><!-- value="social">Social Discount</option>-->
<!--        <option --><?php //echo true === $product->getDiscount()->isSale()?'selected="selected"':'' ?><!-- value="sale">Sale</option>-->
<!--        <option --><?php //echo true === $product->getDiscount()->isFixed()?'selected="selected"':'' ?><!-- value="fixed">Fixed Discount</option>-->
<!--        <option --><?php //echo true === $product->getDiscount()->isDiscounted()?'':'selected="selected"' ?><!-- value="">No Discount</option>-->
<!--    </select>-->
</div>

<div class="form-group" style="display:none">
    <label for="discount_value">Enter the discount rate as a percentage</label>
    <pre>We suggest 5% as a good social discount reward.</pre>
    <input type="text" placeholder="Discount Rate (e.g.: 5 = 5%)" class="form-control" name="discount_value" id="discount_value" value="<?php echo 5;//$product->getDiscount()->getDiscountValue() ?>">
</div>