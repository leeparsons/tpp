<?php

    $errorMessage = array();
	
	
    $eMessage = array(
                      'n'   =>  'Please enter your name',
                      'b'   =>  'Please enter your business',
                      'e'   =>  'Please enter your email',
                      'url' =>  'Please enter a valid url',
                      'd'   =>  'Please select a start date'
                      );
    
	if (isset($_POST['t'])) {
        
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        
        
        
		if (isset($prices[$_POST['t']])) {
			$amount = $prices[$_POST['t']]->price;	
		} else {
			$errorMessage['t'] = 'Please select an amount';	
		}
		
		if (!isset($_POST['n']) || str_replace(' ', '', $_POST['n']) == '') {
            $errorMessage['n'] = 'Please enter your name';
		} 
		
		if (!isset($_POST['b']) || str_replace(' ', '', $_POST['b']) == '') {
			$errorMessage['b'] = 'Please enter your business name';
		}
		
		if (!isset($_POST['e']) || filter_var($_POST['e'], FILTER_VALIDATE_EMAIL) === false) {
			$errorMessage['e'] = 'Please enter your email';
		}
		
		
        
        
		if (!isset($_POST['d'])) {
			$errorMessage['d'] = 'Please enter a start date in the future';
		} else {
			$strDate = str_replace(',', '', $_POST['d']);
            
			$dateTime = strtotime($strDate);
			if ($strDate == '' || ($dateTime == 0 || $dateTime <= time())) {
				$errorMessage['d'] = 'Please enter a start date in the future';
			} elseif ($dateTime > strtotime('+30day')) {
				$errorMessage['d'] = 'Please enter a start date no more than 30 days in the future (needs to be <br/>before ' . date('d, M, Y', strtotime('+31day')) . ')';
			}
		}
		
        if (isset($_POST['url']) && substr($_POST['url'], 0, 7) != 'http://' && substr($_POST['url'], 0, 8) != 'https://') {
            $_POST['url'] = 'http://' . $_POST['url'];
        }
        
		if (!isset($_POST['url']) || filter_var($_POST['url'], FILTER_VALIDATE_URL) === false) {
			$errorMessage['url'] = 'Please enter a valid url';
		}
		
		//if (empty($errorMessage)) {
        $imageUrl = '';
        if (isset($_POST['image-choice'])) {
            if ($_POST['image-choice'] == 1) {
                
                
                if (isset($_FILES['image']) && !empty($_FILES['image'])) {
                    
                    
                    
                    if ($_FILES['image']['error'] > 0) {
                        if ($_FILES['image']['error'] == 4) {
                            $errorMessage['image'] = 'Please select an image';	
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
                                    
                                    if ($w != 200 || $h != 90) {
                                        $errorMessage['image'] = 'The image needs to be exactly 200x90 pixels';
                                    }
                                    
                                    
                                    if (empty($errorMessage)) {
                                        $imageBase = md5($_POST['n']);
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
                    $errorMessage['image'] = 'Please choose an image';
                }
            }
			//}
			
			if (empty($errorMessage)) {
                if (!isset($_SESSION['advert_log_id'])) {
                    $wpdb->insert(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Starting advert submission',
                                        'data'              =>  serialize($_POST),
                                        'client_name'       =>  $_POST['n'],
                                        'stage'             =>  'processing-data',
                                        'ip'                =>  $ip,
                                        'client_id'         =>  0,
                                        'client_business'   =>  $_POST['b'],
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                        ),
                                  array(
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s'
                                        )
                                  );
                    $_SESSION['advert_log_id'] = $wpdb->insert_id;
                    
                } else {
                    $wpdb->update(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Starting advert submission',
                                        'data'              =>  serialize($_POST),
                                        'stage'             =>  'processing-data',
                                        'ip'                =>  $ip,
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                        ),
                                  array(
                                        'id'    =>  $_SESSION['advert_log_id']
                                        ),
                                  array(
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
                    
                }
                
                //email Rosie!
				
				$_POST['n'] = stripslashes($_POST['n']);
				
				$_POST['e'] = stripslashes($_POST['e']);
				
				$_POST['url'] = stripslashes($_POST['url']);
                
				$_POST['b'] = stripslashes($_POST['b']);
				
				$_POST['d'] = stripslashes($_POST['d']);
				
				
                
				
				$message = '<p>You have received the START of an order for an advert! FROM: ' . $_POST['n'] . ' of ' . $_POST['b'] . '</p>';
				
				$message .= '<p>' . $_POST['n'] . ' would like to have an advert for: <br/>' . $prices[$_POST['t']]->duration . ' at a cost of £' . $prices[$_POST['t']]->price . '</p>';
				
				$message .= '<p>' . $_POST['n'] . ' has placed the advert with: ' . $_POST['b'] . '</p>';
				
				if ($imageUrl != '') {
					$message .= '<p>' . $_POST['n'] . ' has uploaded the image they would like to use for the advert: <a href="http://www.loveluxeblog.com/' . $imageUrl . '">' . $imageUrl . '</a></p>';
				} else {
					$message .= '<p>' . $_POST['n'] . ' would like to send you an image at a later point.</p>';				
				}
				
				if ($_POST['url'] != '') {
					$message .= '<p>' . $_POST['n'] . ' would like the advert to point to: <a href="' . $_POST['url'] . '">' . $_POST['url'] . '</a></p>';
				} else {
					$message .= '<p>' . $_POST['n'] . ' did not enter a url for the advert to point to</p>';
				}
				
				if (isset($_POST['d']) && $_POST['d'] != '') {
					$message .= '<p>' . $_POST['n'] . ' would like the advert to start on: ' . $_POST['d'] . '</p>';
					$expiry = date('l, d, F, Y h:i:s', strtotime(str_replace(',', '', $_POST['d'] . ' 09:00:00') . '+' . $prices[$_POST['t']]->quantity_duration));
					$message .= '<p>The advert expires on: ' . 	$expiry . '</p>';
				} else {
					$expiry = '';
					$message .= '<p>' . $_POST['n'] . ' did not enter a start date for the advert. This will need to be agreed before hand.</p>';
				}
				
				$_SESSION['advert_payment']['n'] = $_POST['n'];
				$_SESSION['advert_payment']['e'] = $_POST['e'];
                
                
				$_SESSION['advert_payment']['startdate'] = $_POST['d'];
				$_SESSION['advert_payment']['image'] = $imageUrl;
				$_SESSION['advert_payment']['item'] = $prices[$_POST['t']];
				$_SESSION['advert_payment']['enddate'] = $expiry;
				$_SESSION['advert_payment']['aid'] = uniqid();
				$_SESSION['advert_payment']['b'] = $_POST['b'];
				$_SESSION['advert_payment']['url'] = $_POST['url'];
                
				
				$message .= '<p>The advert information has a reference: <a href="http://www.loveluxeblog.com/advertise/advert-information/?aid=' . $_SESSION['advert_payment']['aid'] . '">' . $_SESSION['advert_payment']['aid'] . '(click to preview)</a></p>';
				
				$headers = "From: advertise@loveluxeblog.com\r\n";
				$headers .= "Reply-To: advertise@loveluxeblog.com\r\n";
				$headers .= "Return-Path: advertise@loveluxeblog.com\r\n";
				$headers .= "Organization: Loveluxe Blog\r\n";
				$headers .= "MIME-Version: 1.0\n";			
				$headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
                
				$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
				
				mail('advertise@loveluxeblog.com', 'You have received THE START OF an advert request from: ' . $_POST['n'], $message, $headers, "-fadvertise@loveluxeblog.com");
                
				
				
				
				$wpdb->insert( 
							  'adverts_submissions', 
							  array( 
									'startdatetime'	=>	date('Y-m-d', strtotime(str_replace(',', '', $_POST['d']))) . ' 09:00:00', 
									'enddatetime'	=>	($_POST['d'] != '') ? date('Y-m-d', strtotime(str_replace(',', '', $_POST['d']) . '+' . $prices[$_POST['t']]->quantity_duration)) . ' 09:00:00' : time(),
									'image'			=>	$imageUrl,
									'option_id'		=>	$_SESSION['advert_payment']['item']->id,
									'status'		=>	'pending',
									'name'			=>	$_SESSION['advert_payment']['n'],
									'email'			=>	$_SESSION['advert_payment']['e'],
									'description'	=>	$_SESSION['advert_payment']['item']->description,
									'duration'		=>	$_SESSION['advert_payment']['item']->duration,
									'price'			=>	$_SESSION['advert_payment']['item']->price,
									'url'			=>	$_SESSION['advert_payment']['url'],
									'aid'			=>	$_SESSION['advert_payment']['aid'],
									'business'		=>	$_SESSION['advert_payment']['b']
									), 
							  array( 
									'%s',
									'%s',
									'%s',
									'%d',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s'
									) 
							  );
				
				if (!isset($_SESSION['advert_log_id'])) {
                    $wpdb->insert(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Starting advert submission',
                                        'data'              =>  serialize($_POST),
                                        'stage'             =>  'insert data',
                                        'ip'                =>  $ip,
                                        'client_id'         =>  $_SESSION['advert_payment']['aid'],
                                        'client_name'       =>  $_SESSION['advert_payment']['name'],
                                        'client_business'   =>  $_SESSION['advert_payment']['business'],
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                        ),
                                  array(
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s'
                                        )
                                  );
                } else {
                    $wpdb->update(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Starting advert submission',
                                        'data'              =>  serialize($_POST),
                                        'stage'             =>  'insert data',
                                        'ip'                =>  $ip,
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT'],
                                        'client_id'         =>  $_SESSION['advert_payment']['aid']
                                        ),
                                  array(
                                        'id'         =>  $_SESSION['advert_log_id'],
                                        ),
                                  array(
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
                }
                
                header('location: /advertise/advert-preview/?adpr=1&aid=' . $_SESSION['advert_payment']['aid']);
                
				die();
			} else {
                
                if (!isset($_SESSION['advert_log_id'])) {
                    $wpdb->insert(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Error in post data',
                                        'data'              =>  serialize($errorMessage),
                                        'client_name'       =>  $_POST['n'],
                                        'stage'             =>  'error-handling',
                                        'ip'                =>  $ip,
                                        'client_id'         =>  $_SESSION['advert_log_id'],
                                        'client_business'   =>  $_POST['b'],
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                        ),
                                  array(
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s'
                                        )
                                  );
                    
                    $_SESSION['advert_log_id'] = $wpdb->insert_id;
                } else {
                    
                    $wpdb->update(
                                  'adverts_logs',
                                  array(
                                        'message'           =>  'Error in post data',
                                        'data'              =>  serialize($errorMessage),
                                        'stage'             =>  'error-handling',
                                        'ip'                =>  $ip,
                                        'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                        ),
                                  array(
                                        'id'         =>  $_SESSION['advert_log_id'],
                                        ),
                                  array(
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
                    
                }
                
                
            }
			
		}
		
	}

?>

<div class="entry-content">
<?php

    if (!empty($errorMessage)) {
        echo '<div class="break-whole"></div>';
        echo '<p class="e-border error">There were a few problems with the information you provided, please see below for more information:.</p>';
        
        foreach($errorMessage as $ek => $ev) {
            
            echo '<p class="error">' . $ev . '</p>';
            
        }
        echo '<div class="break-whole"></div>';
    }
    

?></div>

<!--p>
<label class="w100 fl" for="name">Your name:</label>
<?php
    if (isset($errorMessage['n'])) {
        echo '<span class="clear"></span><span class="error">' . $errorMessage['n'] . '</span><span class="clear"></span>';
    } else {
        echo '<span class="clear"></span><span class="error" style="display:none">' . $eMessage['n'] . '</span><span class="clear"></span>';
    } ?> 

<input type="text" <?php if (isset($errorMessage['n'])) {echo ' class="bd-e" ';} ?> name="n" id="name" value="<?php if (isset($_POST['n'])) { echo htmlspecialchars($_POST['n']);} ?>"/>
</p>
<p>
<label class="w100 fl" for="businessname">Business name:</label>
<?php
    if (isset($errorMessage['b'])) {
        echo '<span class="clear"></span><span class="error">' . $errorMessage['b'] . '</span><span class="clear"></span>';
    } else {
        echo '<span class="clear"></span><span class="error" style="display:none">' . $eMessage['b'] . '</span><span class="clear"></span>';
    } ?>
<input type="text" <?php if (isset($errorMessage['b'])) {echo ' class="bd-e" ';} ?> name="b" id="businessname" value="<?php if (isset($_POST['b'])) { echo htmlspecialchars($_POST['b']);} ?>"/>
</p>
<p>
<label for="email">Your email address:</label>
<?php
    if (isset($errorMessage['e'])) {
        echo '<span class="clear"></span><span class="error">' . $errorMessage['e'] . '</span><span class="clear"></span>';
    } else {
        echo '<span class="clear"></span><span class="error" style="display:none">' . $eMessage['e'] . '</span><span class="clear"></span>';
    } ?>
<input type="text" <?php if (isset($errorMessage['e'])) {echo ' class="bd-e" ';} ?> id="email" name="e" value="<?php if (isset($_POST['e'])) {echo htmlspecialchars($_POST['e']);} ?>" />
</p>
<p>
<label for="url">The url to point your advert to (this can be changed later):</label>
<?php
    if (isset($errorMessage['url'])) {
        echo '<span class="clear"></span><span class="error">' . $errorMessage['url'] . '</span><span class="clear"></span>';
    } else {
        echo '<span class="clear"></span><span class="error" style="display:none">' . $eMessage['url'] . '</span><span class="clear"></span>';
    } ?>		<input type="text" name="url" <?php if (isset($errorMessage['url'])) {echo ' class="bd-e" ';} ?>  id="url" value="<?php if (isset($_POST['url'])) {echo htmlspecialchars($_POST['url']);} ?>" />
</p>
<p>
<strong>Advertising options</strong>
</p>

<p>
<strong><?php echo $prices[1]->description; ?></strong>
</p>
<?php 
    
    foreach ($prices as $k => $option) {
		
		?>
<p>
<input <?php if ((isset($_POST['t']) && $_POST['t'] == $k) || (!isset($_POST['t']) && $k == 1)) {echo 'checked="checked"';} ?> type="radio" id="<?php echo str_replace(' ', '-', $option->duration); ?>" name="t" value="<?php echo $k; ?>">
<label for="<?php echo str_replace(' ', '-', $option->duration); ?>">£<?php echo $option->price; ?> for <?php echo $option->duration; ?></label>
</p>
<?php
    
    }
    
    ?>
<p>
<label for="date" style="height:50px">I would like my advert to start on (enter a date):<br/>Must be before: <?php echo date('d, M, Y', strtotime('+31day')); ?></label>
<span class="clear fl" style="height:20px;padding-bottom:5px;">Adverts run from 9am on the start date and finish at 9am on the end date, GMT time.</span>
<?php if (isset($errorMessage['d'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['d'] . '</span><span class="clear"></span>';} ?> 
<input type="text" id="date" name="d" <?php if (isset($errorMessage['d'])) {echo ' class="bd-e" ';} ?> value="<?php if (isset($_POST['d'])) {echo htmlspecialchars($_POST['d']);} else {echo date('l, j, F, Y', strtotime('+1day'));} ?>"/>
<script type="text/javascript">/*<![CDATA[*/$('input#date').datepicker({"dateFormat": "DD, d, MM, yy"});/*]]>*/</script>
</p>
<p>
<strong>Image options</strong>
</p>
<p>
<label for="choose-image-now">Choose an advertising image (200 pixels wide and 90 pixels high)</label><span class="clear"></span>
<?php if (isset($errorMessage['image'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['image'] . '</span><span class="clear"></span>';} elseif (!empty($errorMessage)) { 
    echo '<span class="clear"></span><span class="error">Please reupload your image</span><span class="clear"></span>';
}	
    ?>
<input <?php if (isset($_POST['image-choice']) && $_POST['image-choice'] == '1') {echo 'checked="checked""';} ?> type="hidden" value="1" id="choose-image-now" name="image-choice" />
<span class="clear"></span>
</p>
<div class="uploadimage">
<strong class="fr" style="min-width:200px;width:270px;margin-bottom:15px;">Download an example image:</strong>
<img src="/images/sidebar-template_200by90.jpg" alt="template" class="fr" style="margin-left:10px;clear:both;"/>
<strong class="" style="min-width:200px;width:270px;margin-bottom:15px;">Upload a banner image:</strong>

<a style="height:20px;line-height:20px;margin:39px 0 0 0;padding:5px;min-width:50px;" href="/images/sidebar-template_200by90.psd" class="pseudo-submit fr">PSD</a>
<a style="height:20px;line-height:20px;padding:5px;min-width:50px;left:62px;position:relative;" href="/images/sidebar-template_200by90.jpg" class="pseudo-submit fr">JPG</a>
<div id="file-uploader"></div>
<input style="margin-top:20px;" type="file" name="image" id="image" />
</div>
<p>
<input type="submit" value="Submit" class="submit-payment fl"/>
<span class="clear"></span>
<img src="/images/287.gif" alt="loading..." style="visibility:hidden;float:left;"/>
<span style="display:none;width:100%;float:left;margin-top:10px;">Thank you, uploading your image and sending you over to paypal...</span>
</p-->
<input type="submit" class="fr" value="NEXT &gt; your details" />
</form>


<script type="text/javascript">/*<![CDATA[*/


$('.submit-payment').click(function(e) {$(this).next('img').css('visibility', 'visible').next('span').show();});
$('form.aform').on('blur', 'input[type="text"]', function() {if (this.value.replace(/ /g, '') == '') {
                   
                   $(this).parent('p').find('span.error').slideDown();
                   $(this).css('border', '1px solid #C11B17');
                   } else {
                   
                   $(this).parent('p').find('span.error').slideUp();
                   $(this).css('border', '1px solid #E0DACC');
                   }});
/*]]>*/</script>