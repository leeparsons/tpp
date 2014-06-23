<?php
    /*
     *  Template Name: Advertising Submission Renewal Complete
     */
	if (!session_id()) {
        session_start();
        
    }
    $advertInfo = unserialize($_SESSION['advert_payment']['serialized']);



	$query = array();
    
    set_time_limit(120);

    if (isset($_SESSION['dev'])) {
		//	 dev
            $apiUser = 'rosie_api1.rosieparsons.com';
            $apiPWD = 'LR3X8R7NGPVYFFGD';
            $apiSig = 'ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp';
            $apiUrl = 'https://api-3t.sandbox.paypal.com/nvp';

    } else {
        
        /* live */
        $apiUser = 'rosie_api1.rosieparsons.com';
        $apiPWD = 'K5ULLT635CMGSQTY';
        $apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3';
        $apiUrl  = 'https://api-3t.paypal.com/nvp';
    }	

	if (isset($_SESSION['advert_payment'])) {
        
		$advertInfo = unserialize($_SESSION['advert_payment']['serialized']);

		if (isset($_GET['PayerID'])) {
			$_SESSION['advert_payment']['payerid'] = $_GET['PayerID'];
			header('location: http://' . $_SERVER['SERVER_NAME'] . '/advertise/renewal-complete/?aid=' . $advertInfo['aid']);
			die();
		}
        
        
        
		//update the database with a completed transaction!
		/* live */
		$fields = array(								
						'USER'										=>	$apiUser,
						'PWD'										=>	$apiPWD,
						'SIGNATURE'									=>	$apiSig,
						'VERSION'									=>	'72.0',
						'METHOD'									=>	'GetExpressCheckoutDetails',
						'TOKEN'										=>	$_SESSION['advert_payment']['token']
						);


		$fields_string = '';
		foreach($fields as $key=>$value) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$res = curl_exec($ch);
		curl_close($ch);
        
		$res = explode('&', $res);

		//see if the email is set?
		$paypalEmail = '';
		foreach ($res as $arr) {
			$tmpArr = explode('=', $arr);
			if (strtolower($tmpArr[0]) == 'email') {
				$paypalEmail = $tmpArr[1];
				break;
			}
		}
        
		if ($paypalEmail != '' && !isset($_SESSION['dev'])) {
			$wpdb->update( 
						  'adverts_submissions', 
						  array( 
								'payerid'	=> $_SESSION['advert_payment']['payerid'],
								'paypal_email'	=> urldecode($paypalEmail)
								), 
						  array( 'aid' => $advertInfo['aid'] ), 
						  array( 
								'%s',
								'%s'
								), 
						  array( '%s' ) 
						  );		
		} elseif (isset($_SESSION['dev'])) {
			$arr = array( 
				'payerid'	=> $_SESSION['advert_payment']['payerid'],
				'paypal_email'	=> urldecode($paypalEmail)
				);
			echo 'dev: would have inserted:' . print_r($arr, true);
		}
        
        if (isset($advertInfo['aid'])) {

            $query2 = $wpdb->get_results($wpdb->prepare("SELECT numerical_days, renewal_cost, description, duration, quantity_duration FROM advert_options WHERE id = %s", $advertInfo['option_id']));


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
                                                       $advertInfo['aid']
                                                       )
                                        );

