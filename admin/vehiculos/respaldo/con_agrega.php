<?php
  if(isset($_SESSION["altaybajadevehiculos"]))
  {

  $numero=$_POST["numero"];

  $consulta1  = " SELECT * FROM tb_vehiculos WHERE txt_economico_veh=?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $numero);
  $query1->execute();
  $cuenta=0;
  while($registro1 = $query1->fetch())
    $cuenta++;

  if($cuenta)
  {

?>

    <div class="container">
       <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>El número económico <?php echo $numero ?>ya ha sido registrado previamente.</strong>.
        </div>
    </div>

<?php
   exit();
  }

  $serie=$_POST["serie"];
  $circuito=$_POST["circuito"];
  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //En la linea de abajo recibimos el tipo de camion
  $tipocamion=$_POST["tipocamion"];
  $especial=0;
  if(isset($_POST["especial"]))
    if($_POST["especial"]==1)
       $especial=1;
/*
  $consulta  = "INSERT INTO tb_vehiculos
				(txt_economico_veh,num_serie_veh,fk_clave_cir,num_seguimientoespecial_veh,num_latitud_veh,num_longitud_veh,num_zonariesgo_veh,txt_tperdida_veh,fec_posicion_veh,tipo,status,fecha_mod,usuario_mod)
		        VALUES (?,?,?,?,0,0,0,'',NOW(),?,1,NOW(),?)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $numero);
  $query->bindParam(2, $serie);
  $query->bindParam(3, $circuito);
  $query->bindParam(4, $especial);
  $query->bindParam(5, $tipocamion);
  $query->bindParam(6, $_SESSION["usuario"]);
  $query->execute();

  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //La consulta de abajo es para que inserte en la tabla informacion_veh los campos de tipo de camion por default el status = 1
  //$consulta2  = "INSERT INTO informacion_veh (txt_numero_veh,idtipounidad,status) VALUES (?,?,1)";
  //$query2 = $conn->prepare($consulta2);
  //$query2->bindParam(1, $numero);
  //$query2->bindParam(2, $tipocamion);
  //$query2->execute();

  $query->closeCursor();

  // Bitacora Alta Usuario
  $accion = "Alta Vehiculo";
  $modulo = "8";
  $insertBi ="INSERT INTO bitacora_usuarios (txt_usuario_usu,txt_modificado, id_modulo,fecha, accion)
  VALUES (?,?,?,NOW(),?)";
  $querybi = $conn->prepare($insertBi);
  $querybi->bindParam(1, $_SESSION["usuario"]);
  $querybi->bindParam(2, $numero);
  $querybi->bindParam(3, $modulo);
  $querybi->bindParam(4, $accion);
  $querybi->execute();
  $querybi->closeCursor();

*/
  if ($tipocamion == '1') {
    $tipo = 'Trailer';
  }elseif ($tipocamion == '2') {
    $tipo = 'Torton';
  }else {
    $tipo = 'Otro';
  }

  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail = new PHPMailer();
  $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 465; // Puerto de conexión al servidor de envio.
  /*
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
  */
  $mail->AddAddress("desarrolloti16_lem@castores.com.mx"); // Esta es la dirección a donde enviamos
  $mail->Subject = "Alta Vehículo"; // Este es el titulo del email.
  // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
  $body = "Por este medio Transportes Castores de Baja California S.A. de C.V.  Le informa que se ha Generado una alta de un vehículo:\n Número ecónomico: ".$numero."
  \n No. de serie: ".$serie  ."\n Tipo de vehículo: ".$tipo."\nRealizado por: ".$_SESSION["usuario"]." - " $_SESSION['nombre'];
  $mail->Body = $body; // Mensaje a enviar.
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
      echo "El correo fue enviado correctamente.";
  } else {
      echo "Hubo un problema. Contacta a un administrador.";
  }


  //$redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>

<?php
  }
  else
  {
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
