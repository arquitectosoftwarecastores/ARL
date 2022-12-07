<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
//include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");
//  while(true){
    echo "Iniciando... \n";
    echo "<br>";
    $consulta = " SELECT * FROM monitoreo.tb_remolques r join monitoreo.geocercasporunidad gpu on gpu.economico = r.txt_economico_rem where fec_posicion_rem > (now() - '24:00:00'::interval) and r.estatus = 1";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $economico1 = $registro["txt_economico_rem"];
        $latitud1 = $registro["num_latitud_rem"];
        $longitud1 = $registro["num_longitud_rem"];
        $sucursal1 = $registro["sucursal"];
        $latitud1 = $registro["num_latitud_rem"];
        $longitud1 = $registro["num_longitud_rem"];
        $sucursal = $registro["sucursal"];
        $zona = $registro["fk_clave_zon"];  
        $fronteriza1 = $registro["zonaroja"];
        $tperdida = $registro["num_icono_rem"];
        $segespecial = $registro["num_seguimiento_rem"];
        $ubicacion1 = $registro["txt_georeferencia_mun"];
        $ubicacion2 = $registro["txt_georeferencia_cas"];
        $fronteriza = checazona($latitud1, $longitud1, -3, $conn);
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
            echo "Unidad ".$economico1." esta en la frontera";
            $cons1 = "select count(*) as bandera from unidades_usa where noeconomico = ? limit 1";
              $query1 = $conn->prepare($cons1);
              $query1 -> bindParam(1, $economico1);
              $query1 -> execute();
              $registro1 = $query1 ->fetch();
            if ($registro1["bandera"] == 0) {
                     echo " *** Se inserto unidad en USA *** ";
                      $consultanp = "INSERT INTO unidades_usa (noeconomico,fecha_ingreso) VALUES (?,now())";
                      $querynp = $conn->prepare($consultanp);
                      $querynp->bindParam(1, $economico1);
                      $querynp->execute();
                      $querynp->closeCursor();
                  }
            $query1 -> closeCursor();
          }
          if($fronteriza == 0){
            $cons1 = "select count(*) as bandera from unidades_usa where noeconomico = ? limit 1";
                $query1 = $conn->prepare($cons1);
                $query1 -> bindParam(1, $economico1);
                $query1 -> execute();
                $registro1 = $query1 ->fetch();
              if ($registro1["bandera"] == 1) {
                echo "Unidad ".$economico1." salió de la frontera";
                $consultausa = " SELECT * FROM monitoreo.unidades_usa where noeconomico = ?";
                $query12 = $conn->prepare($consultausa);
                $query12 -> bindParam(1, $economico1);
                $query12->execute();
                while ($registrousa = $query12->fetch()) {
                  $entrada = $registrousa["fecha_ingreso"];
                }
                echo "Con fecha ".$entrada;
                  echo " *** Se inserto unidad en USA *** ";
                      $consultanp2 = "INSERT INTO unidades_usa_historico (noeconomico,fecha_ingreso,fecha_salida,dias) VALUES (?,?,now(),0)";
                      $querynp2 = $conn->prepare($consultanp2);
                      $querynp2->bindParam(1, $economico1);
                      $querynp2->bindParam(2, $entrada);
                      $querynp2->execute();
                      $querynp2->closeCursor();
                       echo " *** Se elimino unidad en USA *** ";
                        $consultanp = "delete from unidades_usa where noeconomico = ?";
                        $querynp = $conn->prepare($consultanp);
                        $querynp->bindParam(1, $economico1);
                        $querynp->execute();
                        $querynp->closeCursor();
                $query12->closeCursor();
                }
              $query1 -> closeCursor();
              }
    }    
    echo "Terminado";
    $query->closeCursor();
  sleep(60);
 // }  
?>
