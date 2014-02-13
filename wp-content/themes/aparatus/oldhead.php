<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta name="google-site-verification" content="QlCldWYkiMZwNFqnwTJ-dPgrwhlKK7S8VXQniIfqoD8" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<?php global $options;
 		foreach ($options as $value) {
			 if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); 
		}
	} ?>            
<?php if (isset (!$_REQUEST['lee'])) { ?>
	<link rel="stylesheet" href="/wp-content/themes/aparatus/style3.css" type="text/css" media="screen" />
<?php } else { ?>
	<link rel="stylesheet" href="/wp-content/themes/aparatus/style2.css" type="text/css" media="screen" />
<?php } ?>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php $b = $_SERVER['HTTP_USER_AGENT']; ?>
<?php if (stripos($b,"MSIE 6") !== false) { ?>
	<script type="text/javascript" src="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/scripts/unitpngfix.js"></script>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie6.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>	
<?php } elseif (stripos($b,"MSIE 7") !== false) { ?>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie7.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>		
<?php } elseif (stripos($b,"MSIE 8") !== false) { ?>
	<link href="<?php echo str_replace("http://www.thephotographyparlour.com/","/",get_bloginfo('template_directory')); ?>/forie8.css" rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8"/>		
<?php } ?>
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
<?php if (stripos($b,"msie") !== false) { ?>
$(document).ready(function () {
$('#nav2 ul li').each(function (i) {
	$(this).mouseenter(function () {
		$(this).addClass("sfhover");
	});
	$(this).mouseleave(function () {
		$(this).removeClass("sfhover");
	});
});
});
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
/*]]>*/
	</script>
	<script type="text/javascript" charset="utf-8">/*<![CDATA[*/
			window.addEvent('domready', init);
			function init() {
				myTabs1 = new mootabs('myTabs', {height: '320px', width: '300px', changeTransition: Fx.Transitions.Back.easeOut, mouseOverClass: 'over'});
				
			}
	/*]]>*/</script>
<?php wp_head();flush(); ?>
</head>
<body>
<div class="bodywrapper">
<div id="header"><?php

/** header **/

?><div id="twitter-badge"><a href="http://twitter.com/<?php if ($apa_Twitter == ''){echo '_fearlessflyer';}else{echo $apa_Twitter;} ?>" onclick="pageTracker._trackEvent('Twitter', 'Follow me');" >follow</a></div><!--twitter-badge-->
<?php if (is_front_page()) { ?><h1><?php } ?><a id="logo" href="<?php bloginfo('url');?>"><?php bloginfo('name');?></a><?php if (is_front_page()) { ?></h1><?php } ?>
<?php if (!isset ($_REQUEST['lee'])) { ?><h2 style="float:left;margin:-20px 0 10px 10px;font-family:verdana,arial,helvetica;font-size:10px;font-weight:bold;">The home of tips, advice and inspiration for aspiring professional photographers</h2><?php } ?>
<div id="navigation">
<div id="page-nav">
<ul>
<li id="welcome" class="current_page_item2"><a href="<?php bloginfo('url');?>" title="Home">Welcome</a></li>
<?php if (isset ($_REQUEST['lee'])) { ?>
<li style="color: #FFFFFF;"><h2 style="font-family:verdana,arial,helvetica;font-size:10px;font-weight:bold;">The home of tips, advice and inspiration for aspiring professional photographers</h2></li><?php } else { ?>
<li class="cat-item <?php if (is_page('2')) { ?> current-cat<?php } ?>"><a href="/about/" title="About the Photography Parlour">About</a></li>
<li class="cat-item"><a href="/forum/" title="photography forum">Forum</a></li>
<li class="cat-item"><a href="/shop/" title="photography shop">Shop</a></li>
<li class="cat-item"><a href="/advertising info/" title="photography Advertising Information">Advertise</a></li>
<li class="cat-item"><a href="/contact/" title="contact the photography parlour team">Contact</a></li>

<?php } ?>

<?php /*wp_list_pages('title_li=&depth=1&sort_column=menu_order&exclude=398');*/ ?>
</ul>
<span id="login"><?php /* <a href="<?php echo get_option('home'); ?>/wp-admin/">Login to Site</a>*/ ?>
<a href="/info-for-advertisers" title="Information for Advertisers">Advertising Info</a>
<a class="rss" href="http://feeds.feedburner.com/thephotographyparlour" onclick="pageTracker._trackEvent('RSS', 'RSS FEED sign up');"></a>
</span>
</div><!--page-nav-->
<script type="text/javascript">/*<![CDATA[*/Cufon.now();/*]]>*/</script>
<div id="cat-nav">
<ul id="nav2">
<?php if (isset ($_REQUEST['lee'])) { ?>
<li class="cat-item <?php if (is_page('2')) { ?> current-cat<?php } ?>"><a href="/about/" title="About the Photography Parlour">About</a></li>
<?php } ?>
<?php wp_list_categories('title_li=&sort_column=menu_order&exclude=1');?>
<?php if (!isset ($_REQUEST['lee'])) { ?>
<li class="cat-item"><a href="/forum/" title="photography forum">Forum</a></li>
<li class="cat-item"><a href="/contact/" title="contact the photography parlour team">Contact</a></li>
<?php } ?>
</ul>

</div><!--cat-nav-->
</div><!--navigation-->
</div><!--header-->
<div id="wrap">