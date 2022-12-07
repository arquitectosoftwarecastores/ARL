<?php

include("../conexion/conexion.php");

$economico = $_GET["economico"];

$conUn = "SELECT * FROM tb_remolques WHERE txt_economico_rem = ?";
$queryUn = $conn->prepare($conUn);
$queryUn->bindParam(1, $economico);
$queryUn->execute();
$row_array = array();

while ($unidad = $queryUn->fetch()) {


  $latitud = $unidad['num_latitud_rem'];
  $longitud = $unidad['num_longitud_rem'];


  $fila = array(
    'latitud' => $latitud,
    'longitud' => $longitud
  );

  $row_array[] = $fila;
}  // fin del while posiciones
$queryUn->closeCursor();

$myJSON = json_encode($row_array);
echo $myJSON;
