<?php

include_once('../conexion/conexion.php');

$arrRes = array();

session_name('ARL');
session_start();

if (isset($_SESSION['id'])) {
  $conCon = "SELECT 
             (SELECT COUNT(*) FROM tb_conexionesgps WHERE estatus = 1) AS conexiones,
             (SELECT COUNT(*) FROM tb_conexionesgps WHERE estatus = 0) AS desconexiones";
  $qryCon = $conn->prepare($conCon);
  $qryCon->execute();
  $resCon = $qryCon->fetch();

  $con = $resCon['conexiones'];
  $descon = $resCon['desconexiones'];

  $arrCount = array(
    'conexiones' => $con,
    'desconexiones' => $descon
  );

  $arrConex = array();

  $conPos = "SELECT 
                nserie, txt_economico_rem AS economico,
                ch.estatus AS estatus, fecha
              FROM tb_conexionesgps_historico AS ch
              LEFT JOIN tb_remolques AS tr
                ON ch.nserie = tr.txt_nserie_rem
              ORDER BY pk_conexiongps_his DESC
              LIMIT 15000";
  $qryPos = $conn->prepare($conPos);
  $qryPos->execute();

  while ($resPos = $qryPos->fetch()) {
    $UTC = $resPos['fecha'];

    $MX = new DateTime($UTC, new DateTimeZone('UTC'));
    $MX = $MX->setTimezone(new DateTimeZone('America/Mexico_City'));
    $MX = date_format($MX, 'Y-m-d H:i:s');

    $arrPos = array(
      'esn' => $resPos['nserie'],
      'noeconomico' => $resPos['economico'],
      'estatus' => $resPos['estatus'],
      'fecha' => $MX,
    );

    array_push($arrConex, $arrPos);
  }
  
  $arrRes = array(
    'count' => $arrCount,
    'conexiones' => $arrConex,
  );
}



echo json_encode($arrRes);
