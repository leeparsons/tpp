<?php

$mentor_sessions = $this->getMentorsModel()->setData(array('store_id'    =>  $product->store_id))->getMentorSessionsByMentor($product->getMentor()->mentor_id, 1, 3, 1, $product->product_id);

if (!empty($mentor_sessions)): ?>
    <div class="align-left store-products product-aside">

        <h3 class="indent">Some of my sessions</h3>

        <div class="white-bg">

            <?php $store_product_count = count($mentor_sessions); ?>

            <?php $iterator = 1; foreach($mentor_sessions as $store_product): ?>
                <?php

                $store_main_image = $store_product->getMainImage('main');

                $permalink = $store_product->getPermalink()

                ?>
                <div class="store-product wrap <?php echo $iterator == $store_product_count?'last':'' ?>">
                    <?php if (!empty($store_main_image)): ?>
                        <a class="align-left" href="<?php echo $permalink; ?>"><?php echo $store_main_image['main']->getSrc('store_related', true); ?></a>
                    <?php endif; ?>
                    <div class="align-left store-product-meta">
                        <a class="wrap" href="<?php echo $permalink; ?>"><strong><?php echo $store_product->getShortTitle(); ?></strong></a>
                    </div>

                    <form method="post" action="/shop/cart/add">
                        <?php

                        //echo $store_product->getDisplayAvailability();

                        ?>
                        <a class="price" href="<?php echo $permalink; ?>"><?php echo $store_product->getFormattedPrice(true); ?></a>
                        <input type="hidden" name="product"  value="<?php echo $store_product->product_id; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="submit" class="align-right btn btn-primary" value="Add to Cart">
                    </form>
                </div>

                <?php $iterator++; endforeach;

            unset($store_main_image);
            ?>
        </div>
    </div>

<?php endif; ?>