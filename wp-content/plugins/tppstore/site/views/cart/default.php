<?php
/**
 * User: leeparsons
 * Date: 26/12/2013
 * Time: 20:49
 */

get_header(); ?>

    <article class="page-article cart-page">

    <header>
        <h1>My Cart</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render() ?>

    <?php

    $item_count = $cart->getItemCount();

    $items = $cart->getProducts();

    $i = 0;

    $subtotal = 0;
    $discounts = 0;
    $tax = 0;

    ?>
    <?php if ($item_count > 0): ?>

        <p>Thank you for adding these items to your cart. You can either checkout and purchase these items now, or <a href="/shop" class="btn btn-primary">continue shopping</a> to make sure you've got everything you need.</p>
        <!---->
        <!--        <div class="wrap checkout text-right">-->
        <!--            <a href="/shop/checkout" class="btn btn-primary">Checkout</a>-->
        <!--        </div>-->

        <?php foreach ($items as $store_id => $cart_store): ?>
            <?php

            $store = $this->getStoreModel()->setData(array('store_id' =>  $store_id));

            $store->getStoreById();

            ?>

            <header class="wrap">
                <h3><a class="strap-tag" href="<?php echo $store->getPermalink(); ?>"><?php echo $store->store_name; ?></a></h3>
            </header>



            <article class="aside-25 store-card">

                <?php

                echo $store->getSrc(true, 'thumb');

                ?>
                <p>
                    <a class="store-tag align-right" href="<?php echo $store->getPermalink() ?>">Browse store &gt;&gt;</a>
                </p>
            </article>

            <aside class="aside-75">
                <header>
                    <h3>Products</h3>
                </header>


                <?php foreach($cart_store['products'] as $cart_item): ?>

                    <div class="cart-product wrap">
                        <div class="wrap">
                            <div class="wrap">
                                <div class="aside-75">
                                    <?php echo $cart_item->getImage('cart_thumb', true, array('class'   =>  'align-left')); ?>
                                    <a href="<?php echo $cart_item->getPermalink() ?>" class="align-left"><strong><?php echo $cart_item->product_title ?></strong></a>
                                    <p><?php echo $cart_item->getShortDescription() ?></p>
                                </div>
                                <div class="aside-25">
                                    <div class="wrap">
                                        <?php if ($cart_item->discount > 0): ?>
                                            <span>original Price:</span>
                                            <span class="red price"><?php echo $cart_item->getFormattedPrice(true) ?></span>
                                            <span>Your discounted price:</span>
                                            <span class="green price"><?php echo $cart_item->getFormattedDiscountedPrice(true); ?>
                                        <?php else: ?>
                                            <span class="price align-right">Price: <?php echo $cart_item->getFormattedPrice(true) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="quantity-update wrap">
                                        <form method="post" action="/shop/cart/update" class="update">
                                            <label for="quantity_<?php echo $cart_item->product_id ?>">Quantity:</label>
                                            <input type="text" size="3" id="quantity_<?php echo $cart_item->product_id ?>" name="quantity" value="<?php echo $cart_item->order_quantity ?>">
                                            <input type="hidden" name="product" value="<?php echo $cart_item->product_id; ?>">
                                            <input type="submit" value="Update" class="btn btn-info ">
                                        </form>
                                    </div>

                                    <div class="delete wrap">
                                        <form method="post" action="/shop/cart/remove">
                                            <input type="hidden" name="product" value="<?php echo $cart_item->product_id; ?>">
                                            <input type="hidden" name="store" value="<?php echo $cart_item->store_id; ?>">
                                            <input type="submit" value="remove" class="btn btn-danger align-right">
                                        </form>
                                    </div>

                                    <div class="wrap">
                                        <?php if ($cart_item->tax_rate > 0): ?>
                                            <span class="price text-right">VAT/Tax: <?php echo $cart_item->getFormattedTax(true, true, $cart_item->order_quantity) ?></span>
                                        <?php endif; ?>
                                        <span class="price text-right">Line Total: <?php echo $cart_item->getLineItemFormattedTotal(true, true); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

                <div class="wrap checkout">
                    <div class="align-right">
                        <span>Store Total: <?php echo $cart_item->getFormattedCurrency() .  $cart_store['total'] ?></span>
                    </div>
                </div>

                <div class="wrap checkout">
                    <form method="post" action="/shop/checkout/process" class="align-right">
                        <label>Checkout and purchase these products</label>
                        <input type="hidden" name="store" value="<?php echo $store_id ?>">
                        <input type="submit" value="checkout &amp; Purchase" class="btn btn-primary">
                    </form>
                </div>

            </aside>


            <hr>

            </aside>


        <?php endforeach; ?>



    <?php else: ?>

        <p class="wp-error">Your cart is empty. <a class="btn btn-primary" href="/shop">Continue Shopping</a></p>

    <?php endif; ?>

    </article>

<?php get_footer();