if (isset($_SESSION['dev'])) {
echo 'querying db for advert info <br>';
echo $wpdb->prepare( 
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
                                                       $advertInfo['aid']
                                                       );
}            

        }
        
        
        
        
        
        if (!empty($query)) {
            

            //now do the express checkout!
            $fields = array(								
                            'USER'						=>	$apiUser,
                            'PWD'						=>	$apiPWD,
                            'SIGNATURE'					=>	$apiSig,
                            'VERSION'					=>	'72.0',
                            'METHOD'					=>	'DoExpressCheckoutPayment',
                            'TOKEN'						=>	$_SESSION['advert_payment']['token'],
                            'PAYMENTACTION'					=>	'Sale',
                            'PAYERID'					=>	$_SESSION['advert_payment']['payerid'],
                            'PAYMENTREQUEST_0_AMT'				=>	number_format($query2[0]->renewal_cost, 2),
                            'PAYMENTREQUEST_0_ITEMAMT'			=>	number_format($query2[0]->renewal_cost, 2),
                            'PAYMENTREQUEST_0_TAXAMT'			=>	'0.00',
                            'PAYMENTREQUEST_0_SHIPPINGAMT'			=>	'0.00',
                            'PAYMENTREQUEST_0_HANDLINGAMT'			=>	'0.00',
                            'PAYMENTREQUEST_0_INSURANCEAMT'			=>	'0.00',
                            'PAYMENTREQUEST_0_CURRENCYCODE'			=>	'GBP',
                            'L_PAYMENTREQUEST_0_NAME0'			=>	'Advert Signup: ' . $query2[0]->duration,
                            'L_PAYMENTREQUEST_0_DESCRIPTION0'		=>	$query2[0]->description,
                            'L_PAYMENTREQUEST_0_AMT0'			=>	number_format($query2[0]->renewal_cost, 2),
                            'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	'rosie@rosieparsons.com',
                            'CANCELURL'					=>	'http://' . $_SERVER['SERVER_NAME'] . '/advertise/renewal-cancelled',
                            'RETURNURL'					=>	'http://' . $_SERVER['SERVER_NAME']. '/advertise/renewal-complete'						
                            );
            
		if (isset($_SESSION['dev'])) {
			echo '<br><br>Fields sent to paypal: ' . print_r($fields, true);
		}

            $fields_string = '';
            foreach($fields as $key=>$value) {
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');
            
            $ch = curl_init();
            
            
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            $res = curl_exec($ch);
            curl_close($ch);
            
            
            
            $res = explode('&', $res);
            
            
		if (isset($_SESSION['dev'])) {
			echo '<br><br>result from curl post : ' . print_r($res, true);
		}

            $correlationID = '';
            $success = false;
            $fee = (string)0.00;
            $status = 'pending';

            foreach ($res as $arr) {
                $tmpArr = explode('=', $arr);	
                
                if (strtolower($tmpArr[0]) == 'correlationid') {
                    $correlationID = urldecode($tmpArr[1]);
                }
                
                if (strtolower($tmpArr[0]) == 'ack') {
                    if (strtolower($tmpArr[1]) == 'success') {
                        $success = true;
                    } else {
                        break;
                    }
                }
                
                if (strtolower($tmpArr[0]) == 'paymentinfo_0_paymentstatus') {
                    $status = strtolower(urldecode($tmpArr[1]));
                    if ($status == 'completed') {
                        $status = 'complete';
                    }
                }
                
                if (strtolower($tmpArr[0]) == paymentinfo_0_feeamt) {
                    $fee = (string)urldecode($tmpArr[1]);
                }
                
                
            }
            
        } else {
            $success = false;
        }
		if ($success === true) {
                        
			$startDate = $query[0]->startdate;
            
			//figure out the enddate
			$endDate =  date('Y-m-d', strtotime(str_replace(',', '', $advertInfo['startdate']) . '+' . $advertInfo['quantity_duration'])) . ' 09:00:00';
            $insertion = array('status'	=>	$status,
								'fee'		=>	(float)$fee,
								'correlationid'	=>	$correlationID,
								'startdatetime'	=>	date('Y-m-d', $startDate) . ' 09:00:00', 
								'enddatetime'	=>	$endDate,
								'business'	=>	$advertInfo['business'],	
								'name'		=>	$advertInfo['name'],
								'email'		=>	$advertInfo['email'],
								'description'	=>	$query2[0]->description,
								'duration'	=>	$query2[0]->duration,
                                'price'     =>  $query2[0]->renewal_cost,
								'image'		=>	$advertInfo['image'],
								'token'		=>	$_SESSION['advert_payment']['token'],
								'option_id'	=>	$advertInfo['option_id'],
				                                'reminder' 	=> 	'0');

mail('parsolee@gmail.com', 'renewal data', print_r($insertion, true));
		if (isset($_SESSION['dev'])) {
			echo '<br><br>data to insert into db: ' . print_r($insertion, true);
            die();
            
		}   
                        
            $wpdb->update( 
						  'adverts_submissions', 
						  array( 
								'status'	=>	$status,
								'fee'		=>	(float)$fee,
								'correlationid'	=>	$correlationID,
								'startdatetime'	=>	date('Y-m-d', $startDate) . ' 09:00:00', 
								'enddatetime'	=>	$endDate,
								'business'	=>	$advertInfo['business'],	
								'name'		=>	$advertInfo['name'],
								'email'		=>	$advertInfo['email'],
								'description'	=>	$query2[0]->description,
								'duration'	=>	$query2[0]->duration,
                                'price'     =>  $query2[0]->renewal_cost,
								'image'		=>	$advertInfo['image'],
								'token'		=>	$_SESSION['advert_payment']['token'],
								'option_id'	=>	$advertInfo['option_id'],
                                'reminder' 	=> 	'0'
								), 
						  array( 'aid' => $advertInfo['aid'] ), 
						  array( 
								'%s',
								'%f',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
                                '%s',
								'%s',
								'%s',
								'%s',
                                '%d'
								), 
						  array( '%s' ) 
                               );

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
                                                       $advertInfo['aid']
                                                       )
                                        );
			if (isset($advertInfo['logid'])) {
                
                $wpdb->update(
                              'adverts_logs',
                              array(
                                    'message'           =>  'Completed advert renewal payment',
                                    'stage'             =>  'complete',
                                    'ip'                =>  $ip,
                                    'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                                    ),
                              array(
                                    'id'                =>  $advertInfo['logid']
                                    ),
                              array(
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
            
			$messageBase = '<html><head><meta content="text/html; charset=UTF-8" http-equiv="Content-Type"></head>
			<body style="width:100%%;margin:auto;text-align:center;background-color:#fcfcfc">
			<table style="color:#5E5E5D;text-align:left;">
			<tbody>
			<tr>
            <td><h1 style="background:#F6F1E7;padding:10px;font-size:14px;border:1px solid #A89D87;min-width:200px;">Hello from LoveLuxe Blog</h1></td>
			</tr>
			<tr>
            %s
			</tr>
			</tbody>
			</table>
			</body>
			</html>';
			
			//email Rosie!
			$message = '<p>You have received a COMPLETED RENEWAL order for an advert! FROM: ' . $query[0]->name . ' of ' . $query[0]->business . '</p>';
            
			if ($status != 'complete') {
				$message .= '<p>WARNING: the payment has not yet cleared!</p>';
			}
			
            $message .= '<p>' . $query[0]->name . ' would like to have an advert for: <br/>' . $query[0]->duration . ' at a cost of £' . $query[0]->price . '</p>';
            
            
            if ($query[0]->image != '') {
                $message .= '<p>' . $query[0]->name . ' has uplaoded the image they would like to use for the advert: <a href="http://www.loveluxeblog.com/' . $query[0]->image . '">' . $query[0]->image . '</a></p>';
            } else {
                $message .= '<p>' . $query[0]->name . ' would like to send you an image at a later point.</p>';				
            }
            
            if ($query[0]->url != '') {
                $message .= '<p>' . $query[0]->name . ' would like the advert to point to: <a href="' . $query[0]->url . '">' . $query[0]->url . '</a></p>';
            } else {
                $message .= '<p>' . $query[0]->name . ' did not enter a url for the advert to point to</p>';
            }
            
            if (isset($query[0]->startdate) && $query[0]->startdate != '') {
                $message .= '<p>Your advert will continue rolling on until it expires on: ' . 	date('l, j, M, Y', $query[0]->enddate) . ' at 09:00:00</p>';
            } else {
                $message .= '<p>' . $query[0]->name . ' did not enter a start date for the advert. This will need to be agreed before hand.</p>';
            }
            
            $message .= '<p>The advert information has a reference: <a href="http://www.loveluxeblog.com/advertise/advert-information/?aid=' . $advertInfo['aid'] . '">' . $advertInfo['aid'] . ' (click to preview)</a></p>';
            
			$headers = "From: advertise@loveluxeblog.com\r\n";
			$headers .= "Reply-To: advertise@loveluxeblog.com\r\n";
			$headers .= "Return-Path: advertise@loveluxeblog.com\r\n";
			$headers .= "Organization: Loveluxe Blog\r\n";
			$headers .= "MIME-Version: 1.0\n";			
			$headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
			
			$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
			
            
            
            mail('advertise@loveluxeblog.com', 
            'You have received a COMPLETED RENEWAL advert request! FROM: ' . $query[0]->name . ' of ' . $query[0]->business,
            str_replace(
                        array('<p>',
                              '<a ',
                              '<strong>'
                              ), 
                        array('<p style="color:#8B8A8A">', 
                              '<a style="color:#FA8A70" ',
                              '<strong style="font-weight:600;color:#FA8A70">'
                              ), 
                        sprintf($messageBase, $message)
                        ),
            $headers,
            '-fadvertise@loveluxeblog.com');	
            
            
            //email submitter
            
            
			$message = '<p><strong>' . $query[0]->name . '</strong>, thanks ever so much for placing your advert on LoveLuxe Blog! We are thrilled to have you on board with us and hope it will be a long term relationship where it will bring in lots more business for you!</p>';
			
			$message .= '<p>To complement your advert and get your marketing on turbocharge, do send us in submissions of real weddings or parties you’ve been involved with and even better, consider contributing articles once a month as an expert guest blogger. Email us <a href="mailto:guestbloggers@loveluxeblog.com">guestbloggers@loveluxeblog.com</a> for more information about providing articles in your area of expertise!</p>';
            
            $message .= '<p>Your advert has been booked for: <br/>' . $query[0]->duration . ' at a cost of £' . $query[0]->price . '</p>';
            
            
            if ($query[0]->image != '') {
                $message .= '<p>You have uploaded the image would like to use for the advert:</p><p><a href="http://www.loveluxeblog.com' . $query[0]->image . '"><img src="http://www.loveluxeblog.com' . $query[0]->image . '"></a></p>';
            } else {
                $message .= '<p>You have not chosen an image for your advert. You will need to send one to advertise@loveluxeblog.com (remember to include your name and your advert id: ' . $advertInfo['aid'] . ' in the email).</p>';				
            }
            
            if ($query[0]->url != '') {
                $message .= '<p>You would like the advert to point to: <a href="' . $query[0]->url . '">' . $query[0]->url . '</a></p>';
            } else {
                $message .= '<p>You did not enter a url for the advert to point to, so you will need to email advertise@loveluxeblog.com with the url (and remember to include your advert id: ' . $advertInfo['aid'] . ' in the email)</p>';
            }
            
            
			
            if (isset($query[0]->startdate) && $query[0]->startdate != '') {
                $message .= '<p>The advert will roll on until it expires on: ' . date('l, j, M, Y', $query[0]->enddate) . ' at 09:00:00</p>';
            } else {
                $message .= '<p>You did not enter a start date for the advert. You will need to email this to <a href="mailto:advertise@loveluxeblog.com">advertise@loveluxeblog.com</a> before the advert can go live (remember to include your advert id: <strong>' . $advertInfo['aid'] . '</strong> in the email).</p>';
            }
            
            $message .= '<p>Remember, you can view your advert information at: <a href="http://www.loveluxeblog.com/advertise/advert-information/?aid=' . $advertInfo['aid'] . '">http://www.loveluxeblog.com/advertise/advert-information/?aid='. $advertInfo['aid'] . '</a></p>';
            
			$message .= '<p>If you need to contact <a href="http://www.loveluxeblog.com">loveluxe blog</a> about your advert please email us on: <a href="mailto:advertise@loveluxeblog.com">advertise@loveluxeblog.com</a> and quote your advert reference: <strong>' . $advertInfo['aid'] . '</strong></p>';
			mail($query[0]->email, 
            'Thank you for your advert renewal submission', 
            str_replace(
                        array('<p>',
                              '<a ',
                              '<strong>'
                              ), 
                        array('<p style="color:#8B8A8A">', 
                              '<a style="color:#FA8A70" ',
                              '<strong style="font-weight:600;color:#FA8A70">'
                              ), 
                        sprintf($messageBase, $message)), 
            $headers, 
            "-fadvertise@loveluxeblog.com"
            );
            
            mail('parsolee@gmail.com', 
            'Thank you for your advert renewal submission', 
            str_replace(
                        array('<p>',
                              '<a ',
                              '<strong>'
                              ), 
                        array('<p style="color:#8B8A8A">', 
                              '<a style="color:#FA8A70" ',
                              '<strong style="font-weight:600;color:#FA8A70">'
                              ), 
                        sprintf($messageBase, $message)), 
            $headers, 
            "-fadvertise@loveluxeblog.com"
            );
            
            
		}		
	} else {
		if (isset($_GET['aid'])) {
			header('location: /advertise/advert-information/?aid=' . $_GET['aid']);
			die();
		}
	}
    
	get_header();
	
	?><section class="col1"><?php
		
        while(have_posts()) {
            
            the_post();
            
            ?><article><div class="entry-content"><header><h1 class="section-title"><span><?php
                
                the_title();
                
                ?></span><span class="stripe"></span></h1></header><?php
                    
                    
                    if ($success === false) {
                        echo '<p class="error">There was a problem taking your payment. please contact us to resolve this issue.</p>';
                    } elseif ($status != 'complete') {
                        echo '<p class="error">Your payment is not yet completed, it could take a while for this to update.</p>';	
                    }
                    
                    
                    the_content();
                    
                    
                    
                    ?></div><h2 class="section-title"><span>Your advert submission details</span><span class="stripe"></span></h2><div class="entry-content aform-complete">



<p><span>Name:</span> <?php echo stripslashes($query[0]->name); ?></p>

<p><span>Business name:</span> <?php echo stripslashes($query[0]->business); ?></p>

<p><span>Email:</span><?php echo stripslashes($query[0]->email); ?></p>

<p><span>Start Date:</span><?php echo ($query[0]->startdate == '') ? 'Not set. You will need to confirm this by email.' : date('l, j, M, Y', $query[0]->startdate) . ' 09:00:00'; ?></p>

<p><span>Expiry Date:</span><?php echo ($query[0]->enddate == '') ? 'Not set. You will need to confirm this by email.' : date('l, j, M, Y', $query[0]->enddate) . ' 09:00:00'; ?></p> 

<p><span>Url to link to:</span><a href="<?php echo $query[0]->url; ?>"><?php echo stripslashes($query[0]->url); ?></a></p>

<p><span class="fl">Image:</span><?php 
    
    echo ($query[0]->image == '') ? 'Not uploaded. You will need to confirm this before your advert goes live!' : '<img src="' . $query[0]->image . '"/>';
    
    ?></p>

<p><strong>Details:</strong></p>

<p><span>Option <?php echo $query[0]->option_id; ?>:</span><?php echo $query[0]->duration; ?> &pound;<?php echo $query[0]->price; ?></p>

<p>You can view your advert information at: <a href="http://www.loveluxeblog.com/advertise/advert-information/?aid=<?php echo $advertInfo['aid'];  ?>">http://www.loveluxeblog.com/advertise/advert-information/?aid=<?php echo $advertInfo['aid'];  ?></a></p>

</div>
</article><?php
	}
    
    ?></section><?php
        
        unset($_SESSION['advert_payment']);
        
        get_sidebar();
        
        get_footer();
