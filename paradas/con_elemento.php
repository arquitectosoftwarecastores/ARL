<?php
//session_start();
$vehiculo = $_POST['vehiculo'];
$from = $_POST['from'];
$to = $_POST['to'];
$distancia = $_POST["distancia"];

$valUni = true;

if ($valUni) {
$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();


$consulta1 = " SELECT * FROM tb_remolques WHERE txt_economico_rem=?";
$query1 = $conn->prepare($consulta1);
$query1->bindParam(1, $vehiculo);
$query1->execute();
$nserie = 0;
while ($registro1 = $query1->fetch()) {
    $eco = $registro1['txt_economico_rem'];
    $nserie = trim($registro1['txt_nserie_rem']);
    $fecha_actual = date("Y/m/d H:i:s", time());
    $ubicacion_actual = $registro1['txt_georeferencia_mun'];
    $lat_actual = $registro1['num_latitud_rem'];
    $lon_actual = $registro1['num_longitud_rem'];
}
if ($nserie == 0) {
    echo "<p>No se encontró el vehículo.</p>";
    exit();
}

$consulta22 = " SELECT * FROM avl_secundario WHERE sec_primario=?";
$query22 = $conn->prepare($consulta22);
$query22->bindParam(1, $nserie);
$query22->execute();
$nseriesec = 0;
//$from=date('Y-m-d H:i:s',strtotime('+'.$ajustegps.' hour',strtotime($from)));
//$to=date('Y-m-d H:i:s',strtotime('+'.$ajustegps.' hour',strtotime($to)));
$fechainicialq = date('Y-m-d H:i:s', strtotime($ajustegps . ' hour', strtotime($from . '00:00:00')));
$fechafinalq = date('Y-m-d H:i:s', strtotime(($ajustegps + 24) . ' hour', strtotime($to . '00:00:00')));
//echo  $fechainicialq."  ".$fechafinalq;
while ($registro22 = $query22->fetch()) {
    $nseriesec = $registro22['sec_secundario'];
}
if ($nseriesec == 0) {
  if($fechainicialq > '2021-06-01 00:00:00'){
    $strSQL = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";
    }elseif ($fechainicialq > '2021-05-01 00:00:00') {
    $strSQL = " SELECT * FROM tb_posiciones_historico52021 WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";
    }elseif($fechainicialq > '2021-04-01 00:00:00'){
    $strSQL = " SELECT * FROM tb_posiciones_historico42021 WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";
    }elseif ($fechainicialq > '2021-03-01 00:00:00') {
    $strSQL = " SELECT * FROM tb_posiciones_historico32021 WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";
    }elseif ($fechainicialq > '2021-02-01 00:00:00') {
      $strSQL = " SELECT * FROM tb_posiciones_historico22021 WHERE num_nserie_pos='" . $nserie . "'
                 AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
                 AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
                 ORDER BY fec_ultimaposicion_pos ";
      }elseif ($fechainicialq > '2021-01-01 00:00:00') {
        $strSQL = " SELECT * FROM tb_posiciones_historico12021 WHERE num_nserie_pos='" . $nserie . "'
                   AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
                   AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
                   ORDER BY fec_ultimaposicion_pos ";
        }
} else {
    if ($fechainicialq < '2020-12-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico112020 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
       ) posiciones
        ORDER BY fec_ultimaposicion_pos ";  
    } elseif ($fechainicialq < '2021-01-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico122020 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
       ) posiciones
        ORDER BY fec_ultimaposicion_pos ";  
    } elseif ($fechainicialq < '2021-02-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico12021 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";
    } elseif ($fechainicialq < '2021-03-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico22021 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";
    } elseif ($fechainicialq < '2021-04-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico32021 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";
    } elseif ($fechainicialq < '2021-05-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico42021 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";
    } elseif ($fechainicialq < '2021-06-01 00:00:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico52021 WHERE num_nserie_pos='" . $nserie . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
       UNION
        SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nseriesec . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";
    } else {
        $strSQL = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nserie . "'
                   AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
                   AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
                   ORDER BY fec_ultimaposicion_pos ";
    }
}
}
?>
