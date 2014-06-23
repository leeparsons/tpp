<?php
/*
 *  Template Name: Advert Confirm Change
 */

	get_header();

	?><section class="col1"><?php

		
	while(have_posts()) {
	
		the_post();
			
		
		?><article><div class="entry-content"><header><h1 class="section-title"><span><?php
				
		
		the_title();
				
		?></span><span class="stripe"></span></h1></header><?php
					
				
		
		if (isset($_GET['et']) && isset($_GET['aid'])) {
			
			
			$query = $wpdb->get_results(
										$wpdb->prepare(
													   "SELECT * FROM advert_edits WHERE edit_token = %s AND aid = %s",
													   $_GET['et'],
													   $_GET['aid']
													   )
										);
			$original = $wpdb->get_results(
										   $wpdb->prepare(
														  "SELECT * FROM adverts_submissions WHERE edit_token = %s AND aid = %s",
														  $_GET['et'],
														  $_GET['aid']
														  )
										   );
			
			//see if the advert exists?
			
				if (!empty($query) && !empty($original)) {
				
					if ($query[0]->image != $original[0]->image) {
						//try and unlink the old image!
						@unlink($_SERVER['DOCUMENT_ROOT'] . $original[0]->image);
					}
					
					//transfer
					$wpdb->update(
								  'adverts_submissions',
								  array(
										'email'			=>	$query[0]->email,
										'name'			=>	$query[0]->name,
										'business'		=>	$query[0]->business,
										'image'			=>	$query[0]->image,
										'url'			=>	$query[0]->url,
										'edit_token'	=>	'',
										),
								  array(
										'aid'			=>	$query[0]->aid
										),
								  array(
										'%s',
										'%s',
										'%s',
										'%s',
										'%s',
										'%s'
										),
								  array(
										'%s'
										)
								  );
					
					$wpdb->query(
								 $wpdb->prepare(
												"DELETE FROM advert_edits WHERE edit_token = %s AND aid = %s",
												$_GET['et'],
												$_GET['aid']
												)
								 );

					the_content();
					
				} else {
					echo '<p>Your advert information has already been updated</p>';			
				}
				
			} else {
				echo '<p>Your advert information has already been updated</p>';	
			}
		
	}

	?></div></article></section><?php

	get_sidebar();
	
	get_footer();