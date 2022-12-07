<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
require_once('xmlrpc.inc');
date_default_timezone_set("America/Mexico_City");


$consulta = "SELECT tv.txt_economico_veh, txt_telefono_num, 
            CONCAT('ST600ADP;',sec_primario,';02;T;T;1;9878;0;0;0;0;0.00;0') AS cmd1, 
            CONCAT('ST600NTW;',sec_primario,';02;0;internet.itelcel.com;;;69.172.241.230;9878;69.172.241.230;9878;;0209') AS cmd2
            FROM tb_vehiculos AS tv
            LEFT JOIN avl_secundario AS asec ON tv.num_serie_veh = asec.sec_primario
            LEFT JOIN tb_numerossms AS tns ON tv.txt_economico_veh = tns.txt_economico_veh
            LEFT JOIN tb_configuraciongps AS tcg ON asec.sec_primario = tcg.imei
            WHERE tv.txt_economico_veh >= '20000' AND tns.linea = 1 AND tv.status = 1 AND protocolo != 'T'
            ORDER BY tv.txt_economico_veh ASC
            LIMIT 20";
$query = $conn->prepare($consulta);
$query->execute();

while ($registro = $query->fetch()) {
    
    $economico = $registro['txt_economico_veh'];
    $cmd1 =  $registro['cmd1'];
    $cmd2 =  $registro['cmd2'];
    
    // Consulta catalogo al que pertenece la unidad
    $consulta7 = "SELECT * FROM tb_numerossms WHERE txt_economico_veh = ? AND linea = 1";
    $query7 = $conn->prepare($consulta7);
    $query7->bindParam(1, $economico);
    $query7->execute();
    $registro7 = $query7->fetch();

    $paquetetelcel = $registro7['catalogo_telcel'];

    // Consulta Usuario y Contraseña del catalogo
    $consulta8 = "SELECT * FROM monitoreo.catalogo_acceso_telcel where idpaquete = ?";
    $query8 = $conn->prepare($consulta8);
    $query8->bindParam(1, $paquetetelcel);
    $query8->execute();
    $registro8 = $query8->fetch();
    $usuario = $registro8["usuario"];
    $contrasena = $registro8["contrasena"];
    
    $telefono = $registro7["txt_telefono_num"];

    // Envia Comando 1
    enviarSMS($economico, $telefono, $cmd1 ,$usuario, $contrasena);

    // Envia Comando 2
    enviarSMS($economico, $telefono, $cmd2 ,$usuario, $contrasena);

}


function enviarSMS ($economico, $telefono, $mensaje_enviar ,$usuario, $contrasena) {

    echo '\n'.$economico.' - '.$telefono.' - '.$mensaje_enviar.'\n';
    
    $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
    $xmlrpc_client->setSSLVerifyPeer(0);
    $xmlrpc_client->setCredentials(trim($usuario),trim($contrasena));

    $structArray = array();
    $structArray[] = new xmlrpcval(
                    array(
                        new xmlrpcval(array(
                            "celular" => new xmlrpcval($telefono, "string"),
                            "texto" => new xmlrpcval($mensaje_enviar, "string"),
                            "fecha" => new xmlrpcval("", "string")
                                ), "struct"),
                    ), "array");
    $xmlrpc_msg = new xmlrpcmsg('Tiaxa.SendMsg', $structArray);
    $xmlrpc_resp = $xmlrpc_client->send($xmlrpc_msg);
    $txt_respuesta = $xmlrpc_resp->serialize();
    $txt_respuesta = str_replace("</string>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<value>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</value>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<array>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<data>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</array>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</data>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<string>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<methodResponse>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<params>", "", $txt_respuesta);
    $txt_respuesta = str_replace("<param>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</methodResponse>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</params>", "", $txt_respuesta);
    $txt_respuesta = str_replace("</param>", "", $txt_respuesta);
    $txt_respuesta = html_entity_decode($txt_respuesta);
    $txt_respuesta = str_replace("&aacute;", "a", $txt_respuesta);
    $txt_respuesta = str_replace("&eacute;", "e", $txt_respuesta);
    $txt_respuesta = str_replace("&iacute;", "i", $txt_respuesta);
    $txt_respuesta = str_replace("&oacute;", "o", $txt_respuesta);
    $txt_respuesta = str_replace("&uacute;", "u", $txt_respuesta);
    $txt_respuesta = str_replace("\n", "", $txt_respuesta);
    $txt_respuesta = str_replace("\r", " ", $txt_respuesta);
    echo "\nEconomico: " . $economico . ", RESPUESTA DE ENVIO DE MENSAJE " . $txt_respuesta . "\n\n";
}

?>