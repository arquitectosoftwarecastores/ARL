<?php 
//Consulta para obtener los puntos de una unidad guardadas en la tabla monitoreo.puntos_rutas 
$id=$_GET["id"]; 
$consulta = "select (select txt_nombre_zon from monitoreo.tb_zonas where pk_clave_zon = r.fk_clave_zon1 ) as origen, (select txt_nombre_zon from monitoreo.tb_zonas where pk_clave_zon = r.fk_clave_zon2) as destino, pr.latitud, pr.longitud, r.txt_nombre_rut from monitoreo.puntos_rutas pr inner join monitoreo.tb_rutas r on pr.ruta = r.pk_clave_rut where ruta = ?";
$query = $conn->prepare($consulta);
$query->bindParam(1, $id);
$query->execute();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
$registro = $query->fetch();  
?>
<!--Encabezado de la Pagina donde aparece la informacion de la ruta -->
<!DOCTYPE html> <html>
  <head>   
 <title> Trazado de Ruta</title>   
 <div class="col-md-3">
    Ruta: <?php echo $registro['txt_nombre_rut'] ?>.  
 </div> 
 <div class="col-md-4">
    Origen: <?php echo $registro['origen'] ?>. <br>
 </div> 
 <div class="col-md-3">
    Destino: <?php echo $registro['destino'] ?>.
 </div> 

<!--Estilos de la pagina -->
 <style type="text/css">
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: Roboto, Noto, sans-serif;
      }

      #map {
        height: 500px;
      }

      #interpolate {
        width: 2em;
        height: 2em;
      }

      #coords {
        resize: vertical;
        min-height: 75px;
        max-height: 200px;
       }

      .block {
        clear: both;
        margin: 1.5em auto;
        text-align: center;
      }

      #legend {
        float: center;
        margin: 5px 15px;
        font-size: 13px;
      }

      .button {
      display: inline-block;
      position: relative;
      border: 0;
      padding: 0 1.7em;
      min-width: 120px;
      height: 32px;
      line-height: 32px;
      border-radius: 2px;
      font-size: 0.9em;
      background-color: #fff;
      color: #646464;
    }

    .button.narrow {
      width: 60px;
    }

    .button.grey {
      background-color: #eee;
    }

    .button.blue {
      background-color: #4285f4;
      color: #fff;
    }

    .button.green {
      background-color: #0f9d58;
      color: #fff;
    }

    .button.raised {
      transition: box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      transition-delay: 0.2s;
      box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
    }

    .button.raised:active {
      box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2);
      transition-delay: 0s;
    }

    .floating {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 5;
      background-color: rgba(255, 255, 255, 0.75);
      padding: 1px;
      border: 1px solid #999;
      text-align: center;
      line-height: 18px;
    }

    .floating.panel {
      width: 400px;
    }

    .coords-small {
      width: 350px;
    }

    .coords-large {
      width: 400px;
    }

    .button-div {
      padding: 0px 50px;
      width: 300px;
      line-height: 40px;
    }

    #toggle {
      width: 25px;
      z-index: 10;
      cursor: default;
      font-size: 2em;
      padding: 1px;
      color: #999;
      display: none;
    }

    </style>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyA3g0rYfYLQ0yIa50zWwa-dkghVnfk5d-4">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
    // Replace with your own API key
var API_KEY = 'AIzaSyA3g0rYfYLQ0yIa50zWwa-dkghVnfk5d-4';

// Icons for markers
var RED_MARKER = 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';
var GREEN_MARKER = 'https://maps.google.com/mapfiles/ms/icons/green-dot.png';
var BLUE_MARKER = 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
var YELLOW_MARKER = 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';

// URL for places requests
var PLACES_URL = 'https://maps.googleapis.com/maps/api/place/details/json?' +
                'key=' + API_KEY + '&placeid=';

// URL for Speed limits
var SPEED_LIMIT_URL = 'https://roads.googleapis.com/v1/speedLimits';

var coords;
/**
 * @type Array<ExtendedLatLng>
 */
var originals = [];     // the original input points, a list of ExtendedLatLng
var interpolate = true;
var map;
var placesService;
var originalCoordsLength;

// Settingup Arrays
var infoWindows = [];
var markers = [];
var placeIds = [];
var polylines = [];
var snappedCoordinates = [];

