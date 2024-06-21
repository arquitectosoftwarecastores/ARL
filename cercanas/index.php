<?php if (isset($_SESSION["cercanas"]) || true) {
  if (isset($_POST["id"]))
    $id = $_POST["id"];
  else
    $id = 0;

  if (isset($_POST["pinteres"]))
    $pinteres = $_POST["pinteres"];
  else
    $pinteres = 0;

  if (isset($_POST["latitud"]))
    $latitud = $_POST["latitud"];
  else
    $latitud = 0;

  if (isset($_POST["longitud"]))
    $longitud = $_POST["longitud"];
  else
    $longitud = 0;

  if (isset($_POST["radio"]))
    $radio = $_POST["radio"];
  else
    $radio = 10;

  $consulta  = " SELECT * FROM tb_remolques where estatus = 1 ORDER BY txt_economico_rem ASC, txt_nserie_rem ASC";
  $query = $conn->prepare($consulta);
  $query->execute();

  $consulta2  = " SELECT * FROM tb_puntosseguros ORDER BY txt_nombre_pun";
  $query2 = $conn->prepare($consulta2);
  $query2->execute();

  /** Almacena Consultas de Google Maps  */
  include('procesos/app_consultamap.php');
  consultaMaps(22, NULL, $conn);

  /** Google Maps API Key */
  include('./googlemapsapi/key.php');

?>
  <form id="form1" name="form1" method="POST" action="?index.php&amp;seccion=cercanas">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-2">
          Unidad:<br>
          <select name="id" id="id" class="text-input text form-control">
            <option value="0" <?php if ($id == 0) echo "selected" ?>>Seleccione una unidad</option>
            <?php

            $selects = "";
            while ($registro = $query->fetch()) {
              if ($id == $registro['txt_economico_rem'])
                $seleccionado = "selected";
              else
                $seleccionado = "";

              $select .= '<option data-latitud="' . $registro['num_latitud_rem'] . '" data-longitud="' . $registro['num_longitud_rem'] . '" value="' . $registro['txt_economico_rem'] . '">' . $registro['txt_economico_rem'] . '</option>';
            }

            echo $select;

            ?>
          </select>
        </div>
        <div class="col-md-4">
          Punto de interés:<br>
          <select name="pinteres" id="pinteres" class="text-input text form-control">
            <option value="0" <?php if ($pinteres == 0) echo "selected" ?>>Seleccione un punto de interés</option>
            <?php

            while ($registro2 = $query2->fetch()) {
              if ($pinteres == $registro2['pk_clave_pun'])
                $seleccionado = "selected";
              else
                $seleccionado = "";
            ?>
              <option data-latitud="<?php echo $registro2['num_latitud_pun'] ?>" data-longitud="<?php echo $registro2['num_longitud_pun'] ?>" value="<?php echo $registro2['pk_clave_pun']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2['txt_nombre_pun']; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-1">
          Radio en Kms: <input type="text" class="validate[required] form-control" name="radio" id="radio" value="<?php echo $radio ?>" />
        </div>
        <div class="col-md-1">
          Latitud: <input type="text" class="form-control" name="latitud" id="latitud" value="<?php echo $latitud ?>" />
        </div>
        <div class="col-md-1">
          Longitud: <input type="text" class="form-control" name="longitud" id="longitud" value="<?php echo $longitud ?>" />
        </div>
        <div class="col-md-2">
          <br />
          <button class="btn btn-primary" type="button" class="form-control" onclick="buscarCercanas();">CONSULTAR</button>
        </div>
      </div>
    </div>

  </form>

  <script>
    $("#id").change(function() {

      $.ajax({
          url: "cercanas/con_unidad.php?economico=" + $('#id').val(),
          cache: false
        })
        .done(function(pos) {
          pos = JSON.parse(pos);

          $("#latitud").val(pos[0].latitud);
          $("#longitud").val(pos[0].longitud);
          /*
          $("#latitud").val($(this).find(':selected').data('latitud'));
          $("#longitud").val($(this).find(':selected').data('longitud'));
          */
          $("#pinteres").val(0);

        });

    });

    $("#pinteres").change(function() {
      $("#latitud").val($(this).find(':selected').data('latitud'));
      $("#longitud").val($(this).find(':selected').data('longitud'));
      $("#id").val(0);
    });
  </script>


  <div id="map"></div>
  <input type="hidden" id="infovehiculo" value="" />
  <script>
    var vehiculos = [];
    var map;
    var punto, circulo;

    function initMap() {

      <?php
      if ($latitud == 0) {
        $zoom = 5;
      ?>
        var myLatLng = {
          lat: 19.419444,
          lng: -99.145556
        };
      <?php } else {
        $zoom = 10;
      ?>
        var myLatLng = {
          lat: 19.419444,
          lng: -99.145556
        };
      <?php } ?>


      map = new google.maps.Map(document.getElementById('map'), {
        zoom: <?php echo $zoom ?>,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });


      circulo = new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        map: map,
        zIndex: 1,
        center: myLatLng,
        radius: 0
      });

      var bounds = new google.maps.LatLngBounds();
      bounds.extend(myLatLng);
      //map.fitBounds(circulo.getBounds());


      punto = new google.maps.Marker({
        position: myLatLng,
        map: map
      });

      google.maps.event.addListener(map, 'click', function(e) {
        var coordenadas = e.latLng;
        punto.setPosition(e.latLng);
        map.setCenter(e.latLng);
        $("#latitud").val(coordenadas.lat());
        $("#longitud").val(coordenadas.lng());
        $("#pinteres").val(0);
        $("#id").val(0);
      });

      panorama = map.getStreetView();
    }


    function toggleStreetView(latitud, longitud) {
      var toggle = panorama.getVisible();
      var myLatLng = {
        lat: latitud,
        lng: longitud
      };
      if (toggle == false) {
        panorama.setPosition(myLatLng);
        panorama.setVisible(true);
      } else {
        panorama.setVisible(false);
      }
    }
  </script>



  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk; ?>&libraries=places&callback=initMap"></script>
  <?php
    include_once('helpers/LoggerApiGoogleMaps.php');
    $log = new LoggerApiGoogleMaps($conn);
    $log->saveLog($_SESSION["usuario"], 1, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    unset($log);
  ?>
  <script>
    // Define the overlay, derived from google.maps.OverlayView
    function Label(opt_options) {
      // Initialization
      this.setValues(opt_options);

      // Label specific
      var span = this.span_ = document.createElement('span');

      span.className = this.get('color');

      span.style.cssText = 'position: relative; left: -50%; top: 0px; ' +
        'white-space: nowrap; ' +
        'padding: 2px; z-index: 100; font-weight: bold; font-size:10px; color: ' + this.get('codigocolor') + '; text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;';

      var div = this.div_ = document.createElement('div');
      div.appendChild(span);
      div.style.cssText = 'position: absolute; display: none';
    };
    Label.prototype = new google.maps.OverlayView;

    // Implement onAdd
    Label.prototype.onAdd = function() {
      var pane = this.getPanes().overlayLayer;
      pane.appendChild(this.div_);

      // Ensures the label is redrawn if the text or position is changed.
      var me = this;
      this.listeners_ = [
        google.maps.event.addListener(this, 'position_changed',
          function() {
            me.draw();
          }),
        google.maps.event.addListener(this, 'text_changed',
          function() {
            me.draw();
          })
      ];
    };

    // Implement onRemove
    Label.prototype.onRemove = function() {
      this.div_.parentNode.removeChild(this.div_);

      // Label is removed from the map, stop updating its position/text.
      for (var i = 0, I = this.listeners_.length; i < I; ++i) {
        google.maps.event.removeListener(this.listeners_[i]);
      }
    };

    // Implement draw
    Label.prototype.draw = function() {
      var projection = this.getProjection();
      var position = projection.fromLatLngToDivPixel(this.get('position'));

      var div = this.div_;
      div.style.left = position.x + 'px';
      div.style.top = position.y + 'px';
      div.style.display = 'block';

      this.span_.innerHTML = this.get('text').toString();
    };
  </script>

  <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th>No</th>
        <th>Unidad</th>
        <th>Distancia</th>
        <th>Ubicación</th>
        <th>Fecha-hora</th>
      </tr>
    </thead>
    <tbody id="tUnidad">

    </tbody>
  </table>




  <script>
    var tabla_un;
    var mksCercanas = [];
    var labCercanas = [];

    function buscarCercanas() {

      var latC = Number(document.getElementById('latitud').value);
      var lngC = Number(document.getElementById('longitud').value);
      var radC = Number(document.getElementById('radio').value);

      tabla_un = "";
      document.getElementById("tUnidad").innerHTML = tabla_un;

      // Coloca  Marcador del Punto Central
      var latLng = {
        lat: latC,
        lng: lngC
      };

      // Ajusta Mapas
      punto.setPosition(latLng);
      map.panTo(latLng);
      map.setZoom(10);

      // Configura Circulo
      circulo.setVisible(true);
      circulo.setCenter(latLng);
      circulo.setRadius(radC * 1000);

      limpiarUnidades();

      $.ajax({
          url: "cercanas/con_cercanas.php?latitud=" + latC + "&longitud=" + lngC + "&rad=" + radC,
          cache: false
        })
        .done(function(cercanas) {

          if (cercanas) {
            jCercanas = JSON.parse(cercanas);

            if (jCercanas.length > 0) {

              var carcanasLen = jCercanas.length;


              for (var i = 0; i < carcanasLen; i++) {
                // Añade Marcadores de Unidades Cercanas  
                addMarkerUnidad(jCercanas[i], i);
              }

            }

          }

        });

    }


    function addMarkerUnidad(unidad, i) {

      // Almacena los datos de la Unidad 
      var idUn = unidad.unidad;
      var pkUn = unidad.pk;
      var latUn = Number(unidad.latitud);
      var lonUn = Number(unidad.longitud);
      var fecUn = unidad.fecha;
      var perdida = Number(unidad.perdida);
      var especial = Number(unidad.especial);
      var distancia = unidad.distancia;
      var posicion = unidad.posicion;
      var latLng = new google.maps.LatLng(latUn, lonUn);

      var no = i + 1;
      tabla_un = tabla_un +
        "<tr>" +
        "<td>" + no + "</td>" +
        "<td >" + idUn + "</td>" +
        "<td class='derecha'>" + distancia + " Km </td>" +
        "<td>" + posicion + "</td>" +
        "<td>" + fecUn + "</td>" +
        "</tr>";

      document.getElementById("tUnidad").innerHTML = tabla_un;

      // Selecciona el color corespondiende del Icono
      if (perdida != null & perdida != '') {
        // Amarillo
        color = 'yellow';
        codigocolor = "#FDF569";
      } else {

        if (especial == 0) {
          // Verde
          color = 'green';
          codigocolor = "#00E64D";

        } else {
          // Naranja        
          color = 'orange';
          codigocolor = "#EB7032";
        }

      }
      icon = "http://maps.google.com/mapfiles/ms/icons/" + color + ".png";


      // Añade Markers al mapa 
      var marker = new google.maps.Marker({
        position: latLng,
        icon: new google.maps.MarkerImage(icon),

        title: idUn
      });
      marker.setMap(map);
      mksCercanas.push(marker);


      var label = new Label({
        map: map,
        color: color,
        codigocolor: codigocolor,
        text: idUn
      });
      label.bindTo('position', marker, 'position');
      labCercanas.push(label);

      infowindow = new google.maps.InfoWindow({
        content: 'Info '
      });


      google.maps.event.addListener(marker, 'click', (function(marker) {
        return function() {
          map.setCenter(marker.getPosition());

          var content = "--";

          $.ajax({
              url: "vehiculos/app_detalle.php?id=" + pkUn,
              cache: false
            })
            .done(function(html) {
              $('#infovehiculo').val(html);
              infowindow.setContent(" ");
              infowindow.setContent($('#infovehiculo').val());
              infowindow.open(map, marker);
            });
        };
      })(marker));
    }

    function limpiarUnidades() {
      for (var i = 0; i < mksCercanas.length; i++) {
        mksCercanas[i].setMap(null);
        labCercanas[i].setMap(null);
      }
      mksCercanas = [];
      labCercanas = [];
    }
  </script>



<?php 
} else { ?>

  <div class="container">
    <div class="alert alert-warning">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <strong>Su usuario no tiene acceso a este módulo</strong>.
    </div>
  </div>

<?php }  ?>