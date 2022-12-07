    <!-- Consultas -->
<?php 
  $codigoverde="#00E64D";
  $id=$_GET["id"];
  $idrespaldo = $id;
  
  /** Almacena Consultas de Google Maps  */
  include('procesos/app_consultamap.php');
  consultaMaps(15, $id, $conn);
  
  //Consulta para obtener las rutas
  $consulta  = " SELECT r.txt_nombre_rut, r.pk_clave_rut, r.fk_clave_zon1 AS cveorigen,zo.txt_nombre_zon AS origen,
                 r.fk_clave_zon2 AS cvedestino,zd.txt_nombre_zon AS destino, dzo.num_latitud_zon AS latitudorigen, dzo.num_longitud_zon AS longitudorigen,   
                 dzd.num_latitud_zon AS latituddestino, dzd.num_longitud_zon AS longituddestino
                 FROM tb_rutas AS r, tb_zonas AS zo, tb_zonas AS zd , tb_detallezonas AS dzo, tb_detallezonas AS dzd 
                 WHERE r.fk_clave_zon1=zo.pk_clave_zon AND r.fk_clave_zon2=zd.pk_clave_zon
                 AND zo.pk_clave_zon=dzo.fk_clave_zon AND zd.pk_clave_zon=dzd.fk_clave_zon
                 AND r.pk_clave_rut=?";
  
  //modificacion para que hallemos la informacion de las rutas
  
/*  $consulta = "Select nombre, idoficinaorigen_avl AS origen, idoficinadestino_avl AS destino from ruta_avl where idruta = 818";  */
  
  //GROUP BY origen, destino;    
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();
  
  
    //Consulta para obtener los puntos de esa unidad
  $consulta2  = " SELECT * from monitoreo.puntos_rutas where ruta=?";
  //GROUP BY origen, destino;    
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $id);
  $query2->execute();
  $registro2 = $query2->fetch();
  
  
  
  //Consulta para obtener los puntos alternativos
  $consulta2 = "SELECT z.num_latitudcen_zon AS latitud ,z.num_longitudcen_zon AS longitud, z.txt_nombre_zon AS nombre FROM monitoreo.puntos_intermedios pi join tb_zonas z on pi.clave_zon=z.pk_clave_zon where clave_rut=?";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $id);
  $query2->execute();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
  $registro = $query->fetch();  
  
  //Consulta para contar el numero de unidades que se encuentran en la misma ruta
  $consulta4  = "select count(*) as total from monitoreo.unidadesxruta where ruta = ?";
  $query4 = $conn->prepare($consulta4);
  $query4->bindParam(1, $idrespaldo);
  $query4->execute();
  $registro4 = $query4->fetch();
  $totalvehiculosenruta=$registro4['total'];
?>

<!-- Menú de Arriba de la Página Web -->
<div>
  <div class="row">    
    <div class="col-md-4">
      <h1>RUTA: <?php echo $registro["txt_nombre_rut"]?></h1> 
    </div>
    <div class="col-md-4">
     Origen:  <?php echo $registro["origen"]?> <br>
     Destino: <?php echo $registro["destino"]?>    
    </div>      
    <div class="col-md-2">
      <img src="http://maps.google.com/mapfiles/ms/icons/green.png" width="16px" />
      Unidades: (<?php echo $totalvehiculosenruta?>)
      <input type="checkbox" value="1" checked id="verde">           
      <input type="hidden" value="1" id="valorverde" />
    </div>  
    <div class="col-md-2">
      <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">REGRESAR</button></a>   
    </div>
  </div>
</div>

<!-- Mostrar Mapa -->
<div id="map" ></div>
<script>
var vehiculos = []; // Arreglo para el número de vehículos
var map; 
  