// Tipo de Símbolo animado que pasa por la ruta
var lineSymbol = {
  path: google.maps.SymbolPath.FORWARD_OPEN_ARROW,
  scale: 4,
  strokeColor: '#815db5',
  strokeWidth: '#005db5'
};

var puntos='';
var size=0;
var contador=0;
<?php 
$sizeRegistro=0;
$contadorRegistro=0;
while($registro = $query->fetch()){    
    $contadorRegistro++;
    $sizeRegistro=($query->rowCount())-1;  
    ?>
          size=<?php echo $sizeRegistro ?>; 
          contador=<?php echo $contadorRegistro ?>; 
      <?php
     if($contadorRegistro==$sizeRegistro){         
         ?>         
            puntos+='<?php echo $registro["latitud"] ?>,'+'<?php echo $registro["longitud"] ?>';   
       <?php              
     }
     else{   
      ?>   
       puntos+='<?php echo $registro["latitud"] ?>,'+'<?php echo $registro["longitud"] ?>|';     
      <?php
     }//fin else
  }
?>
//console.log("los puntos son"+puntos+"-->"+size+"-->"+contador);
var eg1 = puntos;
//funcion inicializa mostrar el mapa
function initialize() {      
  $('#eg1').click(function(e) {
    $('#coords').val(eg1);
    $('#plot').trigger('click');
  });

/*  $('#eg2').click(function(e) {
    $('#coords').val(eg2);
    $('#plot').trigger('click');
  });

  $('#eg3').click(function(e) {
    $('#coords').val(eg3);
    $('#plot').trigger('click');
  });

  $('#eg4').click(function(e) {
    $('#coords').val(eg4);
    $('#plot').trigger('click');
  });
*/
  $('#toggle').click(function(e) {
     if ($('#panel').css("display") != 'none') {
        $('#toggle').html("+");
        $('#panel').hide("scale",{origin:["top","right"]});
     } else {
        $('#toggle').html("&mdash;");
        $('#panel').show("scale",{origin:["top","right"]});
     }
  });

  // Centramos el mapa para inicializar en la ciudad de Leon Gto.
  var mapOptions = {
    center: {'lat': 21.11, 'lng':-101.62},
    zoom: 10
  };

  // Creamos el objeto del mapa
  map = new google.maps.Map(document.getElementById('map'), mapOptions);

  // Creamos el objeto de los lugares
  placesService = new google.maps.places.PlacesService(map);

  // Limpiamos el mapa tanto la linea como los marcadores
  function clearMap() {
    // Limpiamos la polilinea
    for (var i = 0; i < polylines.length; i++) {
      polylines[i].setMap(null);
    }
    // Limpiamos los marcadores
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }

    //Vaciamos todo
    polylines = [];
    markers = [];
    snappedCoordinates = [];
    placeIds = [];
    infoWindows = [];
    $('#unsnappedPoints').empty();
  }

  // Parse the value in the input element to get all coordinates
  function parseCoordsFromQuery(input) {
    var coords;
    input = decodeURIComponent(input);
    if (input.split('path=').length > 1) {
      input = decodeURIComponent(input);
      // Split on the ampersand to get all params
      var parts = input.split('&');
      // Check each part to see if it starts with 'path='
      // grabbing out the coordinates if it does
      for (var i = 0; i < parts.length; i++) {
        if (parts[i].split('path=').length > 1) {
          coords = parts[i].split('path=')[1];
          break;
        }
      }
    } else {
      coords = decodeURIComponent(input);
    }

    // Parse the "Lat,Lng|..." coordinates into an array of ExtendedLatLng
    originals = [];
    var points = coords.split('|');
    for (var i = 0; i < points.length; i++) {
      var point = points[i].split(',');
      originals.push({lat: Number(point[0]), lng: Number(point[1]), index:i});
    }
    return coords;
  }

  // Clear the map of any old data and plot the request
  $('#plot').click(function(e) {
    clearMap();
    bendAndSnap();
    e.preventDefault();
  });

  // Make AJAX request to the snapToRoadsAPI
  // with coordinates parsed from text input element.
  function bendAndSnap() {
    coords = parseCoordsFromQuery($('#coords').val());
    location.hash = coords;
    $.ajax({
      type: 'GET',
      url: 'https://roads.googleapis.com/v1/snapToRoads',
      data: {
        interpolate: $('#interpolate').is(':checked'),
        key: API_KEY,
        path: coords
      },
      success: function(data) {
        $('#requestURL').html('<a target="blank" href="' +
            this.url + '">Request URL</a>');
        processSnapToRoadResponse(data);
        drawSnappedPolyline(snappedCoordinates);
        drawOriginals(originals);
        fitBounds(markers);
      },
      error: function() {
        $('#requestURL').html('<strong>That query didn\'t work :(</strong>' +
            '<p>Try looking at the <a href="' + this.url +
            '">Request URL</a></p>');
        clearMap();
      }
    });
  }

  //Recupera las coordenadas de los elementos pasados por puntos
  function getMissingPoints(originalIndexes, originalCoordsLength) {
    var unsnappedPoints = [];
    var coordsArray = coords.split('|');
    var hasMissingCoords = false;
    for (var i = 0; i < originalCoordsLength; i++) {
      if (originalIndexes.indexOf(i) < 0) {
        hasMissingCoords = true;
        var latlng = {
          'lat': parseFloat(coordsArray[i].split(',')[0]),
          'lng': parseFloat(coordsArray[i].split(',')[1])
        };
        unsnappedPoints.push(latlng);
        latlng.unsnapped = true;
      }
    }
    return unsnappedPoints;
  }

  // Parse response from snapToRoads API request
  // Store all coordinates in response
  // Calls functions to add markers to map for unsnapped coordinates
  function processSnapToRoadResponse(data) {
    var originalIndexes = [];
    var unsnappedMessage = '';
    for (var i = 0; i < data.snappedPoints.length; i++) {
      var latlng = {
        'lat': data.snappedPoints[i].location.latitude,
        'lng': data.snappedPoints[i].location.longitude
      };
      var interpolated = true;
      if (data.snappedPoints[i].originalIndex != undefined) {
        interpolated = false;
        originalIndexes.push(data.snappedPoints[i].originalIndex);
        latlng.originalIndex = data.snappedPoints[i].originalIndex;
      }
      latlng.interpolated = interpolated;
      snappedCoordinates.push(latlng);
      placeIds.push(data.snappedPoints[i].placeId);
      // Cross-reference the original point and this snapped point.
      latlng.related = originals[latlng.originalIndex];
      originals[latlng.originalIndex].related = latlng;
    }
    var unsnappedPoints = getMissingPoints(
        originalIndexes,
        coords.split('|').length
    );

    for (var i = 0; i < unsnappedPoints.length; i++) {
      var marker = addMarker(unsnappedPoints[i]);
      var infowindow = addBasicInfoWindow(marker, unsnappedPoints[i], i);
      infoWindows.push(infowindow);
      unsnappedMessage += unsnappedPoints[i].lat + ',' +
          unsnappedPoints[i].lng + '<br>';
    }

    if (unsnappedPoints.length) {
      unsnappedMessage = '<strong>' +
         'These points weren\'t snapped: ' +
         '</strong><br>' + unsnappedMessage;
      $('#unsnappedPoints').html(unsnappedMessage);
    }
  }

  // Draw the polyline for the snapToRoads API response
  // Call functions to add markers and infowindows for each snapped
  // point along the polyline.
  function drawSnappedPolyline(snappedCoords) {
    var snappedPolyline = new google.maps.Polyline({
      path: snappedCoords,
      strokeColor: '#005db5',
      strokeWeight: 6,
      icons: [{
        icon: lineSymbol,
        offset: '100%'
      }]
    });

    snappedPolyline.setMap(map);
    animateCircle(snappedPolyline);

    polylines.push(snappedPolyline);

    for (var i = 0; i < snappedCoords.length; i++) {
      var marker = addMarker(snappedCoords[i]);
      var infoWindow = addDetailedInfoWindow(marker,
          snappedCoords[i],
          placeIds[i]);
      infoWindows.push(infoWindow);
    }
  }

  // Draw the original input.
  // Call functions to add markers and infowindows for each point.
  function drawOriginals(originalCoords) {
    for (var i = 0; i < originalCoords.length; i++) {
      var marker = addMarker(originalCoords[i]);
      var infoWindow = addBasicInfoWindow(marker, originalCoords[i], i);
      infoWindows.push(infoWindow);
    }
  }

  // Infowindow used for unsnappable coordinates
  function addBasicInfoWindow(marker, coords, index) {
    var infowindow = new google.maps.InfoWindow();
    var content = '<div style="width:99%"><p>' +
        '<strong>Lat/Lng:</strong><br>' +
        '(' + coords.lat + ',' + coords.lng + ')<br>' +
        (index != undefined ? '<strong>Index: </strong>' + index : '') +
        '</p></div>';

    infowindow.setContent(content);

    google.maps.event.addListener(marker, 'click', function() {
      openInfoWindow(infowindow, marker);
    });

    return infowindow;
  }

  // Infowindow used for snapped points
  // Makes request to Places Details API to get data about each
  // Place ID.
  // Requests speed limit of each location using Roads SpeedLimit API
  function addDetailedInfoWindow(marker, coords, placeId) {
    var infowindow = new google.maps.InfoWindow();
    var placesRequestUrl = PLACES_URL + placeId;
    var detailsUrl = '<a target="_blank" href="' +
        placesRequestUrl + '">' +
        placeId + '</a></li>';

    // On click we make a request to the Places API
    // This is to avoid OVER_QUERY_LIMIT if we just requested everything
    // at the same time
    google.maps.event.addListener(marker, 'click', function() {
      content = '<div style="width:99%"><p>';

      function finishInfoWindow(placeDetails) {
        content += '<strong>Place Details: </strong>' + placeDetails + '<br>' +
            '<strong>' +
            (coords.interpolated ? 'Coords' : 'Snapped coords') +
            ': </strong>' +
            '(' + coords.lat.toFixed(5) + ',' +
            coords.lng.toFixed(5) + ')<br>';

        if (!(coords.interpolated)) {
          var original = originals[coords.originalIndex];
          content += '<strong>Original coords: </strong>' +
              '(' + original.lat + ',' + original.lng + ')<br>' +
              '<strong>Original Index: </strong>' +
              coords.originalIndex;
        }
        content += '</p></div>';
        infowindow.setContent(content);
        openInfoWindow(infowindow, marker);
      };

      getPlaceDetails(placeId, function(place) {
        if (place.name) {
          content += '<strong>' + place.name + '</strong><br>';
        }
        getSpeedLimit(placeId, function(data) {
          if (data.speedLimits) {
            content += '<strong>Speed Limit: </strong>' +
                data.speedLimits[0].speedLimit + ' km/h <br>';
          }
          finishInfoWindow(detailsUrl);
        });
      }, function() { finishInfoWindow("<em>None available</em>"); });
    });
    return infowindow;
  }

  // Avoid infoWindows staying open if the pano changes
  listenForPanoChange();

  // If the user came to the page with a particular path or URL,
  // immediately plot it.
  if (location.hash.length > 1) {
    coords = parseCoordsFromQuery(location.hash.slice(1));
    $('#coords').val(coords);
    $('#plot').click();
  }
} // End init function

