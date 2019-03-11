<?php

if(isset($_POST['submit'])){
$startloc = $_POST['start'];
$endloc = $_POST['end'];

//echo $startloc;
//echo " to ".$endloc.;

//$url = "http://api.openweathermap.org/data/2.5/weather?q='.$startloc.'&units=imperial&appid=YOUR_API_KEY";


//$contents = file_get_contents($url);
//$clima=json_decode($contents,true);
//$temp_max = $clima['coord']['lon'];
//echo $temp_max;
//$temp_max=$clima->main->temp;

//$string1 = "http://api.openweathermap.org/data/2.5/weather?q=".$startloc."&appid=your appid";
//$data1 = json_decode(file_get_contents($string1),true);
 
 //$temp1 = $data1[main]['temp'];
 //$temp1 = $data1->main->temp;
//echo $temp1;
//echo '<script language="javascript">';
//echo 'alert($temp1)';
//echo '</script>';     '.$temp1.'
//echo '<script type="text/javascript">alert("' .$temp_max. '");</script>';
}


?>

<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Directions Service (Complex)</title>
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
      #warnings-panel {
        width: 100%;
        height:10%;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <div id="floating-panel">
    </div>
    <div id="map"></div>
    &nbsp;
    <div id="warnings-panel"></div>
    <script>
	
      function initMap() {
        var markerArray = [];

        // Instantiate a directions service.
        var directionsService = new google.maps.DirectionsService;

        // Create a map and center it on Manhattan.
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: {lat: 40.771, lng: -73.974}
        });

        // Create a renderer for directions and bind it to the map.
        var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

        // Instantiate an info window to hold step text.
        var stepDisplay = new google.maps.InfoWindow;

        // Display the route between the initial start and end selections.
        calculateAndDisplayRoute(
            directionsDisplay, directionsService, markerArray, stepDisplay, map);
        // Listen to change events from the start and end lists.
        var onChangeHandler = function() {
          calculateAndDisplayRoute(
              directionsDisplay, directionsService, markerArray, stepDisplay, map);
        };
        document.getElementById('start').addEventListener('change', onChangeHandler);
        document.getElementById('end').addEventListener('change', onChangeHandler);
      }

      function calculateAndDisplayRoute(directionsDisplay, directionsService,
          markerArray, stepDisplay, map) {
        // First, remove any existing markers from the map.
        for (var i = 0; i < markerArray.length; i++) {
          markerArray[i].setMap(null);
        }

        // Retrieve the start and end locations and create a DirectionsRequest using
        // WALKING directions.
        directionsService.route({
          origin: "<?php echo $_POST['start'] ?>",
          destination: "<?php echo $_POST['end'] ?>",
          travelMode: 'WALKING'
        }, function(response, status) {
          // Route the directions and pass the response to a function to create
          // markers for each step.
          if (status === 'OK') {
            document.getElementById('warnings-panel').innerHTML =
                '<b>' + response.routes[0].warnings + '</b>';
            directionsDisplay.setDirections(response);
            showSteps(response, markerArray, stepDisplay, map);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

      function showSteps(directionResult, markerArray, stepDisplay, map) {
        // For each step, place a marker, and add the text to the marker's infowindow.
        // Also attach the marker to an array so we can keep track of it and remove it
        // when calculating new routes.
		//following code retireves the waypoints , reduce the count using i value
        var myRoute = directionResult.routes[0].legs[0];
        for (var i = 0; i < myRoute.steps.length; i+=50) {
          
	var marker = markerArray[i] = markerArray[i] || new google.maps.Marker;
          marker.setMap(map);
          marker.setPosition(myRoute.steps[i].start_location);
		  //get the latitue and longitude separately:
	var latitude = myRoute.steps[i].start_location.lat();
	var longitude = myRoute.steps[i].start_location.lng();
	//window.alert(myRoute.steps[i].start_location); myRoute.steps[i].start_location has lat,lon
	//window.alert(latitude);
	//window.alert(longitude);
	  
	var requestString = "http://api.openweathermap.org/data/2.5/weather?lat="+latitude+"&lon="+longitude+"&units=imperial&APPID=YOUR_API_KEY";
    request = new XMLHttpRequest();
    request.onload = proccessResults;
    request.open("get", requestString, true);
    request.send();
	var proccessResults = function() {
    console.log(this);
    var results = JSON.parse(this.responseText);
	   var t = results.main.temp;
		var city = results.name;
		window.alert(city+" "+":"+t);
	
  };
	attachInstructionText(
              stepDisplay, marker, myRoute.steps[i].instructions, map);
	
	
 
        
	}
	
      }

      function attachInstructionText(stepDisplay, marker, text, map) {
        google.maps.event.addListener(marker, 'click', function() {
          // Open an info window when the marker is clicked on, containing the text
          // of the step.
          //stepDisplay.setContent(text);
          //stepDisplay.open(map, marker);
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
    </script>
  </body>
</html>
