<?php 

  $id=$_GET["id"];
  $consulta  = " SELECT r.txt_nombre_rut, r.pk_clave_rut, r.fk_clave_zon1 AS cveorigen,zo.txt_nombre_zon AS origen,
                 r.fk_clave_zon2 AS cvedestino,zd.txt_nombre_zon AS destino, dzo.num_latitud_zon AS latitudorigen, dzo.num_longitud_zon AS longitudorigen,   
                 dzd.num_latitud_zon AS latituddestino, dzd.num_longitud_zon AS longituddestino
                 FROM tb_rutas AS r, tb_zonas AS zo, tb_zonas AS zd , tb_detallezonas AS dzo, tb_detallezonas AS dzd 
                 WHERE r.fk_clave_zon1=zo.pk_clave_zon AND r.fk_clave_zon2=zd.pk_clave_zon
                 AND zo.pk_clave_zon=dzo.fk_clave_zon AND zd.pk_clave_zon=dzd.fk_clave_zon
                 AND r.pk_clave_rut=?  GROUP BY origen,  destino";  

  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();


?>
<div class="container">
  <div class="row">    
    <div class="col-md-6">
      <h1>MUESTRA RUTA <?php echo $registro["txt_nombre_rut"]?></h1> 
      <p>Origen: <?php echo $registro["origen"]?></p>
      <p>Destino: <?php echo $registro["destino"]?></p>     
    </div>
    <div class="col-md-3">
      <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">REGRESAR</button></a>   
    </div>  
  </div>
</div>
<div id="map" ></div>


<script>


function initMap() {
  var directionsService = new google.maps.DirectionsService;
  var directionsDisplay = new google.maps.DirectionsRenderer;
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 7,
    center: {lat: <?php echo $registro["latitudorigen"]?>, lng: <?php echo $registro["longitudorigen"]?>}
  });
  directionsDisplay.setMap(map);

  ruta(directionsService,directionsDisplay);
 
}

function ruta(directionsService, directionsDisplay) {
  directionsService.route({
    origin: {lat: <?php echo $registro["latitudorigen"]?>, lng: <?php echo $registro["longitudorigen"]?>},
    destination: {lat: <?php echo $registro["latituddestino"]?>, lng: <?php echo $registro["longituddestino"]?>},
    travelMode: google.maps.TravelMode.DRIVING
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    } else {
      window.alert('Directions request failed due to ' + status);
    }
  });
}

</script>

  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&signed_in=true&callback=initMap"></script>