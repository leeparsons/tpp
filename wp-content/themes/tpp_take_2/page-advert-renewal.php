<?php
/*
 *  Template Name: Advert Renewal
 */


if (!session_id()) {
    session_start();
}

if (isset($_REQUEST['dev'])) {
    $_SESSION['dev'] = 1;
}

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



    $selected_option = $wpdb->get_results($wpdb->prepare("SELECT * FROM advert_options WHERE id = %d", $query[0]->option_id));

    //force to one month extension only
    $res = $wpdb->get_results("SELECT * FROM advert_options WHERE type IN (SELECT type FROM advert_options ao INNER JOIN adverts_submissions a ON a.option_id = ao.id WHERE option_id = " . $query[0]->option_id . ") AND ordering = 1");


}




    $availability = array();

    for ($x = 0; $x < 5; $x++) {
        //can select up to four months ahead
        //months to cycle through to find availability
        $months_from_now_to_start_of_period[] = $x;
    }


if (is_array($res) && !empty($res)) {
    $type_id = $res[0]->type;
} else {
    exit('Unable to retrieve advert details');
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
    $end_of_period = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period + ($res[0]->numerical_days/30)) . ' month'));

    if ($res[0]->numerical_days == 30) {



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
            type_id,
            $start_of_period,
            $end_of_period,
            $type_id,
            $query[0]->option_id
        );


    } else {


        $start_of_period_plus_month = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period+1) . 'month'));

        $end_of_period_minus_month = date('Y-m-01 09:00:00', strtotime('+' . ($month_from_now_to_start_of_period + ($res[0]->numerical_days/30)) . 'month'));

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
            $type_id,
            $end_of_period_minus_month,
            $start_of_period_plus_month,
            $type_id,
            $query[0]->option_id
        );

    }


    $sql = $wpdb->prepare(
        $sql,
        $swap_array
    );
