<?php
/*
 *  Template Name: Advertise Renewal Cancelled Paypal Payment Process
 */

	if (!session_id()) {
        session_start();
    }
	
	
	

unset($_SESSION['advert_payment']);


	get_header();

	?><section class="col1"><?php
		
		while(have_posts()) {
			
			the_post();
			
		?><div class="entry-content"><article><header><h1 class="section-title"><span><?php
				
			the_title();
				
		?></span><span class="stripe"></span></h1></header><?php
		

				the_content();
					
		?></article></div><?php
	}
	
	?></section><?php
	get_sidebar();

	get_footer();