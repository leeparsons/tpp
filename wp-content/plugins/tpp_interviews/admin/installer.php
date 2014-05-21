<?php


function installTppInterviews()
{

    $table_name = "tpp_interviews";


    global $wpdb;

    $sql = "CREATE TABLE $table_name (
  `post_id` bigint(20) unsigned NOT NULL,
  `interview_date` date DEFAULT NULL,
  `start_time` char(5) DEFAULT NULL,
  `end_time` char(5) DEFAULT NULL,
  `video` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `interview_date` (`interview_date`,`start_time`,`end_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}