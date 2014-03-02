<?php



require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;

wp_enqueue_style('store', '/assets/css/store.css');

get_header(); ?>

    <section class="innerwrap store">

    <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
        <header class="wrap">
            <h1 class="mob"><?php echo $store->store_name ?></h1>
        </header>
    <?php endif; ?>

    <article class="aside-25">
            <?php echo $store->getSrc(true, 'thumb'); ?>

            <div class="form-group">
                <a href="/shop/ask/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Ask store owner a question</a>
            </div>

        <?php if (false !== $store->getPages()->getTerms()): ?>
            <div class="form-group">
                <a href="/shop/terms/<?php echo $store->store_slug ?>" class="btn btn-primary form-control">Store Terms &amp; Conditions</a>
            </div>
        <?php endif; ?>

            <!---->
            <!--            <div id="facebook_friends" class="align-left wrap"></div>-->

        </article>

        <div class="aside-75">
            <div class="wrap">
                <?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
                    <header>
                        <h1><?php echo $store->store_name ?></h1>
                    </header>
                <?php endif; ?>
                <div class="description">
                    <pre><?php echo $store->getDescription() ?></pre>
                </div>
            </div>
        </div>


</section>
<?php if (count($products) == 0): ?>
    <p class="wrap">There are no products in this store. Please check back later</p>
<?php else: ?>
</div>
    <?php echo $paginator->render() ?>
    <div class="wrap wrap-white store-product-list">
        <section class="innerwrap">
            <header class="wrap store-header">
                <h2 class="innerwrap">Items for sale</h2>
            </header>
        </section>
    </div>
            <?php

            include TPP_STORE_PLUGIN_DIR . 'site/views/products/store_list.php' ?>

<?php endif; ?>


<?php get_footer();