//echo $sql . '<br><br>';

    $booked_matrix = $wpdb->get_results($sql);

    if (!isset($booked_matrix[1])) {
        //no adverts booked

        $available_date = new DateTime(date('Y-M-01', strtotime($booked_matrix[0]->available_from)));

        $tmp_start_date = new DateTime(date('Y-M-01', strtotime('+' . ($month_from_now_to_start_of_period + ($res[0]->numerical_days/30)) . 'months')));

        $availability[$month_from_now_to_start_of_period] = ($available_date < $tmp_start_date);
        $diff = date_diff($available_date, $tmp_start_date);
        $diff = $diff->format('%R');

        //$availability[$month_from_now_to_start_of_period] = $diff == '+';
//            $availability[$month_from_now_to_start_of_period] = false;


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

$intervalstmp = array(
    array('n'            =>  30,
        'available'    =>  false
    ),
    array('n'            =>  60,
        'available'    =>  false
    ),
    array('n'            =>  90,
        'available'    =>  false
    ),
    array('n'            =>  120,
        'available'    =>  false
    ),
    array('n'            =>  150,
        'available'    =>  false
    ),
    array('n'            =>  180,
        'available'    =>  false
    )

);
$intervals = array();
foreach ($intervalstmp as $interval) {

    $sql = $wpdb->prepare(
        "SELECT max_bookable, id FROM
                              
                              adverts_submissions AS ads
                              
                              INNER JOIN advert_options AS ao ON ao.id = ads.option_id
                              LEFT JOIN advert_options_booking_settings AS abs ON abs.type_id = ao.type
                              
                              WHERE status = 'complete'
                              AND type = %d
                              
                              AND enddatetime < NOW() + INTERVAL %d day AND enddatetime > NOW() + INTERVAL %d day
                              
                              HAVING max_bookable <= COUNT(id)
                              ",
        array(
            $res[0]->type,
            $interval['n'],
            $interval['n'] - 30
        )
    );
    $number_available = $wpdb->get_results($sql);


    if (!empty($number_available[0])) {
        //the number booked in the next 30 days meets the maximum available.
        //So figure out when the next date available is!
        //not available!
        $intervals[] = array(
            'n'            =>  $interval['n'],
            'available'    =>  false
        );
    } else {
        //can book anytime

        $intervals[] = array(
            'n'    =>  $interval['n'],
            'available'    =>  true
        );
    }

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


        if (!isset($_POST['d'])) {
            $errorMessage['d'] = 'Please enter a start date in the future';
        } else {
            $strDate = str_replace(',', '', $_POST['d']);

            foreach ($intervals as $interval) {

                if (!$interval['available'] && strtotime($strDate) > strtotime('+' . $interval['n'] . 'days') && strtotime($strDate) < strtotime('+' . ($interval['n']+30) . 'days')) {
                    $errorMessage['d'] = 'Please select an available date';
                    break;
                }
            }

            if ($errorMessage['d'] == '') {

                $dateTime = strtotime($strDate);
                if ($strDate == '' || ($dateTime == 0 || $dateTime <= time())) {
                    $errorMessage['d'] = 'Please enter a start date in the future';
                } elseif ($query[0]->option_id <= 3 && $dateTime > strtotime('+45day')) {
                    $errorMessage['d'] = 'Please enter a start date no more than 45 days in the future (needs to be <br/>before ' . date('d, M, Y', strtotime('+46days')) . ')';
                }
            }
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

                            if ($w != $selected_option[0]->max_width || $h != $selected_option[0]->max_height) {
                                $errorMessage['image'] = 'The image needs to be exactly ' . $selected_option[0]->max_width . 'x' . $selected_option[0]->max_height . ' pixels';
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

            set_time_limit(120);
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip=$_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip=$_SERVER['REMOTE_ADDR'];
            }

            $wpdb->insert(
                'adverts_logs',
                array(
                    'message'           =>  'Starting advert renewal',
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


            //$durationQuery = $wpdb->get_results($wpdb->prepare("SELECT quantity_duration FROM advert_options WHERE id = %s", $_POST['t']));

            //get the option_id from the database for the minimum amount of time


            //sort out the paypal stuff!
            $adInfoArr = array(
                'name'			=>	$newName,
                'business'		=>	$newBusiness,
                'email'			=>	$newEmail,
                'url'			=>	$newUrl,
                'image'			=>	$imageUrl,
                'edit_token'		=>	$_POST['et'],
                'aid'			=>	$_POST['aid'],
                'option_id'		=>	$res[0]->id,
                'logid'			=>	$wpdb->insert_id,
                'startdate'		=>	$_POST['d'],
                'quantity_duration'	=>	$res[0]->quantity_duration//$durationQuery[0]->quantity_duration
            );


            $adInfo = serialize($adInfoArr);

            //$apiUser = 'rosie_api1.rosieparsons.com';
            //$apiPWD = 'K5ULLT635CMGSQTY';
            //$apiSig = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AYuaOvrjEMsmQ-G05RxBaojuwCj3';
            //$apiUrl  = 'https://api-3t.paypal.com/nvp';

            //get the prices from the database!


            $query2 = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT renewal_cost, duration, description FROM advert_options WHERE id = %s",
                    $query[0]->option_id												   )
            );


            $fields = array(
                'USER'						=>	$apiUser,
                'PWD'						=>	$apiPWD,
                'SIGNATURE'					=>	$apiSig,
                'VERSION'					=>	'72.0',
                'PAYMENTACTION'					=>	'AUTHORIZATION',
                'METHOD'					=>	'SetExpressCheckout',
                'PAYMENTREQUEST_0_NUMBER'			=>	'123',
                'PAYMENTACTION'					=>	'Sale',
                'PAYMENTREQUEST_0_AMT'				=>	number_format($res[0]->renewal_cost),
                'PAYMENTREQUEST_0_ITEMAMT'			=>	number_format($res[0]->renewal_cost),
                'PAYMENTREQUEST_0_TAXAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_SHIPPINGAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_HANDLINGAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_INSURANCEAMT'			=>	'0.00',
                'PAYMENTREQUEST_0_CURRENCYCODE'			=>	'GBP',
                'L_PAYMENTREQUEST_0_NAME0'			=>	'Advert Signup: ' . $res[0]->duration,
                'L_PAYMENTREQUEST_0_DESCRIPTION0'		=>	$res[0]->description,
                'L_PAYMENTREQUEST_0_AMT0'			=>	number_format($res[0]->renewal_cost),
                'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'	=>	'rosie@rosieparsons.com',
                'CANCELURL'					=>	'http://' . $_SERVER['SERVER_NAME'] . '/advertise/renewal-cancelled',
                'RETURNURL'					=>	'http://' . $_SERVER['SERVER_NAME'] . '/advertise/renewal-complete',
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
                    'message'           =>  'Starting advert renewal payment',
                    'stage'             =>  'pending-payment',
                    'ip'                =>  $ip,
                    'user_agent'        =>  $_SERVER['HTTP_USER_AGENT']
                ),
                array(
                    'id'         =>  $adInfoArr['logid']
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
            //force to 1 month or 30 days
            $_SESSION['advert_payment']['token'] = $token;
            $_SESSION['advert_payment']['serialized'] = $adInfo;
            header('location: ' . $checkOutUrl . '&token=' . $token);
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





    ?></div><h2 class="section-title"><span>Renewal Details</span><span class="stripe"></span></h2><div class="entry-content aform-complete"><?php




    if (!empty($query)) {


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




        if ((int)$query[0]->enddate < time()) {
            $renewable = true;
        } elseif ((int)$query[0]->startdate < time() && (int)$query[0]->enddate > time() && (int)$query[0]->enddate <= time() + 30*24*60*60) {
            $renewable = true;
        } elseif ((int)$query[0]->startdate < time() && (int)$query[0]->enddate < time()) {
            $renewable = true;
        } else {
            $renewable = false;
            echo '<p class="error">Your advert can not be renewed yet, please come back on ' . date('l, j, M, Y', (int)$query[0]->enddate - 60*60*24*30) . '</p>';
        }


        ?><p><span>Name:</span> <?php




        if (isset($errorMessage['n'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['n'] . '</span><span class="clear"></span>';}


        ?><input type="text" name="n" value="<?php

            if (isset($_POST['n'])) {
                echo htmlspecialchars(stripslashes($_POST['n']));
            } else {
                echo htmlspecialchars(stripslashes($query[0]->name));
            }

            ?>"/><?php


        ?></p>

    <p><span>Business name:</span><?php

        if (isset($errorMessage['b'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['b'] . '</span><span class="clear"></span>';}


        ?><input type="text" name="b" value="<?php

            if (isset($_POST['b'])) {
                echo htmlspecialchars(stripslashes($_POST['b']));
            } else {
                echo htmlspecialchars(stripslashes($query[0]->business));
            }

            ?>"/><?php


        ?></p>

    <p><span>Email:</span><?php



        if (isset($errorMessage['e'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['e'] . '</span><span class="clear"></span>';}


        ?><input type="text" name="e" value="<?php
            if (isset($_POST['e'])) {
                echo htmlspecialchars(stripslashes($_POST['e']));
            } else {
                echo htmlspecialchars(stripslashes($query[0]->email));
            }
            ?>"/><?php




        ?></p>




    <p><span>Url to link to:</span><?php



        if (isset($errorMessage['url'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['url'] . '</span><span class="clear"></span>';}



        ?><input type="text" name="url" value="<?php

            if (isset($_POST['url'])) {
                echo htmlspecialchars(stripslashes($_POST['url']));
            } else {

                echo htmlspecialchars(stripslashes($query[0]->url));

            }
            ?>"/></p>

    <p><span class="fl">Image:</span><?php

        echo ($query[0]->image == '') ? 'Not uploaded. You will need to confirm this before your advert goes live!' : '<img src="' . $query[0]->image . '"/>';


        ?></p><?php



        if ($renewable === true) {




            echo '<span class="clear"></span>';

            echo '<p><span style="width:100%">Upload new image ('. $selected_option[0]->max_width .'px wide by ' . $selected_option[0]->max_height . 'px high):</span></p>';

            if (isset($errorMessage['image'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['image'] . '</span><span class="clear"></span>';}

            echo '<p><input type="file" name="image"/><input type="hidden" name="oimage" value="' . $query[0]->image . '"</p>';



            ?>


        <p><span style="width:100%">Renew option</span></p>



            <?php

            if ($query[0]->option_id <= 3) {

                $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM advert_options WHERE type = %d AND ordering = 1 ORDER BY price ASC, description ASC", $selected_option[0]->type));
                $prices = array();

                foreach ($result as $option) {
                    $prices[$option->id] = $option;
                }


                foreach ($prices as $k => $option) {

                    ?>
                <p>
                    <input checked="checked" <?php if ((isset($_POST['t']) && $_POST['t'] == $k) || (!isset($_POST['t']) && $k == 1)) {echo 'checked="checked"';} ?> type="radio" id="<?php echo str_replace(' ', '-', $option->duration); ?>" name="t" value="<?php echo $k; ?>">
                    <label for="<?php echo str_replace(' ', '-', $option->duration); ?>">£<?php echo $option->renewal_cost; ?> for <?php echo $option->duration; ?></label>
                </p>



                    <?php

                }

                ?> <p><span>Start Date:</span><?php

                if (isset($errorMessage['d'])) {echo '<span class="clear"></span><span class="error">' . $errorMessage['d'] . '</span><span class="clear"></span>';}


                if ((int)$query[0]->enddate < time()) {
                    echo 'Your advert can be renewed, please select a date to renew your advert from (subject to availability)';
                    ?><input type="text" id="start-date" name="d" value="<?php echo date('l, d, F, Y') ?>"/><?php
                } elseif ((int)$query[0]->startdate < time() && (int)$query[0]->enddate > time() && (int)$query[0]->enddate <= time() + 30*24*60*60) {
                    echo 'Your advert can be renewed, please select a date to renew your advert from (subject to availability)';
                    ?><input type="text" id="start-date" name="d" value="<?php echo date('l, d, F, Y') ?>"/><?php
                } elseif ((int)$query[0]->startdate < time() && (int)$query[0]->enddate < time()) {
                    echo 'Your advert can be renewed, please select a date to renew your advert from (subject to availability)';
                    ?><input type="text" id="start-date" name="d" value="<?php echo date('l, d, F, Y') ?>"/><?php
                } else {
                    echo '</p><p class="error">Your advert can not be renewed yet, please come back on ' . date('l, d, F, Y', (int)$query[0]->enddate - 60*60*24*30) . '';
                }

                ?></p><?php

                echo '<p><input type="submit" class="fr" value="Renew"/><a href="/advertise/advert-information/?aid=' . $_GET['aid'] . '" class="fr pseudo-submit" style="margin-right:10px;">Cancel</a></p></form>';

            } else {

                //top 6 or large

                $html = array();

                foreach ($availability as $month_period   =>  $available) {
                    if ($available === false) continue;


                    $start_date = date('Y-M-01', strtotime('+' . $month_period . 'months'));

                    $end_date = date('Y-M-01', strtotime('+' . ($month_period + $res[0]->numerical_days/30) . 'month'));

                    $html[] = '<option value="' .  date('l, d, F, Y', strtotime($start_date)) . '">' . date('l, j, F, Y', strtotime($start_date)) . ' - ' .  date('l, j, F, Y', strtotime($end_date)) . '</option>';


                }



                if (count($html) == 0) {
                    ?><td><p><span class="error">sold out!</span><br/> <a style="margin-left:10px;" href="mailto:advertise@loveluxeblog.com">Contact us to discuss advertising options</a>.</p></td><?php
                } else {

                    ?><select name="d">
                    <?php echo implode('', $html); ?>
                </select><?php

                }
            echo '<p><input type="submit" class="fr" value="Renew"/><a href="/advertise/advert-information/?aid=' . $_GET['aid'] . '" class="fr pseudo-submit" style="margin-right:10px;">Cancel</a></p></form>';
            }



        }

    } else {
        echo 'Your advert is not yet renewable.';
    }
    ?>		</div>

</article><?php


}

?><script type="text/javascript" src="/wp-content/themes/love2/js/jquery_ui.js"></script><link href="/wp-content/themes/love2/css/date.css" rel="stylesheet"/>
<script type="text/javascript">/*<![CDATA[*/
<?php
//set up the date available intervals
$scr = '';

foreach ($intervals as $k => $interval) {

if ($interval['available']) continue;

for ($x = $interval['n']; $x <= $interval['n']+30; $x++) {

$scr .= ($scr == '') ? '"' . date('n-j-Y', strtotime('+' . $x . 'days')) . '"' : ',"' . date('n-j-Y', strtotime('+' . $x . 'days')) . '"';

}

}

echo 'var unavailable_dates = [' . $scr . '];';

//prevent booking max booked periods
?>
var max_date = new Date(<?php echo date('Y', strtotime('+45days')) ?>, <?php echo date('m', strtotime('+45days'))-1 ?>, <?php echo date('d', strtotime('+45days')) ?>);
function check_available(date) {
    //check to see if the date is in the unavailable selection?
    if (unavailable_dates.length > 0) {
        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        for (i = 0; i < unavailable_dates.length; i++) {
            if ($.inArray((m+1) + '-' + d + '-' + y,unavailable_dates) != -1 || new Date() > date) {
                return [false, 'booked', 'maximum adverts have been booked for this date'];
            } else if (date > max_date) {
                return [true, '', 'can not select this date yet'];        
            } else {
                return [true, '', 'available'];            
            }
        }
    }
    if (date > max_date) {
        return [true, '', 'can not select this date yet'];
    } else {
        return [true, '', 'available'];
    }
}
jQuery(document).ready(function() {
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
                                      
                                      
                                      
                                      d.setDate(d.getDate() + <?php echo $selected_option[0]->numerical_days ?>);
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
                                      
                                      }});
});
/*]]>*/</script></section><?php

unset($_SESSION['advert_payment']);

get_sidebar();

get_footer();
