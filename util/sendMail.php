<?php
include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");

/**
 * @param array $configuracionCorreo datos de configuracion para el envio de correo, ejemplo de estructura del array: array("host" => "0.0.0.0", "userName" => "", "password" => "", "port" => 0)
 * @param String $asunto Asunto del correo
 * @param String $mensaje mensaje del correo
 * @param array $enviarA Correos destinatarios
 * @param array $conCopia Correos con copia
 */
function sendMail($configuracionCorreo, $asunto, $mensaje, $enviarA, $conCopia = array())
{

    $mail = new PHPMailer();
    $mail->Host = $configuracionCorreo["host"]; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
    $mail->Username = $configuracionCorreo["userName"]; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
    $mail->Password = $configuracionCorreo["password"]; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
    $mail->Port = $configuracionCorreo["port"]; // Puerto de conexión al servidor de envio.
    for ($i = 0; $i < count($enviarA); $i++) {
        if($enviarA[$i] != ''){
            $mail->AddAddress($enviarA[$i]);
        }
    }
    for ($i = 0; $i < count($conCopia); $i++) {
        if($conCopia[$i] != ''){
            $mail->AddCC($conCopia[$i]);
        }
    }
    $mail->CharSet = "UTF-8";
    $mail->Subject = $asunto; // Este es el titulo del email.
    $mail->Body = $mensaje; // Mensaje a enviar.
    $exito = $mail->Send(); // Envía el correo.
    if ($exito) {
        echo "El correo fue enviado correctamente.";
    } else {
        echo "Hubo un problema. Contacta a un administrador.";
    }
}
