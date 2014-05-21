<?php
/**
 * User: leeparsons
 * Date: 19/03/2014
 * Time: 08:05
 */
 
if (count($products) > 0):

    if (!isset($image_size)) {
        $image_size = 'store_related';
    }
?>
    <div class="wrap">
        <div class="blog-divider-top"></div>
        <h3 class="related">Related Products</h3>
        <div class="blog-divider-bottom"></div>
    </div>
    <ul class="related-products wrap">
    <?php foreach ($products as $product): ?>
        <li>
            <a href="<?php echo $product->getPermalink() ?>">
                <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                <span class="strong"><?php echo $product->getShortTitle() ?></span>
                <span class="price">£5.87</span>
            </a>
            <form method="post" action="/shop/cart/add/">
                <input type="hidden" name="product" value="254">
                <input type="hidden" name="quantity" value="1">
                <input type="submit" value="Add to cart" class="btn btn-cart">
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
<?php
    /*
     * old stuff
     *
    ?>

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
<?php */

endif;