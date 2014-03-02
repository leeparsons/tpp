<div class="product-aside align-left">
    <a class="store-tag" href="<?php echo $product->getMentor()->getPermalink() ?>"><strong>About this Mentor</strong></a>

    <?php echo $product->getMentor()->getSrc('store_thumb', true); ?>

    <span class="indent"><?php echo $product->getMentor()->getLocation() ?></span>

    <a class="indent" href="<?php echo $product->getMentor()->getPermalink() ?>">Read more about the mentor</a>



    <div id="facebook_friends" class="align-left wrap"></div>

</div>