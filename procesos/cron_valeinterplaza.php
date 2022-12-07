<?php

include('../conexion/conexion.php');
include('../funciones/checazona.php');
echo "Ejecutando Vale Interplaza...";

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

// Consulta Vehiculos con Circuito Multiple - 17
$conVeh = "SELECT cc.`noeconomico` FROM talones.viajes AS tv
          INNER JOIN camiones.`camiones` AS cc ON tv.idunidad = cc.unidad
          WHERE tv.idcatalogo_viajes = 17 AND
          tv.estatus IN (2) AND cc.`status` = 1;";
$result = $con->query($conVeh);
if ($result ->num_rows > 0) {
  while($row = $result->fetch_assoc()) {


    // Consulta Vehiculo
    $conVeh = "SELECT txt_economico_veh, num_latitud_veh, num_longitud_veh, fec_posicion_veh, patio FROM tb_vehiculos AS tv LEFT JOIN geocercasporunidad AS gpu ON tv.txt_economico_veh = gpu.economico WHERE tv.txt_economico_veh = ? ";
    $queryVeh= $conn->prepare($conVeh);
    $queryVeh->bindParam(1, $row["noeconomico"]);
    $queryVeh->execute();
    while ($rowVeh = $queryVeh->fetch()) {


      $ecoVeh = $rowVeh['txt_economico_veh'];
      $latVeh = $rowVeh['num_latitud_veh'];
      $lonVeh = $rowVeh['num_longitud_veh'];
      $fecVeh = $rowVeh['fec_posicion_veh'];
      $patVeh = $rowVeh['patio'];

      // Checa Zona Patio
      $patio = checazona($latVeh, $lonVeh, -8, $conn);

      // Verifica si cambio de Zona
      if ($patio != $patVeh ) {
        // Actualiza historico
        if ($patio != 0) {
          // Entro a Patio
          enPH($ecoVeh, $patio, $fecVeh, $conn);
          echo "-- Entro --";
        }else{
          // Salio de Patio
          saPH($ecoVeh, $patVeh, $fecVeh, $conn);
          echo "-- Salio --";
        }

        acGPU($patio,$ecoVeh,$conn);
        echo $ecoVeh." - ".$patio." <br> ";
      }
    }
  }
} else {
  echo "0 results";
}

$queryVeh->closeCursor();
$con->close();
$conn = null;
echo "Finalizado";




/* --- FUNCIONES --- */

// Registra en el historico
function enPH($ecoVeh, $patio, $fecVeh, $conn){
  $inHP = "INSERT INTO tb_patios_historico (economico, patio, fecha_entrada) VALUES (?,?,?) ";
  $queryHP = $conn->prepare($inHP);
  $queryHP->bindParam(1, $ecoVeh);
  $queryHP->bindParam(2, $patio);
  $queryHP->bindParam(3, $fecVeh);
  $queryHP->execute();
  $queryHP->closeCursor();
}

// Actualiza el historico
function saPH($ecoVeh, $patio, $fecVeh, $conn){
  $inHP = "UPDATE tb_patios_historico SET fecha_salida = ? WHERE economico = ? AND patio = ? AND fecha_salida IS NULL";
  $queryHP = $conn->prepare($inHP);
  $queryHP->bindParam(1, $fecVeh);
  $queryHP->bindParam(2, $ecoVeh);
  $queryHP->bindParam(3, $patio);
  $queryHP->execute();
  $queryHP->closeCursor();
}

// Actualiza Patio de geocercasporunidad
function acGPU($patio, $ecoVeh, $conn){
  $upGPU = "UPDATE geocercasporunidad SET patio = ? WHERE economico = ?";
  $queryGPU = $conn->prepare($upGPU);
  $queryGPU->bindParam(1, $patio);
  $queryGPU->bindParam(2, $ecoVeh);
  $queryGPU->execute();
  $queryGPU->closeCursor();
}

?>
