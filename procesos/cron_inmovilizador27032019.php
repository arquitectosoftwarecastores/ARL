<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
require_once('xmlrpc.inc');
date_default_timezone_set("America/Mexico_City");
while (true) {    
    echo "Iniciando...";
    echo "<br>";
    $consulta = " select * from monitoreo.tb_vehiculos v
	 join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh 
         where v.txt_economico_veh not like '00%'";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $mensaje = '0';
        $economico1 = $registro["txt_economico_veh"];
        $riesgo = $registro["riesgo"];
        echo "Economico: " . $economico1;
        echo "Riesgo: " . $riesgo;
        $estatus;
        if ($riesgo == 3683) {
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_inmovilizados where noeconomico = ? and automatico = 1";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 0) {
                    $mensaje = '89';
                }
            }
        } else {
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_inmovilizados where noeconomico = ? and automatico = 1";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 1) {
                    $mensaje = '88';
                }
            }
        }
        $query2->closeCursor();

        if($mensaje != '0'){        
        echo "Realiza el cambio a: ".$mensaje;
        //$user = $_SESSION["usuario"];
        $user = "11040274";
        $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
        $xmlrpc_client->setSSLVerifyPeer(0);
        $xmlrpc_client->setCredentials('castores', 'PAQUETERIAtelcel');
        $comentario = "Esta es una prueba por desarrollo";
        $vehiculo_actual = $economico1;
        $consulta5 = "SELECT num_serie_veh FROM tb_vehiculos where txt_economico_veh = ?";
        $query5 = $conn->prepare($consulta5);
        $query5->bindParam(1, $vehiculo_actual);
        $query5->execute();
        $registro5 = $query5->fetch();
        if (isset($registro5["num_serie_veh"])) {
            $consulta6 = "SELECT * FROM tb_tiposdemensajessms where pk_clave_tipm = ?";
            $query6 = $conn->prepare($consulta6);
            $query6->bindParam(1, $mensaje);
            $query6->execute();
            $registro6 = $query6->fetch();
            if (isset($registro6["txt_codigo_tipm"])) {
                $consulta7 = "SELECT * FROM tb_numerossms where txt_economico_veh = ?";
                $query7 = $conn->prepare($consulta7);
                $query7->bindParam(1, $vehiculo_actual);
                $query7->execute();
                $registro7 = $query7->fetch();
                $mensaje_enviar = str_replace("#", trim($registro5["num_serie_veh"]), $registro6["txt_mensaje_tipm"]);
                $telefono = $registro7["txt_telefono_num"];
            }
        }
        /* Cambio Por Marco Sanchez para aplicar comando independientmente del tipo de gps */
// 83 Solicitar Posicionar
        switch ($registro6["pk_clave_tipm"]) {
            case '83' || '84' || '85' || '86' || '87' || '90':
                //         echo "si entra";
                $modelogps = substr(($registro5["num_serie_veh"]), 0, 3);
                switch ($modelogps) {
                    case '008':
                        $mensaje_enviar = 'ST600' . $mensaje_enviar;
                        break;
                    case '107':
                        $mensaje_enviar = 'ST600' . $mensaje_enviar;
                        break;
                    case '205':
                        $mensaje_enviar = 'ST300' . $mensaje_enviar;
                        break;
                    case '907':
                        $mensaje_enviar = 'ST300' . $mensaje_enviar;
                        break;
                    default:
                        $mensaje_enviar = 'SA200' . $mensaje_enviar;
                }
                break;
        }
            //inmovilizador
        if ($mensaje == '89') {
            $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
            $query12 = $conn->prepare($consulta13);
            $query12->bindParam(1, $vehiculo_actual);
            $query12->execute();
 //           $query12->closeCursor();

            $consulta12 = "INSERT INTO monitoreo.vehiculos_inmovilizados (noeconomico,usuario,fecha,automatico) VALUES (?,?,NOW(),1)";
            $query12 = $conn->prepare($consulta12);
            $query12->bindParam(1, $vehiculo_actual);
            $query12->bindParam(2, $user);
            $query12->execute();
            $query12->closeCursor();

            //insertamos la fecha cuando la unidad entro a zona de riesgo
            try{
                $consulta15 = "INSERT INTO monitoreo.unidadesrutariesgo (economico,entrada,salida,fecha_entrada,estatus) VALUES (?,1,0,NOW(),1)";
                $query15 = $conn->prepare($consulta15);
                $query15->bindParam(1,$vehiculo_actual);
                $query15->execute();
                $query15->closeCursor();

            }catch(Exception $exc){
                echo $exc->getTraceAsString();
            }
        }
        //Desinmovilizador  
        if ($mensaje == '88') {
            $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
            $query13 = $conn->prepare($consulta13);
            $query13->bindParam(1, $vehiculo_actual);
            $query13->execute();
            $query13->closeCursor();

            //actualizar la fecha, indicando la salida de la zona de riesgo
            try{     
                $economico = (string) $vehiculo_actual;           
                $consulta14 = "UPDATE monitoreo.unidadesrutariesgo set fecha_salida = NOW(), entrada = 0, salida = 1, estatus = 0 where economico = ? and entrada = 1";
                $query14 = $conn->prepare($consulta14); 
                $query14->bindParam(1,$economico);                
                $query14->execute();
                $query14->closeCursor();
            }catch(Exception $exc){
                echo $exc->getTraceAsString();
            }
        }
        
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
        echo "<p class='centrado'><strong>Económico: " . $vehiculo_actual . ", RESPUESTA DE ENVIO DE MENSAJE " . $txt_respuesta . "</strong></p>";
        }
    }
    $query->closeCursor();
}
?><?php

