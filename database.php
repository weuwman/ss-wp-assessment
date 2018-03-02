<?php
	//global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	//table prefix
	$table_prefix = "mycf_";

	// create tables
	$sqldb = array();

	$sqldb[] = "CREATE TABLE {$table_prefix}data (
				  id int(11) unsigned NOT NULL AUTO_INCREMENT,
				  name varchar(50) NOT NULL,
				  email varchar(50) NOT NULL,
				  message longtext DEFAULT NULL,
				  created int(10) unsigned NOT NULL,
				  updated int(10) unsigned NOT NULL,
				  PRIMARY KEY  (id)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";	

	foreach ($sqldb as $tabledb)
	{
		dbDelta( $tabledb );
	}