<?php
    /*
     *  Template Name: Advert details
     */

    set_time_limit(120);
    
	if (!session_id()) {
        session_start();
    }

    if (!isset($_SESSION['advert_info'])) {
        header('location: /advertise/');
        die;
    }
    
	if (isset($_GET['dev'])) {
		$_SESSION['dev'] = true;
	}

    //get the soonest date available for this advert:
    
    
    //get the advert options, and determine the availability:
    
    
        
    $availability = array();

    for ($x = 0; $x < 5; $x++) {
        //can select up to four months ahead
        //months to cycle through to find availability
        $months_from_now_to_start_of_period[] = $x;
    }    
    
    //get a matrix of availability for this period and the start dates
    foreach ($months_from_now_to_start_of_period as $month_from_now_to_start_of_period) {
        

        if ($month_from_now_to_start_of_period == 0 && date('n') > 1) {
            $availability[] = false;
            continue;
        }
        

        /*
         
         get adverts which have:
         if period > 1 month
            started <= end of period - 1 month
            AND
            end >= start of period + 1 month
         else if period == 1 month
            started <= start of period
            AND
            end >= end of period
         
         
        */
        $start_of_period = date('Y-m-01 09:00:00', strtotime('+' . $month_from_now_to_start_of_period . ' month'));
        $end_of_period = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period + ($_SESSION['advert_info']['option']->numerical_days/30)) . ' month'));

        if ($_SESSION['advert_info']['option']->numerical_days == 30) {
            

            
            $sql = "SELECT

            available_from, max_bookable, COUNT(ads.aid) AS c

            FROM advert_options AS ao

            LEFT JOIN advert_options_booking_settings AS aos ON aos.type_id = ao.type

            LEFT JOIN adverts_submissions AS ads ON ads.option_id = ao.id

            WHERE type_id = %d

            AND 

            status = 'complete'

            AND 
            
            startdatetime <= %s AND enddatetime >= %s

            GROUP BY ao.type

            UNION

            SELECT 

            available_from, max_bookable, 0 AS c 

            FROM advert_options AS ao

            LEFT JOIN advert_options_booking_settings AS aos ON aos.type_id = ao.type

            WHERE type_id = %d

            AND ao.id = %d

            GROUP BY ao.type";
            
            $swap_array = array(
                          $_SESSION['advert_info']['option']->type,
                          $start_of_period,
                          $end_of_period,
                          $_SESSION['advert_info']['option']->type,
                          $_SESSION['advert_info']['option']->id
                          );
            

        } else {
            

            $start_of_period_plus_month = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period+1) . 'month'));

            $end_of_period_minus_month = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period + ($_SESSION['advert_info']['option']->numerical_days/30)) . 'month'));

            $sql = "SELECT
            
            available_from, max_bookable, COUNT(ads.aid) AS c
            
            FROM advert_options AS ao
            
            LEFT JOIN advert_options_booking_settings AS aos ON aos.type_id = ao.type
            
            LEFT JOIN adverts_submissions AS ads ON ads.option_id = ao.id
            
            WHERE type_id = %d
            
            AND 
            
            status = 'complete'
            
            AND 
            
            startdatetime <= %s AND enddatetime >= %s
            
            GROUP BY ao.type
            
            UNION
            
            SELECT 
            
            available_from, max_bookable, 0 AS c 
            
            FROM advert_options AS ao
            
            LEFT JOIN advert_options_booking_settings AS aos ON aos.type_id = ao.type
            
            WHERE type_id = %d
            
            AND ao.id = %d
            
            GROUP BY ao.type";
            
            $swap_array = array(
                                $_SESSION['advert_info']['option']->type,
                                $end_of_period_minus_month,
                                $start_of_period_plus_month,
                                $_SESSION['advert_info']['option']->type,
                                $_SESSION['advert_info']['option']->id
                                );
            
        }
        
        
        $sql = $wpdb->prepare(
                              $sql,
                              $swap_array
                              );
