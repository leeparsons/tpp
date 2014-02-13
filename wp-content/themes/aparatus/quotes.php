<?php
/*
Template Name: Quotes
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
<div class="post-content quotesbody">
<p>Here are a selection of quotes and feedback from both aspiring and professional photographers who are users of this website. Feel free to use any of the quotes below in features mentioning The Photography Parlour.</p>
<?php the_content();?>
</div><!--post-content-->
<script type="text/javascript">/*<![CDATA[*/Cufon.replace(".quote h4");Cufon.now();/*]]>*/</script>
<?php
endwhile;
endif; ?>
</div>
<?php
get_sidebar();?>
<?php get_footer();?>