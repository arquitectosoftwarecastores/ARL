<?php

include_once('../conexion/conexion.php');
include_once('../funciones/distancia.php');

$fechaIni = date("Y-m-d", strtotime("-7 days")) . " 00:00:00";
$fechaFin = date("Y-m-d", strtotime('-1 days')) . " 23:59:59";



$conRem = "SELECT
              txt_nserie_rem AS serie,
              txt_economico_rem AS economico
            FROM tb_remolques
            WHERE estatus = 1
            ORDER BY txt_nserie_rem ASC";
$qryRem = $conn->prepare($conRem);
$qryRem->execute();


$z = 0;
while ($rowRem = $qryRem->fetch()) {
  $z++;
  echo $z . " - Economico: " .  $rowRem['economico'] . " - ";

  $conPos = "SELECT 
                  txt_nserie_pos, 
                  num_latitud_pos AS latitud, 
                  num_longitud_pos AS longitud, 
                  MIN(fec_ultimaposicion_pos) as fecha
                FROM tb_posiciones tp 
                WHERE
                  txt_nserie_pos = ? AND
                  fec_ultimaposicion_pos > ? AND
                  fec_ultimaposicion_pos < ?
                group by txt_nserie_pos, num_latitud_pos, num_longitud_pos
                order by txt_nserie_pos ASC, fecha ASC";
  $qryPos = $conn->prepare($conPos);
  $qryPos->bindParam(1, $rowRem["serie"]);
  $qryPos->bindParam(2, $fechaIni);
  $qryPos->bindParam(3, $fechaFin);
  $qryPos->execute();

  $rowsPos = $qryPos->fetchAll();
  $qryPos->closeCursor();

  $kmTotal = 0;

  // Realiza Calculo de Recorrido
  for ($i = 1; $i < count($rowsPos); $i++) {
    $actPos = $rowsPos[$i];
    $lastPos = $rowsPos[$i - 1];

    if ($actPos['latitud'] != 0 & $lastPos['latitud'] != 0 & $actPos['latitud'] != 24.7491381 & $lastPos['latitud'] != 24.7491381) {
      $actKm = ((int)(distancia($actPos['latitud'], $actPos['longitud'], $lastPos['latitud'], $lastPos['longitud'])));
      if ($actKm > 0.05 && $actKm < 2000) {
        $kmTotal += $actKm;
      }
    }
  }

  $kmTotal = (int) $kmTotal;

  echo "Distancia Total: " . $kmTotal . "\n";

  $conKm = "INSERT INTO rep_km_remolques (
                idrepkmremolque,
                noeconomico,
                nserie,
                kilometros,
                fecha_inicio,
                fecha_termino,
                fecha_reporte
              ) VALUES 
                (DEFAULT, ?, ?, ?, ?, ?, NOW())";
  $qryKm = $conn->prepare($conKm);
  $qryKm->bindParam(1, $rowRem['economico']);
  $qryKm->bindParam(2, $rowRem['serie']);
  $qryKm->bindParam(3, $kmTotal);
  $qryKm->bindParam(4, $fechaIni);
  $qryKm->bindParam(5, $fechaFin);
  $qryKm->execute();
  $qryKm->closeCursor();
}
$qryRem->closeCursor();