error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
require_once('xmlrpc.inc');
date_default_timezone_set("America/Mexico_City");
while (true) {    
    echo "Iniciando...";
    echo "<br>";
    $consulta = " select * from monitoreo.tb_vehiculos v
	 join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh 
         where v.txt_economico_veh not like '00%'";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $mensaje = '0';
        $economico1 = $registro["txt_economico_veh"];
        $riesgo = $registro["riesgo"];
        echo "Economico: " . $economico1;
        echo "Riesgo: " . $riesgo;
        $estatus;
        if ($riesgo != 0) {
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_inmovilizados where noeconomico = ? and automatico = 1";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 0) {
                    $mensaje = '89';
                }
            }
        } else {
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_inmovilizados where noeconomico = ? and automatico = 1";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 1) {
                    $mensaje = '88';
                }
            }
        }
        $query2->closeCursor();

        if($mensaje != '0'){        
        echo "Realiza el cambio a: ".$mensaje;
        //$user = $_SESSION["usuario"];
        $user = "11040274";
        $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
        $xmlrpc_client->setSSLVerifyPeer(0);
        $xmlrpc_client->setCredentials('castores', 'PAQUETERIAtelcel');
        $comentario = "Esta es una prueba por desarrollo";
        $vehiculo_actual = $economico1;
        $consulta5 = "SELECT num_serie_veh FROM tb_vehiculos where txt_economico_veh = ?";
        $query5 = $conn->prepare($consulta5);
        $query5->bindParam(1, $vehiculo_actual);
        $query5->execute();
        $registro5 = $query5->fetch();
        if (isset($registro5["num_serie_veh"])) {
            $consulta6 = "SELECT * FROM tb_tiposdemensajessms where pk_clave_tipm = ?";
            $query6 = $conn->prepare($consulta6);
            $query6->bindParam(1, $mensaje);
            $query6->execute();
            $registro6 = $query6->fetch();
            if (isset($registro6["txt_codigo_tipm"])) {
                $consulta7 = "SELECT * FROM tb_numerossms where txt_economico_veh = ?";
                $query7 = $conn->prepare($consulta7);
                $query7->bindParam(1, $vehiculo_actual);
                $query7->execute();
                $registro7 = $query7->fetch();
                $mensaje_enviar = str_replace("#", trim($registro5["num_serie_veh"]), $registro6["txt_mensaje_tipm"]);
                $telefono = $registro7["txt_telefono_num"];
            }
        }
        /* Cambio Por Marco Sanchez para aplicar comando independientmente del tipo de gps */
// 83 Solicitar Posicionar
        switch ($registro6["pk_clave_tipm"]) {
            case '83' || '84' || '85' || '86' || '87' || '90':
                //         echo "si entra";
                $modelogps = substr(($registro5["num_serie_veh"]), 0, 3);
                switch ($modelogps) {
                    case '008':
                        $mensaje_enviar = 'ST600' . $mensaje_enviar;
                        break;
                    case '107':
                        $mensaje_enviar = 'ST600' . $mensaje_enviar;
                        break;
                    case '205':
                        $mensaje_enviar = 'ST300' . $mensaje_enviar;
                        break;
                    case '907':
                        $mensaje_enviar = 'ST300' . $mensaje_enviar;
                        break;
                    default:
                        $mensaje_enviar = 'SA200' . $mensaje_enviar;
                }
                break;
        }
            //inmovilizador
        if ($mensaje == '89') {
            $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
            $query12 = $conn->prepare($consulta13);
            $query12->bindParam(1, $vehiculo_actual);
            $query12->execute();
 //           $query12->closeCursor();

            $consulta12 = "INSERT INTO monitoreo.vehiculos_inmovilizados (noeconomico,usuario,fecha,automatico) VALUES (?,?,NOW(),1)";
            $query12 = $conn->prepare($consulta12);
            $query12->bindParam(1, $vehiculo_actual);
            $query12->bindParam(2, $user);
            $query12->execute();
            $query12->closeCursor();

            //insertamos la fecha cuando la unidad entro a zona de riesgo
            try{
                $consulta15 = "INSERT INTO monitoreo.unidadesrutariesgo (economico,entrada,salida,fecha_entrada,estatus) VALUES (?,1,0,NOW(),1)";
                $query15 = $conn->prepare($consulta15);
                $query15->bindParam(1,$vehiculo_actual);
                $query15->execute();
                $query15->closeCursor();

            }catch(Exception $exc){
                echo $exc->getTraceAsString();
            }
        }
        //Desinmovilizador  
        if ($mensaje == '88') {
            $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
            $query13 = $conn->prepare($consulta13);
            $query13->bindParam(1, $vehiculo_actual);
            $query13->execute();
            $query13->closeCursor();

            //actualizar la fecha, indicando la salida de la zona de riesgo
            try{     
                $economico = (string) $vehiculo_actual;           
                $consulta14 = "UPDATE monitoreo.unidadesrutariesgo set fecha_salida = NOW(), entrada = 0, salida = 1, estatus = 0 where economico = ? and entrada = 1";
                $query14 = $conn->prepare($consulta14); 
                $query14->binpParam(1,$economico);                
                $query14->execute();
                $query14->closeCursor();
            }catch(Exception $exc){
                echo $exc->getTraceAsString();
            }
        }
        
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
        echo "<p class='centrado'><strong>Económico: " . $vehiculo_actual . ", RESPUESTA DE ENVIO DE MENSAJE " . $txt_respuesta . "</strong></p>";
        }
    }
    $query->closeCursor();
}
?>