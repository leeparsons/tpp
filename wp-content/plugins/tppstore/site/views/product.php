<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 23:12
 */



$images = $product->getImagesBySize('full');

if (count($images) > 0):

    $i = 0;
    $slide_images = array();
    $thumb_images = array();

    $og_images = array();



    foreach ($images as $image):
        $og_images[$i] = get_site_url() . $image->getSrc('full');

        if ($i == 0) {
            $main_image = $og_images[$i];
        }

        $slide_images[] = '<img ' . $image->getWidth(true) . ' ' . $image->getHeight(true) . ' ' .  ($i>0?'class="vhidden"':'class="active"') . ' src="' . $og_images[$i] . '" alt="' . $image->alt . '">';
        $thumb_images[] = '<img ' . ($i>0?'':'class="active"') . ' src="' . $image->getSrc('slideshow_thumb') . '" alt="slide thumbnail ' . $image->alt . '">';
        $i++;
    endforeach;

    unset($image);
    unset($i);

    TppStoreHelperHtml::getInstance()->addOgImages($og_images);

endif;

get_header();

$user = TppStoreControllerUser::getInstance()->loadUserFromSession();

if (isset($preview) && $preview == 1) {

    $preview_string = 'You are in preview mode.';

    if (isset($_SESSION['preview_session'])) {
        $preview_string .= ' <a href="/shop/dashboard/preview_close" class="btn btn-primary">close preview</a>';
    }

    TppStoreMessages::getInstance()->addMessage('message', $preview_string);
}

?>

<article class="product page">
    <div class="wrap" id="errors">
        <?php TppStoreMessages::getInstance()->render() ?>
    </div>

<?php


if (count($images) > 0): ?>
<div class="half-left">


    <div class="product-images" id="product_images">

        <?php



        ?>

        <?php foreach ($images as $image): ?>
            <?php



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
            <!--        <fieldset>-->
            <!--            <legend>Add to cart</legend>-->
            <!--        </fieldset>-->
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

    <div class="product-aside align-left white-bg" id="wish_list">

        <?php if (false === $user): ?>
            <a class="btn btn-primary" href="/shop/store_login/?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">Login to add to your wish list</a>
        <?php else: ?>
            <form method="post" action="/shop/wishlist/add/">
                <input type="hidden" name="product" value="<?php echo $product->product_id ?>">
                <input type="submit" value="Add to wishlist" class="btn btn-primary form-control">
            </form>
            <a href="/shop/myaccount/wishlist" class="btn btn-primary"><?php

                $total = TppStoreModelWishlist::getInstance()->setData(array(
                    'user_id'   =>  $user->user_id
                ))->getTotalItems();

                echo $total;

                ?> item<?php echo $total == 1?'':'s' ?> in your wish list</a>
        <?php endif; ?>
    </div>
</div>




<div class="half-left">
    <div class="description">
        <h2>Details</h2>
        <div class="wrap"><pre><?php echo $product->product_description ?></pre></div>
    </div>
</div>
<div class="half-right" id="store_profile">
    <div class="product-aside align-left">
        <a class="store-tag" href="<?php echo $store->getPermalink() ?>"><strong>About this store</strong></a>

        <?php echo $store->getSrc(true, 'store_thumb'); ?>
        <a class="indent" href="/shop/profile/<?php echo $store->user_id ?>"><?php echo $store->getStoreByUserID()->getOwner() ?></a>



        <span class="indent"><?php echo $store->getLocation() ?></span>

        <a class="indent" href="<?php echo $store->getPermalink() ?>">Read more about the seller</a>



        <div id="facebook_friends" class="align-left wrap"></div>

    </div>

