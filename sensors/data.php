<?php
// read sensor ID ('esp8266-'+ChipID)
$headers = array();
if (isset($_SERVER['HTTP_SENSOR'])) $headers['Sensor'] = $_SERVER['HTTP_SENSOR'];
if (isset($_SERVER['HTTP_X_SENSOR']))$headers['Sensor'] = $_SERVER['HTTP_X_SENSOR'];
$json = file_get_contents('php://input');
$results = json_decode($json,true);
header_remove();

$now = gmstrftime("%Y/%m/%d %H:%M:%S");
$today = gmstrftime("%Y-%m-%d");

// copy sensor data values to values array
foreach ($results["sensordatavalues"] as $sensordatavalues) {
	$values[$sensordatavalues["value_type"]] = $sensordatavalues["value"];
}
// print transmitted values
echo "Sensor: ".$headers['Sensor']."\r\n";
// check if data dir exists, create if not
/*
if (!file_exists('data')) {
	mkdir('data', 0755, true);
}
*/

// save data values to CSV (one per day)
$datafile = "data-".$headers['Sensor']."-".$today.".csv";

if (!file_exists($datafile)) {
	$outfile = fopen($datafile,"a");
	fwrite($outfile,"Time;durP1;ratioP1;P1;durP2;ratioP2;P2;SDS_P1;SDS_P2;Temp;Humidity;BMP_temperature;BMP_pressure;BME280_temperature;BME280_humidity;BME280_pressure;Samples;Min_cycle;Max_cycle;Signal\n");
	fclose($outfile);
}
if (! isset($values["durP1"])) { $values["durP1"] = ""; }
if (! isset($values["ratioP1"])) { $values["ratioP1"] = ""; }
if (! isset($values["P1"])) { $values["P1"] = ""; }
if (! isset($values["durP2"])) { $values["durP2"] = ""; }
if (! isset($values["ratioP2"])) { $values["ratioP2"] = ""; }
if (! isset($values["P2"])) { $values["P2"] = ""; }
if (! isset($values["SDS_P1"])) { $values["SDS_P1"] = ""; }
if (! isset($values["SDS_P2"])) { $values["SDS_P2"] = ""; }
if (! isset($values["temperature"])) { $values["temperature"] = ""; }
if (! isset($values["humidity"])) { $values["humidity"] = ""; }
if (! isset($values["BMP_temperature"])) { $values["BMP_temperature"] = ""; }
if (! isset($values["BMP_pressure"])) { $values["BMP_pressure"] = ""; }
if (! isset($values["BME280_temperature"])) { $values["BME280_temperature"] = ""; }
if (! isset($values["BME280_humidity"])) { $values["BME280_humidity"] = ""; }
if (! isset($values["BME280_pressure"])) { $values["BME280_pressure"] = ""; }
if (! isset($values["samples"])) { $values["samples"] = ""; }
if (! isset($values["min_micro"])) { $values["min_micro"] = ""; }
if (! isset($values["max_micro"])) { $values["max_micro"] = ""; }
if (! isset($values["signal"])) { $values["signal"] = ""; } else { $values["signal"] = substr($values["signal"],0,-4); }
$outfile = fopen($datafile,"a");
fwrite($outfile,$now.";".$values["durP1"].";".$values["ratioP1"].";".$values["P1"].";".$values["durP2"].";".$values["ratioP2"].";".$values["P2"].";".$values["SDS_P1"].";".$values["SDS_P2"].";".$values["temperature"].";".$values["humidity"].";".$values["BMP_temperature"].";".$values["BMP_pressure"].";".$values["BME280_temperature"].";".$values["BME280_humidity"].";".$values["BME280_pressure"].";".$values["samples"].";".$values["min_micro"].";".$values["max_micro"].";".$values["signal"]."\n");
fclose($outfile);

// ****************
// Log  to database
// ****************

trace ("Begin recording for ".$headers['Sensor']);

$cnn = mysql_connect("localhost", "specslab_sensor", "9T3&(&FnTySx"); 
if (!$cnn)
	{
		mysql_close();	
		trace ("Failed Database connection.", true); 
		return;
	}	
if (mysql_select_db( "specslab_science4all_sensors" ,$cnn )  == false)
	{
		mysql_close();	
		trace(" Database not found." , true); 
		return ;
	}	
	
// 1. Find all the channels for the current node ID

// Find the node
$query = "select ND_KEY from NODES where ND_ID ='".$headers['Sensor']."'";
$result = mysql_query($query, $cnn);

if (/*!$resut ||*/ mysql_num_rows($result)==0) trace ("Node id ".$headers['Sensor']."not found in database. ", true);

$nodeID = mysql_fetch_array($result);
trace ("Found Node".$headers['Sensor']. "ID".$nodeID[0] );
$query = "select CH_ID, CH_PARAM_ID from CHANNEL where CH_ND_KEY =".$nodeID[0];
//trace($query);
$result = mysql_query($query, $cnn);
if (/*!$resut ||*/ mysql_num_rows($result)==0) trace ("No channels registered for node ".$headers['Sensor'], true);

// Fill each channel
// TODO Now hardcoded. Every Parameter has a lable to make the match automatically.
trace ("Num rows".mysql_num_rows($result));
// Fill each channel
// TODO Now hardcoded. Every Parameter has a lable to make the match automatically.
while ( $chnn =  mysql_fetch_array($result))
{
	trace("chn id:".$chnn[1]);
	// Hack!!. labels have to be matched
	$query = "insert into RECORDINGS (CH_ID, RC_DATA) values (".$chnn[0].",";
	switch ($chnn[1])
	{
		case 1:
			$query .= $values["SDS_P1"];
			break 1;
		case 2:
			$query .= $values["SDS_P2"];
			break 1;
		case 3:
			$query .= $values["temperature"];
			break 1;
		case 4:
			$query .= $values["humidity"];
			break 1;
		default:
			trace("Chn ID not found".$chnn[1]);
			continue;
			break;
	}

	$query .= ")";
	trace($query);
	$result1 = mysql_query($query, $cnn);

	if( !$result1)
	{
		// Session not found
		trace("Recording couldn't be saved. ".mysql_error(),true); 
	}
}

// ********
//   Aux
// *******
function trace($entry, $die = false)
{

		$logID =  date( 'd');
		$path = date('Ym');
		$open = fopen("logs/$path$logID.log", "a+"); 
		fwrite($open,  date('H:i:s_').$entry."\n");
		fclose($open);

	if ($die) 
	{
		echo "died!";
		die;
	}

}

?>
ok!!