<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
//include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");
$c = 0;
//do {
  $consulta = " SELECT * FROM monitoreo.tb_remolques r join monitoreo.geocercasporunidad gpu on gpu.economico = r.txt_economico_rem where fec_posicion_rem > (now() - '24:00:00'::interval) and r.estatus = 1 -- and r.txt_economico_rem = '11544'";
  $query = $conn->prepare($consulta);
  $query->execute();
  while ($registro = $query->fetch()) {
      $economico1 = $registro["txt_economico_rem"];
      $serie = $registro["txt_nserie_rem"];
      $latitud1 = $registro["num_latitud_rem"];
      $longitud1 = $registro["num_longitud_rem"];
      $sucursal1 = $registro["sucursal"];
      $zona = $registro["fk_clave_zon"];
      $fronteriza1 = $registro["zonaroja"];
      $tperdida = $registro["num_icono_rem"];
      $segespecial = $registro["num_seguimiento_rem"];
      $ubicacion1 = $registro["txt_georeferencia_mun"];
      $ubicacion2 = $registro["txt_georeferencia_cas"];
      echo "\nEconomico: ".$economico1;
      $fronteriza = checazona($latitud1, $longitud1, -3, $conn);
      echo " Zona Fonteriza: ".$fronteriza;
      $sucursal = checazona($latitud1,$longitud1,2,$conn);	
      $alejada = checazona($latitud1,$longitud1,1,$conn);

    if($alejada == 4340){
      $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_rem = ? and fk_clave_tipa = 203 and fec_fecha_ale > now() - interval '1440 minute' limit 1";
      $queryultale = $conn->prepare($consultaultale);
      $queryultale->bindParam(1, $economico1);
      $queryultale->execute();
      $registroultale = $queryultale->fetch();
      if ($registroultale["bandera"] == 0) {
              echo " *** Se inserto alerta de exceso de dias en USA";
              $consultanp = "INSERT INTO tb_alertas(fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale) VALUES (210,now(),?,?,?,3,?,?,?,0)";
              echo $consultanp;
              $querynp = $conn->prepare($consultanp);
              $querynp->bindParam(1, $ubicacion1);
              $querynp->bindParam(2, $economico1);
              $querynp->bindParam(3, $ignicion1);
              $querynp->bindParam(4, $latitud1);
              $querynp->bindParam(5, $longitud1);
              $querynp->bindParam(6, $ubicacion2);
              $querynp->execute();
              $querynp->closeCursor();
          }
          $queryultale->closeCursor();
      }

      /* Alerta por llegada de remolque para mantenimiento */
      if ($sucursal1 !== $sucursal) {
        echo " Actualiza Sucursal *********************************" . $sucursal;
        echo "<br> \n";
        $inserta_sucursal = "UPDATE monitoreo.geocercasporunidad SET sucursal = ? where economico = ?";
        $query2 = $conn->prepare($inserta_sucursal);
        $query2->bindParam(2, $economico1);
        $query2->bindParam(1, $sucursal);
        $query2->execute();
        $query2->closeCursor();
	}

        if($sucursal == 535){

        $desfasados = "select count(*) as bandera from monitoreo.tb_mantenimientos where fecha_sigmtto < now() and economico_rem = ?";
        $querytdesfasados = $conn->prepare($desfasados);
        $querytdesfasados->bindParam(1, $economico1);
        $querytdesfasados->execute();
        $registrodesfasados = $querytdesfasados->fetch();     
        
        if (($registrodesfasados["bandera"]) == 1) {
          $consultaultale = "select count(*) as bandera from tb_mantenimientos_remolques where noeconomico = ? ";
          $queryultale = $conn->prepare($consultaultale);
          $queryultale->bindParam(1, $economico1);
          $queryultale->execute();
          $registroultale = $queryultale->fetch();
          if ($registroultale["bandera"] == 0) {
                  echo " *** Se inserta unidad a correos por mantenimiento";
                  $consultanp = "INSERT INTO tb_mantenimientos_remolques(noeconomico) VALUES (?)";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $economico1);
                  $querynp->execute();
                  $querynp->closeCursor();

                  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
                  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
                  $mail = new PHPMailer();
                  $mail->Host = "10.3.1.181"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
                  $mail->Username = "gerentemseg_lem@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
                  $mail->Password = "JR505nCOvf"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
                  $mail->Port = 500; // Puerto de conexión al servidor de envio.
                  $mail->AddAddress("gerenteinnovacionti_lem@castores.com.mx");
                  $mail->AddAddress("jefenalintercambio_lem@castores.com.mx");
                  $mail->AddAddress("coordintercambioremolques_lem@castores.com.mx");
                  $mail->AddAddress("monitoreoremolques_lem@castores.com.mx");
                  $mail->AddAddress("gerentedeflotaremolques_lem@castores.com.mx");	
                  $mail->Subject = "Vehículo en Corporativo para mantenimiento"; // Este es el titulo del email.
                  $body = "Por medio del presente correo se le notifica que el remolque con número económico ".$economico1." se encuentra con plazo de mantenimiento vencido y actualmente se encuentra en corporativo";
                  $mail->Body = $body; // Mensaje a enviar.
                  $exito = $mail->Send(); // Envía el correo.
                  if ($exito) {
                      echo "El correo fue enviado correctamente.";
                  } else {
                      echo "Hubo un problema. Contacta a un administrador.";
                  }  


              }
              $queryultale->closeCursor();
          }
      }else{
        echo " *** Se elimina unidad a correos por mantenimiento";
                  $consultanp = "delete from tb_mantenimientos_remolques where noeconomico = ?";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $economico1);
                  $querynp->execute();
                  $querynp->closeCursor();
      
    }      
  
    /* alerta por estar en usa más de 10 dias*/
    if($fronteriza != 0){
    $tiempousa = "select count(*) as bandera from monitoreo.unidades_usa where fecha_ingreso < (now() - '240:00:00'::interval) and  noeconomico = ?";
    $querytusa = $conn->prepare($tiempousa);
    $querytusa->bindParam(1, $economico1);
    $querytusa->execute();
    $registrotusa = $querytusa->fetch();
    if (($registrotusa["bandera"]) == 1) {
      $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_rem = ? and fk_clave_tipa = 210 and fec_fecha_ale > now() - interval '1440 minute' limit 1";
      $queryultale = $conn->prepare($consultaultale);
      $queryultale->bindParam(1, $economico1);
      $queryultale->execute();
      $registroultale = $queryultale->fetch();
      if ($registroultale["bandera"] == 0) {
              echo " *** Se inserto alerta de exceso de dias en USA";
              $consultanp = "INSERT INTO tb_alertas(fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale) VALUES (210,now(),?,?,?,3,?,?,?,0)";
              echo $consultanp;
              $querynp = $conn->prepare($consultanp);
              $querynp->bindParam(1, $ubicacion1);
              $querynp->bindParam(2, $economico1);
              $querynp->bindParam(3, $ignicion1);
              $querynp->bindParam(4, $latitud1);
              $querynp->bindParam(5, $longitud1);
              $querynp->bindParam(6, $ubicacion2);
              $querynp->execute();
              $querynp->closeCursor();
          }
          $queryultale->closeCursor();
      }
      $querytusa->closeCursor();
  }

    
    
    /* alerta por estar sin movimiento */
      $tiempo= "select num_latitud_pos, num_longitud_pos, fec_ultimaposicion_pos from monitoreo.tb_posiciones
                 where txt_nserie_pos = ? and fec_ultimaposicion_pos < (now() - '168:00:00'::interval)
                 limit 1";
      $queryt = $conn->prepare($tiempo);
      $queryt->bindParam(1, $serie);
      $queryt->execute();
      $registrot = $queryt->fetch();
      if(isset($registrot['num_latitud_pos'])){
        $latitud_ant = $registrot['num_latitud_pos'];
        $longitud_ant = $registrot['num_longitud_pos'];
        $fecha_ant = $registrot['fec_ultimaposicion_pos'];
        if(abs($latitud1 - $latitud_ant)+abs($longitud1 - $longitud_ant)<0.0001 && $latitud1 != 0 and $longitud_ant != 0){
        echo " *** Se inserto alerta de sin Movimiento *** ";
        $consultanp = " INSERT INTO tb_alertas
                               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                               VALUES (202,now(),?,?,?,3,?,?,?,0)";
        $querynp = $conn->prepare($consultanp);
        $querynp->bindParam(1, $ubicacion1);
        $querynp->bindParam(2, $economico1);
        $querynp->bindParam(3, $ignicion1);
        $querynp->bindParam(4, $latitud1);
        $querynp->bindParam(5, $longitud1);
        $querynp->bindParam(6, $ubicacion2);
        $querynp->execute();
        $querynp->closeCursor();
        }
        /*
        if($latitud1>34){
          echo " *** Se inserto alerta de Exceso de Kilometraje*** ";
          $consultanp = " INSERT INTO tb_alertas
                                 (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                 VALUES (203,now(),?,?,?,3,?,?,?,0)";
          $querynp = $conn->prepare($consultanp);
          $querynp->bindParam(1, $ubicacion1);
          $querynp->bindParam(2, $economico1);
          $querynp->bindParam(3, $ignicion1);
          $querynp->bindParam(4, $latitud1);
          $querynp->bindParam(5, $longitud1);
          $querynp->bindParam(6, $ubicacion2);
          $querynp->execute();
          $querynp->closeCursor();
          }*/
      }
    echo "<br>";
  
  /*
  if(($fronteriza!=0){
      $consultasinpos = "select count(*) as bandera from monitoreo.vw_unidades_no_reportando_riesgo where economico = ? and economico not like '00%' limit 1";
      $querysinpos = $conn->prepare($consultasinpos);
      $querysinpos->bindParam(1, $economico1);
      $querysinpos->execute();
      $registrosinpos = $querysinpos->fetch();
      if (($registrosinpos["bandera"]) == 1) {
          $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 201 and fec_fecha_ale > now() - interval '35 minute' limit 1";
          $queryultale = $conn->prepare($consultaultale);
          $queryultale->bindParam(1, $economico1);
          $queryultale->execute();
          $registroultale = $queryultale->fetch();
          if ($registroultale["bandera"] == 0) {
              $consultaunicon = "select count(*) as bandera from monitoreo.unidades_sin_posicionar where txt_economico_veh = ? and fecha_registro > now() - interval '720 minute' limit 1";
              $queryunicon = $conn->prepare($consultaunicon);
              $queryunicon->bindParam(1, $economico1);
              $queryunicon->execute();
              $registrounicon = $queryunicon->fetch();
              if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de sin posicionar";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (201,now(),?,?,?,3,?,?,?,0)";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $ubicacion1);
                  $querynp->bindParam(2, $economico1);
                  $querynp->bindParam(3, $ignicion1);
                  $querynp->bindParam(4, $latitud1);
                  $querynp->bindParam(5, $longitud1);
                  $querynp->bindParam(6, $ubicacion2);
                  $querynp->execute();
                  $querynp->closeCursor();
              }
              $queryunicon->closeCursor();
          }
          $queryultale->closeCursor();
      }
      $querysinpos->closeCursor();
  }
  */


  if ($fronteriza1 !== $fronteriza) {
    echo " Actualiza frontera *********************************" . $fronteriza;
    echo "<br> \n";
    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET zonaroja = ? where economico = ?";
    $query2 = $conn->prepare($inserta_geocerca);
    $query2->bindParam(2, $economico1);
    $query2->bindParam(1, $fronteriza);
    $query2->execute();
    $query2->closeCursor();
  }
  
  if($fronteriza == 4292){

        $consultaang = "select count(*) as bandera from tb_alertas where txt_economico_rem = ? and fk_clave_tipa = 201 and fec_fecha_ale > now() - interval '1440 minute' limit 1";
          $queryultaang = $conn->prepare($consultaang);
          $queryultaang -> bindParam(1, $economico1);
          $queryultaang -> execute();
    $registrounicon = $queryultaang ->fetch();
     if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de Zona Fronteriza *** ";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (201,now(),?,?,?,3,?,?,?,0)";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $ubicacion1);
                  $querynp->bindParam(2, $economico1);
                  $querynp->bindParam(3, $ignicion1);
                  $querynp->bindParam(4, $latitud1);
                  $querynp->bindParam(5, $longitud1);
                  $querynp->bindParam(6, $ubicacion2);
                  $querynp->execute();
                  $querynp->closeCursor();
              }
    $queryultaang -> closeCursor();
  }
  


  /*
  $consultadesvio = "select count(*) as bandera from monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad g on g.economico=v.txt_economico_veh where g.desvio <> 0 and v.txt_economico_veh = ? and v.txt_economico_veh not like '00%'";
      $querydesvio = $conn->prepare($consultadesvio);
      $querydesvio->bindParam(1, $economico1);
      $querydesvio->execute();
      $registrosdesvio = $querydesvio->fetch();
      if (($registrosdesvio["bandera"]) == 1) {
          $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 203 and fec_fecha_ale > now() - interval '15 minute' limit 1";
          $queryultale = $conn->prepare($consultaultale);
          $queryultale->bindParam(1, $economico1);
          $queryultale->execute();
          $registroultale = $queryultale->fetch();
          if ($registroultale["bandera"] == 0) {
              $consultaunicon = "select count(*) as bandera from monitoreo.unidades_sin_posicionar where txt_economico_veh = ? and fecha_registro > now() - interval '60 minute' limit 1";
              $queryunicon = $conn->prepare($consultaunicon);
              $queryunicon->bindParam(1, $economico1);
              $queryunicon->execute();
              $registrounicon = $queryunicon->fetch();
              if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de desvio de ruta";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (203,now(),?,?,?,3,?,?,?,0)";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $ubicacion1);
                  $querynp->bindParam(2, $economico1);
                  $querynp->bindParam(3, $ignicion1);
                  $querynp->bindParam(4, $latitud1);
                  $querynp->bindParam(5, $longitud1);
                  $querynp->bindParam(6, $ubicacion2);
                  $querynp->execute();
                  $querynp->closeCursor();
              }
             $queryunicon->closeCursor();
          }
        $queryultale->closeCursor();
      }
      $querydesvio->closeCursor();
  echo "<br>";
  */
  }
  


  /* Inserta Fecha de Ejecución 
      $actualiza_cron_historico = "insert into monitoreo.tb_estatus_crones_historico(id_cron,fecha_registro) values (2,now())";
      $query3 = $conn->prepare($actualiza_cron_historico);
      $query3->execute();
      $query3->closeCursor(); 
      $actualiza_cron = "update monitoreo.tb_estatus_crones set ultimo_registro=now() where id_cron=2";
      $query4 = $conn->prepare($actualiza_cron);
      $query4->execute();
      $query4->closeCursor();
      $query->closeCursor();
      $c = 0;*/
//} while(true);
?>
