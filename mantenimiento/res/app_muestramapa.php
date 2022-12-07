<?php  session_start(); ?>    
<!doctype html>
<html lang="es">
<?php 
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");
    include ('../conexion/conexion.php');
    $latitudcentro=24.517002;
    $longitudcentro=-101.788702;
?>
<head>
<meta charset="utf-8" />
<meta name="description" content="" >
<meta name="author" content="" >
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="keywords" content=" " >
<meta name="robots" content="NOINDEX, NOFOLLOW" >
<meta name="geo.region" content="MX-GUA"/>
<meta name="geo.placename" content="León Gto, Guanajuato"/>
<meta name="geo.position" content=" "/>
<title></title>

    <!-- jQuery plugin -->
    <script src="../librerias/jquery.min.js"></script>
    <!-- Google Maps API -->
    <?php include("../googlemapsapi/key.php") ?>

    <!-- Bootstrap plugin -->
    <script src="../librerias/bootstrap/js/bootstrap.min.js"></script>     
    <link href='../librerias/bootstrap/css/bootstrap.min.css' rel='stylesheet'>

    <?php  include ('../css/estilo.php');  ?>

</head>
<body>  


<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0; padding: 0 }
  #map { height: 100% }
</style>

<div id="map"></div>
<input type="hidden" id="infovehiculo" value="" />
<?php

  $codigoblanco="#67DDDD";
  $codigoazul="#6991FD";
  $codigoverde="#00E64D";
  $codigoamarillo="#FDF569";
  $economico=$_GET["economico"];

  $consulta  = " SELECT * FROM tb_vehiculos WHERE txt_economico_veh=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $economico);
  $query->execute();
  $registro = $query->fetch();
  $latitud=$registro["num_latitud_veh"];
  $longitud=$registro["num_longitud_veh"];

  $id=$registro['pk_clave_veh'];      
  $zonaderiesgo=$registro['num_zonariesgo_veh'];
  if(strlen($registro['txt_tperdida_veh'])) 
    $color=2; 
  else 
    $color=1; 
  $especial=$registro['num_seguimientoespecial_veh'];

?>

<script>
  var map;
  var latitud=<?php echo  $latitud?>;
  var  longitud=<?php echo  $longitud?>;
  var  economico=<?php echo  $economico?>;
  var  id=<?php echo  $id?>;

  function initMap() {

    var myLatLng = {lat: latitud, lng: longitud};
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: myLatLng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    });

    <?php
      if(strlen($registro['txt_tperdida_veh'])) 
        echo "color=2;"; 
      else 
        echo "color=1;"; 
    ?>
    switch (color) {
        case 1:
            color = "green";
            codigocolor="<?php echo  $codigoverde?>";
            break;
        case 2:
            color = "yellow";
            codigocolor="<?php echo  $codigoamarillo?>";                
            break;
    }

    especial=<?php echo $especial?>;

    if(especial=="1")
    { 
      color="orange";
      codigocolor="#FF9900";
    }

    icon = "http://maps.google.com/mapfiles/ms/icons/"+color+".png";

    var marker = new google.maps.Marker({
        position: myLatLng,
        icon: new google.maps.MarkerImage(icon),
        title: '<?php echo $economico;?>'
    });
    marker.setMap(map);

    infowindow = new google.maps.InfoWindow({
        content: 'Info '
      });

    google.maps.event.addListener(marker,'click', (function(marker){ 
        return function() {
            map.setCenter(marker.getPosition());
            var content = economico; 

            $.ajax({
              url: "../vehiculos/app_detalle.php?id="+id,
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

    panorama = map.getStreetView();
    panorama.setPosition(myLatLng);
    panorama.setPov(/** @type {google.maps.StreetViewPov} */({
      heading: 265,
      pitch: 0
    }));

  } // fin de initmap

 
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


</script>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&callback=initMap"></script>