// Call the initialize function once everything has loaded
google.maps.event.addDomListener(window, 'load', initialize);

//Load the control panel in a floating div if it is not loaded in an iframe
//after the textarea has been rendered
$("#coords").ready(function() {
    if (!window.frameElement) {
       $('#panel').addClass("floating panel");
       $('#button-div').addClass("button-div");
       $('#coords').removeClass("coords-large").addClass("coords-small");
       $('#toggle').show();
       $('#map').height('100%');
    }
});

    /**
*  latlng literal with extra properties to use with the RoadsAPI
*  @typedef {Object} ExtendedLatLng
*   lat:string|float
*   lng:string|float
*   interpolated:boolean
*   unsnapped:boolean
*/

/**
 * Add a line to the map for highlighting the connection between two
 * markers while the mouse is over it.
 * @param {ExtendedLatLng} from - The origin of the line
 * @param {ExtendedLatLng} to - The destination of the line
 * @return {!Object} line - the polyline object created
 */
function addOverline(from, to) {
  return addLine("overline", from, to, '#ff77ff', 4, 1.0, 2.0, false);
}

/**
 * Add a line to the map for highlighting the connection between two
 * markers while the mouse is NOT over it.
 * @param {ExtendedLatLng} from - The origin of the line
 * @param {ExtendedLatLng} to - The destination of the line
 * @return {!Object} line - the polyline object created
 */
