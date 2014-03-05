<?php
/**
 * User: leeparsons
 * Date: 29/11/2013
 * Time: 23:12
 */

$cacher->setCacheName('image_array');

$can_cache = (isset($preview) && $preview == 1);

$can_cache = !$can_cache;

if ($can_cache && false === ($_images = $cacher->readCache(-1))) {

    $_images = $product->getImagesBySize('main');

    $slide_images = array();
    $thumb_images = array();

    $og_images = array();

    if (count($_images) > 0):

        $i = 0;




        foreach ($_images as $image):
            $og_images[$i] = get_site_url() . $image->getSrc('full');

            if ($i == 0) {
                $main_image = $og_images[$i];
            }
            if (wp_is_mobile() && $i > 0) {
                continue;
            }
            $slide_images[] = '<img ' .  ($i>0?'class="vhidden"':'class="active"') . ' src="' . $og_images[$i] . '" alt="' . $image->alt . '">';
            $thumb_images[] = '<img ' . ($i>0?'':'class="active"') . ' src="' . $image->getSrc('slideshow_thumb') . '" alt="slide thumbnail ' . $image->alt . '">';
            $i++;
        endforeach;

        unset($image);
        unset($i);

    endif;

    $_images = array(
        'og_images'     =>  $og_images,
        'slide_images'  =>  $slide_images,
        'thumb_images'  =>  $thumb_images
    );

    $cacher->saveCache($_images);
} else {
    $og_images = $_images['og_images'];
    $slide_images = $_images['slide_images'];
    $thumb_images = $_images['thumb_images'];
}

unset($_images);

TppStoreHelperHtml::getInstance()->addOgImages($og_images);

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

<?php if (count($slide_images) > 0): ?>
    <div class="half-left">

        <div class="product-images" id="product_images">

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
<?php endif;?>

    <div class="half-right">

        <?php

        switch ($product->product_type) {
            case '4':
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/mentor/cart.php';
                break;

            case '5':
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/event/cart.php';
                break;
            default:
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/default/cart.php';
                break;
        }
        ?>


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
    <?php

    include TPP_STORE_PLUGIN_DIR . 'site/views/product/default/share.php';

    ?>

    <div class="description">
        <h2>Details</h2>
        <div class="wrap"><pre><?php echo $product->product_description ?></pre></div>
    </div>
    <?php

    include TPP_STORE_PLUGIN_DIR . 'site/views/product/default/share.php';

    ?>
</div>
<?php

$cacher->setCacheName('card');

if ($can_cache && false === ($card_html = $cacher->readCache(-1))) {

    ob_start();

?>
    <div class="half-right" id="store_profile">

        <?php

        switch ($product->product_type) {
            case '4':
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/mentor/mentor_card.php';
                break;

            case '5':
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/event/event_card.php';
                break;

            default:
                include TPP_STORE_PLUGIN_DIR . 'site/views/product/default/store_card.php';
                break;
        }

        $card_html = ob_get_contents();

    ob_end_clean();

    $cacher->saveCache($card_html);

}

        echo $card_html;
        unset($card_html);


    ?>



    <div class="product-aside align-left">
        <a href="/shop/ask/<?php echo $store->store_slug ?>/" class="btn btn-primary form-control">Ask store owner a question</a>
    </div>

    <?php

    switch ($product->product_type) {
        case '4':
            include TPP_STORE_PLUGIN_DIR . 'site/views/product/mentor/mentor_sessions.php';
            break;

        default:
            include TPP_STORE_PLUGIN_DIR . 'site/views/product/default/store_products.php';
            break;
    }




   ?>

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
    <?php wp_enqueue_script('product', TPP_STORE_PLUGIN_URL . '/site/assets/js/product-ck.js', array('jquery'), 2.5, true) ?></script>
<?php get_footer();


