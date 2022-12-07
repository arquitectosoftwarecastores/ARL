<?php

include('../../conexion/conexion.php');
$msj = $_POST['msj'];
$numAle = '216';
$res = '1FAIL';


// Genera alerta
$insAle = 'INSERT INTO tb_alertas (fk_clave_tipa, fec_fecha_ale, txt_economico_veh, txt_ubicacion_ale, num_latitud_ale, num_longitud_ale) VALUES (?, NOW(), ?, ?, ?, ?)';
$qryAle = $conn->prepare($insAle);
$qryAle->bindParam(1, $numAle);
$qryAle->bindParam(2, $msj.eco);
$qryAle->bindParam(3, $msj.pos);
$qryAle->bindParam(4, $msj.lat);
$qryAle->bindParam(5, $msj.lon);
$qryAle->execute();

// 
$insMsj = 'INSERT INTO tb_mensajesenviadossms (fk_clave_usu, txt_economico_veh, fec_fecha_mene, txt_respuesta_mene, num_latitud_mene, num_longitud_mene, txt_posicion_mene, fk_clave_tipm, txt_comentario_mene) VALUES (?, ?, now(), ?, ?, ?, ?, ?, ?)';
$qryMsj = $conn->prepare($insMsj);
$qryMsj->bindParam(1, $msj.usr);
$qryMsj->bindParam(2, $msj.eco);
$qryMsj->bindParam(3, $res);
$qryMsj->bindParam(4, $msj.lat);
$qryMsj->bindParam(5, $msj.lon);
$qryMsj->bindParam(6, $msj.pos);
$qryMsj->bindParam(7, $msj.tip);
$qryMsj->bindParam(8, $msj.com);
$qryMsj->execute();

echo '{ "res": "0OK" }';

?>