//echo $sql . '<br><br>';
//echo '<div style="display:none">' . $sql . '</div>';

        $booked_matrix = $wpdb->get_results($sql);

        
        if (!isset($booked_matrix[1])) {
            //no adverts booked
            
            $available_date = new DateTime(date('Y-M-01', strtotime($booked_matrix[0]->available_from)));            
            
            $tmp_start_date = new DateTime(date('Y-M-01', strtotime('+' . ($month_from_now_to_start_of_period + ($_SESSION['advert_info']['option']->numerical_days/30)) . 'months')));
            
            //$availability[$month_from_now_to_start_of_period] = $available_date < $tmp_start_date;            
            $diff = date_diff($available_date, $tmp_start_date);
            $diff = $diff->format('%R');

            //$availability[$month_from_now_to_start_of_period] = $diff == '+';
            $availability[$month_from_now_to_start_of_period] = false;

            
            unset($available_date);
            unset($tmp_start_date);

        } else {
            //some adverts have been booked.
            
            $available_from = new DateTime($booked_matrix[0]->available_from);
            $base_date = new DateTime($start_of_period);
            
            $diff = date_diff($base_date, $available_from);
            $diff_parity = $diff->format('%R');
            
            if ($diff_parity == '+') {
                //time has not yet passed or might have been reached for availability
                $availability[$month_from_now_to_start_of_period] = $booked_matrix[0]->c < $booked_matrix[0]->max_bookable && $diff->format('%d%M%Y') == '00000';
            } else {
                //available date has passed. See if can still book?
                $availability[$month_from_now_to_start_of_period] = $booked_matrix[0]->c < $booked_matrix[0]->max_bookable;
            }
            
            
        }
    }    

    if (isset($_POST['n'])) {
        //validate the information
        $errorMessage = array();
       
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

        $eMessage = array(
                          'n'   =>  'Please enter your name',
                          'b'   =>  'Please enter your business',
                          'e'   =>  'Please enter your email',
                          'url' =>  'Please enter a valid url',
                          'd'   =>  'Please select a start date'
                          );
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
                      
            //check to see if the start date they have selected is available:
            $datetime_selected_to_start = strtotime($strDate);

            //number of months ahead of our time
            if ($_SESSION['advert_info']['option']->type != 2 && !$availability[date_diff(date_create(date('Y-M-01')), date_create($strDate))->format('%m')]) {
                $errorMessage['d'] = 'Please select an available period';
            } else {
                $datetime_selecetd_to_start = strtotime($strDate);
                if ($strDate == '' || ($datetime_selected_to_start == 0 || $datetime_selected_to_start <= time())) {
                    $errorMessage['d'] = 'Please enter a start date in the future';
                } elseif ($datetime_selecetd_to_start > strtotime('+4month')) {
                    $errorMessage['d'] = 'Please enter a start date no more than 4 months in the future (needs to be <br/>before ' . date('d, M, Y', strtotime('+4months')) . ')';
                }
            }
		}
		
        if (isset($_POST['url']) && substr($_POST['url'], 0, 7) != 'http://' && substr($_POST['url'], 0, 8) != 'https://') {
            $_POST['url'] = 'http://' . $_POST['url'];
        }
        
		if (!isset($_POST['url']) || filter_var($_POST['url'], FILTER_VALIDATE_URL) === false) {
			$errorMessage['url'] = 'Please enter a valid url';
		}

        $imageUrl = '';


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
                            
                            if ($w != $_SESSION['advert_info']['option']->max_width || $h != $_SESSION['advert_info']['option']->max_height) {
                                $errorMessage['image'] = 'The image needs to be exactly ' . $_SESSION['advert_info']['option']->max_width . 'x' . $_SESSION['advert_info']['option']->max_height . ' pixels';
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
            
            $message .= '<p>' . $_POST['n'] . ' would like to have an advert for: <br/>' . $_SESSION['advert_info']['option']->duration . ' at a cost of £' . $_SESSION['advert_info']['option']->price . '</p>';
            
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
                $expiry = date('l, d, F, Y h:i:s', strtotime(str_replace(',', '', $_POST['d'] . ' 09:00:00') . '+' . $_SESSION['advert_info']['option']->quantity_duration));
                $message .= '<p>The advert expires on: ' . 	$expiry . '</p>';
            } else {
                $expiry = '';
                $message .= '<p>' . $_POST['n'] . ' did not enter a start date for the advert. This will need to be agreed before hand.</p>';
            }
            
            $_SESSION['advert_payment']['n'] = $_POST['n'];
            $_SESSION['advert_payment']['e'] = $_POST['e'];
            
            
            $_SESSION['advert_payment']['startdate'] = $_POST['d'];
            $_SESSION['advert_payment']['image'] = $imageUrl;
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
            if ($_SESSION['advert_info']['option']->type == 2) {
                $wpdb->insert( 
                              'adverts_submissions', 
                              array( 
                                    'startdatetime'	=>	date('Y-m-d', strtotime(str_replace(',', '', $_POST['d']))) . ' 09:00:00', 
                                    'enddatetime'	=>	($_POST['d'] != '') ? date('Y-m-d', strtotime(str_replace(',', '', $_POST['d']) . '+' . $_SESSION['advert_info']['option']->quantity_duration)) . ' 09:00:00' : time(),
                                    'image'			=>	$imageUrl,
                                    'option_id'		=>	$_SESSION['advert_info']['option']->id,
                                    'status'		=>	'pending',
                                    'name'			=>	$_SESSION['advert_payment']['n'],
                                    'email'			=>	$_SESSION['advert_payment']['e'],
                                    'description'	=>	$_SESSION['advert_info']['option']->description,
                                    'duration'		=>	$_SESSION['advert_info']['option']->duration,
                                    'price'			=>	$_SESSION['advert_info']['option']->price,
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
            } else {
                
                
                $wpdb->insert( 
                              'adverts_submissions', 
                              array( 
                                    'startdatetime'	=>	date('Y-m-01', strtotime(str_replace(',', '', $_POST['d']))) . ' 09:00:00', 
                                    'enddatetime'	=>	($_POST['d'] != '') ? date('Y-m-01', strtotime(str_replace(',', '', $_POST['d']) . '+' . ($_SESSION['advert_info']['option']->numerical_days/30) . ' month')) . ' 09:00:00' : time(),
                                    'image'			=>	$imageUrl,
                                    'option_id'		=>	$_SESSION['advert_info']['option']->id,
                                    'status'		=>	'pending',
                                    'name'			=>	$_SESSION['advert_payment']['n'],
                                    'email'			=>	$_SESSION['advert_payment']['e'],
                                    'description'	=>	$_SESSION['advert_info']['option']->description,
                                    'duration'		=>	$_SESSION['advert_info']['option']->duration,
                                    'price'			=>	$_SESSION['advert_info']['option']->price,
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
            }
            
            
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
            
            //error message
            if (!isset($_SESSION['advert_log_id'])) {
                $wpdb->insert(
                              'adverts_logs',
                              array(
                                    'message'           =>  'Error in post data',
                                    'data'              =>  serialize($errorMessage),
                                    'client_name'       =>  $_POST['n'],
                                    'stage'             =>  'error-handling',
                                    'ip'                =>  $ip,
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
                                    'client_name'       =>  $_POST['n'],
                                    'stage'             =>  'error-handling',
                                    'ip'                =>  $ip,
                                    'client_business'   =>  $_POST['b'],
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
                                    '%d'
                                    )
                              );

                
            }

        }
    }    

	get_header(); 
    
    ?><section class="col1"><?php
        
        
        
        if (have_posts()) {
            while ( have_posts() ) {
                the_post();
				
                
                
                ?><div class="entry-content"><?php
                ?><article><header><h1 class="section-title"><span><?php
                        
                the_title();
                        
                ?></span><span class="stripe"></span></h1></header><?php
                
                the_content();
                    
                            
                ?></article></div><?php
            }
        }

    ?>
<script type="text/javascript" src="/wp-content/themes/love2/js/jquery_ui.js"></script><link href="/wp-content/themes/love2/css/date.css" rel="stylesheet"/>
<h2 class="section-title"><span>Enter your details below</span><span class="stripe"></span></h2>
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
    
    
    ?>
    <form action="/advertise/advert-details/" name="payment" class="aform fl w100" method="post" enctype="multipart/form-data">
    <table class="w100">
        <thead>
            <tr><th></th><th></th></tr>
        </thead>
        <tbody>
        <tr>
            <td><label for="start-date">Available Periods:<span style="font-size:10px;color:#454545;height:20px;padding-bottom:5px;display:block;">You can book your advert up to 4 months in advance</span></td>
            <?php
                
                if ($_SESSION['advert_info']['option']->type != 2) {
                    
                    $html = array();
                    
                    foreach ($availability as $month_period   =>  $available) {
                        if ($available === false) continue;

    
                            $start_date = date('Y-M-01', strtotime('+' . $month_period . 'months'));
    
                            $end_date = date('Y-M-01', strtotime('+' . ($month_period + $_SESSION['advert_info']['option']->numerical_days/30) . 'month'));
    
                            $html[] = '<option value="' .  date('l, d, F, Y', strtotime($start_date)) . '">' . date('l, j, F, Y', strtotime($start_date)) . ' - ' .  date('l, j, F, Y', strtotime($end_date)) . '</option>';

                        
                    }
                    

                
                    if (count($html) == 0) {
                        ?><td><p><span class="error">sold out!</span><br/> <a style="margin-left:10px;" href="../">try with a different length of time</a>.</p></td><?php
                    }
                }
                
                ?>


<?php if ($_SESSION['advert_info']['option']->type == 2) { //can book anytime! ?>
            <td><input <?php if (isset($errorMessage['d'])) {echo ' class="bd-e" ';} ?> type="text" placeholder="<?php echo date('l, d, F, Y', strtotime('+1day')) ?>" name="d" id="start-date" value="<?php echo isset($_POST['d']) ? htmlspecialchars($_POST['d']) : date('l, d, F, Y', strtotime('+1day')) ?>" /></td>
<?php } elseif (count($html) > 0) { ?>
        
        <td><select name="d">
            <?php echo implode('', $html); ?>
        </select></td>
<?php } ?>
        </tr>
<?php if ($_SESSION['advert_info']['option']->type == 2) { ?>
<?php
    
    if (isset($_POST['d'])) {
      
        $date = new DateTime(date('Y-m-d', strtotime($strDate)));
        
        $date->add(DateInterval::createFromDateString($_SESSION['advert_info']['option']->quantity_duration));

    }
    
    ?>
        <tr>
            <td style="padding-top:10px;"><label>End Date:</label></td>
            <td style="padding-top:10px;"><input type="text" readonly="readonly" value="<?php echo isset($_POST['d']) ? $date->format('l, d, F, Y') : date('l, d, F, Y', strtotime('+' . (1+$_SESSION['advert_info']['option']->numerical_days) . 'day')); ?>" id="end-date" /></td>
<?php
    
    }

    ?>
        </tr>

        <tr>
            <td style="width:250px"><label for="your-name">Your Name</label></td>
            <td><input <?php if (isset($errorMessage['n'])) {echo ' class="bd-e" ';} ?>  type="text" placeholder="your name" name="n" id="your-name" value="<?php echo isset($_POST['n']) ? $_POST['n'] : '' ?>" /></td>
        </tr>
        <tr>
            <td><label for="your-company">Business Name</label></td>
            <td><input <?php if (isset($errorMessage['b'])) {echo ' class="bd-e" ';} ?> type="text" placeholder="business name" name="b" id="your-company" value="<?php echo isset($_POST['b']) ? $_POST['b'] : '' ?>" /></td>
        </tr>
        <tr>
            <td><label for="your-email">Your Email</label></td>
            <td><input <?php if (isset($errorMessage['e'])) {echo ' class="bd-e" ';} ?> type="text" placeholder="your email" name="e" id="your-email" value="<?php echo isset($_POST['e']) ? $_POST['e'] : '' ?>" /></td>
        </tr>
        <tr>
            <td><label for="url">Link your advert should point to</label></td>
            <td><input <?php if (isset($errorMessage['url'])) {echo ' class="bd-e" ';} ?> type="text" id="url" name="url" placeholder="http://www" value="<?php echo isset($_POST['url']) ? $_POST['url'] : ''; ?>" /></td>
        </tr>
        <tr>
<td><label for="upload">Upload your ad<br><span style="font-size:10px;color:#454545;height:20px;padding-bottom:5px;"><?php echo $_SESSION['advert_info']['option']->description; ?></span></label><?php if (isset($errorMessage['image'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['image'] . '</span><span class="clear"></span>';} elseif (!empty($errorMessage)) { 
    echo '<span class="clear"></span><span class="error">Please reupload your image</span><span class="clear"></span>';
}	
    ?></td>
        <td><input <?php if (isset($errorMessage['image']) || !empty($errorMessage)) {echo 'class="bd-e"';} ?> type="file" name="image" id="upload" /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="next &gt; Your Details" class="fr" style="margin-right:40px;" /></td>
        </tr>
    </tbody>
</table>
<?php
    if ($_SESSION['advert_info']['option']->type == 2) { ?>
<script type="text/javascript">/*<![CDATA[*/
<?php
    
//    //set up the date available intervals
    $scr = array();
//    
//    foreach ($intervals as $k => $interval) {
//
//        if ($interval['available']) continue;
//        $month = date('j', strtotime('+' . $interval['n'] . ' months'));
//
//        $year = date('Y', strtotime('+' . $interval['n'] . ' months'));
//        for ($day = 1; $day <= date('t', strtotime('+' . ($interval['n']+1) . ' months')); $day++) {
//            //for this month, unavailable dates 
//            $scr[] = '"' . ($month . '-' . $day . '-' . $year) . '"';
//        }
//   
//    }
    
        $scr[] = '"' . date('j-n-Y') . '"';
    
    
   echo 'var unavailable_dates = [' . implode(',', $scr) . '];';
//
//    $scr = array();
//    
//    if (isset($availability) && !empty($availability[0])) {
//        for ($x = 0; $x < $availability[0]->days_to_start; $x++) {
//            $scr[] = '"' . date('n-j-Y', strtotime('+' . $x . 'days')) . '"';
//        }
//    }

    echo 'var inaccessible_dates = [' . implode(',', $scr) . '];';
    
 //prevent booking max booked periods    
        ?>console.log(unavailable_dates);


var max_date = new Date(<?php echo date('Y', strtotime('+4months')) ?>, <?php echo date('m', strtotime('+4months'))-1 ?>, <?php echo date('d', strtotime('+4months')) ?>);
function check_available(date) {
    //check to see if the date is in the unavailable selection?

    if (new Date() > date) {
        return [false, '', 'date has passed'];
    }
    var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();

    //check to see if the date is in the unavailable selection?
    if (unavailable_dates.length > 0) {

        for (i = 0; i < unavailable_dates.length; i++) {

            if ($.inArray((m+1) + '-' + d + '-' + y, unavailable_dates) != -1) {
                return [false, 'booked', 'maximum adverts have been booked for this date'];
            } else if (date > max_date) {
                return [false, '', 'can not select this date'];        
            }
        }
    }
    if (inaccessible_dates.length > 0) {
        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();

        if ($.inArray((m+1) + '-' + d + '-' + y, inaccessible_dates) != -1) {
            return [false, '', 'not available yet'];
        }
    }
    
    
    
    if (date > max_date) {
        return [false, '', 'can not select this date yet'];
    } else {
        return [true, '', 'available'];
    }
}
jQuery('input#start-date').datepicker({"dateFormat": "DD, d, MM, yy",  
                                      "minDate": new Date(<?php echo date('Y'); ?>, <?php echo date('m')-1; ?>, <?php echo date('d'); ?>),
                                      "maxDate": max_date,
                                      "constrainInput": true,
                                      "beforeShowDay": check_available,
                                      "onSelect": function(dateText, inst) {

                                                                             var day1 = jQuery("input#start-date").datepicker('getDate').getDate();

                                                                             var month1 = jQuery("input#start-date").datepicker('getDate').getMonth();             
                                                                             var year1 = jQuery("input#start-date").datepicker('getDate').getFullYear();
                                                                             var fullDate = year1 + "-" + month1 + "-" + day1;
                                                                             
                                                                            
                                      
                                                                             var d = new Date();
                                                                             d.setDate(day1);
                                                                             d.setMonth(month1);
                                                                             d.setYear(year1);

                                      
                                      
                                                                             d.setDate(d.getDate() + <?php echo $_SESSION['advert_info']['option']->numerical_days ?>);
                                                                            var month = 'December';
                                                                             switch (d.getMonth() + 1) {
                                                                                case '01':
                                                                                case 1:
                                                                                    month = 'January';
                                                                                break;
                                                                                case '02':
                                                                             case 2:
                                                                                    month = 'February';
                                                                                break;
                                                                                case '03':
                                                                             case 3:
                                                                                    month = 'March';
                                                                                break;
                                                                             case '04':
                                                                             case 4:
                                                                             month = 'April';
                                                                             break;
                                                                             case '05':
                                                                             case 5:
                                                                             month = 'May';
                                                                             break;
                                                                             case '06':
                                                                             case 6:
                                                                             month = 'June';
                                                                             break;
                                                                             case '07':
                                                                             case 7:
                                                                             month = 'July';
                                                                             break;
                                                                             case '08':
                                                                             case 8:
                                                                             month = 'August';
                                                                             break;
                                                                             case '09':
                                                                             case 9:
                                                                             month = 'September';
                                                                             break;
                                                                             case 10:
                                                                             month = 'October';
                                                                             break;
                                                                             case 11:
                                                                             month = 'November';
                                                                             break;
                                                                                default:
                                                                                    month = 'December';
                                                                                break;
                                                                             }
                                                                             
                                                                             var day = 'Sunday';
                                                                             
                                                                             switch (d.getDay()) {
                                                                             default:
                                                                             day = 'Sunday';
                                                                             break;
                                                                             case 1:
                                                                             day = 'Monday';
                                                                             break;
                                                                             case 2:
                                                                             day = 'Tuesday';
                                                                             break;
                                                                             case 3:
                                                                             day = 'Wednesday';
                                                                             break;
                                                                             case 4:
                                                                             day = 'Thursday';
                                                                             break;
                                                                             case 5:
                                                                             day = 'Friday';
                                                                             case 6:
                                                                             day = 'Saturday';
                                                                             }
                                                                             
                                                                             
                                                                             jQuery('input#end-date').val(day + ', ' + d.getDate() + ', ' + month + ', ' + d.getFullYear());
                                                                             
                                                                             }});/*]]>*/</script>
<?php } //end check on option type from session ?>
</form>
</div>
</section>
<script type="text/javascript">/*<![CDATA[*/!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");/*]]>*/</script>
<?php
   get_sidebar();
    ?><div id="fb-root"></div>
<script type="text/javascript">/*<![CDATA[*/
window.fbAsyncInit = function() {
    FB.init({appId: '259581140813728', status: true, cookie: true,xfbml: true});
    FB.Event.subscribe('edge.create', function(url) {
                       _gaq.push(['_trackSocial', 'facebook', 'like', url]);
                       });
    FB.Event.subscribe('edge.remove', function(url) {
                       _gaq.push(['_trackSocial', 'facebook', 'unlike', url]);
                       });
};
(function() {
 var e = document.createElement('script'); e.async = true;
 e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
 document.getElementById('fb-root').appendChild(e);
 }());
/*]]>*/</script><?php
    get_footer();