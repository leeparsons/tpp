<?php
/**

EDIT this file in text editor only!


**/


add_filter('xmlrpc_enabled', '__return_false');

remove_action('wp_head', 'rsd_link');

if ( get_magic_quotes_gpc() ) {
    $_POST      = array_map( 'stripslashes_deep', $_POST );
    $_GET       = array_map( 'stripslashes_deep', $_GET );
    $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
    $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
}

$themename = "Aparatus";
$shortname = "apa";
$options = array (

array( "name" => "General Settings",
	"type" => "sub-title"), 
array( "type" => "open"),
array( "name" => "Style Sheet",
	"desc" => "Enter the Style Sheet you like",
	"id" => $shortname."_style_sheet",
	"type" => "select",
	"options" => array("maroon", "black", "blue", "green"), 
	"std" => "maroon"), 
array( "name" => "Twitter Account",
	"desc" => "Enter the your Twitter Account",
	"id" => $shortname."_Twitter",
	"type" => "text",
	"std" => ""),	
array( "type" => "close"),	
	
array( "name" => "Home Page Settings",
	"type" => "sub-title"), 
array( "type" => "open"),

array( "name" => "No Slideshow?",
	"desc" => "Click this box if you DONT want to use the Mootools Slideshow",
	"id" => $shortname."_use_slide_show",
	"type" => "checkbox",
	"std" => ""),
array( "name" => "Dont automatic Slide?",
	"desc" => "Click this box if you DONT want the Slideshow to slide on load",
	"id" => $shortname."_auto_slide_show",
	"type" => "checkbox",
	"std" => ""),
array( "name" => "Category Name for the Content Slider",
	"desc" => "Enter the category name of the posts you would like to show up in the content slider. If you don't enter a value, The latest 5 Posts will be used",
	"id" => $shortname."_feature_cat_name",
	"type" => "text",
	"std" => ""),		
array( "type" => "close"),

array( "name" => "Advertising Blocks",
	"type" => "sub-title"), 
array( "type" => "open"),

array( "name" => "300 x 250 Big Ad Block Image Source",
	"desc" => "Enter the Full Image URL of the 300 x 250 Ad block in the Sidebar here",
	"id" => $shortname."_ad300x250image",
	"type" => "text",
	"std" => ""),	
array( "name" => "300 x 250 Big Ad Block Click Destination",
	"desc" => "Enter the Click Destination URL of the 300 x 250 Ad block in the Sidebar here",
	"id" => $shortname."_ad300x250destination",
	"type" => "text",
	"std" => ""),
	
array( "name" => "1st Small Ad Block Image Source",
	"desc" => "Enter the Full Image URL of the first 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_1_ad_image",
	"type" => "text",
	"std" => ""),	
array( "name" => "1st Small Ad Block Click Destination",
	"desc" => "Enter the Click Destination URL of the first 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_1_ad_destination",
	"type" => "text",
	"std" => ""),		

array( "name" => "2nd Small Ad Block Image Source",
	"desc" => "Enter the Full Image URL of the second 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_2_ad_image",
	"type" => "text",
	"std" => ""),	
array( "name" => "2nd Small Ad Block Click Destination",
	"desc" => "Enter the Click Destination URL of the second 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_2_ad_destination",
	"type" => "text",
	"std" => ""),	

array( "name" => "3rd Small Ad Block Image Source",
	"desc" => "Enter the Full Image URL of the third 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_3_ad_image",
	"type" => "text",
	"std" => ""),	
array( "name" => "3rd Small Ad Block Click Destination",
	"desc" => "Enter the Click Destination URL of the third 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_3_ad_destination",
	"type" => "text",
	"std" => ""),

array( "name" => "4th Small Ad Block Image Source",
	"desc" => "Enter the Full Image URL of the fourth 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_4_ad_image",
	"type" => "text",
	"std" => ""),	
array( "name" => "4th Small Ad Block Click Destination",
	"desc" => "Enter the Click Destination URL fourth 125 x 125 Small Ad block in the Sidebar here",
	"id" => $shortname."_4_ad_destination",
	"type" => "text",
	"std" => ""),	
	


array( "type" => "close") 
);

