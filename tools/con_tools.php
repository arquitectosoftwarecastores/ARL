<?php

include_once('../conexion/conexion.php');

$arrRes = array();

if (isset($_POST['id'])) {
  $id = $_POST['id'];


  if (strlen($id) > 1) {
    $conRem = "SELECT * 
                FROM tb_remolques
                WHERE 
                  (txt_economico_rem = ? OR
                  txt_nserie_rem = ?) AND 
                  estatus = 1
                LIMIT 1";
    $qryRem = $conn->prepare($conRem);
    $qryRem->bindParam(1, $id);
    $qryRem->bindParam(2, $id);
    $qryRem->execute();
    $resRem = $qryRem->fetch();

    $esn = $id;
    $eco = $resRem['txt_economico_rem'];
    $ind = $resRem['num_icono_rem'];

    if (strlen($resRem['txt_nserie_rem']) > 0) {
      $esn = $resRem['txt_nserie_rem'];
    }

    $remolque = array(
      'noeconomico' => $eco,
      'esn' => $esn,
      'indicador' => $ind,
    );

    $cadenas = array();

    if ($esn !== null) {
      $conPos = "SELECT * 
                  FROM tb_posiciones
                  WHERE txt_nserie_pos = ?
                  ORDER BY fec_ultimaposicion_pos DESC
                  LIMIT 50";
      $qryPos = $conn->prepare($conPos);
      $qryPos->bindParam(1, $esn);
      $qryPos->execute();

      while ($resPos = $qryPos->fetch()) {
        $UTC = $resPos['fec_ultimaposicion_pos'];

        $MX = new DateTime($UTC, new DateTimeZone('UTC'));
        $MX = $MX->setTimezone(new DateTimeZone('America/Mexico_City'));
        $MX = date_format($MX, 'Y-m-d H:i:s');

        $arrPos = array(
          'esn' => $esn,
          'ultimaposicion' => $MX,
          'bdposicion' => substr($UTC, 0, 16),
          'coordenadas' => $resPos['num_latitud_pos'] . ', ' . $resPos['num_longitud_pos'],
          'ignicion' => $resPos['num_ignicion_pos'],
          'vias' => $resPos['num_charge_pos'],
          'voltaje' => $resPos['num_voltage_pos'],
          'motion' => $resPos['num_motion_pos'],
          'power' => $resPos['num_powerstate_pos'],
          'event' => $resPos['num_event_pos'],
          'fixstatus' => $resPos['txt_fixstatus_pos'],
          'satelites' => $resPos['num_satellites_pos'],
          'carrier' => $resPos['num_carrier_pos'],
          'rssi' => $resPos['num_rssi_pos'],
        );

        array_push($cadenas, $arrPos);
      }
    }

    $arrRes = array(
      'remolque' => $remolque,
      'cadenas' => $cadenas,
    );
  }
}

echo json_encode($arrRes);
