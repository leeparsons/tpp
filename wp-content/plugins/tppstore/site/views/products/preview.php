<?php

get_header();

TppStoreMessages::getInstance()->addMessage('message', 'You are in preview mode. Not all buttons and links on this page will work!'); ?>


<article class="product page">
    <div class="wrap" id="errors">
        <?php TppStoreMessages::getInstance()->render() ?>
</div>

<?php if (count($images) > 0): ?>
    <div class="half-left">


            <div class="product-images" id="product_images">

                <?php

                $slide_images = array();
                $thumb_images = array();

                $i = 0;
                $main_image = '';
                ?>

                <?php foreach ($images as $image): ?>
                    <?php

                    if ($image->parent_id > 0) {
                        continue;
                    }

                    $slide_images[] = '<img ' . ($i>0?'class="vhidden"':'class="active"') . ' src="' . $image->getSrc('full') . '" alt="' . $image->alt . '">';
                    $thumb_images[] = '<img ' . ($i>0?'':'class="active"') . ' src="' . $image->getSrc('slideshow_thumb') . '" alt="slide thumbnail ' . $image->alt . '">';

                    if ($i == 0) {
                        $main_image = $image->getSrc('thumb');
                    }

                    $i++;

                    ?>
                <?php endforeach; ?>

                <div class="slides">
                    <?php echo implode('', $slide_images); ?>
                </div>
                <?php if (count($slide_images) > 1): ?>
                    <nav class="slide-navigation">
                        <?php echo implode('', $thumb_images); ?>
                    </nav>
                <?php endif; ?>
            </div>
    </div>
<?php endif; ?>

    <div class="half-right">

        <form class="cart-group" id="cart_form" method="post" action="/shop/cart/add">

            <div class="product-particulars">
                <header>
                    <h1><?php echo $product->product_title; ?></h1>
                </header>

                <div class="form-group">
                    <a href="<?php echo $store->getPermalink() ?>"><span>by: <span class="author"><?php echo $store->store_name ?></span></span></a>
                </div>

                <?php $product_options = $product->getOptions(); ?>

                <?php if (intval($product->unlimited) == 1 || $product->quantity_available > 0): ?>
                    <div class="form-group">
                        <?php if (false !== $product_options): ?>
                            <select name="product_option">
                                <option value="-1"><?php echo $product->getFormattedPrice(true) ?></option>
                                <?php foreach ($product_options as $option): ?>
                                    <option value="<?php echo $option->option_id ?>"><?php echo $option->option_name . ' ' . $product->getFormattedCurrency() . $option->option_price ?></option>
                                <?php endforeach ?>
                            </select>
                        <?php else: ?>
                            <p><strong><?php echo $product->getFormattedPrice(true) ?></strong></p>
                        <?php endif; ?>
                    </div>
                    <?php $sold_out = false; ?>
                    <div class="form-group">
                        <?php if (intval($product->unlimited) == 0): ?>
                            <?php if ($product->quantity_available > 0): ?>
                                <p><strong><?php echo $product->quantity_available ?> Available</strong></p>
                            <?php else: ?>
                                <?php $sold_out = true; ?>
                                <p><strong class="red">Sorry, sold out!</strong></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group hidden">
                        <label for="quantity">Quantity:</label>
                        <input type="text" class="form-control" value="1" name="quantity" id="quantity">
                        <input type="hidden" id="product" name="product" value="<?php echo $product->product_id ?>">
                    </div>

                <?php else: ?>
                    <?php $sold_out = true; ?>
                    <div class="form-group">
                        <p><strong>Sorry, sold out!</strong></p>
                    </div>
                <?php endif; ?>
                <div class="form-group cart-buttons">
                    <?php if ($product->getDiscount()->isSocialDiscount()): ?>
                    <a href="#" onclick="alert('sorry, this does not work in preview mode');return false;" class="btn btn-primary form-control">Share to get 5% discount</a>
                    <?php endif; ?>
                    <a href="#" class="btn-primary btn-cart btn form-control" onclick="alert('sorry, this does not work in preview mode');return false;" style="margin-bottom:0">Add to cart</a>
                </div>
            </div>
        </form>


    </div>




    <div class="half-left">
        <div class="description">
            <h2>Item Details</h2>
            <div class="wrap"><pre><?php echo $product->product_description ?></pre></div>
        </div>
    </div>
    <div class="half-right" id="store_profile">
        <div class="product-aside align-left">
            <a class="store-tag" href="<?php echo $store->getPermalink() ?>"><strong>About the store</strong></a>

            <?php echo $store->getSrc(true, 'store_thumb'); ?>
            <a class="indent" href="/shop/profile/<?php echo $store->user_id ?>"><?php echo $store->getStoreByUserID()->getOwner() ?></a>



            <span class="indent"><?php echo $store->getLocation() ?></span>

            <a class="indent" href="<?php echo $store->getPermalink() ?>">Read more about the seller</a>



            <div id="facebook_friends" class="align-left wrap"></div>

        </div>

        <?php $store_products = $store->getProducts(1, 1, false, 3, $product->product_id);

        if (!empty($store_products)): ?>
            <div class="align-left store-products product-aside">

                <h3 class="indent">Some of my products</h3>

                <div class="white-bg">

                    <?php $store_product_count = count($store_products); ?>

                    <?php $iterator = 1; foreach($store_products as $store_product): ?>
                        <?php

                        $main_image = $store_product->getMainImage('main');

                        $permalink = $store_product->getPermalink()

                        ?>
                        <div class="store-product wrap <?php echo $iterator == $store_product_count?'last':'' ?>">
                            <?php if (!empty($main_image)): ?>
                                <a class="align-left" href="<?php echo $permalink; ?>"><?php echo $main_image['main']->getSrc('store_related', true); ?></a>
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
                                <a href="#" onclick="return false;" class="align-right btn btn-primary">Add to Cart</a>
                            </form>
                        </div>

                        <?php $iterator++; endforeach; ?>
                </div>
            </div>

        <?php endif; ?>


    </div>


</article>
    <script>
        var main_image = "<?php echo get_bloginfo('url') .  $main_image ?>";
        var permalink = "<?php echo $product->getPermalink() ?>";
        var title = "<?php echo esc_attr($product->product_title) ?>";
        var shop_fb_id = "<?php echo $store->getFacebookId() ?>";
        var description = "<?php echo esc_attr(str_replace('<br>', ' ', nl2br($product->excerpt, false))) ?>";
        <?php wp_enqueue_script('product', TPP_STORE_PLUGIN_URL . '/site/assets/js/product-ck.js', 'jquery', 1.1, 1) ?></script>
<?php get_footer();