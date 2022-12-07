<?php

  $consulta2  = "SELECT * FROM tb_usuarios WHERE pk_clave_usu = ?";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $_GET["id"]);
  $query2->execute();
  $registro = $query2->fetch();
  $oldUsuario = $registro["txt_usuario_usu"];
  $oldNombre = $registro["txt_nombre_usu"];
  $query2->closeCursor();

    /* Eliminar circuitos por Usuario */
  $consulta1  = " DELETE FROM tb_circuitosxusuario
                  WHERE fk_clave_usu=?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $_GET["id"]);
  $query1->execute();

  $consulta1  = " DELETE FROM tb_usuarios WHERE pk_clave_usu = ?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $_GET["id"]);
//  $id=$_GET["id"];
  $query1->execute();
  $query1->closeCursor();

  $accion = "Elimino Usuario";
  $modulo = "1";
  $insertBi ="INSERT INTO bitacora_usuarios (txt_usuario_usu, txt_modificado, id_modulo, fecha, accion) VALUES (?,?,?,NOW(),?)";
  $query = $conn->prepare($insertBi);
  $query->bindParam(1, $_SESSION["usuario"]);
  $query->bindParam(2, $oldUsuario);
  $query->bindParam(3, $modulo);
  $query->bindParam(4, $accion);
  $query->execute();
  $query->closeCursor();

  /*
  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail = new PHPMailer();
  $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 465; // Puerto de conexión al servidor de envio.
  $mail->AddAddress("gerenteinnovacionti_lem@castores.com.mx");
//  $mail->AddAddress("jefemonitoreoseg_lem@castores.com.mx");
//  $mail->AddAddress("jefeadministrativoml_lem@castores.com.mx");
//  $mail->AddAddress("jefeoperativoml_lem@castores.com.mx");
//  $mail->AddAddress("liderinnovacionti_lem@castores.com.mx"); 
//  $mail->AddAddress("desarrolloti11_lem@castores.com.mx");
  $mail->Subject = "Baja de Usuario"; // Este es el titulo del email.
  // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
  $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado la baja del usuario: ".$oldUsuario." - ".$oldNombre.". del sistema ARL realizado por: ". $_SESSION['usuario']." - ".$_SESSION['nombre'];
  $mail->Body = $body; // Mensaje a enviar.
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
      echo "El correo fue enviado correctamente.";
  } else {
      echo "Hubo un problema. Contacta a un administrador.";
  }
  */

  $redireccionar="?seccion=".$seccion."&accion=lista";

  if(isset($_GET["rxp"]))
  	$redireccionar.="&rxp=".$_GET["rxp"];
  if(isset($_GET["orden"]))
  	$redireccionar.="&orden=".$_GET["orden"];
  if(isset($_GET["busca"]))
  	$redireccionar.="&busca=".$_GET["busca"];
  if(isset($_GET["inicia"]))
    $redireccionar.="&inicia=".$_GET["inicia"];

?>
<script>
   window.location.href = "<?php echo  $redireccionar; ?>";
</script>
