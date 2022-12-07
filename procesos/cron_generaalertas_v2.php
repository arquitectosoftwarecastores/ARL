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
do {
echo "Iniciando...";
echo "<br>";

$conCadenas = "SELECT  COUNT(*) AS nocad FROM  avl_cadenas_g WHERE  cad_estatus = 1";
$queryCad = $conn->prepare($conCadenas);
$queryCad->execute();
$cadenas = $queryCad->fetch();

if ($cadenas["nocad"] <= 10000) {
  
  $consulta = " SELECT * FROM monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh where v.status = 1";
  $query = $conn->prepare($consulta);
  $query->execute();
  while ($registro = $query->fetch()) {
      $economico1 = $registro["txt_economico_veh"];
      $latitud1 = $registro["num_latitud_veh"];
      $longitud1 = $registro["num_longitud_veh"];
      $sucursal = $registro["sucursal"];
      $zona = $registro["fk_clave_zon"];
      
      $zonaroja1 = $registro["zonaroja"];
      $desvio1 = $registro["desvio"];
      $riesgo1 = $registro["riesgo"];

      $tperdida = $registro["txt_tperdida_veh"];
      $segespecial = $registro["num_seguimientoespecial_veh"];
      echo "\nEconomico: ".$economico1;
   //   echo  " Tperdida".$tperdida;
      $zonaroja = checazona($latitud1, $longitud1, -9, $conn);
      echo ", Zona Roja: ".$zonaroja; 
      $riesgo = checazona($latitud1, $longitud1, 3, $conn);
      echo ", Riesgo: ".$riesgo;
      $desvio = checazona($latitud1, $longitud1, -2, $conn);
      echo " Desvio: ".$desvio;
      echo " Sucursal: ".$sucursal;
      echo " Zona: ".$zona;
      
      if ($zonaroja1 != $zonaroja OR $desvio1 != $desvio OR $riesgo1 != $riesgo) {
        echo "Actualiza ZonaRoja, Desvio y Riesgo";
        $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET riesgo = ?, desvio = ?, zonaroja = ?  where economico = ?";
        $query2 = $conn->prepare($inserta_geocerca);
        $query2->bindParam(4, $economico1);
        $query2->bindParam(3, $zonaroja);
        $query2->bindParam(2, $desvio);
        $query2->bindParam(1, $riesgo);
        $query2->execute();
        $query2->closeCursor();
        $ubicacion1 = $registro["txt_posicion_veh"];
        $ignicion1 = $registro["num_ignicion_veh"];
        $ubicacion2 = $registro["txt_upsmart_veh"];
      }
  
      $tiempo= "SELECT num_seguimientoespecial_veh,txt_economico_veh,((DATE_PART('day', now()::timestamp - fec_posicion_veh::timestamp) * 24 + DATE_PART('hour', now()::timestamp - fec_posicion_veh::timestamp)) * 60 +
                 DATE_PART('minute', now()::timestamp - fec_posicion_veh::timestamp)) AS tiempo FROM monitoreo.tb_vehiculos v
      left join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh where txt_economico_veh= ?";
      $queryt = $conn->prepare($tiempo);
      $queryt->bindParam(1, $economico1);
      $queryt->execute();
      $registrot = $queryt->fetch();
    //  echo " Tiempo".$registrot["tiempo"];
      if(($registrot["tiempo"])<=35){
      //  echo " ****Tiempo*****". $registrot["tiempo"];
       // $actualizar="update monitoreo.tb_vehiculos set txt_tperdida_veh = '' where txt_economico_veh= ?";
    if($registrot["num_seguimientoespecial_veh"]!=1){
        $actualizar="update monitoreo.tb_vehiculos set txt_tperdida_veh = '', num_seguimientoespecial_veh = 0 where txt_economico_veh= ?";
          }else{
        $actualizar="update monitoreo.tb_vehiculos set txt_tperdida_veh = '' where txt_economico_veh= ?";
          }
        $querya = $conn->prepare($actualizar);
        $querya->bindParam(1, $economico1);
        $querya->execute();
        $querya->closeCursor();
          }else if(($registrot["tiempo"])>35){
          $actualizar="update monitoreo.tb_vehiculos set txt_tperdida_veh = ? where txt_economico_veh= ?";
          $querya = $conn->prepare($actualizar);
          $querya->bindParam(1, $registrot["tiempo"]);
          $querya->bindParam(2, $economico1);
          $querya->execute();
          $querya->closeCursor();
          }
      $queryt->closeCursor();
    echo "<br>";
  
  if(($riesgo!=0 || $zona == 2210 || $segespecial==1) && $sucursal == 0){
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
  
/*  
  if($zonaroja != 0){
	if($zonaroja != 3871){
        $consultaang = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 214 and fec_fecha_ale > now() - interval '30 minute' limit 1";
          $queryultaang = $conn->prepare($consultaang);
          $queryultaang -> bindParam(1, $economico1);
          $queryultaang -> execute();
    $registrounicon = $queryultaang ->fetch();
     if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de Zona Roja *** ";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (214,now(),?,?,?,3,?,?,?,0)";
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
  }else{

$consultaang = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 20 and fec_fecha_ale > now() - interval '30 minute' and (CURRENT_TIME < '06:00' or  CURRENT_TIME > '21:00') limit 1";
          $queryultaang = $conn->prepare($consultaang);
          $queryultaang -> bindParam(1, $economico1);
          $queryultaang -> execute();
    $registrounicon = $queryultaang ->fetch();
     if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de Zona Tepo *** ";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (20,now(),?,?,?,3,?,?,?,0)";
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

}
*/
  
  if($riesgo == 3711){
        $consultaang = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 215 and fec_fecha_ale > now() - interval '30 minute' limit 1";
          $queryultaang = $conn->prepare($consultaang);
          $queryultaang -> bindParam(1, $economico1);
          $queryultaang -> execute();
    $registrounicon = $queryultaang ->fetch();
     if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta Deshuesadero Ojo de Agua *** ";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (215,now(),?,?,?,3,?,?,?,0)";
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
  
  if($riesgo == 3687){
        $consultaang = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 210 and fec_fecha_ale > now() - interval '30 minute' limit 1";
          $queryultaang = $conn->prepare($consultaang);
          $queryultaang -> bindParam(1, $economico1);
          $queryultaang -> execute();
    $registrounicon = $queryultaang ->fetch();
     if ($registrounicon["bandera"] == 0) {
                  echo " *** Se inserto alerta de angelopolis *** ";
                  $consultanp = " INSERT INTO tb_alertas
                                         (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                         VALUES (210,now(),?,?,?,3,?,?,?,0)";
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
  }
  /* Inserta Fecha de Ejecución */
      $actualiza_cron_historico = "insert into monitoreo.tb_estatus_crones_historico(id_cron,fecha_registro) values (2,now())";
      $query3 = $conn->prepare($actualiza_cron_historico);
      $query3->execute();
      $query3->closeCursor();
  
      $actualiza_cron = "update monitoreo.tb_estatus_crones set ultimo_registro=now() where id_cron=2";
      $query4 = $conn->prepare($actualiza_cron);
      $query4->execute();
      $query4->closeCursor();
      $query->closeCursor();

      $c = 0;

}else{


  
  // Actualiza la Fecha en el cron estatus a 0 (1970)
  $fec70 = date("Y-m-d H:i:s", 0);
  $actualiza_cron = "UPDATE monitoreo.tb_estatus_crones SET ultimo_registro = ? WHERE id_cron = 2";
  $queryT = $conn->prepare($actualiza_cron);
  $queryT->bindParam(1, $fec70);
  $queryT->execute();
  $queryT->closeCursor();
  
  // Cuenta el Tiempo que entro estado Amarillo
  $tespera = 30;
  $c += 1;
  $tiempo = ($c * $tespera) / 60;

  echo "  --- NUMERO DE CADENAS ALTAS  ";
  sleep($tespera);
  echo $tiempo."min. ---   ";
}


} while(true);
?>
