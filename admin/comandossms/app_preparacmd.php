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


$validarparo = 0;

switch ($tipo_msj) {
    /** Unidad en Movimiento 
     * Si el comando es un paro de motor,
     * consulta si la unidad esta en movimiento */
    case '84':
        $conMov = "SELECT num_longitud_pos,num_latitud_pos,num_ignicion_pos FROM tb_posiciones WHERE num_nserie_pos = ? ORDER BY fec_ultimaposicion_pos DESC LIMIT 2";
        $qryMov = $conn->prepare($conMov);
        $qryMov->bindParam(1, $imei);
        $qryMov->execute();
        $latitud = 0;
        $longitud = 0;
        while ($regMov = $qryMov->fetch()) {
            $latitud = abs($latitud - $regMov['num_latitud_pos']);
            $longitud = abs($longitud - abs($regMov['num_longitud_pos']));
            $ignicion = $regMov['num_ignicion_pos'];
    
            if (($latitud + $longitud) < .001) {
                $validarparo = 0;
            } else {
                $validarparo = 1;
            }
        }        
        break;

    /** Inmovilizador */
    case '89':
        $conInmo = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
        $qryInmo = $conn->prepare($conInmo);
        $qryInmo->bindParam(1, $economico);
        $qryInmo->execute();
        
        $user = '0';
        $conInmo = "INSERT INTO monitoreo.vehiculos_inmovilizados (noeconomico,usuario,fecha,estatus) VALUES (?,?,NOW(),1)";
        $qryInmo = $conn->prepare($conInmo);
        $qryInmo->bindParam(1, $economico);
        $qryInmo->bindParam(2, $user);
        $qryInmo->execute();
        $qryInmo->closeCursor();
        break;

    /** Desinmovilizador */
    case '88':
        $conDes = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
        $qryDes = $conn->prepare($conDes);
        $qryDes->bindParam(1, $economico);
        $qryDes->execute();
        $qryDes->closeCursor();
        break;

    default:
        break;
}


/** Consulta y Crea Comando */
$conCmd = "SELECT * FROM tb_tiposdemensajessms WHERE pk_clave_tipm = ?";
$qryCmd = $conn->prepare($conCmd);
$qryCmd->bindParam(1, $tipo_msj);
$qryCmd->execute();

$resCmd = $qryCmd->fetch();
$cmd = $resCmd['txt_mensaje_tipm'];
$qryCmd->closeCursor();

// Remplaza IMEI en plantilla de comandos
$cmd = str_replace('#', $imei, $cmd);
// Añade el tipo de GPS al comando

if(substr($imei,0,2)=='05'){
    $cmd = str_replace('02;Enable1', '04;01', $cmd);
    $cmd = str_replace('02;Disable1', '04;02', $cmd);    
}else{
    $cmd = 'ST600'.$cmd;
}



// Crea Json
$myArr = array(
    'protocolo' => $protocolo,
    'puerto' => $puerto,
    'imei' => $imei,
    'lat' => $lat,
    'lon' => $lon,
    'pos' => $pos,
    'cmd' => $cmd,
    'paro' => $validarparo
);


$myJSON = json_encode($myArr);
echo $myJSON;

?>