<?php global $options;
 		foreach ($options as $value) {
			 if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); 
		}
	} ?>

<div id="sidebar">
<?php if (isset ($_REQUEST['lee'])) { ?>
<div class="quicklinks">
<a class="theforums" href="/forum/" title="the photography parlour forums">the forums</a>
<span class="theshop">the shop</span>
</div>
<?php } else { ?>
<div id="searchform">		
		<form method="get" action="<?php bloginfo('home'); ?>/">
		<input name="s" type="text" class="inputs" id="s" value="<?php echo wp_specialchars($s, 1); ?>" size="32" />
		<input type="submit" class="go" value="SEARCH" />	     
		</form>
</div><!--searchform-->
<?php } ?>
<?php
/** get the big ads at random **/
$bigAd = $wpdb->get_results("SELECT image,url FROM AdRotator WHERE Type = 'Big' ORDER BY RAND() LIMIT 1",ARRAY_A);
?>
<div id="adblock-big">
<a href="<?php echo $bigAd[0]['url']; ?>" onclick="pageTracker._trackEvent('Adblock','Image: <?php echo $bigAd[0]['image']; ?>');"><img src="<?php echo "/wp-content/images/" . $bigAd[0]['image']; ?>" alt=""/></a>
</div><!--adblock-big-->
<?php
/** old code!
<div id="adblock-big">
<a href="<?php echo $apa_ad300x250destination; ?>"><img src="<?php if ($apa_ad300x250image == ""){echo bloginfo('template_directory'). '/images/ad-blocks-big.jpg'; } else {echo $apa_ad300x250image; } ?>"  /></a>
</div><!--adblock-big-->

**/
?>
<!--the twitter widget-->
<div id="twitter-entry">
<?php if ($apa_Twitter == ''){$apa_Twitter = 'mks6804';} ?>
<?php require_once(ABSPATH . 'wp-includes/class-snoopy.php');
$tweet   = get_option("lasttweet");
$url  = 'http://twitter.com/statuses/user_timeline/' .$apa_Twitter. '.json?count=20';
if ($tweet['lastcheck'] < ( mktime() - 60 ) ) {
  $snoopy = new Snoopy;
  $result = $snoopy->fetch($url);
  if ($result) {
    $twitterdata   = json_decode($snoopy->results,true);

    $i = 0;
    while ($twitterdata[$i]['in_reply_to_user_id'] != '') {
      $i++;
    }


    $twitterarr = explode(" ",$twitterdata[$i]["text"]);

    ?><p><?php

    foreach ($twitterarr as $bit) {

    if (stripos($bit,"@") !== false) {
        ?><a onclick="pageTracker._trackEvent('Twitter', 'View <?php echo str_replace("@","",$bit); ?>');" href="http://www.twitter.com/<?php echo str_replace("@","",$bit); ?>"><?php echo $bit; ?></a> <?php
    } elseif (stripos($bit,"http://") !== false) {
        ?><a href="<?php echo $bit; ?>" onclick="pageTracker._trackEvent('Twitter', 'View <?php echo $bit; ?>');" ><?php echo $bit; ?></a> <?php
    } else {
        echo $bit . " ";
    }

    }


   } else {
echo "Twitter Not Responding";
   }
} else {

?><p><?php

echo $tweet['data'];

}
    ?></p><?php

/** old code
    $pattern  = '/@([a-zA-Z]+)/';
    $replace  = '<a href="http://twitter.com/'.strtolower('1').'">@1</a>';
    $output   = preg_replace($pattern,$replace,$twitterdata[$i]["text"]); 
	$output = make_clickable($output);  
    $tweet['lastcheck'] = mktime();
    $tweet['data']    = $output;
    $tweet['rawdata']  = $twitterdata;
    $tweet['followers'] = $twitterdata[0]['user']['followers_count'];
    update_option('lasttweet',$tweet);
  } else {
    echo "Twitter API not responding.";
  }

} else {
  $output = $tweet['data'];
}
echo "<p>".$output."</p>";
**/
?>
</div><!--twitter widget-->
<div id="adblock-small">
<ul>
	<li><a href="<?php echo $apa_1_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_1_ad_image == ""){echo bloginfo('template_directory'). '/images/1st-ad-blocks.jpg'; } else {echo $apa_1_ad_image; } ?>"/></a></li>
	<li><a href="<?php echo $apa_2_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_2_ad_image == ""){echo bloginfo('template_directory'). '/images/2nd-ad-blocks.jpg'; } else {echo $apa_2_ad_image; } ?>"  /></a></li>
	<li><a href="<?php echo $apa_3_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_3_ad_image == ""){echo bloginfo('template_directory'). '/images/3rd-ad-blocks.jpg'; } else {echo $apa_3_ad_image; } ?>"  /></a></li>
	<li><a href="<?php echo $apa_4_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_4_ad_image == ""){echo bloginfo('template_directory'). '/images/4th-ad-blocks.jpg'; } else {echo $apa_4_ad_image; } ?>"  /></a></a></li>
</ul>
</div><!--adblock-small-->


<?php include (TEMPLATEPATH . '/tabbed-container.php');?>

	<?php if ( !function_exists('dynamic_sidebar')
		|| !dynamic_sidebar('sidebar') ) : ?>
	<?php endif; ?>
</div><!--sidebar-->
<script type="text/javascript">/*<![CDATA[*/Cufon.replace('.sidebar-row h3');Cufon.now();/*]]>*/</script>