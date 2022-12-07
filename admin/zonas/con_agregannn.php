<?php
  $nombre=$_POST["nombre"];
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];
  $tipodezona=$_POST["tipodezona"];
  echo $nombre."<br>";
  echo $ciudad."<br>";
  echo $tipo."<br>";

  $consulta  = "INSERT INTO tb_zonas (txt_nombre_zon,fk_clave_mun,fk_clave_tipz) VALUES (?,?,?,NOW(),?,1)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $nombre);
  $query->bindParam(2, $ciudad);
  $query->bindParam(3, $tipo);
  $query->bindParam(4, $_SESSION['usuario']);
  $query->execute();
  $ultimoinsertado = $conn -> lastInsertId();
  $query->closeCursor();

  $consulta1  = "SELECT MAX(pk_clave_zon) as maximo FROM tb_zonas";
  $query1 = $conn->prepare($consulta1);
  $query1->execute();
  $registro1 = $query1->fetch();
  echo $registro1["maximo"]."<br>";

  $consulta2 = "INSERT INTO definirtipodezona(tipo,clave_zon) VALUES (?,?)";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $tipodezona);
  $query2->bindParam(2, $registro1["maximo"]);
  $query2->execute();
  $query2->closeCursor();
  echo $tipodezona."<br>";
  echo $ultimoinsertado."<br>";

  $consultaZon  = "SELECT * FROM tb_zonas ORDER BY pk_clave_zon DESC LIMIT 1";
  $queryZon = $conn->prepare($consultaZon);
  $queryZon->execute();
  $registroZon = $queryZon->fetch();
  $numero = $registroZon['pk_clave_zon'];
  $queryZon->closeCursor();

  // Bitacora Alta Zona
  $accion = "Alta Zona";
  $modulo = "12";
  $insertBi ="INSERT INTO bitacora_usuarios (txt_usuario_usu,txt_modificado, id_modulo,fecha, accion)
  VALUES (?,?,?,NOW(),?)";
  $querybi = $conn->prepare($insertBi);
  $querybi->bindParam(1, $_SESSION["usuario"]);
  $querybi->bindParam(2, $numero);
  $querybi->bindParam(3, $modulo);
  $querybi->bindParam(4, $accion);
  $querybi->execute();
  $querybi->closeCursor();

    if($tipo == 3) {
    include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
    include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
    $mail = new PHPMailer();
    $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
    $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
    $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
    $mail->Port = 465; // Puerto de conexión al servidor de envio.
    $mail->AddAddress("gerenteadmonunidades_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefeadmonunidades_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefeatencionopth_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefeatencionoptr_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefemonitoreoseg_lem@castores.com.mx");
    $mail->AddAddress("gerentemseg_lem@castores.com.mx");
    $mail->AddAddress("auxatencionop1_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("auxatencionop2_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("auxatencionop3_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("subgerenteoperaciones2_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefeadministrativoml_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("monitoreo_logistica_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("jefeoperativoml_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("gerenteoperaciones_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("gerenteatencionop_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->AddAddress("desarrolloti16_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
    $mail->Subject = "Alta de Zona de Riesgo"; // Este es el titulo del email.
    // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
    $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado una Alta de una nueva zona de riesgo denominada: " . $nombre;
    $mail->Body = $body; // Mensaje a enviar.
    $exito = $mail->Send(); // Envía el correo.
    if ($exito) {
        echo "El correo fue enviado correctamente.";
    } else {
        echo "Hubo un problema. Contacta a un administrador.";
    }
}


  $redireccionar="?seccion=zonas&accion=mapa&id=".$registro1["maximo"];
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>
