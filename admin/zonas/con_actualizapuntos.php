﻿<?php
$id = $_GET["id"];

$user = $_SESSION["usuario"];
$latitud = $_POST["latitud"];
$longitud = $_POST["longitud"];
$nombrecorto = "Modificar Zona";
$tabla = "tb_zonas";
$cambio = "prueba";


//echo $cambio;
//echo date('Y-m-d H:i:s');
//echo $nombrecorto;
//echo $tabla;
//echo $id;

$definirzona = " SELECT * FROM monitoreo.tb_zonas WHERE pk_clave_zon =" . $id;
$queryzona = $conn->prepare($definirzona);
$queryzona->execute();
$registrozona = $queryzona->fetch();
$nombrezona = $registrozona['txt_nombre_zon'];
$tipozona = $registrozona['fk_clave_tipz'];

$cambio = "INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto) VALUES (?,?,?,?)";
$query3 = $conn->prepare($cambio);
$query3->bindParam(1, $user);
$query3->bindParam(2, $id);
$query3->bindParam(3, date('Y-m-d H:i:s'));
$query3->bindParam(4, $nombrecorto);
//    $query3->bindParam(5, $tabla);
//    $query3->bindParam(6, $id);
$query3->execute();

if (isset($_POST["puntos"])) {
    $consulta = "DELETE FROM tb_detallezonas WHERE fk_clave_zon=? ";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $query->execute();
    $puntos = $_POST["puntos"];
    for ($x = 1; $x <= $puntos; $x++) {
        $coordenadas = explode(",", $_POST["latlong$x"]);
        $consulta1 = "INSERT INTO tb_detallezonas (fk_clave_zon,num_latitud_zon,num_longitud_zon) VALUES (?,?,?)";
        $query1 = $conn->prepare($consulta1);
        $query1->bindParam(1, $id);
        $query1->bindParam(2, $coordenadas[0]);
        $query1->bindParam(3, $coordenadas[1]);
        $query1->execute();
    }
}
$consulta2 = "UPDATE tb_zonas SET num_latitudcen_zon=?, num_longitudcen_zon=? WHERE pk_clave_zon=?";
$query2 = $conn->prepare($consulta2);
$query2->bindParam(1, $latitud);
$query2->bindParam(2, $longitud);
$query2->bindParam(3, $id);
$query2->execute();

/* Email */

if ($tipozona == 3) {
    include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
    include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
    $mail = new PHPMailer();
    $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
    $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente. 
    $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
    $mail->Port = 465; // Puerto de conexión al servidor de envio. 
    $mail->AddAddress("gerenteadmonunidades_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos   
    $mail->AddAddress("jefeadmonunidades_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos   
    $mail->AddAddress("jefeatencionopth_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos   
    $mail->AddAddress("jefeatencionoptr_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("auxatencionop1_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("auxatencionop2_lem@correo.castores.com.mxx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("auxatencionop3_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("subgerenteoperaciones2_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("jefeadministrativoml_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("monitoreo_logistica_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("jefeoperativoml_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("gerenteoperaciones_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("gerenteatencionop_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("desarrolloti16_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->AddAddress("desarrolloti11_lem@correo.castores.com.mx"); // Esta es la dirección a donde enviamos 
    $mail->Subject = "Modificacion en Zona de Riesgo"; // Este es el titulo del email.
    $link = "http://69.172.241.230/historico/for_mapa1.php?id=" . $id;
    $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado una Modificacion en la zona de riesgo denominada: " . $nombrezona
            . "\nPara visualizar el trazado actual de la Zona de Riesgo da click en el siguiente enlace " . $link . " \n\n   Saludos Cordiales";
    $mail->Body = $body; // Mensaje a enviar. 
    $exito = $mail->Send(); // Envía el correo.
    if ($exito) {
        echo "El correo fue enviado correctamente.";
    } else {
        echo "Hubo un problema. Contacta a un administrador.";
    }
}
$redireccionar = "?seccion=" . $seccion . "&accion=lista";
?>
<script>
    window.location.href = "<?php echo $redireccionar; ?>";
</script>