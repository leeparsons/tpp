<div class="product-aside align-left">

    <span class="store-tag">Location</span>
    <pre>

<?php echo $product->address ?></pre>
    <?php /*if ($product->lat && $product->lng): ?>
        <div id="map_canvas"></div>
        <!--    <script type="text/javascript"-->
        <!--            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6kw_hop7sLhFORdevW6i9tE73sTx_1M0&sensor=false">-->
        <!--    </script>-->
        <script src="https://maps.googleapis.com/maps/api/js?sensor=false"
                type="text/javascript"></script>
        <script>
            window.onload = function() {
                var mapOptions = {
                    center: new google.maps.LatLng(<?php echo $values['lat'] ?>, <?php echo $values['lng'] ?>),
                    zoom: 12
                };
                var map = new google.maps.Map(document.getElementById("map-canvas"),
                    mapOptions);
            }
        </script>

    <?php endif; */ ?>

    <?php echo $product->getMap() ?>
</div>

<div class="product-aside align-left">
    <a class="store-tag" href="<?php echo $store->getPermalink() ?>"><strong>About this store</strong></a>

    <?php echo $store->getSrc(true, 'store_thumb'); ?>
    <a class="indent" href="/shop/profile/<?php echo $store->user_id ?>"><?php echo $store->getStoreByUserID()->getOwner() ?></a>

    <span class="indent"><?php echo $store->getLocation() ?></span>

    <a class="indent" href="<?php echo $store->getPermalink() ?>">Read more about the seller</a>

    <div id="facebook_friends" class="align-left wrap"></div>

</div>