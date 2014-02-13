<div class="widget">
    <h5>Latest products</h5>
    <?php foreach ($products as $product): ?>
        <a class="wrap latest-product" href="<?php echo $product->getPermalink(); ?>">
            <?php echo $product->getProductImage()->getSrc('slideshow_thumb', true, array('class'   =>  'align-left')) ?>
            <span class="strong"><?php echo $product->getShortTitle(); ?></span>
            <br/>
            <span class="price"><?php echo $product->getFormattedPrice(true); ?></span>
        </a>
    <?php endforeach; ?>
</div>