<?php
/*
 *  Template Name: Advert Preview
 */



	$query = array();

    set_time_limit(120);
    
	if (!session_id()) {
        session_start();
    }
    


    if (!isset($_SESSION['advert_info']['option'])) {
        header('location: /advertise/');
        die;
    }

    if (isset($_GET['dev'])) {
        $_SESSION['dev'] = true;
    }

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

    
    if (isset($_POST['af-aid'])) {
        $newPost = array();
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, 3) == 'af-') {
                $newPost[substr($k, 3)] = $v;
            } else {
                $newPost[$k] = $v;
            }
            unset($_REQUEST[$k]);
            unset($_POST[$k]);
        }
        
        $_POST = $newPost;
            
        $newRequest = array();
        foreach ($_REQUEST as $k => $v) {
            $newRequest[$k] = $v;
            unset($_REQUEST[$k]);
        }
        $_REQUEST = array_merge($newRequest, $newPost);
        unset($newPost);
        unset($newRequest);
        
    }
    
    
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

	if (!empty($query) && isset($_POST['aid'])) {

		$_SESSION['advert_payment']['aid'] = $_POST['aid'];
		
        if (isset($_SESSION['dev'])) {
            //				 dev
            $apiUser = 'rosie_api1.rosieparsons.com';
            $apiPWD = 'LR3X8R7NGPVYFFGD';
            $apiSig = 'ALO1jE.eI2fzLRNPuc9giY898XkvAcppLX01gHUqDoLFq0h5TqGTZMMp';
            $apiUrl = 'https://api-3t.sandbox.paypal.com/nvp';
            $checkOutUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
            
        } else {				
            /* live */
            $apiUser = 'rosie_api1.rosieparsons.com';
            $apiPWD = 'K5ULLT635CMGSQTY';
            $apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3';
            $apiUrl  = 'https://api-3t.paypal.com/nvp';
            $checkOutUrl = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
        }
        
        if (!isset($_SESSION['advert_log_id'])) {
            $wpdb->insert(
                          'adverts_logs',
                          array(
                                'message'           =>  'Starting advert submission',
                                'data'              =>  serialize($_POST),
                                'client_name'       =>  $query[0]->name,
                                'stage'             =>  'set-express-checkout',
                                'ip'                =>  $ip,
                                'client_id'         =>  $query[0]->aid,
                                'client_business'   =>  $query[0]->b,
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
                                'message'       =>  'Starting advert payment',
                                'stage'         =>  'set-express-checkout',
                                'ip'            =>  $ip,
                                'user_agent'    =>  $_SERVER['HTTP_USER_AGENT']
                                ),
                          array(
                                'id'    =>  $_SESSION['advert_log_id']
                                ),
                          array(
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                                ),
                          array(
                                '%d'
                                )
                         );
        }
        
        $fields = array(								
                        'USER'										=>	$apiUser,
                        'PWD'										=>	$apiPWD,
                        'SIGNATURE'									=>	$apiSig,
                        'VERSION'									=>	'72.0',
                        'PAYMENTACTION'								=>	'AUTHORIZATION',
                        'METHOD'									=>	'SetExpressCheckout',
                        'PAYMENTREQUEST_0_NUMBER'					=>	'123',
                        'PAYMENTACTION'								=>	'Sale',
                        'PAYMENTREQUEST_0_AMT'						=>	$_SESSION['advert_info']['option']->price,
                        'PAYMENTREQUEST_0_ITEMAMT'					=>	$_SESSION['advert_info']['option']->price,
                        'PAYMENTREQUEST_0_TAXAMT'					=>	'0.00',
                        'PAYMENTREQUEST_0_SHIPPINGAMT'				=>	'0.00',
                        'PAYMENTREQUEST_0_HANDLINGAMT'				=>	'0.00',
                        'PAYMENTREQUEST_0_INSURANCEAMT'				=>	'0.00',
                        'PAYMENTREQUEST_0_CURRENCYCODE'				=>	'GBP',
                        'L_PAYMENTREQUEST_0_NAME0'					=>	'Advert Signup: ' . $_SESSION['advert_info']['option']->duration,
                        'L_PAYMENTREQUEST_0_DESCRIPTION0'			=>	$query[0]->description,
                        'L_PAYMENTREQUEST_0_AMT0'					=>	$_SESSION['advert_info']['option']->price,
                        'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	'rosie@rosieparsons.com',
                        'CANCELURL'									=>	'http://' . $_SERVER['SERVER_NAME'] . '/advertise/cancelled',
                        'RETURNURL'									=>	'http://' . $_SERVER['SERVER_NAME'] . '/advertise/complete',
                        'PAYMENTREQUEST_0_ALLOWEDPAYMENTMETHOD'		=>	'InstantPaymentOnly'
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

        $result = explode('&', $res);
        
        
        $token = '';
        $success = false;
        foreach ($result as $tmpArr) {
            
            $tmp = explode('=', $tmpArr);
            if ($tmp[0] == 'TOKEN') {
                $token = urldecode($tmp[1]);
            } elseif ($tmp[0] == 'ACK') {
                if (strtolower($tmp[1]) == 'success') {
                    $success = true;
                }
            }
            
        }
        
        $wpdb->update(
                      'adverts_logs',
                      array(
                            'message'           =>  'Starting advert payment',
                            'stage'             =>  'pending-payment',
                            'ip'                =>  $ip,
                            'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                            ),
                      array(
                            'id'         =>  $_SESSION['advert_log_id']
                            ),
                      array(
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                            ),
                      array(
                            '%d'
                            )
                      );
        
        $wpdb->update( 
                      'adverts_submissions', 
                      array( 
                            'token'		=>	$token,
                            'status'	=>	'pending'
                            ), 
                      array( 'aid' => $query[0]->aid ), 
                      array( 
                            '%s',
                            '%s'
                            ), 
                      array( '%s' )
                      );
        
        $_SESSION['advert_payment']['token'] = $token;
        header('location: ' . $checkOutUrl . '&token=' . $token);
        die();
		
		
	} elseif (!empty($query) && isset($_GET['adpr'])) {
        
        $headers = "From: advertise@loveluxeblog.com\r\n";
        $headers .= "Reply-To: advertise@loveluxeblog.com\r\n";
        $headers .= "Return-Path: advertise@loveluxeblog.com\r\n";
        $headers .= "Organization: Loveluxe Blog\r\n";
        $headers .= "MIME-Version: 1.0\n";			
        $headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
        
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        
        
        //email submitter
        
        
        $message = '<p><strong>' . $query[0]->name . '</strong>, thanks ever so much for placing your advert on LoveLuxe Blog! We are thrilled to have you on board with us and hope it will be a long term relationship where it will bring in lots more business for you!</p>';
        
        $message .= '<p>We just need payment from you to start your advert, if you have not done so already, you can visit this link to make payment at any point for your advert to go live: <a href="http://' . $_SERVER['SERVER_NAME'] . '/advertise/advert-preview/?aid=' . $query[0]->aid . '">http://' . $_SERVER['SERVER_NAME'] . '/advertise/advert-preview/?aid=' . $query[0]->aid . '</a></p>';
        
        $message .= '<p>To complement your advert and get your marketing on turbocharge, do send us in submissions of real weddings or parties you’ve been involved with and even better, consider contributing articles once a month as an expert guest blogger. Email us <a href="mailto:guestbloggers@loveluxeblog.com">guestbloggers@loveluxeblog.com</a> for more information about providing articles in your area of expertise!</p>';
        
        $message .= '<p>Your advert has been booked for: <br/>' . $_SESSION['advert_info']['option']->duration . ' at a cost of &pound;' . $_SESSION['advert_info']['option']->price . '</p>';
        
        
        if ($query[0]->image != '') {
            $message .= '<p>You have uploaded the image would like to use for the advert:</p><p><a href="http://www.loveluxeblog.com' . $query[0]->image . '"><img src="http://www.loveluxeblog.com' . $query[0]->image . '"></a></p>';
        } else {
            $message .= '<p>You have not chosen an image for your advert. You will need to send one to advertise@loveluxeblog.com (remember to include your name and your advert id: ' . $query[0]->aid . ' in the email).</p>';				
        }
        
        if ($query[0]->url != '') {
            $message .= '<p>You would like the advert to point to: <a href="' . $query[0]->url . '">' . $query[0]->url . '</a></p>';
        } else {
            $message .= '<p>You did not enter a url for the advert to point to, so you will need to email advertise@loveluxeblog.com with the url (and remember to include your advert id: ' . $query[0]->aid . ' in the email)</p>';
        }
        
        
        
        if (isset($query[0]->startdate) && $query[0]->startdate != '') {
            $message .= '<p>You have chosen to start the advert on: ' . date('l, d, F, Y', $query[0]->startdate) . ' at 09:00:00</p>';
            $message .= '<p>The advert expires on: ' . date('l, d, F, Y', $query[0]->enddate) . ' at 09:00:00</p>';
        } else {
            $message .= '<p>You did not enter a start date for the advert. You will need to email this to <a href="mailto:advertise@loveluxeblog.com">advertise@loveluxeblog.com</a> before the advert can go live (remember to include your advert id: <strong>' . $query[0]->aid . '</strong> in the email).</p>';
        }
        
        $message .= '<p>Remember, you can preview your advert information at: <a href="http://www.loveluxeblog.com/advertise/advert-preview/?aid=' . $query[0]->aid . '">http://www.loveluxeblog.com/advertise/advert-preview/?aid='. $query[0]->aid . '</a></p>';
        
        $message .= '<p>If you need to contact <a href="http://www.loveluxeblog.com">loveluxe blog</a> about your advert please email us on: <a href="mailto:advertise@loveluxeblog.com">advertise@loveluxeblog.com</a> and quote your advert reference: <strong>' . $query[0]->aid . '</strong></p>';
        
        
        $messageBase = '<html><head><meta content="text/html; charset=UTF-8" http-equiv="Content-Type"></head>
        <body style="width:100%%;margin:auto;text-align:center;background-color:#fcfcfc">
        <table style="color:#5E5E5D;text-align:left;">
        <tbody>
        <tr>
        <td><h1 style="background:#F6F1E7;padding:10px;font-size:14px;border:1px solid #A89D87;min-width:200px;">Hello from LoveLuxe Blog</h1></td>
        </tr>
        <tr><td>
        %s
        </td></tr>
        </tbody>
        </table>
        </body>
        </html>';

        mail($query[0]->email, 
             'Your LoveLuxe Blog advert: Preview link', 
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
        
               
        $wpdb->update(
                      'adverts_logs',
                      array(
                            'message'           =>  'Starting advert preview',
                            'stage'             =>  'advert-preview',
                            'ip'                =>  $ip,
                            'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                            ),
                      array(
                            'aid'   =>  $query[0]->aid
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
header('location: /advertise/advert-preview/?aid=' . $_GET['aid']);
die();
    }
	
	get_header();
	
	?><section class="col1"><div class="entry-content"><?php
		
	while(have_posts()) {
	
		the_post();
		
	?><article><header><h1 class="section-title"><span><?php
		
		the_title();
		
	?></span><span class="stripe"></span></h1></header><?php
		
		the_content();
		
	?></div><h2 class="section-title"><span>Your advert submission details</span><span class="stripe"></span></h2><div class="entry-content aform-complete"><?php

		

		
		if (!empty($query)) {

			
			?><form class="aform" method="post" enctype="multipart/form-data" action="/advertise/advert-preview/"><?php
			
                foreach ($query[0] as $k => $v) {
                    
                    echo '<input type="hidden" value="' .  htmlspecialchars($v) . '" name="af-' . $k . '"/>';
                    
                    
                }


if ($query[0]->status != 'complete') {

                ?><p>&nbsp;</p>
<p><img class="fl" src="http://www.loveluxeblog.com/images/paywithpaypal.png" style="margin-top:-25px" alt="paypal"/>
<input type="submit" class="submit-payment fr" value="continue to payment"/></p>
<div class="break-whole"></div><p><br/></p>
<?php
} else {
	?><p>Payment has been made, thank you</p><?php
}

?>
                <p><span>Name:</span> <?php 
                                
                                    
    echo stripslashes($query[0]->name);
    
                    
?></p>
                <p><span>Business name:</span><?php
    
	
    echo stripslashes($query[0]->business);
    


?></p>

<p><span>Email:</span><?php
    
    
    
    echo stripslashes($query[0]->email);

?></p>

<p><span>Start Date:</span><?php echo ($query[0]->startdate == '') ? 'Not set. You will need to confirm this by email.' : '9am on: ' . date('l, j, M, Y', $query[0]->startdate); ?></p>

<p><span>Expiry Date:</span><?php echo ($query[0]->enddate == '') ? 'Not set. You will need to confirm this by email.' : '9am on: ' . date('l, j, M, Y', $query[0]->enddate); ?></p> 

<p><span>Url to link to:</span><?php
    
    
    
    ?><a href="<?php echo stripslashes($query[0]->url); ?>"><?php echo stripslashes($query[0]->url); ?></a><?php
    

?></p>

<p><span class="fl">Image:</span><?php 
    
    echo ($query[0]->image == '') ? 'Not uploaded. You will need to confirm this before your advert goes live!' : '<img src="' . $query[0]->image . '"/>';

    
    ?></p>
        

    <p><span>Option <?php echo $_SESSION['advert_info']['option']->id; ?>:</span><?php echo $_SESSION['advert_info']['option']->duration; ?> &pound;<?php echo $_SESSION['advert_info']['option']->price; ?></p>


    <p><span>Payment Status:</span><?php
    
    
    echo $query[0]->status == 'complete' ? 'PAID' : 'pending payment';
    
    if ($query[0]->status == 'complete') {
        
        echo ' <a href="javsacript:return false;">Renew options coming soon!</a>';
    }
    
    ?></p><?php

if ($query[0]->status != 'complete') {

?><div class="break-whole"></div>
<p><img class="fl" src="http://www.loveluxeblog.com/images/paywithpaypal.png" alt="paypal"/>
<input type="submit" class="submit-payment fr" value="continue to payment" style="margin-top:30px;"/></p>
<?php
} else {
	?><p>Payment has been made, thank you</p><?php
}


        
        
            echo '</form>';	
		} else {
            
			echo '<p>No advert was found - please check the advert reference you were given.</p>';
		}
        
        ?>
</div>

</article><?php
                
    }	
						
?></section><?php
	
	
	get_sidebar();
	
	get_footer();
