<?php

/*

Template Name: contact

*/

?>

<?php get_header();?>

<div class="main-container">

<?php



if(have_posts()):

 while(have_posts()):the_post();

the_content();

endwhile;

endif; ?>
</div>

<?php

get_sidebar();?>

<?php get_footer();?>