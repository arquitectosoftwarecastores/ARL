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
  $consulta = " SELECT * FROM monitoreo.tb_remolques r join monitoreo.geocercasporunidad gpu on gpu.economico = r.txt_economico_rem where fec_posicion_rem > (now() - '24:00:00'::interval) and r.estatus = 1";
  $query = $conn->prepare($consulta);
  $query->execute();
  while ($registro = $query->fetch()) {
      $economico1 = $registro["txt_economico_rem"];
      $serie = $registro["txt_nserie_rem"];
      $latitud1 = $registro["num_latitud_rem"];
      $longitud1 = $registro["num_longitud_rem"];
      $sucursal = $registro["sucursal"];
      $zona = $registro["fk_clave_zon"];
      $fronteriza1 = $registro["zonaroja"];
      $tperdida = $registro["num_icono_rem"];
      $segespecial = $registro["num_seguimiento_rem"];
      $ubicacion1 = $registro["txt_georeferencia_mun"];
      $ubicacion2 = $registro["txt_georeferencia_cas"];
      echo "\nEconomico: ".$economico1;
      $fronteriza = checazona($latitud1, $longitud1, -3, $conn);
      echo " Zona Fonteriza: ".$fronteriza;      
      $excesousa = checazona($latitud1, $longitud1, 1, $conn);
    /* alerta por estar en usa más de 3 dias*/
    if($fronteriza != 0){
    $tiempousa = "select count(*) as bandera from monitoreo.unidades_usa where fecha_ingreso < (now() - '72:00:00'::interval) and  noeconomico = ?";
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

  if($excesousa == 4340){
    echo " *** Si entra";
      $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_rem = ? and fk_clave_tipa = 203 and fec_fecha_ale > now() - interval '1440 minute' limit 1";
      $queryultale = $conn->prepare($consultaultale);
      $queryultale->bindParam(1, $economico1);
      $queryultale->execute();
      $registroultale = $queryultale->fetch();
      if ($registroultale["bandera"] == 0) {
              echo " *** Se inserto alerta de exceso de kilometraje en USA";
              $consultanp = "INSERT INTO tb_alertas(fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_rem,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale) VALUES (203,now(),?,?,?,3,?,?,?,0)";
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
// }

}
    




    
    /* alerta por estar sin movimiento */
  /*    $tiempo= "select num_latitud_pos, num_longitud_pos, fec_ultimaposicion_pos from monitoreo.tb_posiciones
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
          }
      }
    echo "<br>";*/
  
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

/*
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
  }*/
  


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
  
  }*/
  

//} while(true);
?>
