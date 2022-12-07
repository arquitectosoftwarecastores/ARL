<?php

include_once('../conexion/conexion.php');
include_once('../funciones/distancia.php');

$fechaIni = date("Y-m-d", strtotime("-7 days")) . " 00:00:00";
$fechaFin = date("Y-m-d", strtotime('-1 days')) . " 23:59:59";



$conRem = "SELECT txt_nserie_rem AS serie,
              txt_economico_rem AS economico,
              uu.fecha_ingreso AS fecha
            FROM tb_remolques tr 
            INNER JOIN unidades_usa uu 
              ON tr.txt_economico_rem = uu.noeconomico 
            WHERE tr.estatus = 1
            ORDER BY txt_nserie_rem ASC";
$qryRem = $conn->prepare($conRem);
$qryRem->execute();


$z = 0;
while ($rowRem = $qryRem->fetch()) {
  $z++;
  echo $z . " - Economico: " .  $rowRem['economico'] . " - Fecha: " . $rowRem['fecha'] . " - ";

  $conPos = "SELECT 
                  txt_nserie_pos, 
                  num_latitud_pos AS latitud, 
                  num_longitud_pos AS longitud, 
                  MIN(fec_ultimaposicion_pos) as fecha
                FROM tb_posiciones tp 
                WHERE
                  txt_nserie_pos = ? AND
                  fec_ultimaposicion_pos >= ?
                group by txt_nserie_pos, num_latitud_pos, num_longitud_pos
                order by txt_nserie_pos ASC, fecha ASC";
  $qryPos = $conn->prepare($conPos);
  $qryPos->bindParam(1, $rowRem["serie"]);
  $qryPos->bindParam(2, $rowRem['fecha']);
  $qryPos->execute();

  $rowsPos = $qryPos->fetchAll();
  $qryPos->closeCursor();

  $kmTotal = 0;

  // Realiza Calculo de Recorrido
  for ($i = 1; $i < count($rowsPos); $i++) {
    $actPos = $rowsPos[$i];
    $lastPos = $rowsPos[$i - 1];

    if ($actPos['latitud'] != 0 & $lastPos['latitud'] != 0 & $actPos['latitud'] != 24.7491381 & $lastPos['latitud'] != 24.7491381) {
      $actKm = (floatval(distancia($actPos['latitud'], $actPos['longitud'], $lastPos['latitud'], $lastPos['longitud'])));
      if ($actKm > 0.05) {
        $kmTotal += $actKm;
      }
    }
  }

  $kmTotal = (int) $kmTotal;

  echo "Distancia Total: " . $kmTotal . "\n";

  $conKm = "UPDATE unidades_usa 
            SET km = ?
            WHERE noeconomico = ?";
  $qryKm = $conn->prepare($conKm);
  $qryKm->bindParam(1, $kmTotal);
  $qryKm->bindParam(2, $rowRem['economico']);
  $qryKm->execute();
  $qryKm->closeCursor();
}
$qryRem->closeCursor();
