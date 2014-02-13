<?php global $options;
 		foreach ($options as $value) {
			 if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); 
		}
	} ?>

<div id="sidebar">
<div id="searchform">		
		<form method="get" action="<?php bloginfo('home'); ?>/">
		<input name="s" type="text" class="inputs" id="s" value="<?php echo wp_specialchars($s, 1); ?>" size="32" />
		<input type="submit" class="go" value="SEARCH" />	     
		</form>
</div>

<?php
/** get the big ads at random **/
$bigAd = $wpdb->get_results("SELECT image,url FROM AdRotator WHERE Type = 'Big' AND status = '1' ORDER BY RAND() LIMIT 1",ARRAY_A);
?>
<div id="block-big">
<a href="<?php echo $bigAd[0]['url']; ?>" onclick="pageTracker._trackEvent('Adblock','Image: <?php echo $bigAd[0]['image']; ?>');"><img src="<?php echo "/wp-content/images/" . $bigAd[0]['image']; ?>" alt=""/></a>
</div><!--adblock-big-->
<?php
/** old code!
<div id="adblock-big">
<a href="<?php echo $apa_ad300x250destination; ?>"><img src="<?php if ($apa_ad300x250image == ""){echo bloginfo('template_directory'). '/images/ad-blocks-big.jpg'; } else {echo $apa_ad300x250image; } ?>"  /></a>
</div><!--adblock-big-->

**/
?>
<?php

/** quick links **/

?>
<div class="quicklinks">
<a class="theforums" href="/forum/" title="the photography parlour forums"></a>
<!--a class="theadvertise" href="/info-for-advertisers/" title="advertise on the photography parlour"></a-->
<a title="the photographyparlour shop" href="/shop/" class="theshop"></a>
</div>
<?php include (TEMPLATEPATH . '/upcoming-events.php'); ?>
<?php

    /** facebook **/

?>
<iframe src="http://www.facebook.com/plugins/likebox.php?id=374880504819&amp;width=292&amp;connections=10&amp;stream=false&amp;header=true&amp;height=287" scrolling="no" frameborder="0" style="margin-top:10px;border:none; overflow:hidden; width:292px; height:287px;"></iframe>
<!--the twitter widget-->
<?php 

//include(TEMPLATEPATH . "/twittersidebar.php");

/*******

small ad blocks

********/


?>
<div id="block-small">
<ul>
<?php

//get the maximum number of groups added into the database!

$groups = $wpdb->get_results("SELECT DISTINCT(grouping) FROM AdRotator WHERE status = '1' AND grouping <> 'NULL'",ARRAY_A);

$c = count($groups)-1;

$r = rand(0,$c);

foreach ($wpdb->get_results("SELECT image,url,friendlyname,purchaser FROM AdRotator WHERE Type = 'small' AND grouping = '" . $groups[$r]['grouping'] . "' ORDER BY id DESC",ARRAY_A) as $small) {

unset($groups);

?>
<li><a href="<?php echo $small['url']; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $small['friendlyname'] . " " . $small['purchaser']; ?> ');"><img style="width:125px;height:125px;" src="<?php echo "/wp-content/images/" . $small['image']; ?>" alt=""/></a></li>
<?php 
}
?>
</ul>
</div><!--adblock-small-->
<?php


unset($r);


/********

old code

<div id="adblock-small">
<ul>
	<li><a href="<?php echo $apa_1_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_1_ad_image == ""){echo bloginfo('template_directory'). '/images/1st-ad-blocks.jpg'; } else {echo $apa_1_ad_image; } ?>"/></a></li>
	<li><a href="<?php echo $apa_2_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_2_ad_image == ""){echo bloginfo('template_directory'). '/images/2nd-ad-blocks.jpg'; } else {echo $apa_2_ad_image; } ?>"  /></a></li>
	<li><a href="<?php echo $apa_3_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_3_ad_image == ""){echo bloginfo('template_directory'). '/images/3rd-ad-blocks.jpg'; } else {echo $apa_3_ad_image; } ?>"  /></a></li>
	<li><a href="<?php echo $apa_4_ad_destination; ?>" onclick="pageTracker._trackEvent('Adblock','Small block: <?php echo $apa_1_ad_destination; ?>');"><img src="<?php if ($apa_4_ad_image == ""){echo bloginfo('template_directory'). '/images/4th-ad-blocks.jpg'; } else {echo $apa_4_ad_image; } ?>"  /></a></li>
</ul>
</div><!--adblock-small-->

*********/



