</div><!--wrap-->
<div id="footer">
<div id="logo-footer">
<?php bloginfo('name');?>
</div>
<script type="text/javascript">/*<![CDATA[*/jQuery("div.post-title-big").css("textIndent","-8px");Cufon.replace('.title-area,.post-title-big, #comments h3, h3#response-title, .author-name, #logo-footer');Cufon.now();/*]]>*/</script>
<div id="page-nav-footer">
<ul>
<li class="first"><a href="<?php bloginfo('url');?>">Home</a></li>
<?php wp_list_pages('title_li=&depth=1&sort_column=menu_order&exclude=530,1754'); ?>
</ul>
</div><!--page-nav-footer-->
<?php /*<!--please do not take this section out-->
<div id="credits">&copy; 2009 All rights reserved. <a href="http://fearlessflyer.com">Web Design by Fearless Flyer</a></div>
<!--credits-->*/ ?>
</div><!--footer-->
<?php if ((!isset ($_REQUEST['preview'])) && (!is_user_logged_in())) { ?>
<script type="text/javascript">/*<![CDATA[*/var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));/*]]>*/</script>
<script type="text/javascript">/*<![CDATA[*/try {var pageTracker = _gat._getTracker("UA-15329337-1");pageTracker._trackPageview();} catch(err) {}/*]]>*/</script>
<?php } ?>
</div>
</body>
</html>