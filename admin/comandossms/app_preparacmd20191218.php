<?php
include('../../conexion/conexion.php');

$unidad = $_GET['array'];

$economico = $unidad['eco'];
$tipo_msj = $unidad['tip'];


/** Consulta IMEI, configuracion y detalles de la Unidad  
 * Obteniendo IMEI, protocolo y puertos
 * Por default debe de regresar el gps primario
 */
$conImei = "SELECT * FROM tb_vehiculos AS tv
            LEFT JOIN tb_configuraciongps AS tcg ON tv.txt_economico_veh = tcg.economico
            LEFT JOIN tb_puertos AS tp ON tcg.puerto = tp.pk_puerto
            WHERE tv.txt_economico_veh = ? AND tv.status = 1 AND tcg.linea = 1";
$qryImei = $conn->prepare($conImei);
$qryImei->bindParam(1, $economico);
$qryImei->execute();
$resImei = $qryImei->fetch();

// Datos de Configuracion del GPS
$protocolo = $resImei['protocolo'];
$puerto = $resImei['num_puerto_http'];

// Datos de la Unidad
$imei = $resImei['imei'];
$economico = $resImei['txt_economico_veh'];
$lat = $resImei['num_latitud_veh'];
$lon = $resImei['num_longitud_veh'];
$pos = $resImei['txt_upsmart_veh'];
$qryImei->closeCursor();


/** Consulta y Crea Comando */
$conCmd = "SELECT * FROM tb_tiposdemensajessms WHERE pk_clave_tipm = ?";
$qryCmd = $conn->prepare($conCmd);
$qryCmd->bindParam(1, $tipo_msj);
$qryCmd->execute();

$resCmd = $qryCmd->fetch();
$cmd = $resCmd['txt_mensaje_tipm'];
$qryCmd->closeCursor();

// Repmlaza IMEI en plantilla de comandos
$cmd = str_replace('#', $imei, $cmd);
// Añade el tipo de GPS al comando
$cmd = 'ST600'.$cmd;


// Crea Json
$myArr = array(
    'protocolo' => $protocolo,
    'puerto' => $puerto,
    'imei' => $imei,
    'lat' => $lat,
    'lon' => $lon,
    'pos' => $pos,
    'cmd' => $cmd
);


$myJSON = json_encode($myArr);
echo $myJSON;

?>