// Inicializa el mapa
function initMap() {
 var directionsService = new google.maps.DirectionsService;
 var directionsDisplay = new google.maps.DirectionsRenderer;
 var map = new google.maps.Map(document.getElementById('map'), {
   zoom: 7,
   center: {lat: <?php echo $registro["latitudorigen"]?>, lng: <?php echo $registro["longitudorigen"]?>}, 
   mapTypeId: google.maps.MapTypeId.HYBRID
   });
    directionsDisplay.setMap(map);
    ruta(directionsService,directionsDisplay);
    //cargaMarcadores();
   // cargaZonas();
   // panorama = map.getStreetView(); 
// Operaciones con los resultados que tenemos en $fila  
<?php

 //Consulta para obtener las unidades que se encuentarn en cierta ruta
  $consulta3 = "select veh.txt_economico_veh as noeconomico, uxr.ruta, rut.txt_nombre_rut as nombreruta, veh.num_latitud_veh as latitud, veh.num_longitud_veh as longitud from monitoreo.tb_rutas rut join unidadesxruta uxr on rut.pk_clave_rut = uxr.ruta join tb_vehiculos veh on veh.txt_economico_veh = uxr.txt_economico_veh where rut.pk_clave_rut = ?";
  $query3 = $conn->prepare($consulta3);
  $query3->bindParam(1, $id);
  $query3->execute();

while($registro3 = $query3->fetch()) {
?>    
  var posunidad = { lat: <?php echo $registro3["latitud"]?> , lng: <?php echo $registro3["longitud"]?> };
//Agrega puntos a la ruta trazada
  console.log("el valor de la unidad es "+posunidad);
  color="yellow";
  codigocolor="#FFFF00"; 
  icon = "http://maps.google.com/mapfiles/ms/icons/"+color+".png"; 
  var marker = new google.maps.Marker({
          position: posunidad,
          icon: new google.maps.MarkerImage(icon),
   //       title: '<?php echo $registro3["noeconomico"]?>',
          label: '<?php echo $registro3["noeconomico"]?>',
          draggable: true,          
          map: map
          //animation: google.maps.Animation.DROP,
          //label: 'x'    
          //title: <?//php $registro3["noeconomico"]?>
      });           
 marker.setMap(map); 
 
 <?php
   }
?> 
 }
 
function toggleStreetView(latitud,longitud) {
   var toggle = panorama.getVisible();
   var myLatLng = {lat: latitud, lng: longitud};
   if (toggle == false) {
      panorama.setPosition(myLatLng);
      panorama.setVisible(true);
      } else {
        panorama.setVisible(false);
      }
} 

 function cargaZonas() {
      var coordenadas= [];
      $.getJSON("zonas/json.php", function(json) {
        $.each(json.zonas, function(index, zona) {           
           if(zona.tipo==3)
            color='#FF0000';  //color para Zona de Riesgo
           else
            color='#67DDDD';  //color para Zona Segura
            $.each(zona.puntos, function(index, punto) {
               coordenadas.push(new google.maps.LatLng(punto.lat, punto.long));   
            });  
          var poligonozona = new google.maps.Polygon({
            paths: coordenadas,
            strokeColor: '#000000',
            strokeOpacity: 0.5,
            strokeWeight: 1,
            fillColor: color,
            fillOpacity: 0.5
          });

        zonas.push(poligonozona);
        coordenadas= [];
        }); // fin del each
      });  // fin del getJSON
      toogleZonas(null);
    } // fin del la función cargaZonas

      var myVar = setInterval(actualizaMarcadores, 30000);
    
function actualizaMarcadores(map) {
   $.getJSON("vehiculos/app_actualizaposicion.php?idusuario=<?php echo $_SESSION['id']?>"+"&rnd="+ Math.random()*10000, function(json1) {
   $.each(json1.vehiculos, function(key, data) { 
   for (var i = 0; i < vehiculos.length; i++) {  
      if(vehiculos[i].get("id")==data.id)
      {
                      $('#infocambio').val(data.id);              
                      $('#infolatitud').val(data.lat);
                      $('#infolongitud').val(data.long);
                      var latLng = new google.maps.LatLng($('#infolatitud').val(), $('#infolongitud').val()); 
                      vehiculos[i].setPosition(latLng); 
                      vehiculos[i].set("latitud", $('#infolatitud').val());
                      vehiculos[i].set("longitud", $('#infolongitud').val());
                    }
                };
              });  // fin del each
            });  // fin del getJSON
      }  // fin de la función actualizaMarcadores
      
  
  
