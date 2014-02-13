<?php get_header();?>
<div class="main-container">
<?php 
global $options;
	foreach ($options as $value) {
		if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
				}
?>
<div class="title-area">
Posts under Tag: <?php single_tag_title();?>
</div><!--title-area-->

<?php if(have_posts()):?><?php while(have_posts()):the_post();?>
<div class="retweet-btn-small">
<script type="text/javascript">
tweetmeme_url = '<?php the_permalink();?>';
tweetmeme_source = '<?php echo $apa_Twitter;?>';
tweetmeme_style = 'compact';
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
</div><!--retweet-btn-->

<div class="post-title">
<a href="<?php the_permalink();?>" title="<?php the_title();?>">
<?php the_title();?>
</a>
</div><!--post-title-->
<div class="post-meta-data">
Added <?php the_time('M j, Y');?>, Under: <?php the_category(',')?>
</div><!--post-meta-data-->

<?php if ( has_post_thumbnail() ) { ?>
	<a href="<?php the_permalink();?>" title="<?php the_title();?>">
	<?php the_post_thumbnail(array(), array('class' => 'thumbs-in-archive')); ?>
	</a>
<?php } elseif (get_post_meta($post->ID, 'image', true) ) {?>
	<a href="<?php the_permalink();?>" title="<?php the_title();?>">
	<img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, 'image', $single = true); ?>&h=65&w=195&zc=1&q=100" alt="<?php the_title(); ?>" class="thumbs-in-archive"/> 
	</a>
<?php } ?>

<div class="post-excerpt">
<?php the_excerpt();?>
</div><!--post-excerpt-->
<div class="author-box-small">
By  <?php the_author_posts_link(); ?> with <?php comments_number('0 comments', '1 comment', '% comments');?>
</div><!--author-box-->




<?php endwhile;?>


<?php if(function_exists('wp_pagenavi')){ 
wp_pagenavi();}else{?>
<div class="next-prev-links">
<?php posts_nav_link(); ?>
</div>
<?php } ?>  



<?php endif;?>
</div><!--main-container-->
<?php get_sidebar();?>
<?php get_footer();?>