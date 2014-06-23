<?php
/*
 *  Template Name: Advert Information
 */
    
    session_start();

	if (isset($_GET['chngd'])) {
		$changed = true;
	} else {
		$changed = false;
	}
    

    
	$query = array();
	
	if (isset($_REQUEST['aid'])) {
		
		$query = $wpdb->get_results(
									$wpdb->prepare( 
												   "SELECT
												   
												   aid,
												   name,
												   business,
												   email,
												   price,
												   url,
												   description,
												   duration,
												   UNIX_TIMESTAMP(startdatetime) AS startdate,
												   UNIX_TIMESTAMP(enddatetime) AS enddate,
												   image,
												   token,
												   status,
												   payerid,paypal_email,
												   correlationid,
												   fee,
												   edit_token,
												   option_id
												
												   FROM adverts_submissions
												   WHERE aid = %s
												   ", 						   
												   $_REQUEST['aid']
												   )
									);
	}
    
    
    if (isset($_GET['ed']) || isset($_POST['et'])) {
        
        $option_query = $wpdb->get_results(
                                           $wpdb->prepare(
                                                          "SELECT * FROM advert_options WHERE id = %d", 
                                                          array($query[0]->option_id)
                                                          )
                                           );
    }
    
	
	if (!empty($query)) {
		
		$errorMessage = array();
		
		if (isset($_POST['et']) && isset($_POST['aid'])) {
			//saving information!
			
			
			if (!isset($_POST['n']) || str_replace(' ', '', $_POST['n']) == '') {
				$errorMessage['n'] = 'Please enter your name';
			} else {
				$newName = stripslashes($_POST['n']);
			}
			
			if (!isset($_POST['b']) || str_replace(' ', '', $_POST['b']) == '') {
				$errorMessage['b'] = 'Please enter your business name';
			} else {
				$newBusiness = stripslashes($_POST['b']);
			}
			
			if (!isset($_POST['e']) || filter_var($_POST['e'], FILTER_VALIDATE_EMAIL) === false) {
				$errorMessage['e'] = 'Please enter your email';
			} else {
				$newEmail = stripslashes($_POST['e']);
			}
			
			if (!isset($_POST['url']) || filter_var($_POST['url'], FILTER_VALIDATE_URL) === false) {
				$errorMessage['url'] = 'Please enter the url link';
			} else {
				$newUrl = stripslashes($_POST['url']);
			}		
			
			$imageUrl = '';
			
			if (isset($_FILES['image']) && !empty($_FILES['image'])) {
				
				
				
				if ($_FILES['image']['error'] > 0) {
					if ($_FILES['image']['error'] == 4) {
						if ($_FILES['image']['tmp_name'] != '') {
							$errorMessage['image'] = 'Please select an image';	
						} else {
							$imageUrl = $_POST['oimage'];
						}
					} else {
						$errorMessage['image'] = 'Your image has an error in it. Please select another one.';
					}
				} else {
					
					
					switch ($_FILES['image']['type']) {
						case 'image/jpg':
						case 'image/jpeg':
						case 'image/pjpeg':
						case 'image/pjpg':
						case 'image/gif':
						case 'image/png':
							//move the image across!
							
							if ($_FILES['image']['size']/1024 > 200) {
								$errorMessage['image'] = 'Please choose an image smaller than 200KB';
							} else {
								
								//get the image dimensions!
								
								list($w, $h) = getimagesize($_FILES['image']['tmp_name']);
								
								if ($w != $option_query[0]->max_width || $h != $option_query[0]->max_height) {
									$errorMessage['image'] = 'The image needs to be exactly ' . $option_query[0]->max_width . 'x' . $option_query[0]->max_height . ' pixels';
								}
								
								
								if (empty($errorMessage)) {
									
									$imageBase = md5($newName);
									
									if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/adverts_temp_store/' . $imageBase . '/')) {
										mkdir($_SERVER['DOCUMENT_ROOT'] . '/adverts_temp_store/' . $imageBase);
										chmod($_SERVER['DOCUMENT_ROOT'] . '/adverts_temp_store/' . $imageBase, 0777);
									}
									$imageUrl = '/adverts_temp_store/' . $imageBase . '/' . time() . '_' . $_FILES['image']['name'];

									if (!@move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $imageUrl)) {
										$errorMessage['image'] = 'There was a problem with your image.';
									}
								}
							}
							break;
							
							
						default:
							
							$errorMessage['image'] = 'Please choose a jpeg, png or gif image.';
							break;
							
					}
				}
				
			} else {
				$imageUrl = $_POST['oimage'];	
			}
			
			if (empty($errorMessage)) {
				
				$wpdb->query(
							 $wpdb->prepare(
											"DELETE FROM advert_edits WHERE aid = %s",
											$_POST['aid']
											)
							 );
				
				$wpdb->insert(
							  'advert_edits',
							  array(
									'name'			=>	$newName,
									'business'		=>	$newBusiness,
									'email'			=>	$newEmail,
									'url'			=>	$newUrl,
									'image'			=>	$imageUrl,
									'edit_token'	=>	$_POST['et'],
									'aid'			=>	$_POST['aid']
									),
							  array(
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s'
									)
							  );
				
				$wpdb->query(
							  $wpdb->prepare(
											 "UPDATE adverts_submissions SET edit_token = %s WHERE aid = %s",
											 $_POST['et'],
											 $_POST['aid']
											 )
							  );
				
				//now mail the client!
				
							
				$message = '<html><head></head><body>';
				
				$message .= '<p>' . $query[0]->name . ',<br/><br/>Loveluxe blog has received a request to change your advert information for advert: <a href="http://www.loveluxeblog.com/advertise/advert-information/?aid=' . $query[0]->aid . '">' . $query[0]->aid . '</a><br/></p>';
				
				$message .= '<p>The changes requested are:<br/></p>';

				$message .= '<table><tbody>';
				
				if ($newName != $query[0]->name) {
				
					$message .= '<tr><td>Name:</td><td>' . $newName . '</td></tr>';
				}
				
				if ($newBusiness != $query[0]->business) {
					
					$message .= '<tr><td>Business:</td><td>' . $newBusiness . '</td></tr>';
				}
				
				if ($newEmail != $query[0]->email) {
					
					$message .= '<tr><td>Email:</td><td>' . $newEmail . '</td></tr>';
				}
				
				if ($imageUrl != $query[0]->image) {
					
					$message .= '<tr><td>Image:</td><td><img src="http://www.loveluxeblog.com' . $imageUrl . '"/></td></tr>';
				}
				
				$message .= '</tbody></table>';
				
				$message .= '<p>If this is correct, before we can make these changes you will need to authorise the change by clicking on this link: <a href="http://www.loveluxeblog.com/advertise/confirm-change/?aid=' . $query[0]->aid . '&amp;et=' . $_POST['et'] . '">http://www.loveluxeblog.com/advertise/confirm-change/?aid=' . $query[0]->aid . '&amp;et=' . $_POST['et'] . '</a><br/></p>';
				
				
				$message .= '</body></html>';
				

				
				$subject = 'Confirm your advert change';
				
				
					$headers = "From: advertise@loveluxeblog.com\r\n";
					$headers .= "Reply-To: advertise@loveluxeblog.com\r\n";
					$headers .= "Return-Path: advertise@loveluxeblog.com\r\n";
					$headers .= "Organization: Loveluxe Blog\r\n";
					$headers .= "MIME-Version: 1.0\n";			
					$headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
					
					$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
					mail($query[0]->email, $subject, $message, $headers, "-fadvertise@loveluxeblog.com");

				header('location: http://www.loveluxeblog.com/advertise/advert-information/?aid=' . $query[0]->aid . '&chngd=1');
				die();
			}
			
		}
		
		
	}
	
	get_header();
	
	?><section class="col1"><?php
		
	while(have_posts()) {
	
		the_post();
		
	?><article><div class="entry-content"><header><h1 class="section-title"><span><?php
		
		the_title();
		
	?></span><span class="stripe"></span></h1></header><?php

		
		if (!empty($errorMessage)) {
			echo '<p class="error">There were a few errors with your update &ndash; please check below for more information.</p>';
		} elseif ($changed === true) {
			echo '<p class="success">Thank you for your changes. We have sent you an email with a link which you need to click to confirm the changes before we can apply them to your advert.</p>';
		}
		
		the_content();
		

		
		
		
	?></div><h2 class="section-title"><span>Your Advert Submission Details</span><span class="stripe"></span></h2><div class="entry-content aform-complete"><?php

		

		
		if (!empty($query)) {

			if (isset($_GET['ed'])) {
			
			?><form class="aform" method="post" enctype="multipart/form-data" action=""><?php
			
				$editToken = uniqid();

				$wpdb->insert(
							  'adverts_submissions',
							  array('edit_token'	=>	$editToken),
							  array('%s'),
							  array('aid'	=>	$_GET['aid']),
							  array('%s')
							  );

				
				echo '<input type="hidden" value="' .  $_GET['ed'] . '" name="ed"/>';
				echo '<input type="hidden" value="' . $_GET['aid'] . '" name="aid"/>';
				echo '<input type="hidden" value="' . $editToken . '" name="et"/>';
			}

	
	if (isset($_GET['ed'])) {


		
		echo '<p><input type="submit" class="fr" value="Save"/><a href="/advertise/advert-information/?aid=' . $_GET['aid'] . '" class="fr pseudo-submit" style="margin-right:10px;">Cancel</a></p>';
	
	} else {
		
		?><p class="aform"><a class="fr pseudo-submit" href="/advertise/advert-information/?aid=<?php echo $_GET['aid']; ?>&amp;ed=1">Click to Edit</a></p><?php

	}
	
			?><p><span>Name:</span> <?php 
				
				
				
				if (isset($_GET['ed'])) { 
					
					if (isset($errorMessage['n'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['n'] . '</span><span class="clear"></span>';}

				
				?><input type="text" name="n" value="<?php 

if (isset($_POST['n'])) {
	echo htmlspecialchars(stripslashes($_POST['n']));
} else {
	echo htmlspecialchars(stripslashes($query[0]->name));
}

?>"/><?php 
				
				
				} else {
				
					echo stripslashes($query[0]->name);
					
				}
					?></p>

		<p><span>Business name:</span><?php
			
			if (isset($_GET['ed'])) { 

				if (isset($errorMessage['b'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['b'] . '</span><span class="clear"></span>';}

				
			?><input type="text" name="b" value="<?php 

if (isset($_POST['b'])) {
	echo htmlspecialchars(stripslashes($_POST['b']));
} else {
	echo htmlspecialchars(stripslashes($query[0]->business));
}

?>"/><?php
				
				
			} else {
			
				echo stripslashes($query[0]->business);
					
			}
				
				?></p>

		<p><span>Email:</span><?php
			
			
			if (isset($_GET['ed'])) { 

				if (isset($errorMessage['e'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['e'] . '</span><span class="clear"></span>';}

				
			?><input type="text" name="e" value="<?php 
if (isset($_POST['e'])) {
	echo htmlspecialchars(stripslashes($_POST['e']));
} else {
	echo htmlspecialchars(stripslashes($query[0]->email));
}
?>"/><?php


			} else {

				echo stripslashes($query[0]->email);
				
			}
				
			?></p>

		<p><span>Start Date:</span><?php echo ($query[0]->startdate == '') ? 'Not set. You will need to confirm this by email.' : '9am on: ' . date('l, j, M, Y', $query[0]->startdate); ?></p>

		<p><span>Expiry Date:</span><?php echo ($query[0]->enddate == '') ? 'Not set. You will need to confirm this by email.' : '9am on: ' . date('l, j, M, Y', $query[0]->enddate); ?></p> 

		<p><span>Url to link to:</span><?php
			
			
			if (isset($_GET['ed'])) { 

				if (isset($errorMessage['url'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['url'] . '</span><span class="clear"></span>';}

				
			
			?><input type="text" name="url" value="<?php

if (isset($_POST['url'])) {
	echo htmlspecialchars(stripslashes($_POST['url']));
} else {

	echo htmlspecialchars(stripslashes($query[0]->url)); 

}
?>"/><?php
				
				
			} else {
			
				?><a href="<?php echo stripslashes($query[0]->url); ?>"><?php echo stripslashes($query[0]->url); ?></a><?php
				
			}
				
			?></p>

		<p><span class="fl">Image:</span><?php 
			
			echo ($query[0]->image == '') ? 'Not uploaded. You will need to confirm this before your advert goes live!' : '<img src="' . $query[0]->image . '"/>';
			
			
			?></p><?php
			
			
				if (isset($_GET['ed'])) {
			

					
					echo '<span class="clear"></span>';
					
                    
					echo '<p><span style="width:100%">Upload new image (' . $option_query[0]->max_width . 'px wide by ' . $option_query[0]->max_height . 'px high):</span></p>';

					if (isset($errorMessage['image'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['image'] . '</span><span class="clear"></span>';}

					echo '<p><input type="file" name="image"/><input type="hidden" name="oimage" value="' . $query[0]->image . '"</p>';
				}
			
			?>

	<p><strong>Details:</strong></p>

	<p><span>Option <?php echo $query[0]->option_id; ?>:</span><?php echo $query[0]->duration; ?> &pound;<?php echo $query[0]->price; ?></p>


		<p><span>Payment Status:</span><?php
			
			
			echo $query[0]->status == 'complete' ? 'PAID' : 'pending payment';
			
			if ($query[0]->status == 'complete') {
				
				echo '<br/>You can use the following link to renew your advert after: <span style="float:none">' . date('l, d, F, Y', strtotime(date('d-M-Y', $query[0]->enddate) . '-30days')) . '</span> by using the link:<br/><a href="/advertise/advert-renewal/?aid=' . $_GET['aid'] . '">http://www.loveluxeblog.com/advertise/advert_renewal?aid=' . $_GET['aid'] . '</a>';
			}
			
			?></p><?php


			if (isset($_GET['ed'])) {

				echo '<p><input type="submit" class="fr" value="Save"/><a href="/advertise/advert-information/?aid=' . $_GET['aid'] . '" class="fr pseudo-submit" style="margin-right:10px;">Cancel</a></p></form>';	
			}
		} else {
				
			echo '<p>No advert was found - please check the advert reference you were given.</p>';
		}
			
			?>
		</div>

	</article><?php
	}
					
?></section><?php
	
	unset($_SESSION['advert_payment']);
	
	get_sidebar();
	
	get_footer();
