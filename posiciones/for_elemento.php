  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXNIR-oeOZjAnQX8XgQnE1dhJXLVjWFUM&signed_in=true&callback=initMap"></script>
<?php include("funciones/distancia.php");?>
<?php include("posiciones/app_referencia.php");?>
<div class="container-fluid">  
    <div class="row">
      <div class="col-md-12">
        <div id="infodata">
          <table class="table table-striped table-bordered table-hover" id="info">
            <thead>
              <tr>
                <th>Fecha-hora</th>
                <th>Posición</th>
                <th>Velocidad</th>
                <th>Distancia Recorrida</th>
                <th>Latitud</th>
                <th>Longitud</th>
              </tr> 
            </thead>
            <tbody> 
          	<?php 	
              $query = $conn->prepare($strSQL);
              $query->execute(); 
              $contador=0;
              $distanciarecorrida=0;
          		while ($registro = $query->fetch()) 
              {
                if($contador)
                  $distancia=distancia($latitudanterior, $longitudanterior,$registro["num_latitud_pos"], $registro["num_longitud_pos"]);
                else
                 {
                  $distancia=0;
                 }
                $latitudanterior=$registro["num_latitud_pos"];
                $longitudanterior=$registro["num_longitud_pos"]; 
                $contador++; 
                $distanciarecorrida+=$distancia;
          	?>
              <tr>
                <td><?php echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))) ?>
                </td>
                <td>
                <?php echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                <?php echo georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                </td>
                <td class="derecha"><?php echo round($registro["num_velocidad_pos"]/0.62137,2)?> Km/hr.  <?php //echo round($distanciarecorrida,2);?></td>
                <td class="derecha"><?php echo round($distanciarecorrida,2);?> Km.</td>
                <td><?php echo $registro["num_latitud_pos"]?></td>
                <td><?php echo $registro["num_longitud_pos"]?></td>
              </tr>	
  		      <?php 
              } 
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<div id="map" ></div>


<script>


  var waypts = [] ;

  <?php
 

    $consulta2  = $strSQL;
    $query2 = $conn->prepare($consulta2);
    $query2->execute();
    $maximo=0;
    while($registro2 = $query2->fetch())
      $maximo++;

    $consulta1  = $strSQL;  
    $query1 = $conn->prepare($consulta1);
    $query1->execute();

    $latitudorigen=0;
    $longitudorigen=0;  
    $latituddestino=0;
    $longituddestino=0;

    $cuenta=1;
    while($registro1 = $query1->fetch())      
    {

      if($cuenta==1)
      {
        $latitudorigen= $registro1["num_latitud_pos"];
        $longitudorigen=$registro1["num_longitud_pos"];
      }
      else
        if($cuenta==$maximo)
        {
          $latituddestino= $registro1["num_latitud_pos"];
          $longituddestino=$registro1["num_longitud_pos"];
        }
         else if ($cuenta<=9) {
              ?>        
                waypts.push({location: {lat:<?php echo $registro1["num_latitud_pos"]?>,lng:<?php echo $registro1["num_longitud_pos"]?>},stopover: true});
              <?php 
              }      

    $cuenta++;
  }
  ?>



function initMap() {
  var directionsService = new google.maps.DirectionsService;
  var directionsDisplay = new google.maps.DirectionsRenderer;
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 7,
    center: {lat: 29.091877, lng: -111.041010}
  });
  directionsDisplay.setMap(map);
  ruta(directionsService, directionsDisplay);
}

function ruta(directionsService, directionsDisplay) {
  directionsService.route({
    origin: {lat: <?php echo $latitudorigen?>, lng: <?php echo $longitudorigen?>},
    destination: {lat: <?php echo $latituddestino?>, lng: <?php echo $longituddestino?>},
    waypoints: waypts,
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

