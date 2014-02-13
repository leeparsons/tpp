<?php if (count($products) > 0): ?>
<?php $image_size = isset($image_size)?$image_size:'thumb' ?>
    <?php echo $paginator->render(); ?>

        <ul class="item-list" id="product_list">
            <?php $i = 1; ?>
            <?php foreach ($products as $product): ?>
                <li class="item-box<?php echo $i%4?'':' last' ?>">
                    <a href="<?php echo $product->getPermalink() ?>">
                        <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                        <span class="strong"><?php echo $product->getShortTitle() ?></span>
                        <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
                    </a>
                    <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
                </li>
                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>
<?php endif; ?>
