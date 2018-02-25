<?php
######################################################################
# RGS - Rehabilitation Gaming System - http://rgs-project.eu
# Web Interface developed by SPECS - http://specs.upf.edu
# All Rights Reserved
######################################################################

// Security Code, avoids possible intrusions and add session control
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['loggedin'])) { exit; } 

# Here we have a class to connect to the DB

// DB Data
$connection_information = array(
	'host' => 'localhost', // Host
	'user' => 'eodyne', // User
	'pass' => 'gamreh2016', // Password
	'db' => 'rgs_mims_production' // DB name
);

// Connection Class
class mysql {
	var $con;
	function __construct($db=array()) {
		$default = array( // Default values
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'db' => 'test'
		);
		$db = array_merge($default,$db);
		$this->con=mysql_connect($db['host'],$db['user'],$db['pass'],true) or die ('Error connecting to MySQL');
		mysql_select_db($db['db'],$this->con) or die('Database '.$db['db'].' does not exist!');
	}}

$m = new mysql($connection_information);

?>