function addOutline(from, to) {
  return addLine("outline", from, to, '#bb33bb', 2, 0.5, 1.35, true);
}

/**
 * Add a line to the map for highlighting the connection between two
 * markers.
 * @param {string}         attrib  - The attribute to use for managing the line
 * @param {ExtendedLatLng} from    - The origin of the line
 * @param {ExtendedLatLng} to      - The destination of the line
 * @param {string}         color   - The color of the line
 * @param {number}         weight  - The weight of the line
 * @param {number}         opacity - The opacity of the line (0..1)
 * @param {number}         scale   - The scale of the arrow-head (pt)
 * @param {boolean}        visible - The visibility of the line
 * @return {!Object}       line    - the polyline object created
 */
function addLine(attrib, from, to, color, weight, opacity, scale, visible) {
  from[attrib] = new google.maps.Polyline({
    path:         [from, to],
    strokeColor:  color,
    strokeWeight:  weight,
    strokeOpacity: opacity,
    icons:[{
      offset: "0%",
      icon: {
        scale: scale/*pt*/,
        path:  google.maps.SymbolPath.BACKWARD_CLOSED_ARROW
      }
    }]
  });
  from[attrib].setVisible(visible);
  from[attrib].setMap(map);
  to[attrib] = from[attrib];
  polylines.push(from[attrib]);
  return from[attrib];
}

