<?php
if (isset($_POST['_wpcf7_mail_sent'])) {
	$expire=time()+60*60*24*30*2;
	setcookie("surveySent2", "true", $expire, '/');
}

/*************
!!!!!!!IMPORTANT!!!!!!!!

If you change the styles then edit the stylesheet querystring here!

*************/

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
	<link rel="stylesheet" href="/wp-content/themes/aparatus/css.php?a=16" type="text/css" media="screen" />
	<?php global $options;
 		foreach ($options as $value) {
			 if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); 
		}
	} ?>            
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<!--[if gte IE 7]>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie8.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>
<![endif]-->
<!--[if lt IE 7]>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie6.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>	
<![endif]-->
<!--[if IE 7]>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie7.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>
<![endif]-->

	<!--Css SmoothGallery-->
	<link rel="stylesheet" href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url')); ?>/gallery-css/jd.gallery.css" type="text/css" media="screen"/>
	<?php wp_enqueue_script("jquery"); ?>
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url')); ?>/scripts/mootools.js"></script>	
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url')); ?>/scripts/jd.gallery.js"></script>
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url')); ?>/scripts/mootabs1.2.js"></script>
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url'));; ?>/scripts/cufon-yui.js"></script>
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_url')); ?>/scripts/League_Gothic_400.font.js"></script>
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
	<script type="text/javascript">/*<![CDATA[*/Cufon.replace('a#logo',{fontSize:'98px',lineHeight:'98px'});/*]]>*/</script>
	<script type="text/javascript">/*<![CDATA[*/
jQuery(document).ready(function () {
jQuery('#nav2 ul li').each(function (i) {
	jQuery(this).mouseenter(function () {
		jQuery(this).addClass("sfhover");
	});
	jQuery(this).mouseleave(function () {
		jQuery(this).removeClass("sfhover");
	});
});
});
<?php /* old code 
<?php } else { ?>
		sfHover = function() {
			var sfEls = document.getElementById("nav2").getElementsByTagName("LI");
			for (var i=0; i<sfEls.length; i++) {
				sfEls[i].onmouseover=function() {
					this.className+=" sfhover";
				}
				sfEls[i].onmouseout=function() {
					this.className=this.className.replace(new RegExp(" sfhoverb"), "");
				}
			}
		}
		if (window.attachEvent) window.attachEvent("onload", sfHover);
<?php } ?>
<?php */ ?>
/*]]>*/
	</script>
	<script type="text/javascript" charset="utf-8">/*<![CDATA[*/
			window.addEvent('domready', init);
			function init() {
				myTabs1 = new mootabs('myTabs', {height: '320px', width: '300px', changeTransition: Fx.Transitions.Back.easeOut, mouseOverClass: 'over'});
				
			}
	/*]]>*/</script>
<meta name="google-site-verification" content="QlCldWYkiMZwNFqnwTJ-dPgrwhlKK7S8VXQniIfqoD8" />
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<?php wp_head();flush(); ?>
</head>
<body>
<div class="bodywrapper">
<div id="header"><?php

/** header **/

?><div id="twitter-badge"><a href="http://twitter.com/<?php if ($apa_Twitter == ''){echo '_fearlessflyer';}else{echo $apa_Twitter;} ?>" onclick="pageTracker._trackEvent('Twitter', 'Follow me');" >follow</a></div><!--twitter-badge-->
<?php if (is_front_page()) { ?><h1><?php } ?><a id="logo" href="<?php bloginfo('url');?>"><?php bloginfo('name');?></a><?php if (is_front_page()) { ?></h1><?php } ?>
<?php if (is_front_page()) { ?><h2 style="float:left;margin:-20px 0 10px 10px;font-family:verdana,arial,helvetica;font-size:10px;font-weight:bold;">Where aspiring photographers <span style="font-size:11px;font-style:italic;color:#750000">click</span> with the pros</h2><?php } else { ?>
<p style="float:left;margin:-20px 0 10px 10px;font-family:verdana,arial,helvetica;font-size:10px;font-weight:bold;">Where aspiring photographers <span style="color:#750000;font-style:italic;font-size:11px;">click</span> with the pros</p>
<?php } ?>
<div id="navigation">
<div id="page-nav">
<ul>
<li id="welcome" class="current_page_item2"><a href="<?php bloginfo('url');?>" title="Home">Welcome</a></li>
<li class="cat-item cat-page<?php if (is_page('2')) { ?> current-cat<?php } ?>"><a href="/about/" title="About the Photography Parlour">About</a></li>
<li class="cat-item cat-page"><a href="/forum/" title="photography forum">Forum</a></li>
<li class="cat-item cat-page"><a href="/shop/" title="photography shop">Shop</a></li>
<li class="cat-item cat-page"><a href="/info-for-advertisers/" title="photography Advertising Information">Advertise</a></li>
<li class="cat-item cat-page"><a href="/media-center/" title="Photography Media Center">Media Center</a></li>
<li class="cat-item cat-page"><a href="/2010/05/contribute-to-the-photography-parlour/1937" title="Contribute to The Photography Parlour">Contribute</a></li>
<li class="cat-item cat-page"><a href="/contact/" title="contact the photography parlour team">Contact</a></li>
</ul>
<span id="login"><?php /* <a href="<?php echo get_option('home'); ?>/wp-admin/">Login to Site</a>
<a href="/info-for-advertisers/" title="Information for Advertisers">Advertising Info</a>*/ ?>
<a href="http://www.twitter.com/photoparlour/" onclick="pageTracker._trackEvent('Twitter', 'twitter sign up');" title="follow us on twitter" class="toptwitter"></a><a href="http://www.facebook.com/thephotographyparlour/" title="follow us on facebook" class="topfacebook" onclick="pageTracker._trackEvent('facebook', 'facebook sign up');"></a>
<a class="rss" href="http://feeds.feedburner.com/thephotographyparlour" onclick="pageTracker._trackEvent('RSS', 'RSS FEED sign up');"></a>
</span>
</div><!--page-nav-->
<script type="text/javascript">/*<![CDATA[*/Cufon.now();/*]]>*/</script>
<div id="cat-nav">
<ul id="nav2">
<?php wp_list_categories('title_li=&sort_column=menu_order&exclude=1,114,115');?>
</ul>
</div><!--cat-nav-->
</div><!--navigation-->
</div><!--header-->
<div id="wrap">
<?php
Forumticker();