//presentation//

function mytheme_add_admin() {
 
global $themename, $shortname, $options;
 
if ( $_GET['page'] == basename(__FILE__) ) {
 
if ( 'save' == $_REQUEST['action'] ) {
 
foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
 
foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
 
header("Location: themes.php?page=functions.php&saved=true");
die;
 
} else if( 'reset' == $_REQUEST['action'] ) {
 
foreach ($options as $value) {
delete_option( $value['id'] ); }
 
header("Location: themes.php?page=functions.php&reset=true");
die;
 
}
}
 
add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
 
}
 
function mytheme_admin() {
 
global $themename, $shortname, $options;
 
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
 
?>
<div class="wrap">
<h2><?php echo $themename; ?> Settings</h2>
 
<form method="post">
 
<?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
<table width="100%" border="0" style="background-color:#eef5fb; padding:10px;">
 
<?php break;
 
case "close":
?>
 
</table><br />
 
<?php break;
 
case "title":
?>
<table width="100%" border="0" style="background-color:#dceefc; padding:5px 10px;"><tr>
<td valign="top" colspan="2"><h3 style="font-family:Georgia,'Times New Roman',Times,serif;"><?php echo $value['name']; ?></h3></td>
</tr>

<!--custom-->
 
 
<?php break; 
case "sub-title":
?>
<h3 style="font-family:Georgia,'Times New Roman',Times,serif; padding-left:8px;"><?php echo $value['name']; ?></h3> 
<!--end-of-custom-->
 
 
<?php break;
 
case 'text':
?>
 
<tr>
<td valign="top" width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
<td width="80%"><input style="width:400px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
</tr>
 
<tr>
<td><small><?php echo $value['desc']; ?></small></td>
</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
 
<?php
break;

case 'textarea':
?>
 
<tr>
<td valign="top" width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
<td width="80%"><textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?></textarea></td>
 
</tr>
 
<tr>
<td><small><?php echo $value['desc']; ?></small></td>
</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
 
<?php
break;
 
case 'select':
?>
<tr>
<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
<td width="80%"><select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
</tr>
 
<tr>
<td><small><?php echo $value['desc']; ?></small></td>
</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
 
<?php
break;
 
case "checkbox":
?>
<tr>
<td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
<td width="80%"><?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
</td>
</tr>
 
<tr>
<td><small><?php echo $value['desc']; ?></small></td>
</tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
 
<?php break;
 
}
}
?>
 
<p class="submit">
<input name="save" type="submit" value="Save changes" />
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
 
<?php
}
add_action('admin_menu', 'mytheme_add_admin');

?>
<?php 
function new_excerpt_length($length) {
	return 60;
}
add_filter('excerpt_length', 'new_excerpt_length');
?>
<?php 
//widtetize//
if ( function_exists('register_sidebar') )

register_sidebar(array('name'=>'sidebar',
'before_widget' => '<div class="sidebar-row">',
'after_widget' => '</div>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
        if ( ! is_admin() ) {
                global $id;
                $comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
                return count($comments_by_type['comment']);
        } else {
                return $count;
        }
}
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
?>
<?php
function my_deregister_styles() {
wp_deregister_style( 'wp-pagenavi' );
}
?>
<?php if (function_exists('add_theme_support')) {
add_theme_support( 'post-thumbnails' ); // Add it
set_post_thumbnail_size( 195, 65 ); // 195 pixels wide by 65 pixels tall - class - thumbnails in archive
} ?>
<?php 
function popularPosts($num) {
    global $wpdb;

    $posts = $wpdb->get_results("SELECT comment_count, ID, post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , $num");

    foreach ($posts as $post) {
        setup_postdata($post);
        $id = $post->ID;
        $title = $post->post_title;
        $count = $post->comment_count;

        if ($count != 0) {
            $popular .= '<li>';
            $popular .= '<a href="' . get_permalink($id) . '" title="' . $title . '">' . $title . '</a> ';
            $popular .= '</li>';
        }
    }
    return $popular;
}
function my_rec_comments($limit){
	global $wpdb;	
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
	comment_post_ID, comment_author, comment_date_gmt, comment_approved,
	comment_type,comment_author_url,
	SUBSTRING(comment_content,1,30) AS com_excerpt
	FROM $wpdb->comments
	LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
	$wpdb->posts.ID)
	WHERE comment_approved = '1' AND comment_type = '' AND
	post_password = ''
	ORDER BY comment_date_gmt DESC
	LIMIT $limit";
	$comments = $wpdb->get_results($sql);
	$output = $pre_HTML;
	$output .= "\n<ul>";
	foreach ($comments as $comment) {
	$output .= "\n<li>".strip_tags($comment->comment_author)
	.":" . "<a href=\"" . get_permalink($comment->ID) .
	"#comment-" . $comment->comment_ID . "\" title=\"on " .
	$comment->post_title . "\">" . strip_tags($comment->com_excerpt)
	."</a></li>";
	}
	$output .= "\n</ul>";
	$output .= $post_HTML;
	echo $output;

}


