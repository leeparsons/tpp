<?php if (count($products) > 0): ?>
    <?php $image_size = isset($image_size)?$image_size:'thumb' ?>
    <?php echo $paginator->render(); ?>

    <div class="wrap wrap-grey store-product-list">
        <section class="innerwrap">


    <?php $i = 1; ?>
    <?php $type = 0; ?>
            <ul class="item-list wrap">

            <?php foreach ($products as $product): ?>
        <?/*php if ($type != $product->product_type): ?>
            <?php $i = 1; ?>
            <?php if ($type > 0): ?>
                </ul>
                </section>
                </div>
            <?php endif; ?>

            <div class="wrap wrap-white store-product-list">
            <section class="innerwrap">

            <h3 class="innerwrap"><?php

            switch ($product->product_type):
                case '1':
                    echo 'Downloads';
                    break;

                case '2':
                    echo 'Services';
                    break;

                case '3':
                    echo 'Products';
                    break;

                case '4':
                    echo 'Mentor Sessions';
                    break;

                case '5':
                    echo 'Workshops and Events';
                    break;

            endswitch;
            ?></h3>

            <?php $type = $product->product_type; ?>



            <ul class="item-list wrap" id="product_list_<?php echo $product->product_type ?>">
                <?php endif; */ ?>
                <li class="item-box<?php echo $i%4?'':' last' ?>">
                    <a href="<?php echo $product->getPermalink() ?>">
                        <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                        <?php include TPP_STORE_PLUGIN_DIR . 'site/views/product/item_box/type_band.php'; ?>
                        <span class="strong"><?php echo $product->getShortTitle() ?></span>
                        <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
                    </a>
                    <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
                </li>
                <?php $i++; ?>
        <?php endforeach; ?>
        </ul>
    </section>
</div>

<?php endif; ?>
