<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");
while(true){
echo "Iniciando...";
echo "<br>";
$consulta = " SELECT * FROM tb_vehiculos where txt_economico_veh not like '00%' ";
$query = $conn->prepare($consulta);
$query->execute();
while ($registro = $query->fetch()) {
    $economico1 = $registro["txt_economico_veh"];
    $latitud1 = $registro["num_latitud_veh"];
    $longitud1 = $registro["num_longitud_veh"];
    echo "Economico: ".$economico1;
   // $zona3 = checazona($latitud1, $longitud1, 3, $conn);
   // echo " Zona3: ".$zona3;
//    $zona2 = checazona($latitud1, $longitud1, 2, $conn);
//    echo " Zona2: ".$zona2;
    $zona1 = checazona($latitud1, $longitud1, 0, $conn);  
    echo " Ciudad: ".$zona1;
    echo "<br>";
    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET ciudad = ? where economico = ?";
    $query2 = $conn->prepare($inserta_geocerca);
    $query2->bindParam(2, $economico1);
    $query2->bindParam(1, $zona1);
  //   $query2->bindParam(1, $zona2);
  //  $query2->bindParam(1, $zona3);
    $query2->execute();
    $query2->closeCursor();   
}
$query->closeCursor();
$query2->closeCursor();
}
?>