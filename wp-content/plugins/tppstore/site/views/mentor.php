<?php
/**
 * User: leeparsons
 * Date: 15/02/2014
 * Time: 12:55
 */


require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;

?>

    <section class="innerwrap store">

        <?php if (wp_is_mobile() && !tpp_is_tablet()): ?>
            <header class="wrap">
                <h1 class="mob"><?php echo $mentor->mentor_name ?></h1>
            </header>
        <?php endif; ?>

        <article class="aside-25">
            <?php echo $mentor->getSrc('thumb', true); ?>

<!--            <div class="form-group">-->
<!--                <a href="/shop/mentor/ask/--><?php //echo $mentor->mentor_slug ?><!--" class="btn btn-primary form-control">Ask mentor a question</a>-->
<!--            </div>-->



        </article>

        <div class="aside-75">
            <div class="wrap">
                <?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
                    <header>
                        <h1><?php echo $mentor->mentor_name ?></h1>
                    </header>
                <?php endif; ?>
                <div class="description"><pre><?php echo nl2br($mentor->getBio()) ?></pre></div>
            </div>
        </div>


    </section>

<?php if (count($products) == 0): ?>
    <p class="wrap">This mentor is not currently running any sessions</p>
<?php else: ?>
    </div>
    <?php echo $paginator->render() ?>

    <div class="wrap wrap-white store-product-list">
        <section class="innerwrap">
            <header class="wrap store-header">
                <h2 class="innerwrap">Sessions Available</h2>
            </header>
            <?php

            include TPP_STORE_PLUGIN_DIR . 'site/views/products/store_list.php' ?>
        </section>
    </div>
<?php endif;
