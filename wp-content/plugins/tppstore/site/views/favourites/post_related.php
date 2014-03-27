<?php
/**
 * User: leeparsons
 * Date: 19/03/2014
 * Time: 08:05
 */
 
if (count($products) > 0): ?>
    <div class="wrap">
        <h3 class="related">Related Products</h3>
    </div>
    <div class="wrap">
    <ul class="related-products item-list">
    <?php foreach ($products as $product): ?>
        <li class="item-box">
            <a href="<?php echo $product->getPermalink() ?>">
                <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                <span class="strong"><?php echo $product->getShortTitle() ?></span>
                <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
            </a>
            <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
    </div>
<?php endif;