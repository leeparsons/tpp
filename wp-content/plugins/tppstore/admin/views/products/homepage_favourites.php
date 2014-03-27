<div class="wrap">

    <h1>Home page Featured Products</h1>

    <?php TppStoreMessages::getInstance()->renderAdmin() ?>

    <select id="sort">
        <option value="best_selling" <?php echo $sort == 'best_selling'?'selected':'' ?>>Best Sellers</option>
        <option value="az" <?php echo $sort == 'p.product_title' && $direction == 'asc'?'selected':'' ?>>Name a-z</option>
        <option value="za" <?php echo $sort == 'p.product_title' && $direction == 'desc'?'selected':'' ?>>Name z-a</option>
    </select>

    <script>
        document.getElementById('sort').onchange = function() {
            window.location.href = '<?php echo admin_url('admin.php') ?>?page=tpp-store-product-favourites&sort=' + this.value + '&e=';
        };
    </script>

    <form method="post" action="<?php echo admin_url('admin.php') ?>">
        <?php wp_nonce_field('save_homepage', 'save_homepage_nonce') ?>
        <input type="hidden" name="action" value="save_homepage_favourite_products">
        <input type="submit" value="Save" class="button-primary">
        <input type="hidden" name="position" value="homepage">
        <a href="<?php echo admin_url('admin.php?page=tpp-store-categories'); ?>" class="button button-secondary">Cancel</a>
        <br><br>
        <table id="sort" class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th style="width:30%">Product</th>
                <th style="width:30%">Store</th>
                <?php if ($sort == 'best_selling'): ?>
                <th style="width:10%;">Amount sold</th>
                <?php endif; ?>
                <th style="width:10%;">On Homepage</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php

                         echo $product->getProductImage()->getSrc('store_related', true)

                        ?><?php echo $product->product_title ?></td>
                    <td><?php echo $product->store_name ?></td>
                    <?php if ($sort == 'best_selling'): ?>
                    <td><?php echo $product->sold; ?></td>
                    <?php endif; ?>
                    <td><img class="toggle" data-id="<?php echo $product->product_id ?>" data-on="<?php echo $product->position == 'homepage'?'1':'0' ?>" src="/assets/images/<?php

                        switch ($product->position) {
                            case 'homepage':
                                echo 'tick.png';

                                break;
                            default:
                                echo 'cross.png';

                                break;
                        }

                        ?>"/>

                        <input style="display:none" id="product_<?php echo $product->product_id ?>" value="<?php echo $product->product_id ?>" type="checkbox" name="product[<?php echo $product->product_id ?>]" <?php echo $product->position == 'homepage'?'checked="checked"':'' ?>>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>
<?php

wp_enqueue_script('jquery-ui-sortable');

wp_enqueue_script('tpp-favourites', TPP_STORE_PLUGIN_URL . '/admin/assets/js/favourites.js', array('jquery')); ?>