<?php



require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;

$total_products = count($products);

wp_enqueue_style('store', '/assets/css/store.css');

get_header(); ?>
<div class="store">
<section class="innerwrap">

    <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
        <header class="wrap">
            <h1 class="mob"><?php echo $store->store_name ?></h1>
        </header>
    <?php endif; ?>

    <div class="left-wrap">
    <article class="aside-40">
        <header>
            <?php echo $store->getSrc(true, 'store_related'); ?>
            <?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
                <h1 class="align-left"><?php echo $store->store_name ?></h1>
            <?php endif; ?>
        </header>

        <?php if ($total_products > 0): ?>
        <a href="#store_products" class="strong btn btn-primary">Browse Store (<?php echo $total_products . ' item' . ($total_products == 1?'':'s'); ?>)</a>
        <?php endif; ?>
        <div class="wrap">
            <?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
<!--                <header>-->
<!--                    <h1>--><?php //echo $store->store_name ?><!--</h1>-->
<!--                </header>-->
            <?php endif; ?>
            <div class="description">
                <?php


                $description = $store->getDescription();


                if (strlen($description) > 400) {

                    $description_parts = explode(' ', $description);

                    $str = '';

                    $short_str = array();
                    $full_str = array();

                    foreach ($description_parts as $part) {

                        if (strlen($str . $part . ' ') < 300) {
                            $short_str[] = $part;
                            $str .= $part . ' ';
                        }

                        $full_str[] = $part;

                    }



                    echo '<pre id="short_description">' . implode(' ', $short_str) . '<br><br><a href="#" id="expand_description">Expand Description ...</a></pre>';

                    echo '<pre id="long_description" style="display:none">' . implode(' ', $full_str) . '<br><a href="#" id="close_description">Minimise Description...</a></pre>';

                } else {
                    echo '<pre>' . $description . '</pre>';
                }

                ?>
            </div>
            <div class="wrap-white">
                <div class="form-group">
                    <a href="/shop/ask/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Ask store owner a question</a>
                </div>

                <?php if (false !== $store->getPages()->getTerms()): ?>
                    <div class="form-group">
                        <a href="/shop/terms/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Store Terms &amp; Conditions</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>


            <!---->
            <!--            <div id="facebook_friends" class="align-left wrap"></div>-->

    </article>
    </div>
<?php /*
        <div class="aside-75">
            <div class="wrap">
                <?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
                    <header>
                        <h1><?php echo $store->store_name ?></h1>
                    </header>
                <?php endif; ?>
                <div class="description">
                    <?php


                        $description = $store->getDescription();


                        if (strlen($description) > 300) {

                            $description_parts = explode(' ', $description);

                            $str = '';

                            $short_str = array();
                            $full_str = array();

                            foreach ($description_parts as $part) {

                                if (strlen($str . $part . ' ') < 300) {
                                    $short_str[] = $part;
                                    $str .= $part . ' ';
                                }

                                $full_str[] = $part;

                            }



                            echo '<pre id="short_description">' . implode(' ', $short_str) . '<br><br><a href="#" id="expand_description">Expand Description ...</a></pre>';

                            echo '<pre id="long_description" style="display:none">' . implode(' ', $full_str) . '<br><a href="#" id="close_description">Minimise Description...</a></pre>';

                        } else {
                            echo '<pre>' . $description . '</pre>';
                        }

                        ?>
                </div>
                <div class="wrap-white">
                <div class="form-group">
                    <a href="/shop/ask/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Ask store owner a question</a>
                </div>

                <?php if (false !== $store->getPages()->getTerms()): ?>
                    <div class="form-group">
                        <a href="/shop/terms/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Store Terms &amp; Conditions</a>
                    </div>
                <?php endif; ?>

            </div>
            </div>
        </div>
*/ ?>

<script>
    if (document.getElementById('expand_description')) {
        document.getElementById('expand_description').onclick = function() {
            document.getElementById('short_description').style.display = 'none';
            document.getElementById('long_description').style.display = 'block';
            return false;
        }
        document.getElementById('close_description').onclick = function() {
            document.getElementById('short_description').style.display = 'block';
            document.getElementById('long_description').style.display = 'none';
            return false;
        }
    }
</script>
<?php if (count($products) == 0): ?>
    <p class="wrap">There are no products in this store. Please check back later</p>
<?php else: ?>

<!--    --><?php //echo $paginator->render() ?>
<!--    <div class="wrap wrap-white store-product-list">-->
<!--        <section class="innerwrap">-->
<!--            <header class="wrap store-header">-->
<!--                <h2 class="innerwrap">Items for sale</h2>-->
<!--            </header>-->
<!--        </section>-->
<!--    </div>-->
            <?php
           // include TPP_STORE_PLUGIN_DIR . 'site/views/products/store_list.php' ?>

    <h2 class="store-products-title" id="store_products">Items for sale:</h2>

    </section>


    <ul class="item-list fn">

        <?php foreach ($products as $product): ?>
            <li class="item-box<?php echo $i%4?'':' last' ?>">
                <a href="<?php echo $product->getPermalink() ?>">
                    <?php echo $product->getProductImage()->getSrc('store_thumb', true) ?>
                    <?php include TPP_STORE_PLUGIN_DIR . 'site/views/product/item_box/type_band.php'; ?>
                    <span class="strong"><?php echo $product->getShortTitle() ?></span>
                    <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
                </a>
                <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
            </li>
        <?php endforeach ?>
    </ul>

<?php endif; ?>

</div>


<?php get_footer();