/** book of the month 

$results = $wpdb->get_results("SELECT image,url,friendlyname,purchaser FROM AdRotator WHERE type = 'BOTM' AND status = '1'",ARRAY_A);

if (count($results) > 0) {

include_once("/home/wwwtpp/www/shop/a/query.php");
$awq = new query();

$xml = new SimpleXMLElement(file_get_contents($awq->itemlookup($results[0]['purchaser'])));
$item = $xml->Items->Item[0];
unset($xml);

?>
<div class="botm">
<h3>Book of the Month</h3>
<div class="w">
<?php if (($results[0]['image'] !== "") && ($results[0]['image'] !== null)) {
?>
<div style="margin-top:10px;">
<a href="<?php echo $results[0]['url']; ?>"><img src="/wp-content/images/books/<?php echo $results[0]['image']; ?>" alt=""/></a>
</div>
<?php }
if (($results[0]['friendlyname'] !== "") && ($results[0]['friendlyname'] !== null)) { ?>
<div style="margin-top:10px;">
<a  href="<?php echo $results[0]['url']; ?>"><?php echo $results[0]['friendlyname']; ?></a>
</div>
<?php
} else { ?>
<div style="margin-top:10px;">
<a  href="<?php echo $results[0]['url']; ?>"><?php echo $item->ItemAttributes->Title; ?></a>
</div>
<?php
}

$prices = false;
if ($item->OfferSummary->LowestNewPrice->FormattedPrice) {
?><div style="margin-top:10px;"><a href="<?php echo $results[0]['url']; ?>">Lowest New Price: <?php
echo $item->OfferSummary->LowestNewPrice->FormattedPrice;
$prices = true;
?></a></div><?php
}
if ($item->OfferSummary->LowestUsedPrice->FormattedPrice) {
$prices = true;
?><div style="margin-top:10px;"><a href="<?php echo $results[0]['url']; ?>">Lowest Used Price: <?php
echo $item->OfferSummary->LowestUsedPrice->FormattedPrice;
$prices = true;
?></a></div><?php
}

if ($prices === 0) {
if ($item->ItemAttributes->ListPrice->Amount->FormattedPrice) {
?><div style="margin-top:10px;"><a href="<?php echo $results[0]['url']; ?>">List Price: <?php
echo $item->ItemAttributes->ListPrice->Amount->FormattedPrice;
$prices = true;
?></a></div><?php
}
}

unset($item);
unset($prices);


?>
<div style="margin-top:10px;">
<a href="<?php echo $results[0]['url']; ?>"><img src="/wp-content/images/purchasefromamazon.jpg" alt="purchase from amazon"></a>
</div>
</div>
</div>
<script type="text/javascript">/*<![CDATA[*/
Cufon.replace("div.botm h3",{fontSize:"35px"});Cufon.now();
/*]]>*/</script>
<?php
}
*/

?>
<?php include (TEMPLATEPATH . '/tabbed-container.php');?>
<?php include (TEMPLATEPATH . '/textlinks-container.php');?>

	<?php if ( !function_exists('dynamic_sidebar')
		|| !dynamic_sidebar('sidebar') ) : ?>
	<?php endif; ?>
</div><!--sidebar-->
<script type="text/javascript">/*<![CDATA[*/Cufon.replace('.sidebar-row h3');Cufon.now();/*]]>*/</script>