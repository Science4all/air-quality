<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
      #floating-panel {
        background-color: #fff;
        border: 1px solid #999;
        left: 25%;
        padding: 5px;
        position: absolute;
        top: 10px;
        z-index: 5;
      }
    </style>
  </head>

  <body>
    <div id="floating-panel">
      <button onclick="toggleHeatmap()">Toggle Heatmap</button>
      <button onclick="changeGradient()">Change gradient</button>
      <button onclick="changeRadius()">Change radius</button>
      <button onclick="changeOpacity()">Change opacity</button>
    </div>
    <div id="map"></div>
    <script>
<?php 

	// ********
	//   Aux
	// *******
	function trace($entry, $die = false)
	{

			$logID =  date( 'd');
			$path = date('Ym');
			$open = fopen("map_logs/$path$logID2.log", "a+"); 
			fwrite($open,  date('H:i:s_').$entry."\n");
			fclose($open);

		if ($die) 
		{
			echo "died!";
			die;
		}

	}
	
	// ********
	//   DB Connection
	// *******
	
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
?>

	
      // This example requires the Visualization library. Include the libraries=visualization
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization">

      var map, heatmap;

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 17,
          center: {lat: 41.253987, lng: 1.900882},
          mapTypeId: 'satellite'
        });
		
     // Heatmap data
      var heatMapData = [

<?php

	if (isset($_GET[timestamp])){
		$timestamp = $_GET[timestamp];
		trace ('1:'.$timestamp);		
	}else{
		$timestamp = date('Y-m-d H:i:s');
		trace ('2:'.$timestamp);		
	}
	trace ('3:'.$timestamp);

	trace ("Getting points from database");

	$query = "SELECT ND_ID, ND_NAME, CH_ID, GPS_LAT, GPS_LON, SN_LABEL, PR_MAGNITUDE, PR_UNITS from GPS 
				LEFT OUTER JOIN NODES ON GPS.GPS_ID =  NODES.GPS_ID
				LEFT OUTER JOIN CHANNEL ON CH_ND_KEY = NODES.ND_KEY 
				LEFT OUTER JOIN PARAMETERS ON PR_ID = CH_PARAM_ID
				WHERE CH_PARAM_ID = 1";

	trace( $query);
	
	$result = mysql_query($query, $cnn);
	$nChannels = mysql_num_rows($result); 	
	trace( "Found ".$nChannels." channels");
	
	$ctr = 1; 
	$markers = ''; 
	
	while ( $channel =  mysql_fetch_array($result))
	{
		$query = "SELECT RC_ID,CH_ID,RC_DATA, TIMESTAMP_SERVER
					FROM RECORDINGS
					WHERE CH_ID = ".$channel['CH_ID']." AND TIMESTAMP_SERVER < '$timestamp'
					ORDER BY TIMESTAMP_SERVER DESC
					LIMIT 1";

		trace( $query);
		
		$result2 = mysql_query($query, $cnn);
		$data =  mysql_fetch_array($result2);
		trace( "data: ".mysql_num_rows($result2)); 
		
		if($data['RC_DATA'] != NULL){
			//We calculate teh sqare of the value to make more difference in the plot
			$sqrData = floatval($data['RC_DATA']) * floatval($data['RC_DATA']); 
			$print = '{location: new google.maps.LatLng('.$channel['GPS_LAT'].', '.$channel['GPS_LON'].'), weight: '.$sqrData.'}'; 
			if($ctr < $nChannels) $print.=','; //-> don`'t add colon in the last one
			echo $print; 
		}
		
		$ctr++;
		$value = $data['RC_DATA']; 
		if($value == NULL) $value = "NULL"; 
					
		//We build the markers here to optimize and also to sow teh current readings. We print it in the javascript bellow
		$marker = '
			var marker'.$ctr.' = new google.maps.Marker({
			  position: {lat: '.$channel['GPS_LAT'].', lng: '.$channel['GPS_LON'].'},
			  map: map,
			  title: \''.$channel['ND_NAME'].'\'
			});
					
			var infowindow'.$ctr.' = new google.maps.InfoWindow({
			  content: \'NodeID: '.$channel['ND_ID'].
						' <br> Name: '.$channel['ND_NAME'].
						' <br> Value: '.$value.' ['.$channel['PR_MAGNITUDE'].' '.$channel['PR_UNITS'].']\'
			});

			marker'.$ctr.'.addListener(\'click\', function() {
			  infowindow'.$ctr.'.open(map, marker'.$ctr.');
			});		
		';		
		
		$markers .= $marker;
				
	}	
?>

        ];
		
		heatmap = new google.maps.visualization.HeatmapLayer({
			data: heatMapData
		});
		
		heatmap.setMap(map);
		
		heatmap.set('radius', 200);
		
<?php

	//PRINT MARKERS IN JAVASCRIPT HERE
	echo $markers; 

?>
      }

      function toggleHeatmap() {
        heatmap.setMap(heatmap.getMap() ? null : map);
      }

      function changeGradient() {
        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
      }

      function changeRadius() {
		  if(heatmap.get('radius') == 100 ) heatmap.set('radius',200);
		  else heatmap.set('radius',100);
      }

      function changeOpacity() {
        heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
      }
		
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3DFk-kEtxckdCN_HaOKlbCzhlIKBGiGM&libraries=visualization&callback=initMap">
    </script>
  </body>
</html>