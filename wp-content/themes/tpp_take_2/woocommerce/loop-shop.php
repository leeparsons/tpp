<?php


global $woocommerce_loop;

$woocommerce_loop['loop'] = 0;
$woocommerce_loop['show_products'] = true;

if (!isset($woocommerce_loop['columns']) || !$woocommerce_loop['columns']) $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);

?>

<?php do_action('woocommerce_before_shop_loop'); ?>

<ul class="product-list-widget">

	<?php 
	
	do_action('woocommerce_before_shop_loop_products');
	
	if ($woocommerce_loop['show_products'] && have_posts()) : while (have_posts()) : the_post(); 
	
		global $product;
		
		if (!$product->is_visible()) continue; 
		
		$woocommerce_loop['loop']++;
		
		?>
		<li class="product <?php if ($woocommerce_loop['loop']%$woocommerce_loop['columns']==0) echo ' last'; if (($woocommerce_loop['loop']-1)%$woocommerce_loop['columns']==0) echo ' first'; ?>">
			
			<?php do_action('woocommerce_before_shop_loop_item'); ?>

				<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
				
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				
				<?php do_action('woocommerce_after_shop_loop_item_title'); ?>

			<?php do_action('woocommerce_after_shop_loop_item'); ?>
			
		</li><?php 
		
	endwhile; endif;
	
	if ($woocommerce_loop['loop']==0) echo '<li class="info">'.__('No products found which match your selection.', 'woocommerce').'</li>';

	?>

</ul>


<?php do_action('woocommerce_after_shop_loop'); ?>
