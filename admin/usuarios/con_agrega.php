<?php

  $usuario=$_POST["usuario"];
  $contrasena=$_POST["password"];
  $nombre=$_POST["nombre"];
  $correo=$_POST["correo"];
  
  if(isset($_POST["activo"]))
      $activo=1;
  else
      $activo=0;

  if(isset($_POST["acceso_externo"]))
      $acceso=1;
  else
      $acceso=0;

  if(isset($_POST["maestro"]))
      $maestro=1;
  else
      $maestro=0;

  $rol = $_POST["rol"];

  // Consulta numero o nombre de usuario
  $seUsuario = "SELECT * FROM tb_usuarios WHERE pk_clave_usu = ?";
  $sequery = $conn->prepare($seUsuario);
  $sequery->bindParam(1, $_SESSION['id']);
  $sequery->execute();
  $seregistro = $sequery->fetch();
  $sesionusuario = $seregistro["txt_usuario_usu"];
  $sequery->closeCursor();

  // Crea Usuario
  $consulta  = "INSERT INTO tb_usuarios (txt_usuario_usu,txt_contrasena_usu,txt_nombre_usu,txt_email_usu,num_activo_usu,fk_clave_rol,fecha_contrasena,fecha_mod,usuarioalta,status,acceso_externo,maestro)
                      VALUES (?,?,?,?,?,?,NOW(),NOW(),?,1,?,?)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $usuario);
  $query->bindParam(2, $contrasena);
  $query->bindParam(3, $nombre);
  $query->bindParam(4, $correo);
  $query->bindParam(5, $activo);
  $query->bindParam(6, $rol);
  $query->bindParam(7, $sesionusuario);
  $query->bindParam(8, $acceso);
  $query->bindParam(9, $maestro);
  $query->execute();
  $query->closeCursor();

  // Bitacora Usuario
  $accion = "Alta Usuario";
  $modulo = "1";
  $insertBi ="INSERT INTO bitacora_usuarios (txt_usuario_usu,txt_modificado, id_modulo,fecha, accion)
  VALUES (?,?,?,NOW(),?)";
  $querybi = $conn->prepare($insertBi);
  $querybi->bindParam(1, $sesionusuario);
  $querybi->bindParam(2, $usuario);
  $querybi->bindParam(3, $modulo);
  $querybi->bindParam(4, $accion);
  $querybi->execute();
  $querybi->closeCursor();


  $consulta1  = " SELECT * FROM  tb_usuarios WHERE txt_usuario_usu=?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $usuario);
  $query1->execute();
  $registro1 = $query1->fetch();
  $usuario=$registro1["pk_clave_usu"];

  $circuitos=$_POST["circuitos"];
  for($i=0; $i<sizeof($circuitos); $i++)
  {
    $consulta2  = " INSERT INTO tb_circuitosxusuario
                        (fk_clave_cir,fk_clave_usu)
                        VALUES(?,?)";
    $query2 = $conn->prepare($consulta2);
    $query2->bindParam(1, $circuitos[$i]);
    $query2->bindParam(2, $usuario);
    $query2->execute();
  }

/*
  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail = new PHPMailer();
  $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 465; // Puerto de conexión al servidor de envio.
  $mail->AddAddress("gerentemseg_lem@castores.com.mx");
  $mail->AddAddress("jefemonitoreoseg_lem@castores.com.mx");
  $mail->AddAddress("jefeadministrativoml_lem@castores.com.mx");
  $mail->AddAddress("jefeoperativoml_lem@castores.com.mx");
  $mail->AddAddress("desarrolloti11_lem@castores.com.mx"); 
  $mail->AddAddress("liderinnovacionti_lem@castores.com.mx");
  $mail->Subject = "Alta de Usuario"; // Este es el titulo del email.
  // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
  $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado una Alta de un nuevo usuario: ". $usuario." - ".$nombre .".  Registrado por ". $_SESSION["id"] ." - ".$_SESSION['usuario'];
  $mail->Body = $body; // Mensaje a enviar.
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
      echo "El correo fue enviado correctamente.";
  } else {
      echo "Hubo un problema. Contacta a un administrador.";
  }
*/

  $seccion = $_GET["seccion"];
  $redireccionar="?seccion=".$seccion."&accion=lista";
  if (isset($_GET["rxp"]))
      $redireccionar .= "&rxp=".$_GET["rxp"];

?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>
