<?php

/**

!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

THIS FILE SHOULD BE EDITED IN TEXT EDIT!

!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


**/

?><div class="twitter-entry"><?php

/** update only every 5 minutes **/

global $wpdb;

unset($info);

$info = $wpdb->get_results("SELECT lasttime FROM twitfeed ORDER BY lasttime ASC",ARRAY_A);


if ((count($info) < 5) || ((int)($info[0]["lasttime"]) < (time() - 320))) {

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, "http://twitter.com/statuses/user_timeline/photoparlour.xml?count=5");
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
	$xmlData = new SimpleXMLElement(curl_exec($curl_handle));
	curl_close($curl_handle);

if (!$xmlData->error) {
	$wpdb->query("UPDATE twitfeed SET lasttime = '" . time() . "'");
	$twitcount = 1;
	foreach ($xmlData->status as $stat) {
		$twitterarr = explode(" ",(string)($stat->text));

		$text = "";
		foreach ($twitterarr as $bit) {
			if (stripos($bit,"@") !== false) {
			       $text .= "<a onclick=\"pageTracker._trackEvent('Twitter', 'View " . str_replace("@","",$bit) . "');\" href=\"http://www.twitter.com/" . str_replace("@","",$bit) . "\">" . $bit . "</a> ";
			} elseif (stripos($bit,"http://") !== false) {
			        $text .= "<a href=\"" . $bit . "\" onclick=\"pageTracker._trackEvent('Twitter', 'View " .  $bit . "');\">" . $bit . "</a> ";
			} else {
			        $text .= $bit . " ";
			}
		}
		unset($twitterarr);

		//need to check to see if the number of tweet feed stored is 5 or less as only storing 5 currently!
		if ($twitcount <= count($info)) {
			$wpdb->query($wpdb->prepare("UPDATE twitfeed SET text = '" . $wpdb->escape($text) . "', lasttime = '" . time() . "' WHERE id = '" . $twitcount . "'"));
		} else {
			$wpdb->query($wpdb->prepare("INSERT INTO twitfeed (text,lasttime) VALUES ('" . $wpdb->escape($text) . "','" . time() . "')"));
		}
		$twitcount++;

		?><p class="twit"><?php echo $text; ?></p><?php
	}
	unset($xmlData);
	unset($twitcount);
	unset($info);

} else {

	foreach ($wpdb->get_results($wpdb->prepare("SELECT text FROM twitfeed ORDER BY lasttime DESC"),ARRAY_A) as $txt) {
		?><p class="twit"><?php
			echo $txt["text"];
		?></p><?php
	}

}
} else {

	foreach ($wpdb->get_results("SELECT text FROM twitfeed",ARRAY_A) as $txt) {
		?><p class="twit"><?php
			echo $txt["text"];
		?></p><?php
	}

}

?>
</div>
<script type="text/javascript">/*CDATA[*/
jQuery('div.twitter-entry').ready(function () {
jQuery('div.twitter-entry .twit').each(function (i) {
jQuery(this).css("opacity","0").css("display","none").find("a").attr("target","_blank");
});
jQuery('div.twitter-entry .twit:first').addClass("active").css("display","block").animate({opacity:"1"},2250);
setInterval(function () {jQuery.mntwit();},7000);
jQuery.mntwit = function () {
if (!jQuery('div.twitter-entry .twit:last').hasClass("active")) {
jQuery('div.twitter-entry .active').animate({opacity:"0"},1250);setTimeout(function () {jQuery('div.twitter-entry .active').css("display","none").removeClass("active").next("p").addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
} else {
jQuery('div.twitter-entry .active').animate({opacity:"0"},1250);setTimeout(function () {jQuery('div.twitter-entry .active').removeClass("active").css("display","none");jQuery('div.twitter-entry .twit:first').addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
}
}
});
/*]]>*/</script>