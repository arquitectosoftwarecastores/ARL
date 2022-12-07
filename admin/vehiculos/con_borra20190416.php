<?php
// exit();
// $camion="";
// $user = $_SESSION["usuario"]; 
    include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
    include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
    $mail = new PHPMailer();
  //  $mail->Host = "smtp2.castores.com.mx"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  //  $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  //  $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  //  $mail->Port = 2525; // Puerto de conexión al servidor de envio.
    $mail->AddAddress("gerenteinnovacionti_lem@castores.com.mx");
    $mail->AddAddress("marcosanchezd@gmail.com");	
 // Esta es la dirección a donde enviamos
    $mail->Subject = "Baja de Vehiculo"; // Este es el titulo del email.
    // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
    $body = "YYYYYYPor este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado una baja de un vehiculo";
    $mail->Body = $body; // Mensaje a enviar.
    $exito = $mail->Send(); // Envía el correo.
    if ($exito) {
        echo "El correo fue enviado correctamente.";
    } else {
        echo "Hubo un problema. Contacta a un administrador.";
    }
?>