function cargaMarcadores() {
    $.getJSON("vehiculos/json.php?idusuario=<?php echo $_SESSION['id']?>", function(json1) {
    $.each(json1.vehiculos, function(key, data) {
    var latLng = new google.maps.LatLng(data.lat, data.long); 
    var color = "";
    switch (data.color) {
        case 1:
            color = "purple";
            codigocolor="<?php echo  $codigoverde?>";
            break;
        }
          icon = "http://maps.google.com/mapfiles/ms/icons/"+color+".png";
              var marker = new google.maps.Marker({
                  position: latLng,
                  icon: new google.maps.MarkerImage(icon),
                   icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + data.economico + '|FF0000|000000',
                  title: data.economico
              });
              marker.setMap(map);
              marker.set("id", data.id );
              marker.set("economico", data.economico );
              marker.set("latitud", data.lat );
              marker.set("longitud", data.long );
              marker.set("color", data.color );
              marker.set("zonaderiesgo", data.zonaderiesgo );
              marker.set("especial", data.especial );
              var label = new Label({
                map: map,
                color: color,
                codigocolor: codigocolor,
                text: data.economico
              });
              label.bindTo('position', marker, 'position');
              //label.bindTo('text', marker, 'position');
              vehiculos.push(marker);
              infowindow = new google.maps.InfoWindow({
                  content: 'Info '
                });
              google.maps.event.addListener(marker,'click', (function(marker){ 
                  return function() {
                      map.setCenter(marker.getPosition());
                      var content = data.economico; 
                      $.ajax({
                        url: "vehiculos/app_detalle.php?id="+data.id,
                        cache: false
                      })
                        .done(function( html ) {
                          $('#infovehiculo').val( html );
                          infowindow.setContent(" ");
                          infowindow.setContent($('#infovehiculo').val());
                          infowindow.open(map,marker);
                        });
                  };
              })(marker));  
            });
            toogleVehiculos();
          });           
      }     

 // Sets the map on all markers in the array.
      function setMapOnAll(map) {
        for (var i = 0; i < vehiculos.length; i++) {
          vehiculos[i].setMap(map);
        }
      }
      
       // Removes the markers from the map, but keeps them in the array.
      function clearMarkers() {
        setMapOnAll(null);
      }

      // Shows any markers currently in the array.
      function showMarkers() {
        setMapOnAll(map);
      }    

function ruta(directionsService, directionsDisplay) {
  var WayPts=[];  
              <?php                  
                   while($registro2=$query2->fetch()){
                ?>                
                //ciclo while
                 WayPts.push({ 
                       location:new google.maps.LatLng(
                             <?php echo $registro2["latitud"]?>,<?php echo $registro2["longitud"]?>               
                        ), 
                        stopover: true 
                    });
                 <?php          
                   }//Fin while
                 ?>
  
  directionsService.route({
    origin: {lat: <?php echo $registro["latitudorigen"]?>, lng: <?php echo $registro["longitudorigen"]?>},
    destination: {lat: <?php echo $registro["latituddestino"]?>, lng: <?php echo $registro["longituddestino"]?>},//},   
  //Para ir agregando puntos intermedios a la consulta
   waypoints:WayPts,        
    travelMode: google.maps.TravelMode.DRIVING 
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    } else {
      window.alert('Directions request failed due to ' + status);
    }
  });
  
  
  /* Verificar funcion sabado 18 de noviembre*/
       function toogleVehiculos() {   

        for (var i = 0; i < vehiculos.length; i++) {
              if(vehiculos[i].get("color")==1 && $("#valorverde").val()==1)
                if($("#valorriesgo").val()==1)
                  if(vehiculos[i].get("zonaderiesgo")==1)
                    vehiculos[i].setMap(map);
                  else                    
                      if(vehiculos[i].get("especial")==1 && $("#valornaranja").val()==1)
                        vehiculos[i].setMap(map);
                      else
                        vehiculos[i].setMap(null);
                else
                  vehiculos[i].setMap(map);
              else
                if(vehiculos[i].get("color")==2 && $("#valoramarillo").val()==1)
                  if($("#valorriesgo").val()==1)
                    if(vehiculos[i].get("zonaderiesgo")==1)
                      vehiculos[i].setMap(map);
                    else
                        if(vehiculos[i].get("especial")==1 && $("#valornaranja").val()==1)
                          vehiculos[i].setMap(map);
                        else
                          vehiculos[i].setMap(null);
                  else
                    vehiculos[i].setMap(map);                    
      }
      }
      
       function toogleZonas(map) {        
        for (var i = 0; i < zonas.length; i++) {          
            zonas[i].setMap(map);
        }
      }



  function toggleStreetView(latitud1,longitud1) {
    var toggle = panorama.getVisible();
    if (toggle == false) {

      
      var myLatLng2 = {lat: latitud1, lng: longitud1};
      panorama.setPosition(myLatLng2);
      panorama.setPov(/** @type {google.maps.StreetViewPov} */({
        heading: 265,
        pitch: 0
      }));

      panorama.setVisible(true);



    } else {
      panorama.setVisible(false);
    }
  }
  
}
</script>

