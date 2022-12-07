<?php

$vehiculo = $_POST['vehiculo'];
$from = $_POST['from'];
$to = $_POST['to'];
$distancia = $_POST["distancia"];

$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();


$consulta1 = " SELECT * FROM tb_vehiculos
                 WHERE txt_economico_veh=?";
$query1 = $conn->prepare($consulta1);
$query1->bindParam(1, $vehiculo);
$query1->execute();
$nserie = 0;
while ($registro1 = $query1->fetch()) {
    $eco = $registro1['txt_economico_veh'];
    $nserie = trim($registro1['num_serie_veh']);
    $fecha_actual = date("Y/m/d H:i:s", time());
    $ubicacion_actual = $registro1['txt_posicion_veh'];
    $lat_actual = $registro1['num_latitud_veh'];
    $lon_actual = $registro1['num_longitud_veh'];
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
    if($fechainicialq < '2019-02-01'){
    $strSQL = " SELECT * FROM tb_posiciones_historico3 WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";
    }else{
    $strSQL = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nserie . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
               ORDER BY fec_ultimaposicion_pos ";            
    }
} else {
    if($fechainicialq < '2018-11-05 18:26:00'){
    $strSQL = "  SELECT * FROM (
               SELECT * FROM tb_posiciones_historico WHERE num_nserie_pos='" . $nserie . "' 
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
              UNION
               SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nseriesec . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
             ) posiciones
               ORDER BY fec_ultimaposicion_pos ";
    }elseif($fechainicialq < '2019-01-17 11:00:00'){
     $strSQL = "  SELECT * FROM (
               SELECT * FROM tb_posiciones_historico2 WHERE num_nserie_pos='" . $nserie . "' 
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
              UNION
               SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nseriesec . "'
               AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
               AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
             ) posiciones
               ORDER BY fec_ultimaposicion_pos ";   
    }elseif($fechainicialq < '2019-02-01 12:40:00'){
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones_historico3 WHERE num_nserie_pos='" . $nserie . "' 
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
       UNION
        SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nseriesec . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";  
    }else{
      $strSQL = "  SELECT * FROM (
        SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nserie . "' 
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
       UNION
        SELECT * FROM tb_posiciones WHERE num_nserie_pos='" . $nseriesec . "'
        AND fec_ultimaposicion_pos >= '" . $fechainicialq . "'
        AND fec_ultimaposicion_pos <= '" . $fechafinalq . "'
      ) posiciones
        ORDER BY fec_ultimaposicion_pos ";  
    }
}
?>