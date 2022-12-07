<?php
//session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', true);
//include ('../conexion/conexion.php');
//include ('../funciones/distancia.php');
//include ('../funciones/puntoseguro.php');
//include ('../posiciones/app_referencia.php');
//include ('../funciones/checazona.php');
echo "Hola";
//include("../funciones/almacenaconsulta.php");
//date_default_timezone_set("America/Mexico_City");
//do {
//  $consulta = " SELECT * FROM monitoreo.tb_remolques r join monitoreo.geocercasporunidad gpu on gpu.economico = r.txt_economico_rem where fec_posicion_rem > (now() - '24:00:00'::interval) and r.estatus = 1";
//  $query = $conn->prepare($consulta);
//  $query->execute();
//  while ($registro = $query->fetch()) {
//      $economico1 = $registro["txt_economico_rem"];
 /*     $serie = $registro["txt_nserie_rem"];
      $latitud1 = $registro["num_latitud_rem"];
      $longitud1 = $registro["num_longitud_rem"];
      $sucursal = $registro["sucursal"];
      $zona = $registro["fk_clave_zon"];  
      $fronteriza1 = $registro["zonaroja"];
      $tperdida = $registro["num_icono_rem"];
      $segespecial = $registro["num_seguimiento_rem"];
      $ubicacion1 = $registro["txt_georeferencia_mun"];
      $ubicacion2 = $registro["txt_georeferencia_cas"];*/
//      echo "\nEconomico: ".$economico1;
//      $fronteriza = checazona($latitud1, $longitud1, -3, $conn);
//      echo " Zona Fonteriza: ".$fronteriza;      
/*     $consultaang = "select count(*) as bandera from tb_alertas where txt_economico_rem = ? and fk_clave_tipa = 201 and fec_fecha_ale > now() - interval '1440 minute' limit 1";
      $queryultaang = $conn->prepare($consultaang);
      $queryultaang -> bindParam(1, $economico1);
      $queryultaang -> execute();
      $registrounicon = $queryultaang ->fetch();
      if ($registrounicon["bandera"] == 0) {

      }
*/
 /* if ($fronteriza1 !== $fronteriza) {
    echo " Actualiza frontera *********************************" . $fronteriza;
    echo "<br> \n";
    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET zonaroja = ? where economico = ?";
    $query2 = $conn->prepare($inserta_geocerca);
    $query2->bindParam(2, $economico1);
    $query2->bindParam(1, $fronteriza);
    $query2->execute();
    $query2->closeCursor();
  }*/


  /*
  if($fronteriza == 4292){
        $cons1 = "select count(*) as bandera from unidades_usa where economico = ? and fecha_salida is null order by fecha_entrada desc limit 1";
        echo $cons1;
          $query1 = $conn->prepare($cons1);
          $query1 -> bindParam(1, $economico1);
          $query1 -> execute();
          $registro1 = $query1 ->fetch();
        if ($registro1["bandera"] == 0) {
                 echo " *** Se inserto alerta de Zona Fronteriza *** ";
                  $consultanp = "INSERT INTO unidades_usa (economico,fecha_entrada,fecha_salida,dias) VALUES (?,now(),'',0)";
                  $querynp = $conn->prepare($consultanp);
                  $querynp->bindParam(1, $economico1);
                  $querynp->execute();
                  $querynp->closeCursor();
              }
        $query1 -> closeCursor();
        }

        */
 // }
  ?>