/**
 * Add a pair of lines to the map for highlighting the connection between two
 * markers; one visible while the mouse is over the marker (the "overline"),
 * the other while it is not (the "outline").
 * @param {ExtendedLatLng} from - The origin of the line (the original input)
 * @param {ExtendedLatLng} to - The destination of the line (the snapped point)
 * @return {!Object} line - the polyline object created
 */
function addCorrespondence(coords, marker) {
  if (!coords.overline) { addOverline(coords, coords.related); }
  if (!coords.outline)  { addOutline(coords, coords.related); }

  marker.addListener('mouseover', function(mevt) {
    coords.outline.setVisible(false);
    coords.overline.setVisible(true);
    coords.related.marker.setOpacity(1.0);
  });
  marker.addListener('mouseout', function(mevt) {
    coords.overline.setVisible(false);
    coords.outline.setVisible(true);
    coords.related.marker.setOpacity(0.5);
  });
}

/**
 * Add a marker to the map and check for special 'interpolated'
 * and 'unsnapped' properties to control which colour marker is used
 * @param {ExtendedLatLng} coords - Coords of where to add the marker
 * @return {!Object} marker - the marker object created
 */
function addMarker(coords) {
  var marker = new google.maps.Marker({
    position: coords,
    title: coords.lat + ',' + coords.lng,
    map: map,
    opacity: 0.5,
    icon: RED_MARKER
  });

  // Coord should NEVER be interpolated AND unsnapped
  if (coords.interpolated) {
    marker.setIcon(BLUE_MARKER);
  } else if (!coords.related) {
    marker.setIcon(YELLOW_MARKER);
  } else if (coords.originalIndex != undefined) {
    marker.setIcon(RED_MARKER);
    addCorrespondence(coords, marker);
  } else {
    marker.setIcon({url: GREEN_MARKER,
                    scaledSize: {width: 20, height: 20}});
    addCorrespondence(coords, marker);
  }

  // Make markers change opacity when the mouse scrubs across them
  marker.addListener('mouseover', function(mevt) {
    marker.setOpacity(1.0);
  });
  marker.addListener('mouseout', function(mevt) {
    marker.setOpacity(0.5);
  });

  coords.marker = marker;  // Save a reference for easy access later
  markers.push(marker);

  return marker;
}

/**
 * Animate an icon along a polyline
 * @param {Object} polyline The line to animate the icon along
 */
function animateCircle(polyline) {
  var count = 0;
  // fallback icon if the poly has no icon to animate
  var defaultIcon = [
    {
      icon: lineSymbol,
      offset: '100%'
    }
  ];
  window.setInterval(function() {
    count = (count + 1) % 200;
    var icons = polyline.get('icons') || defaultIcon;
    icons[0].offset = (count / 2) + '%';
    polyline.set('icons', icons);
  }, 20);
}

/**
 * Fit the map bounds to the current set of markers
 * @param {Array<Object>} markers Array of all map markers
 */
function fitBounds(markers) {
  var bounds = new google.maps.LatLngBounds;
  for (var i = 0; i < markers.length; i++) {
    bounds.extend(markers[i].getPosition());
  }
  map.fitBounds(bounds);
}