<?php /*
 TODO: add for launch
    <div class="product-aside align-left">
        <form method="post" action="/shop/store/ask">
            <div class="form-group">
            <input type="hidden" name="store" value="<?php echo $store->store_id ?>">
            <input type="submit" value="Ask Store Owner a Question" class="btn btn-primary form-control">
            </div>
        </form>
    </div>
*/ ?>
    <?php $store_products = $store->getProducts(1, 1, false, 3, $product->product_id);


    if (!empty($store_products)): ?>
        <div class="align-left store-products product-aside">

            <h3 class="indent">Some of my products</h3>

            <div class="white-bg">

            <?php $store_product_count = count($store_products); ?>

            <?php $iterator = 1; foreach($store_products as $store_product): ?>
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

    <?php /* categories


//$cats = $product->getCategories();
//$total_cats = count($product->getCategories());
 if ($total_cats > 0): ?>
        <div class="tags">
            <h2>Categories:</h2>
            <p><?php $i = 0; ?>
                <?php foreach ($cats as $cat): ?>
                    <?php $i++; ?>
                    <a href="<?php echo $cat->getPermalink() ?>"><?php echo $cat->category_name; ?></a><?php echo ($i < $total_cats?',':'') ?>
                <?php endforeach; ?>
            </p>
        </div>
    <?php endif;

 end categories
 */
 ?>

</div>


<?php /* reviews

 $rating = $product->getAverageRating(); ?>

    <div class="half-left">


        <h2>Reviews &amp; Ratings</h2>

        <?php if ($rating['reviews'] > 0): ?>

        <div class="wrap"><?php

        include TPP_STORE_PLUGIN_DIR . 'helpers/ratings.php';
        TppStoreHelperRatings::renderStars($rating['average']); ?>

            <span class="wrap"><?php echo $rating['reviews']; ?> Review<?php

                echo $rating['reviews'] == 1?'':'s';

                ?></span>

        </div>
        <?php endif; ?>
        <?php if (false === $user): ?>

            <div class="wrap" id="review_form">
                <a class="btn btn-primary" href="/shop/store_login/?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">Login to review</a>
            </div>

        <?php else: ?>



            <form action="/shop/review/add/" id="review_form" method="post" class="wrap">
                <fieldset>
                    <legend>Write a review</legend>

                    <div class="wrap">
                        <?php TppStoreMessages::getInstance()->render(true, 'review'); ?>
                    </div>

                    <input type="hidden" value="<?php echo $product->product_id ?>" name="product">

                    <div class="form-group">
                        <label for="star_rating">Select the rating</label>
                        <select id="star_rating" name="rating" class="form-control">
                            <option value="0">0 stars</option>
                            <option value="1">1 star</option>
                            <option value="2">2 stars</option>
                            <option value="3">3 stars</option>
                            <option value="4">4 stars</option>
                            <option value="5">5 stars</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="review_title">Title (Summary):</label>
                        <input type="text" name="review_title" placeholder="title (summary)" class="form-control" id="review_title">
                    </div>
                    <div class="form-group">
                        <label for="review_description">Review</label>
                        <textarea name="review_description" id="review_description" class="form-control" rows="5" placeholder="review"></textarea>
                    </div>
                    <div style="position:absolute;overflow:hidden;height:0;width:0;">
                        <input type="text" name="mc_first_name" value="">
                    </div>
                    <input type="submit" class="btn-primary btn" value="Post">
                </fieldset>
            </form>

        <?php endif; ?>

        <div class="wrap">
            <?php

            if ($rating['reviews'] > 0):

            $reviews = $product->getReviews();
            ?>
                <ul class="reviews">
                    <?php foreach ($reviews as $review): ?>
                        <li class="align-left wrap">
                            <div class="align-left">
                                <?php echo $review->getUser()->getSrc('slideshow_thumb', true, true); ?>
                            </div>
                            <div class="align-left">
                                <strong><?php echo $review->review_title ?></strong>
                                <div class="wrap">
                                    <?php

                                    TppStoreHelperRatings::renderStars($review->rating);

                                    ?>
                                    <pre><?php echo $review->review_description ?></pre>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php

            endif;

            ?>
        </div>
    </div>

 end reviews
 */ ?>
</article>
<script>
    var main_image = "<?php echo get_bloginfo('url') .  $main_image ?>";
    var permalink = "<?php echo $product->getPermalink() ?>";
    var title = "<?php echo esc_attr($product->product_title) ?>";
    var shop_fb_id = "<?php echo $store->getFacebookId() ?>";
    var description = "<?php echo esc_attr(str_replace('<br>', ' ', nl2br($product->excerpt, false))) ?>";
    <?php wp_enqueue_script('product', TPP_STORE_PLUGIN_URL . '/site/assets/js/product-ck.js', 'jquery', 1.1, true) ?></script>
<?php get_footer();