function Forumticker () {


$link = mysql_connect('localhost', 'wwwtpp_forumu', 'UyJ#dE$?hh%t',true) or die('Could not connect: ' . mysql_error());
if (!mysql_select_db('wwwtpp_PhotoParlForum')) { echo('Could not select database');} else {

$q = "SELECT post_subject,post_text,topic_id,forum_id FROM PPFposts WHERE post_approved = 1 ORDER BY post_time DESC LIMIT 5";
$r = mysql_query($q);
echo '<div class="forumtickcontainer"><div class="forumlatestposts">Latest Forum Posts:</div><div class="forumticker">';
while ($row = mysql_fetch_array($r)) {

$arr = explode(" ", html_entity_decode(substr($row['post_text'],0,200)));
echo "<div class='tick'><div class='tickleft'><a href='/forum/viewtopic.php?f=" . $row['forum_id'] . "&amp;t=" . $row['topic_id'] . "'><b>" . $row['post_subject'] . ": </b><span>";
for ($i=0;$i<count($arr);$i++) {

echo (($arr[$i] == "<br>") || ($arr[$i] == "<br/>"))? "":str_replace("/>","",strip_tags($arr[$i])) . " ";

}
echo "</span></a></div><div class='tickright'>...</div></div>";
unset($arr);
}
echo "</div></div>";
?><script type="text/javascript">/*CDATA[*/
jQuery('div.forumticker').ready(function () {
jQuery('div.forumticker .tick').each(function (i) {
jQuery(this).css("opacity","0").css("display","none").find("a").attr("target","_blank");
});
jQuery('div.forumticker .tick:first').addClass("active").css("display","block").animate({opacity:"1"},2250);
setInterval(function () {jQuery.mn();},7000);
jQuery.mn = function () {
if (!jQuery('div.forumticker .tick:last').hasClass("active")) {
jQuery('div.forumticker .active').animate({opacity:"0"},2250);setTimeout(function () {jQuery('div.forumticker .active').css("display","none").removeClass("active").next("div").addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
} else {
jQuery('div.forumticker .active').animate({opacity:"0"},2250);setTimeout(function () {jQuery('div.forumticker .active').removeClass("active").css("display","none");jQuery('div.forumticker .tick:first').addClass("active").css("display","block").animate({opacity:"1"},2250);},2250);
}
}
});
/*]]>*/</script>
<?php

mysql_free_result($r);
mysql_close($link);
}
}

function contributelarge($type = "") {
$class = "";
switch ($type) {
case "cat":
$class="contributelarge-cat";
break;
case "single":
$class="contributelarge-cat";
break;
default:
$class="contributelarge-pagepost";
break;
}

//if (rand(0,5) < 3) {
 /*
?>
<a href="/contribute-to-the-photography-parlour/" class="<?php echo $class; ?>"><img style="border:2px solid rgb(238, 238, 238);" src="/wp-content/themes/aparatus/images/contributelarge.jpg"></a>
<?php //}
*/

/*
<a class="supportus" href="/info-for-advertisers/"></a><script type="text/javascript">/*<![CDATA[
Cufon.replace('.support,.supportyou');Cufon.now();
/*]]></script>
<?php
    
    */
}
