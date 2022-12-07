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
	echo "************************************************************************************************************************";
    echo "Iniciando el cron...";
    echo "<br>";
    $mensaje = '0';
    $consulta = "select * from monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh join monitoreo.tb_mantenimientos m on m.economico = v.txt_economico_veh where v.txt_economico_veh not like '00%' and fecha_baja is null";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $economico1 = $registro["txt_economico_veh"];
        $latitud1 = $registro["num_latitud_veh"];
        $longitud1 = $registro["num_longitud_veh"];
        $mantenimiento = checazona($latitud1, $longitud1, 2, $conn);
	    echo "Economico: " . $economico1;
        echo "<br>";        
        $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET sucursal = ? where economico = ?";
        $query2 = $conn->prepare($inserta_geocerca);
        $query2->bindParam(2, $economico1);
        $query2->bindParam(1, $mantenimiento);
        $query2->execute();
        echo "Mantenimiento: ".$mantenimiento; 
        if ($mantenimiento == '535') {
            echo "Unidad se encuentra en Corporativo";
            echo "<br>";
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_mantenimiento where unidad = ?";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 0) {
                     echo "Insertamos unidad a Vehiculos en Mantenimiento";
                     echo "<br>";
                    $mensaje = '189';
                }else{
                    echo "Unidad ya incluida en los Vehiculos en Mantenimiento";
                    echo "<br>";
                }
            }
        } else {
            echo "Unidad no se encuentra en Corporativo";
            echo "<br>";
            $counsulta_inmovilizada = "select count(*) as total from monitoreo.vehiculos_mantenimiento where unidad = ?";
            $query2 = $conn->prepare($counsulta_inmovilizada);
            $query2->bindParam(1, $economico1);
            $query2->execute();
            while ($registro2 = $query2->fetch()) {
                $total = $registro2["total"];
                if ($total == 1) {
                    $mensaje = '188';
                    echo "Eliminamos la unidad de Vehiculos en Mantenimiento";
                    echo "<br>";
                }else{
                    $mensaje = '100';
                    echo "Mantenemos fuera la unidad de Vehiculos en Mantenimiento";
                    echo "<br>";
                }
            }
        }
        $query2->closeCursor();

                    // Paquete TELCEL  Fijo -  02/12/2019
                    $usuario = 'castores';
                    $contrasena =  'PAQUETERIAtelcel';            
            
            $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
            $xmlrpc_client->setSSLVerifyPeer(0);
            $xmlrpc_client->setCredentials(trim($usuario),trim($contrasena));
            
            /* Cambio Por Marco Sanchez para aplicar comando independientmente del tipo de gps */

            $mensaje_enviar = "La unidad: ".$economico1." se encuentra en corporativo";
            //inmovilizador
            if ($mensaje == '189') {
                $consulta13 = "DELETE FROM monitoreo.vehiculos_mantenimiento WHERE unidad = ?";
                $query12 = $conn->prepare($consulta13);
                $query12->bindParam(1, $economico1);
                $query12->execute();

                $consulta12 = "INSERT INTO monitoreo.vehiculos_mantenimiento (unidad,fecha) VALUES (?,NOW())";
                $query12 = $conn->prepare($consulta12);
                $query12->bindParam(1, $economico1);
                $query12->execute();
                $query12->closeCursor();
            }
            //Desinmovilizador  
            if ($mensaje == '188') {
                echo "Elimina unidad de vehiculos mantenimiento ";
                echo "<br>";
                $consulta13 = "DELETE FROM monitoreo.vehiculos_mantenimiento WHERE unidad = ?";
                $query13 = $conn->prepare($consulta13);
                $query13->bindParam(1, $economico1);
                $query13->execute();
                $query13->closeCursor();
            }

            if ($mensaje == '189') {

            echo "Finalmente se envia el mensaje ";
            echo "<br>";
            $telefono = "4775648292";
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

                
            $telefono = "4773936178";
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
