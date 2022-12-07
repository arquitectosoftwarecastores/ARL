<?php
$user = $_SESSION["usuario"];
if (isset($_SESSION['comandossms'])) {
    require_once('xmlrpc/xmlrpc.inc');
    $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
    $xmlrpc_client->setSSLVerifyPeer(0);
    $xmlrpc_client->setCredentials('castores', 'PAQUETERIAtelcel');
    $numerodevehiculos = $_POST["numerodevehiculos"];
    $mensaje = $_POST["mensaje"];
    $comentario = $_POST["comentario"];
    for ($i = 0; $i < $numerodevehiculos; $i++) {
        if (isset($_POST["vehiculo$i"])) {
            $vehiculo_actual = $_POST["vehiculo$i"];
         // $consulta5 = "SELECT num_serie_veh FROM tb_vehiculos where txt_economico_veh = ?";
	    $consulta5 = "SELECT s.sec_primario as num_serie_veh FROM tb_vehiculos v join avl_secundario s on (v.num_serie_veh  = s.sec_primario or v.num_serie_veh  = s.sec_secundario) where txt_economico_veh = ?";
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
                    $consulta7 = "SELECT * FROM tb_numerossms where txt_economico_veh = ? and linea = 1";
                    $query7 = $conn->prepare($consulta7);
                    $query7->bindParam(1, $vehiculo_actual);
                    $query7->execute();
                    $registro7 = $query7->fetch();
                    $mensaje_enviar = str_replace("#", trim($registro5["num_serie_veh"]), $registro6["txt_mensaje_tipm"]);
                    $telefono = $registro7["txt_telefono_num"];
                    
                     // Cambio realizadso el 27 de Marzo para dividir segun el paquete contratado de telcel
 //                   $paquetetelcel = $registro7["catalogo_telcel"];
                    // Sacamos el usuario y la contraseña correspondiente
//                    $consulta8 = "select * from monitoreo.catalogo_acceso_telcel where idpaquete = ?";
  //                  $query8 = $conn->prepare($consulta8);
    //                $query8->bindParam(1, $paquetetelcel);
      //              $query8->execute();
        //            $registro8 = $query8->fetch();
          //          $usuario = $registro8["usuario"];
            //        $contrasena = $registro8["contrasena"];
//                    echo "Usuario: " . $usuario;
//                    echo "<br>";
//                    echo "Contraseña: " . $contrasena;
//                    echo "<br>";
//                    echo "Paquete Elegido: " . $paquetetelcel;
//                    echo "<br>";
                    
                }
            }
            
//            $xmlrpc_client = new xmlrpc_client("/X2Mmx/RPC2", "201.159.136.129", 443, 'https');
  //          $xmlrpc_client->setSSLVerifyPeer(0);
    //        $xmlrpc_client->setCredentials(trim($usuario),trim($contrasena));
            
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
                            $mensaje_enviar = 'ST600' . $mensaje_enviar;
                    }
                    break;
            }
		
	//	echo "mensaje_enviar: ".$mensaje_enviar;

            /*  */
            if ($registro6["pk_clave_tipm"] == '89') {
                /*  */
                $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
                $query12 = $conn->prepare($consulta13);
                $query12->bindParam(1, $vehiculo_actual);
                $query12->execute();
    //            $query13->closeCursor();
                
                $consulta12 = "INSERT INTO monitoreo.vehiculos_inmovilizados (noeconomico,usuario,fecha,automatico) VALUES (?,?,NOW(),1)";
                $query12 = $conn->prepare($consulta12);
                $query12->bindParam(1, $vehiculo_actual);
                $query12->bindParam(2, $user);
                $query12->execute();
                $query12->closeCursor();
            }
            if ($registro6["pk_clave_tipm"] == '88') {
                $consulta13 = "DELETE FROM monitoreo.vehiculos_inmovilizados WHERE noeconomico = ?";
                $query13 = $conn->prepare($consulta13);
                $query13->bindParam(1, $vehiculo_actual);
                $query13->execute();
                $query13->closeCursor();
            }
            
            $validarparo = 0;
            if ($registro6["pk_clave_tipm"] == '84') {
                $consulta9 = "select num_longitud_pos,num_latitud_pos,num_ignicion_pos from tb_posiciones where num_nserie_pos = ? order by fec_ultimaposicion_pos desc limit 2";
                $query9 = $conn->prepare($consulta9);
                $query9->bindParam(1, $registro5["num_serie_veh"]);
                $query9->execute();
                $latitud = 0;
                $longitud = 0;
                while ($registro9 = $query9->fetch()) {
                    $latitud = abs($latitud - $registro9['num_latitud_pos']);
                    $longitud = abs($longitud - abs($registro9['num_longitud_pos']));
		    $ignicion = $registro9['num_ignicion_pos'];
                    if (($latitud + $longitud) < .001) {
                        $validarparo = 0;
                    } else {
                        $validarparo = 1;
                    }
                }
            }
            if ($validarparo == 0) {
                $structArray = array();
                $structArray[] = new xmlrpcval(
                                array(
                                    new xmlrpcval(array(
                                        "celular" => new xmlrpcval($telefono, "string"),
                                        "texto" => new xmlrpcval($mensaje_enviar, "string"),
                                        "fecha" => new xmlrpcval("", "string")
                                            ), "struct"),
                                ), "array");
                // creacion de mensaje a enviar
                $xmlrpc_msg = new xmlrpcmsg('Tiaxa.SendMsg', $structArray);
                // envio de mensaje
                $xmlrpc_resp = $xmlrpc_client->send($xmlrpc_msg);
                //interpretar xml de regreso
                // recibe respuesta en objeto xmlrpxval
              //  $txt_respuesta =  $xmlrpc_resp->value();
		// echo "Respuesta: ".$xmlrpc_resp->value(); 
                $txt_respuesta = $xmlrpc_resp->serialize();
		echo "Respuesta: ".$txt_respuesta ;
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
                /* iNSERTA LA INFORMACION DEL COMANDO ejecutado */
                $consulta = "SELECT * FROM tb_vehiculos WHERE txt_economico_veh=?";
                $query = $conn->prepare($consulta);
                $query->bindParam(1, $_POST["vehiculo$i"]);
                $query->execute();
                $registro = $query->fetch();
                $consulta1 = "INSERT INTO tb_mensajesenviadossms 
         (fk_clave_usu,txt_economico_veh,fec_fecha_mene,num_latitud_mene,num_longitud_mene,fk_clave_tipm,txt_comentario_mene,txt_respuesta_mene)
	 VALUES (?,?,NOW(),?,?,?,?,?)";
                $query1 = $conn->prepare($consulta1);
                $query1->bindParam(1, $_SESSION["id"]);
                $query1->bindParam(2, $_POST["vehiculo$i"]);
                $query1->bindParam(3, $registro["num_latitud_veh"]);
                $query1->bindParam(4, $registro["num_longitud_veh"]);
                $query1->bindParam(5, $mensaje);
                $query1->bindParam(6, $comentario);
                $query1->bindParam(7, $txt_respuesta);
                $query1->execute();
                $concatenandoconsulta = "Usuario: " . $user . " Vehiculo: " . $_POST["vehiculo$i"] . "Mensaje: " . $mensaje . " Respuesta " . $txt_respuesta;
                echo "<p class='centrado'><strong>Económico: " . $_POST["vehiculo$i"] . ", RESPUESTA DE ENVIO DE MENSAJE " . $txt_respuesta . "</strong></p>";
            }
        }
    }
    ?>    
    <div class="container-fluid">  
        <div class="row">
            <div class="col-md-12 centrado">
                <?php if ($validarparo == 0) { ?>
                    <strong><h1>Se ha enviado el mensaje con éxito!.</h1></strong>
                <?php } else { ?>
                    <strong><h1>El comando no se ha podido enviar porque es probable que la unidad se encuentre en movimiento.</h1></strong>
                    </br>
                    <strong><h1>Solicita Ayuda a GPS ó Aplica el comando Obligar Paro de Motor.</h1></strong>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
  //  $query5->closeCursor();
 //   $query6->closeCursor();
  //  $query7->closeCursor();
  //  $query1->closeCursor();
} else {
    ?>
    <div class="container">
        <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Su usuario no tiene acceso a este módulo</strong>.
        </div>
    </div>
    <?php
}
?>