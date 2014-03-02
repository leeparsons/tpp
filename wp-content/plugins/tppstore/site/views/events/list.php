<?php

get_header();

?>
    <header>
        <h1>Workshops &amp; Events</h1>
    </header>
<?php if (count($events) > 0): ?>
<?php $image_size = isset($image_size)?$image_size:'thumb' ?>
<?php


require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;


?>

<?php echo $paginator->render(); ?>

</section>
</div>
<div class="wrap wrap-grey">

    <section class="wrap">
        <?php


        ?>
        <ul class="item-list" id="product_list">
            <?php $i = 1; ?>
            <?php foreach ($events as $product): ?>
                <li class="item-box<?php echo $i%4?'':' last' ?>">
                    <a href="<?php echo $product->getPermalink() ?>">
                        <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                        <span class="strong"><?php echo $product->getShortTitle() ?></span>
                        <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
                    </a>
                    <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
                </li>
                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="wrap">
    <?php else: ?>
    <p>No events or workshops listed</p>
        <?php endif; ?>
<?php

get_footer();
?>