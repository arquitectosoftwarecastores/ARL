<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
include ('../conexion/conexion.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/distancia.php');
if (isset($_GET['latitud']) AND isset( $_GET['longitud']) AND isset($_GET['rad'])) {
  $latitud = $_GET['latitud']; 
  $longitud = $_GET['longitud']; 
  $rad = $_GET['rad']; 
  $R = 6371;  // radio de la tierra en km
  $maxLat = $latitud + rad2deg($rad/$R);
  $minLat = $latitud - rad2deg($rad/$R);
  $minLon = $longitud - rad2deg(asin($rad/$R) / cos(deg2rad($latitud)));
  $maxLon = $longitud + rad2deg(asin($rad/$R) / cos(deg2rad($latitud)));
  $latdeg=deg2rad($latitud);
  $londeg=deg2rad($longitud);
  $consulta4 = "SELECT *, acos(sin(?)*sin(radians(num_latitud_rem)) + cos(?)*cos(radians(num_latitud_rem))*cos(radians(num_longitud_rem)-?)) * ? AS distancia  FROM (SELECT * FROM tb_remolques WHERE num_latitud_rem BETWEEN ? AND ? AND num_longitud_rem BETWEEN ? AND ? ) AS FirstCut WHERE acos(sin(?)*sin(radians(num_latitud_rem)) + cos(?)*cos(radians(num_latitud_rem))*cos(radians(num_longitud_rem)-?)) * ? < ? ORDER BY distancia";
  $query4 = $conn->prepare($consulta4);
  $query4->bindParam(1, $latdeg);
  $query4->bindParam(2, $latdeg);
  $query4->bindParam(3, $londeg);
  $query4->bindParam(4, $R);
  $query4->bindParam(5, $minLat);
  $query4->bindParam(6, $maxLat);
  $query4->bindParam(7, $minLon);
  $query4->bindParam(8, $maxLon);
  $query4->bindParam(9, $latdeg);
  $query4->bindParam(10, $latdeg);
  $query4->bindParam(11, $londeg);
  $query4->bindParam(12, $R);
  $query4->bindParam(13, $rad);
  $query4->execute();
  while($unidad = $query4->fetch()) {
    $pk = $unidad["pk_clave_rem"];
    $id = $unidad["txt_economico_rem"];
    $distancia = round($unidad["distancia"],2);
    $ubicacion = $unidad["txt_georeferencia_mun"].", ".$unidad["txt_georeferencia_cas"];
    $fecha = date("d/m/Y H:i:s",strtotime($unidad["fec_posicion_rem"]));
    $perdida = $unidad["num_icono_rem"] ;
    $especial = $unidad["num_seguimiento_rem"];
    $latitud = $unidad['num_latitud_rem'];
    $longitud = $unidad['num_longitud_rem'];
    $unidad_ubicacion = georeferencia($latitud,$longitud,$conn).",".georeferencia_pi($latitud,$longitud,$conn);
    $fila = array ( 'latitud'=>$latitud,
                    'longitud'=>$longitud,
                    'unidad'=>$id, 
                    'pk' => $pk,
                    'posicion'=> $unidad_ubicacion,
                    'fecha' => $fecha,
                    'perdida' => $perdida,
                    'especial' => $especial,
                    'distancia' => $distancia
                  );
    $row_array[]= $fila;
  }  // fin del while posiciones
  $query4->closeCursor();
  $myJSON = json_encode($row_array);
  echo $myJSON;
}else{
  echo "[]";
}
?>