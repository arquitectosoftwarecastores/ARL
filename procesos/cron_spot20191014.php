<?php

// Conexion PostgreSQL
include ('../conexion/conexion.php');

// Conexion MySQL
$servidor="192.168.0.23";
$username="usuarioWin";
$password = "windows";


// Create connection
$con = new mysqli($servidor, $username, $password);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully<br>";

// Actualiza el Circuito a  'Todos Los Circuitos'
$upVeh = "UPDATE tb_vehiculos SET fk_clave_cir= 0 WHERE fk_clave_cir = 12 ";
$queryVeh = $conn->prepare($upVeh);
$queryVeh->execute();
$queryVeh->closeCursor();

echo "- Todos a 0 <br>";

// Consulta Viajes Spot
$conVS = 'SELECT cc.`noeconomico` FROM talones.viajes AS tv
INNER JOIN camiones.`camiones` AS cc ON tv.idunidad = cc.unidad
WHERE tv.idcatalogo_viajes = 1 AND tv.idoficina = "2504" AND
tv.estatus IN (2) AND cc.`status` = 1 ORDER BY noeconomico DESC;';
$result = $con->query($conVS);


if ($result ->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "- " . $row["noeconomico"]." Actualizado<br>";

    // Actualiza el Circuito de los vehiculos en Spot
    $upVeh = "UPDATE tb_vehiculos SET fk_clave_cir= 12 WHERE txt_economico_veh = ? ";
    $queryVeh = $conn->prepare($upVeh);
    $queryVeh->bindParam(1, $row["noeconomico"]);
    $queryVeh->execute();
    $queryVeh->closeCursor();
  }
} else {
  echo "0 results";
}

echo "---------------------------";


$queryVeh->closeCursor();
$con->close();
$conn = null;
 ?>
