<?php
/*
+----------------------------------------------------------------+
+	hdflv-XML
+
+	required for hdflv
+----------------------------------------------------------------+
*/

// look up for the path
require_once( dirname(__FILE__) . '/hdflv-config.php');

// get the path url from querystring
$playlist_id = $_GET['id'];

function get_out_now() { exit; }
add_action('shutdown', 'get_out_now', -1);

global $wpdb;

$title = 'hdflv Playlist';

$themediafiles = array();
$limit = '';


// Otherwise gets most viewed

	// Remove all evil code
	$playlist_id = intval($_GET['id']);
    //echo $playlist_id;
 	$playlist = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."hdflv_playlist WHERE pid = '$playlist_id'");
    //echo "SELECT * FROM ".$wpdb->pefix."hdflv_playlist WHERE pid = '$playlist_id'";
    //print_r($playlist);
 	if ($playlist)
    {
		$select  = " SELECT * FROM ".$wpdb->prefix."hdflv w";
		$select .= " INNER JOIN ".$wpdb->prefix."hdflv_med2play m";
		$select .= " WHERE (m.playlist_id = '$playlist_id'" ;
		$select .= " AND m.media_id = w.vid) GROUP BY w.vid ";
		$select .= " ORDER BY m.sorder ASC , m.porder ".$playlist->playlist_order." ,w.vid ".$playlist->playlist_order;
        //echo $select;
		$themediafiles = $wpdb->get_results( $wpdb->prepare( $select ) );
        //print_r($themediafiles);
	 	$title = $playlist->playlist_name;
	}

$options1 = get_option('HDFLVSettings');
if($options1[0][26]['v'] == "true" ) $ap ="true"; else $ap="false";

// Create XML / XSPF output
header("content-type:text/xml;charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "<playlist autoplay='$ap' random='false'>";


if (is_array ($themediafiles)){

	foreach ($themediafiles as $media) {

                if ($media->image == '')
					$image = get_option('siteurl').'/wp-content/plugins/' . dirname( plugin_basename(__FILE__) ).'/images/hdflv.jpg';
				else
					$image = $media->image;
  				$file = pathinfo($media->file);
                if($media->hdfile != '') $hd="true"; else $hd="false";

		echo '<mainvideo';

		echo ' url="'.htmlspecialchars($media->file).'"';
		echo ' thu_image="'.htmlspecialchars($image).'"';
        echo ' Preview="'.htmlspecialchars($media->opimage).'"';
        echo ' hd="'.$hd.'"';
        echo ' hdpath="'.$media->hdfile.'">';
        echo htmlspecialchars($media->name);
        //echo '<![CDATA[SampleMovie]]> ';
        echo "".'</mainvideo>';

	}
}


echo "</playlist>";

?>