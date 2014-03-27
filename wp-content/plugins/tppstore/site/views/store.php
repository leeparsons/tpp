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


</section>
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
