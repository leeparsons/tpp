<div class="wrap">
    <?php TppStoreMessages::getInstance()->renderAdmin() ?>
    <?php if (count($products) == 0): ?>
        <p>No products in this category</p>
    <?php else: ?>
        <form method="post" action="<?php echo admin_url('admin.php') ?>">
            <?php wp_nonce_field('save_category', 'category_nonce') ?>
            <input type="hidden" name="action" value="save_category_favourite_products">
            <input type="hidden" name="category" value="<?php echo $id ?>">
            <input type="submit" value="Save" class="button-primary">
            <a href="<?php echo admin_url('admin.php?page=tpp-store-categories'); ?>" class="button button-secondary">Cancel</a>
            <br><br>
            <table id="sort" class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th style="width:25%;text-align:left;">Product</th>
                    <th style="width:25%;text-align:left;">Store</th>
                    <th style="width:25%;text-align:left;">Availability</th>
                    <th style="width:10%;text-align:left;">Price</th>
                    <th style="width:10%;text-align:left;">Favourite</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <?php $color = ($product->enabled == 1?'green':'red') ?>
                <tr>
                    <td style="text-align:left;color:<?php echo $color; ?>"><?php echo $product->product_title; ?></td>
                    <td style="text-align:left;color:<?php echo $color; ?>"><?php echo $product->store_name; ?></td>
                    <td style="text-align:left;color:<?php echo $color; ?>"><?php echo $product->unlimited == 0?$product->quantity_available:'Unlimited'; ?></td>
                    <td style="text-align:left;color:<?php echo $color; ?>"><?php echo $product->store_currency . ' ' . $product->price * (1 + ($product->tax_rate / 100)); ?></td>
                    <td style="text-align:left;color:<?php echo $color; ?>">
                        <img style="cursor:pointer;" class="toggle" data-id="<?php echo $product->product_id ?>" data-on="<?php echo is_null($product->favourite)?'0':'1' ?>" <?php echo is_null($product->favourite)?'src="/assets/images/cross.png">':'src="/assets/images/tick.png">'; ?>
                        <input type="checkbox" style="display:none;" <?php echo is_null($product->favourite)?'':' checked="checked" ' ?> name="product[]" value="<?php echo $product->product_id ?>" id="product_<?php echo $product->product_id ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </form>
    <?php endif; ?>
</div>
<script src="<?php echo TPP_STORE_PLUGIN_URL . 'admin/assets/js/favourites.js'; ?>"></script>