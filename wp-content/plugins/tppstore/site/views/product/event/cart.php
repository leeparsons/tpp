<form class="cart-group" id="cart_form" method="post" action="/shop/cart/add">

    <div class="product-particulars">
        <header>
            <h1>Event: <?php echo ucwords(strtolower($product->product_title)); ?></h1>
        </header>

        <div class="form-group">
            <span class="indent">Event Date: <?php echo $product->getEventDisplayDateRange(); ?></span>
        </div>


        <div class="form-group">
            <span class="indent">Event Duration: <?php echo $product->getEventDuration(); ?></span>
        </div>


        <div class="form-group">
            <a href="<?php echo $store->getPermalink() ?>"><span>hosted by: <span class="author"><?php echo $store->store_name ?></span></span></a>
        </div>

        <?php if (intval($product->unlimited) == 1 || $product->quantity_available > 0): ?>
            <div class="form-group">
                <p><strong><?php echo $product->getFormattedPrice(true) ?></strong></p>
            </div>
            <?php $sold_out = false; ?>
            <div class="form-group">
                <?php if (intval($product->unlimited) == 0): ?>
                    <?php if ($product->quantity_available == 0): ?>
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
        <?php if (false === $sold_out): ?>
        <div class="form-group cart-buttons">
            <?php if($product->getDiscount()->isSocialDiscount()): ?>
                <?php if (false !== $user):  ?>
                    <a href="#" id="fb_share" class="btn btn-primary align-left wrap">Share on Facebook to get a 5% discount!</a>
                    <a class="hidden" id="f_click" href="#"></a>
                <?php else: ?>
                    <a class="btn btn-primary align-left wrap" href="/shop/store_login/?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">Login to get a discount</a>
                <?php endif; ?>
                <input type="submit" value="add to cart" class="align-right btn-primary btn-cart btn form-control">
            <?php else: ?>
                <input type="submit" value="add to cart" class="btn-primary btn-cart btn form-control">
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</form>