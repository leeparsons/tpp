<?php
/*
Template Name: Press Releases
*/
?>
<?php get_header(); ?>
<div class="main-container">
<?php if(have_posts()): while(have_posts()): the_post(); ?>
<div class="press-title-big">
<p style="font-size:48px;font-weight:normal;line-height:38px;text-transform:capitalize;color:#333333">Press Release</p>
</div>
<div class="press-title-big">
<h1 style="font-size:48px;font-weight:normal;line-height:38px;text-transform:capitalize;"><?php the_title();?></h1>
</div>
<div class="post-content">
<?php the_content();?>
</div><!--post-content-->
<?php
endwhile;
endif; ?>
</div>
<script type="text/javascript">/*<![CDATA[*/
Cufon.replace(".press-title-big");
/*]]>*/</script>
<?php
get_sidebar();?>
<?php get_footer();?>