/**
 * Uses Places library to get Place Details for a Place ID
 * @param {string}   placeId         The Place ID to look up
 * @param {Function} foundCallback   Called if the place is found
 * @param {Function} missingCallback Called if nothing is found
 * @param {Function} errorCallback   Called if request fails
 */
function getPlaceDetails(placeId,
                         foundCallback, missingCallback, errorCallback) {
  var request = {
    placeId: placeId
  };

  placesService.getDetails(request, function(place, status) {
    if (status == google.maps.places.PlacesServiceStatus.OK) {
      foundCallback(place);
    } else if (status == google.maps.places.PlacesServiceStatus.NOT_FOUND) {
      missingCallback();
    } else if (errorCallback) {
      errorCallback();
    }
  });
}

/**
 * AJAX request to the Roads Speed Limit API.
 * Request the speed limit for the Place ID
 * @param {string}   placeId         Place ID to request the speed limit for
 * @param {Function} successCallback Called if request is successful
 * @param {Function} errorCallback   Called if request fails
 */
function getSpeedLimit(placeId, successCallback, errorCallback) {
  $.ajax({
    type: 'GET',
    url: SPEED_LIMIT_URL,
    data: {
      placeId: placeId,
      key: API_KEY
    },
    success: successCallback,
    error: errorCallback
  });
}

/**
 * Open an infowindow on either the map or the active streetview pano
 * @param {Object} infowindow Infowindow to be opened
 * @param {Object} marker Marker the infowindow is anchored to
 */
function openInfoWindow(infowindow, marker) {
  // If streetView is visible display the infoWindow over the pano
  // and anchor to the marker
  if (map.getStreetView().getVisible()) {
    infowindow.open(map.getStreetView(), marker);
  }
  // Otherwise open it on the map and anchor to the marker
  else {
    infowindow.open(map, marker);
  }
}

/**
 * Add event listener to for when the active pano changes
 */
function listenForPanoChange() {
  var pano = map.getStreetView();

  // Close all open markers when the pano changes
  google.maps.event.addListener(pano, 'position_changed', function() {
    closeAllInfoWindows(infoWindows);
  });
}

/**
 * Close all open infoWindows
 * @param {Array<Object>} infoWindows - all infowindow objects
 */
function closeAllInfoWindows(infoWindows) {
  for (var i = 0; i < infoWindows.length; i++) {
    infoWindows[i].close();
  }
}

    </script>
  </head>
  <body>
    <div class="floating" id="toggle">—</div>
    <div id="panel">
      <div class="col-md-3">
        <strong>Recorrido</strong> <div id="button-div">
          <button id="eg1" class="button raised blue">Actualizar Puntos</button>
 <!--         <button id="eg2" class="button raised blue">EJEMPLO 2</button>
          <button id="eg3" class="button raised blue">EJEMPLO 3</button>
          <button id="eg4" class="button raised blue">EJEMPLO 4</button> -->
        </div>
      </div>
      <form id="controls">
       <div class="col-md-3">
          <div>
            <strong><span id="requestURL">Solicitar URL</span> o ruta de acceso (separadas por barras verticales)</strong><br /> <textarea id="coords" class="u-full-width coords-large" type="text" placeholder="-35.123,150.332 | 80.654,22.439" id="exampleEmailInput"></textarea>
          </div> 
       <!--   <div>
            <label>Interpolar: </label>
            <input for="interpolate" id="interpolate" type='"checkbox" checked/'>
          </div> -->
        </div> 
        <div>
         <div class="col-md-3">
           <button id="plot" class="button raised blue">Indicar un sentido</button> 
          </div> 
          <div id="legend">
          <!--  <img src="https://maps.google.com/mapfiles/ms/icons/green-dot.png" style="height:16px;" /> Original <img src="https://maps.google.com/mapfiles/ms/icons/red-dot.png" /> Ajustado <img src="https://maps.google.com/mapfiles/ms/icons/blue-dot.png" /> Interpolado <img src="https://maps.google.com/mapfiles/ms/icons/yellow-dot.png" /> No ajustable -->
          </div>
          <div>
          <!--  <p id="unsnappedPoints">-->
            </p>
          </div>
        </div>
      </form>
    </div>
    <div id="map">
    </div>
  </body>
</html> 