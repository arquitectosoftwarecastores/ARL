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
    <?php //include("../googlemapsapi/key.php") ?>

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

<iframe width="100%" height="98.5%" frameborder="0 " scrolling="no" marginheight="0" marginwidth="0"
            src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo ($longitud-0.01); ?>%2C<?php echo ($latitud-0.01); ?>%2C<?php echo ($longitud+0.01); ?>%2C<?php echo ($latitud+0.01); ?>&amp;zoom=16&amp;layer=mapnik&amp;marker=<?php echo $latitud; ?>%2C<?php echo $longitud; ?>"
	    src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo ($longitud-0.01); ?>%2C<?php echo ($latitud-0.01); ?>%2C<?php echo ($longitud+0.01); ?>%2C<?php echo ($latitud+0.01); ?>&amp;zoom=16&amp;layer=mapnik&amp;marker=<?php echo $latitud; ?>%2C<?php echo $longitud; ?>"

            style="border: 0px solid white"></iframe>
</body>
</html>