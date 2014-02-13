<!--grab theme option variables-->
<?php 
global $options;
	foreach ($options as $value) {
		if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
				}
?>
<!--slider javascript settings-->
<script type="text/javascript">
			function startGallery() {
				var myGallery = new gallery($('myGallery'), {
					timed: <?php if ($apa_auto_slide_show == '') {echo 'true';}else{echo 'false';} ?>,
					textShowCarousel: '<?php if ($apa_feature_cat_name == '') {echo 'latest posts';}else{echo $apa_feature_cat_name;} ?>'					
				});
			}
			window.addEvent('domready',startGallery);
</script>
<!--start gallery-->
<div class="content" <?php if (is_paged()){echo 'style="display:none;"';} ?>>
			<div id="myGallery">
			<!--the loop-->
			<?php query_posts(array('showposts' => 5, 'orderby'=> date, 'order' => DES, 'category_name' => $apa_feature_cat_name));
					$saved_ids = array();
					while (have_posts()) : the_post();
					$saved_ids[] = get_the_ID();
					$values = get_post_custom_values("image"); ?>			
			<!--custom images conditional statements-->
			
			<!--first, check if there's post thumbnail - wp 2.9-->
			<?php if ( has_post_thumbnail() ) { ?>
				<div class="imageElement">  
					<h3><?php the_title(); ?></h3>					
					<?php the_excerpt(); ?> 
					<a href="<?php the_permalink() ?>" title="Go to Article" class="open"></a> 	
					<?php the_post_thumbnail(array( 590, 278 ), array( 'class' => 'full' )); ?>
					<?php the_post_thumbnail(array( 100, 75 ), array( 'class' => 'thumbnail' )); ?>					
				</div>
				
			<!--define a gallery element if theres a custom image pictrue-->
			<?php }elseif(isset($values[0]))	{?>				
				<div class="imageElement">  
					<h3><?php the_title(); ?></h3>					
					<?php the_excerpt(); ?> 					
					<a href="<?php the_permalink() ?>" title="Go to Article" class="open"></a> 					
					<img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $values[0]; ?>&h=278&w=590&zc=1&q=100" class="full" alt="<?php the_title(); ?>"/> 
					<img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $values[0]; ?>&h=75&w=100&zc=1&q=100" class="thumbnail" alt="<?php the_title(); ?>"/> 
				</div>
			<?php }else{?>
			
			<!--if none - assign the temp image-->
				<div class="imageElement">  
					<h3><?php the_title(); ?></h3>					
					<?php the_excerpt(); ?> 					
					<a href="<?php the_permalink() ?>" title="Go to Article" class="open"></a> 					
					<img src="<?php bloginfo('template_directory'); ?>/images/temp-image-large.jpg" class="full" alt="<?php the_title(); ?>"/> 
					<img src="<?php bloginfo('template_directory'); ?>/images/temp-image-small.jpg" class="thumbnail" alt="<?php the_title(); ?>"/> 
				</div>
			<?php } ?>		
			
			<?php endwhile; wp_reset_query(); ?>					
			</div><!--myGallery-->
</div><!--content-->