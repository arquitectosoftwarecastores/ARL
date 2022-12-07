    <!-- Consultas -->
<?php 
  $codigoverde="#00E64D";
    $id=$_GET["id"];
    $idrespaldo = $id;
    //Consulta para obtener las rutas
    $consulta  = " SELECT r.txt_nombre_rut, r.pk_clave_rut, r.fk_clave_zon1 AS cveorigen,zo.txt_nombre_zon AS origen,
                    r.fk_clave_zon2 AS cvedestino,zd.txt_nombre_zon AS destino, dzo.num_latitud_zon AS latitudorigen, dzo.num_longitud_zon AS longitudorigen,   
                    dzd.num_latitud_zon AS latituddestino, dzd.num_longitud_zon AS longituddestino
                    FROM tb_rutas AS r, tb_zonas AS zo, tb_zonas AS zd , tb_detallezonas AS dzo, tb_detallezonas AS dzd 
                    WHERE r.fk_clave_zon1=zo.pk_clave_zon AND r.fk_clave_zon2=zd.pk_clave_zon
                    AND zo.pk_clave_zon=dzo.fk_clave_zon AND zd.pk_clave_zon=dzd.fk_clave_zon
                    AND r.pk_clave_rut=?";
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

    //Obtener el numero de puntos de esta unidad
    $consulta3  = "SELECT count(*) as total from monitoreo.puntos_rutas where ruta=?";  
    $query3 = $conn->prepare($consulta3);
    $query3->bindParam(1, $id);
    $query3->execute();
    $registro3 = $query3->fetch();
    $totalpuntos=$registro3['total'];
    $ultimo = $registro3["total"];
    $i=0;  
//    while($registro2 = $query2->fetch()) {
//      $prueba[i][0]=$registro2["latitud"];
//      $prueba[i][1]=$registro2["longitud"];
//      $i++;
//    }    
    ?> 
<script>  
    var posiciones = [
    
    
     <?php
    $i=0;
       while($registro2 = $query2->fetch()) {
         $i++;
         if($i != $ultimo){
         ?>
           {lat: <?php echo $registro2["latitud"]; ?> , lng:  <?php echo $registro2["longitud"]; ?> },       
         <?php
         }else{
              ?>
             {lat: <?php echo $registro2["latitud"]; ?> , lng:  <?php echo $registro2["longitud"]; ?> }
         <?php
         } 
      } 
    ?>    
    ]; 
    console.log(posiciones);
    console.log(posiciones[0]);
    
    </script> 
<!-- Menú de Arriba de la Página Web -->

<!-- Mostrar Mapa -->
<div id="map" ></div>

<script>
var vehiculos = []; // Arreglo para el número de vehículos
var map; 

     function initMap() {
         
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 8,
          center: posiciones[0],
          mapTypeId: 'terrain'
        });
        
       
        
        var flightPlanCoordinates = [
 {lat: 17.3821 , lng: -93.1651},
 {lat: 17.4822 , lng: -93.1651},
 {lat: 17.5823 , lng: -93.1651},
 {lat: 17.6821 , lng: -93.1651},
 {lat: 17.7821 , lng: -93.1651},
 {lat: 17.8821 , lng: -93.1615},
 {lat: 17.9821 , lng: -93.1615},
 {lat: 17.6821 , lng: -93.2651},
 {lat: 17.7821 , lng: -93.3651},
 {lat: 17.8821 , lng: -93.4651},
 {lat: 17.8821 , lng: -93.5615},
 {lat: 17.9821 , lng: -93.6615},
 {lat: 17.6821 , lng: -93.7651},
 {lat: 17.7821 , lng: -93.8651},
 {lat: 17.8821 , lng: -93.9651},
 {lat: 17.9821 , lng: -94.0651}
//          {lat: -18.142, lng: 178.431},
  //        {lat: -27.467, lng: 153.027}
          // [0][0]
          // [1][0]
        ];
        //console.log(flightPlanCoordinates);
        var flightPath = new google.maps.Polyline({
          path: posiciones,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 3
        });

        flightPath.setMap(map);
      }



</script>

  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&signed_in=true&callback=initMap"></script> 
<script>

$(document).ready(function(){

    initMap();
});

</script>