<script>
//Oculta o muestra los vehiculos en color verde
$(document).ready(function(){
  $("#verde").click(function(){
    if(parseInt($("#valorverde").val())==1)
      {
        $("#valorverde").val(0);
        toogleVehiculos();
        $(".green").hide();  
      } 
    else
      {
        $("#valorverde").val(1);
        toogleVehiculos();
        $(".green").show(); 
      }
  });
});
</script>

<script>
//Oculta o muestra los vehiculos en color verde
$(document).ready(function(){
  $("#riesgo").click(function(){
    if(parseInt($("#valorriesgo").val())==1)
      {
        $("#valorriesgo").val(0);        
        toogleZonas(null);      
        toogleVehiculos(null);
        $("#zonasderiesgo").hide();  
      } 
    else
      {
        $("#valorriesgo").val(1); 
        toogleZonas(map);
        toogleVehiculos(map);
        $("#zonasderiesgo").show();  
      }
  });
});
</script>

<div>
  <div class="row">    
    <div class="col-md-2">
      <h1>Puntos Intermedios: </h1>
      <br>
    </div>
    <div class="col-md-4">  
     <?php    
      $letra = 66;
      $query2->execute();    
      while($registro2=$query2->fetch()){
                ?>   
      <p><?php echo chr($letra)." : ".$registro2["nombre"]?></p>
      <?php
            $letra++;
            }
      ?> 
    </div>
  </div>
</div>                      
  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&signed_in=true&callback=initMap"></script>  
  <script>
/*
$('#btnvermapa').on('click', function () {
  window.open("index.php?seccion=mapa&accion=muestra&economico="+$("#unidadvermapa").val(), '_blank');
});


$('#economico').on('change', function () {
     
     var idunidad;
     var encontrado;
     var selectVal = $("#economico option:selected").val();

      $.ajax({
              url: "vehiculos/app_ubicacion.php?id="+selectVal,
              cache: false
            })
              .done(function( html ) {  

              $('#muestraubicacion').html(html);

              for (var i = 0; i < vehiculos.length; i++) 
              {      
                if(vehiculos[i].get("economico")==selectVal)
                {          
                  vehiculos[i].setAnimation(1);    
                  vehiculos[i].setAnimation(google.maps.Animation.BOUNCE);
                  stopAnimation(vehiculos[i]);  
                  $('#btnvermapa').show();
                  $('#unidadvermapa').val(vehiculos[i].get("economico"));
                }                
              }  // fin del for
      });

            
});
*/
/*
  function stopAnimation(marcador) {
      setTimeout(function () {
          marcador.setAnimation(null);
      }, 3000);
  }
  */

</script>

<?php   
  include ('autoridades/app_lista.php');
  include ('monitoreo/app_ubicaunidad.php');
  include ('monitoreo/app_panelmensajes.php');
?>

<script>
$('#zonasderiesgo').on('change', function() {
  var coordenadas = $(this).find(":selected").val().split(",");  
  var coordenadaszonaderiesgo = {lat: parseFloat(coordenadas[0]), lng: parseFloat(coordenadas[1])};
  
  map.setZoom(10);
  map.setCenter(coordenadaszonaderiesgo);
});
</script>

<script>

$('#btnvermapa').on('click', function () {
  window.open("index.php?seccion=mapa&accion=muestra&economico="+$("#unidadvermapa").val(), '_blank');
});


$('#economico').on('change', function () {
     
     var idunidad;
     var encontrado;
     var selectVal = $("#economico option:selected").val();

      $.ajax({
              url: "vehiculos/app_ubicacion.php?id="+selectVal,
              cache: false
            })
              .done(function( html ) {  

              $('#muestraubicacion').html(html);

              for (var i = 0; i < vehiculos.length; i++) 
              {      
                if(vehiculos[i].get("economico")==selectVal)
                {          
                  vehiculos[i].setAnimation(1);    
                  vehiculos[i].setAnimation(google.maps.Animation.BOUNCE);
                  stopAnimation(vehiculos[i]);  
                  $('#btnvermapa').show();
                  $('#unidadvermapa').val(vehiculos[i].get("economico"));
                }                
              }  // fin del for
      });

            
});


  function stopAnimation(marcador) {
      setTimeout(function () {
          marcador.setAnimation(null);
      }, 3000);
  }
  

</script>

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
    'padding: 2px;  font-weight: bold; font-size:10px; color: '+this.get('codigocolor')+'; text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;';

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

<script>

$(document).ready(function(){
    initMap();
});

</script>