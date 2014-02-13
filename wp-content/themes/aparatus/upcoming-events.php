<?php

/** file used for getting the upcoming events listed in the upcoming events category and then pushing them onto the sidebar! **/


global $wpdb;


?>
<?php $my_query = new WP_Query('cat=114&posts_per_page=5'); ?>
<?php $c = 0; ?>
<?php if ( $my_query->have_posts() ) : while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

<?php //determine if the ucedate is greater than now 


unset($info);

$info = $wpdb->get_results("SELECT ucedate, uceexpiry FROM UCEDates WHERE id = '" . get_post_meta(get_the_ID(),"uceid",true) . "'",ARRAY_A);

if ((int)($info[0]["uceexpiry"]) > time()) {

?>
<?php if ($c == 0) { $c++; ?>
<div class="upcomingcontainer">
<h4 class="upcoming">Upcoming Events</h4>
<div class="upcominglist">
<?php } ?>
<div class="event"<?php if ($c > 1) { ?> style="display:none"<?php } ?>>

<a class="lnk" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
<span><?php echo date('d F Y',$info[0]["ucedate"]); ?></span>
<p><?php $e = explode(" ",substr(get_the_excerpt(),0,125)); for ($i=0;$i<(count($e)-1);$i++) {echo $e[$i] . " ";  } unset($e); ?>... <a href="<?php the_permalink(); ?>">[read more]</a></p>

</div>

<?php } ?>

<?php endwhile; ?>

<?php endif; ?>
<?php if ($c > 0) { ?>
</div>
<script type="text/javascript">/*<![CDATA[*/Cufon.replace("h4.upcoming",{fontSize:"35px"});Cufon.now();/*]]>*/</script>
</div>
<?php if ($c > 1) { ?>
<script type="text/javascript">/*CDATA[*/
jQuery('div.upcomingcontainer').ready(function () {
jQuery('div.upcomingcontainer .event').each(function (i) {
jQuery(this).css("opacity","0").css("display","none");
});
jQuery('div.upcomingcontainer .event:first').addClass("active").css("display","block").animate({opacity:"1"},2250);
setInterval(function () {jQuery.mnevents();},7000);
jQuery.mnevents = function () {
if (!jQuery('div.upcomingcontainer .event:last').hasClass("active")) {
jQuery('div.upcomingcontainer .active').animate({opacity:"0"},2250);setTimeout(function () {jQuery('div.upcomingcontainer .active').css("display","none").removeClass("active").next("div").addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
} else {
jQuery('div.upcomingcontainer .active').animate({opacity:"0"},2250);setTimeout(function () {jQuery('div.upcomingcontainer .active').removeClass("active").css("display","none");jQuery('div.upcomingcontainer .event:first').addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
}
}
});
/*]]>*/</script>
<?php } unset($c); ?>
<?php } ?>