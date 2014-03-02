<div class="product-aside align-left">
    <a class="store-tag" href="<?php echo $store->getPermalink() ?>"><strong>About this store</strong></a>

    <?php echo $store->getSrc(true, 'store_thumb'); ?>
    <a class="indent" href="/shop/profile/<?php echo $store->user_id ?>"><?php echo $store->getStoreByUserID()->getOwner() ?></a>



    <span class="indent"><?php echo $store->getLocation() ?></span>

    <a class="indent" href="<?php echo $store->getPermalink() ?>">Read more about the seller</a>



    <div id="facebook_friends" class="align-left wrap"></div>

</div>