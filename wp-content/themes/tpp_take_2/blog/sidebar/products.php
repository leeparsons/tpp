<?php $products = TppStoreControllerProduct::getInstance()->getLatestProducts(5); ?>
<?php if (count($products) > 0): ?>
    <h3 class="wrap">Latest Products</h3>
    <div class="blog-divider-top"></div>
    <?php foreach ($products as $product): ?>
        <article class="wrap hentry sidebar-product">
            <a class="align-left" href="<?php echo $product->getPermalink(); ?>"><img src="<?php echo $product->getProductImage()->getSrc('blog_sidebar') ?>" alt="<?php echo esc_textarea($product->product_title) ?>"></a>
            <div class="align-left meta-holder">
                <a class="entry-title align-left" href="<?php echo $product->getPermalink(); ?>"><?php echo $product->getShortTitle(55); ?></a>
                <form method="post" action="/shop/cart/add">
                    <a class="align-left price" href="<?php echo $product->getPermalink(); ?>"><?php echo $product->getFormattedPrice(true); ?></a>
                    <button class="btn btn-cart">Add to cart</button>
                    <?php

                    /*

                    <a class="btn btn-primary" href="<?php echo $product->getPermalink(); ?>">View Item</a>

                    */ ?>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif;

