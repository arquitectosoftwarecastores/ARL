<?php
echo "Tabla: " . $Tabla;
$id = $_GET["id"];
echo "id: " . $id;

if ($Tabla == "tb_zonas") {
    $definirzona = " SELECT * FROM monitoreo.tb_zonas WHERE pk_clave_zon =" . $id;
    $queryzona = $conn->prepare($definirzona);
    $queryzona->execute();
    $registrozona = $queryzona->fetch();
    $nombrezona = $registrozona['txt_nombre_zon'];
    $tipozona = $registrozona['fk_clave_tipz'];
}


$consulta = " DELETE FROM " . $Tabla . " WHERE " . $campoId . "=?";
$query = $conn->prepare($consulta);
$query->bindParam(1, $id);

$query->execute();

if ($Tabla == "tb_zonas") {
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
        $mail->Subject = "Eliminacion de Zona de Riesgo"; // Este es el titulo del email.
        $link = "http://69.172.241.230/historico/for_mapa1.php?id=" . $id;
        $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Eliminado la zona de riesgo denominada: " . $nombrezona;
        $mail->Body = $body; // Mensaje a enviar. 
        $exito = $mail->Send(); // Envía el correo.
        if ($exito) {
            echo "El correo fue enviado correctamente.";
        } else {
            echo "Hubo un problema. Contacta a un administrador.";
        }
    }
}

$redireccionar = "?seccion=" . $seccion . "&accion=lista";

if (isset($_GET["rxp"]))
    $redireccionar.="&rxp=" . $_GET["rxp"];
if (isset($_GET["orden"]))
    $redireccionar.="&orden=" . $_GET["orden"];
if (isset($_GET["busca"]))
    $redireccionar.="&busca=" . $_GET["busca"];
if (isset($_GET["inicia"]))
    $redireccionar.="&inicia=" . $_GET["inicia"];
?>
<script>
    window.location.href = "<?php echo $redireccionar; ?>";
</script>