<?php get_header(); ?>

    <article class="page-article cart-page">

    <header>
        <h1>Order Details</h1>
    </header>
    <p><pre>Thank you for placing an order, you can view your order details below.</pre></p>
    <?php foreach ($order_items as $item): ?>
    	<h2><?php echo $item->product_title ?></h2>
    	<?php if ($item->product_type == 1): ?>
    		<a href="<?php echo $item->getDownloadUrl(true, false, true) ?>">Click here to download <?php echo $item->product_Title ?></a>
    	<?php endif; ?>
	<?php endforeach; ?>
<?php get_footer();