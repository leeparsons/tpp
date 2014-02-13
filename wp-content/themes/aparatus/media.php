<?php
/*
Template Name: Media Center
*/
?>
<?php get_header(); ?>
<div class="main-container">
<?php if(have_posts()): while(have_posts()): the_post(); ?>


<div class="post-title-big">
<h1 style="font-size:48px;font-weight:normal;line-height:38px;text-transform:capitalize;">
<a href="<?php the_permalink();?>" title="<?php the_title();?>">
<?php the_title();?>
</a>
</h1>
</div><!--post-title-->
<div class="post-content">
<?php the_content();?>
</div><!--post-content-->
<script type="text/javascript">/*<![CDATA[*/
Cufon.replace("h4.change");Cufon.now();
Cufon.replace('.media-title-area h3');Cufon.now();
/*]]>*/</script>
<?php
endwhile;
endif; ?>
</div>
<?php
get_sidebar();?>
<?php get_footer();?>