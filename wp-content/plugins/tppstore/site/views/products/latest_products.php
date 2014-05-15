<div class="widget">
    <h5><?php echo $title ?></h5>
    <div class="blog-divider-top"></div>
    <?php foreach ($products as $product): ?>
        <div class="wrap latest-product">
        <a class="wrap" href="<?php echo $product->getPermalink(); ?>">
            <?php echo $product->getProductImage()->getSrc('store_related', true, array('class'   =>  'align-left')) ?>
            <span class="strong"><?php echo $product->getShortTitle(); ?></span>
            <span class="price"><?php echo $product->getFormattedPrice(true); ?></span>
        </a>
        <form method="post" action="/shop/cart/add/">
            <input type="hidden" name="product" value="<?php echo $product->product_id ?>">
            <input type="hidden" name="quantity" value="1">
            <input type="submit" value="Add to cart" class="btn btn-primary">
        </form>
        </div>
    <?php endforeach; ?>
</div>