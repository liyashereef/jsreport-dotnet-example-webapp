{{-- @extends('layouts.app') --}}
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Mobile Patrol Trip</title>
    <style>
      html, body, #map {
        height: 100%;
        margin: 0px;
        padding: 0px
      }

      #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }

      #bar {
        width: 240px;
        background-color: rgba(255, 255, 255, 0.75);
        margin: 8px;
        padding: 4px;
        border-radius: 4px;
      }

      #autoc {
        width: 100%;
        box-sizing: border-box;
      }
    </style>

    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <!-- <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDaPSADB8krG-7m60xjQEQR7ABKQ_4joSw&callback=initMap">
    </script> -->

    <script
      src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}&libraries=drawing,places"></script>
    <script>
var apiKey = 'AIzaSyDaPSADB8krG-7m60xjQEQR7ABKQ_4joSw';

var map;
var drawingManager;
var placeIdArray = [];
var polylines = [];
var snappedCoordinates = [];
apiKey = 'AIzaSyDaPSADB8krG-7m60xjQEQR7ABKQ_4joSw';
function initialize() {
    coordinate_string = "{!! $coordinates['formatted_coordinates'] !!}";
    array_coordinates = coordinate_string.split("|");
    array_coordinate = array_coordinates[0].split(",");
    var latitude = array_coordinate[0];
    var longitude = array_coordinate[1];
  var mapOptions = {
    zoom: 7,
    center: {lat: parseFloat(latitude), lng: parseFloat(longitude)}
  };
  map = new google.maps.Map(document.getElementById('map'), mapOptions);

// For marking Source and Destination
    var pointS = new google.maps.LatLng(array_coordinate[0],array_coordinate[1]);
    destination_coordinate = array_coordinates[array_coordinates.length -1].split(",");
    var pointD = new google.maps.LatLng(destination_coordinate[0],destination_coordinate[1]);

    directionsService = new google.maps.DirectionsService,
    directionsDisplay = new google.maps.DirectionsRenderer({
      map: map
    }),
    markerA = new google.maps.Marker({
      position: pointS,
      title: "",
      label: "S",
      map: map
    }),
     markerB = new google.maps.Marker({
      position: pointD,
      title: "",
      label: "D",
      map: map
    });

  runSnapToRoad();

}

// Snap a user-created polyline to roads and draw the snapped path
function runSnapToRoad() {
  var pathValues = [];
  pathValues = "{!! $coordinates['formatted_coordinates'] !!}";
  //console.log(pathValues.length);
  pathValues_array = pathValues.split('|');

  //result = tokens.join('|'); // those.that
  //console.log(pathValues);
  //console.log(pathValues_array);
  pathValues_array_length = pathValues_array.length;
  for(i=0;i<pathValues_array_length;i=i+100)
  {
    bal = (pathValues_array_length > i+100) ? i+100 : pathValues_array_length ;
    path = '';
    path = pathValues_array.slice(i,bal).join('|');
    
    if(path!='')
    {
      $.get('https://roads.googleapis.com/v1/snapToRoads', {
        interpolate: true,
        key: apiKey,
        path: path
      }, function(data) {
        processSnapToRoadResponse(data);
        drawSnappedPolyline();

      });
    }
   
    
  }
  
     //Draw original points
     var placeIdArray = [];
     var originalpolylines = [];
     originalCoordinates = [];
     //var cdee = [];
      var lat_lng_coordinates = <?php echo json_encode($coordinates['original_coordinates']); ?>;
      //console.log(lat_lng_coordinates);
     for (var i = 0; i < lat_lng_coordinates.length; i++) {
     var latlng = new google.maps.LatLng(
      lat_lng_coordinates[i].latitude,
      lat_lng_coordinates[i].longitude);
        originalCoordinates.push(latlng);
    //placeIdArray.push(data.snappedPoints[i].placeId);
  }

     var originalPolyline = new google.maps.Polyline({
      path: originalCoordinates,
      strokeColor: '#003A63',
      strokeWeight: 5
    });

    originalPolyline.setMap(map);
    originalpolylines.push(originalPolyline);

}

// Store snapped polyline returned by the snap-to-road service.
function processSnapToRoadResponse(data) {
  snappedCoordinates = [];
  placeIdArray = [];
  for (var i = 0; i < data.snappedPoints.length; i++) {
    var latlng = new google.maps.LatLng(
        data.snappedPoints[i].location.latitude,
        data.snappedPoints[i].location.longitude);
    snappedCoordinates.push(latlng);
    placeIdArray.push(data.snappedPoints[i].placeId);
  }
}

// Draws the snapped polyline (after processing snap-to-road response).
function drawSnappedPolyline() {
  var snappedPolyline = new google.maps.Polyline({
    path: snappedCoordinates,
    strokeColor: '#f26538',
    strokeWeight: 4
  });

  snappedPolyline.setMap(map);
  polylines.push(snappedPolyline);
}

// Gets speed limits (for 100 segments at a time) and draws a polyline
// color-coded by speed limit. Must be called after processing snap-to-road
// response.
function getAndDrawSpeedLimits() {
  for (var i = 0; i <= placeIdArray.length / 100; i++) {
    // Ensure that no query exceeds the max 100 placeID limit.
    var start = i * 100;
    var end = Math.min((i + 1) * 100 - 1, placeIdArray.length);

    drawSpeedLimits(start, end);
  }
}

// Gets speed limits for a 100-segment path and draws a polyline color-coded by
// speed limit. Must be called after processing snap-to-road response.
function drawSpeedLimits(start, end) {
    var placeIdQuery = '';
    for (var i = start; i < end; i++) {
      placeIdQuery += '&placeId=' + placeIdArray[i];
    }

    $.get('https://roads.googleapis.com/v1/speedLimits',
        'key=' + apiKey + placeIdQuery,
        function(speedData) {
          processSpeedLimitResponse(speedData, start);
        }
    );
}

// Draw a polyline segment (up to 100 road segments) color-coded by speed limit.
function processSpeedLimitResponse(speedData, start) {
  var end = start + speedData.speedLimits.length;
  for (var i = 0; i < speedData.speedLimits.length - 1; i++) {
    var speedLimit = speedData.speedLimits[i].speedLimit;
    var color = getColorForSpeed(speedLimit);

    // Take two points for a single-segment polyline.
    var coords = snappedCoordinates.slice(start + i, start + i + 2);

    var snappedPolyline = new google.maps.Polyline({
      path: coords,
      strokeColor: color,
      strokeWeight: 6
    });
    snappedPolyline.setMap(map);
    polylines.push(snappedPolyline);
  }
}


function getColorForSpeed(speed_kph) {
  if (speed_kph <= 40) {
    return 'purple';
  }
  if (speed_kph <= 50) {
    return 'blue';
  }
  if (speed_kph <= 60) {
    return 'green';
  }
  if (speed_kph <= 80) {
    return 'yellow';
  }
  if (speed_kph <= 100) {
    return 'orange';
  }
  return 'red';
}

$(window).load(initialize);

    </script>
  </head>

  <body>
    <div id="map"></div>
    <div id="bar">
    </div>
  </body>
</html>
