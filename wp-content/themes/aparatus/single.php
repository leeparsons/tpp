<?php get_header();?>
<div class="main-container">
<?php

    if (!isset($_GET['s'])) {
     

contributelarge('single');

        
    } else {
    
    ?><a class="supportus" href="/info-for-advertisers/"></a>
<?php
    
    }
    
    ?>
<script type="text/javascript">/*<![CDATA[*/
Cufon.replace('.support,.supportyou');Cufon.now();
/*]]>*/</script>

<?php 
global $options;
	foreach ($options as $value) {
		if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
				}

$post = $wp_query->post;

if (in_category(114) || in_category(115)) {
   include(TEMPLATEPATH . '/uceeventssingle.php');
} else {

?>
<?php if(have_posts()):?><?php while(have_posts()):the_post();?>

<div class="retweet-btn">
<script type="text/javascript">
tweetmeme_source = '<?php echo $apa_Twitter;?>';
</script>
<script type="text/javascript" src="/wp-content/themes/aparatus/js/tweetme.js"></script>
</div><!--retweet-btn-->

<div class="post-title-big"> 
<h1 style="font-size:48px;font-weight:normal;line-height:38px;text-transform:capitalize;">
<a href="<?php the_permalink();?>" title="<?php the_title();?>">
<?php the_title();?>
</a>
</h1>
</div><!--post-title-->
<div class="post-meta-data">
Added <?php the_time('M j, Y');?>, Under: <?php the_category(',')?>
</div><!--post-meta-data-->
<div class="post-content">
<?php the_content();?>
<?php contributelarge(); ?>
</div><!--post-content-->
<?php wp_link_pages('before=<div id="page-links">Page&after=</div>'); ?>
<div class="author-box">
<?php echo get_avatar( get_the_author_id() , 40 ); /* ?>
<img style="width:40px;height:40px;" class="avatar avatar-40 photo" src="/wp-content/uploads/userphoto/admin.thumbnail.jpg?341669655" alt="">
*/ ?>
<div class="author-name">Posted by: <?php the_author_firstname(); ?> <?php the_author_lastname(); ?></div><!--author-name-->
<div class="author-description"><?php the_author_description(); ?></div><!--author-description-->
<div class="author-links">
Visit <?php the_author_firstname(); ?>'s <a href="<?php the_author_url(); ?>">Website</a>. View other posts by <?php the_author_posts_link(); ?>
</div><!--author-links-->
</div><!--author-box-->
<div class="social-bar">
<ul id="social-btns">
<li><a id="delicious" rel="nofollow" href="http://del.icio.us/post?url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" title="Bookmark this post on Delicious">Delicious</a></li>
<li><a id="digg" rel="nofollow" href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" title="Share this post on Digg">Digg</a></li>
<li><a id="stumbleupon" rel="nofollow" href="http://www.stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" title="Share this post on Stumbleupon">Stumbleupon</a></li>
<li><a id="technorati" rel="nofollow" href="http://technorati.com/faves?add=<?php the_permalink() ?>" title="Share this post on Technorati">Technorati</a></li>
<li><a id="twitter" rel="nofollow" href="http://twitter.com/home?status=<?php the_title(); ?>+<?php the_permalink() ?>" title="Share this post on Twitter">Twitter</a></li>
</ul>
</div><!--social-bar-->
<?php endwhile;?>
<?php endif;?>


<?php } /** end reroute on category 114**/ ?>



<?php comments_template('', true); ?>

</div><!--main-container-->
<?php get_sidebar();?>
<?php get_footer();?>
<script type="text/javascript">/*<![CDATA[*/jQuery('div.post-content a').each(function (j) {jQuery(this).click(function () {pageTracker._trackEvent('Post Link', jQuery(this).attr("href"));});if (jQuery(this).attr("_target") != "_blank") {jQuery(this).attr("target","_blank");}});/*]]>*/</script>