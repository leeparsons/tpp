<?php
/*
 *  Template Name: Advertise Cancelled Paypal Payment Process
 */

	if (!session_id()) {
        session_start();
    }
	
	
	

	if (isset($_SESSION['advert_payment'])) {
	
		//update the database to say it's been cancelled!
		
		$wpdb->update( 
					  'adverts_submissions', 
					  array( 
							'status'	=> 'cancelled'
							), 
					  array( 'aid' => $_SESSION['advert_payment']['aid'] ), 
					  array( 
							'%s'
							), 
					  array( '%s' ) 
					  );
		
		//remove the image!
		
		if ($_SESSION['advert_payment']['image'] != '' && file_exists($_SERVER['DOCUMENT_ROOT'] . $_SESSION['advert_payment']['image'])) {
			@unlink($_SERVER['DOCUMENT_ROOT'] . $_SESSION['advert_payment']['image']);
			$path = pathinfo($_SERVER['DOCUMENT_ROOT'] . $_SESSION['advert_payment']['image']);
			
			$sc = scandir($path['dirname']);
			
			if (count($sc) == 2) {
				@rmdir($path['dirname']);
			}
			
		}
		
		
				unset($path);
		unset($_SESSION['advert_payment']);
		unset($sc);
		
	}



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