﻿﻿﻿<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('max_execution_time', 300);

include ('../conexion/conexion.php');
include("../posiciones/app_referencia.php");
include("../funciones/distancia.php");
include("../funciones/orientacion.php");
include("../funciones/checazona.php");


//function debug_to_console( $data ) {
//    $output = $data;
//    if ( is_array( $output ) )
//        $output = implode( ',', $output);
//
//    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
//}


date_default_timezone_set("America/Mexico_City");

$rumbo[] = 'Este';
$rumbo[] = 'Estenoreste';
$rumbo[] = 'Noreste';
$rumbo[] = 'Nornoreste';
$rumbo[] = 'Norte';
$rumbo[] = 'Nornoroeste';
$rumbo[] = 'Noroeste';
$rumbo[] = 'Oestenoroeste';
$rumbo[] = 'Oeste';
$rumbo[] = 'Oestesuroeste';
$rumbo[] = 'Suroeste';
$rumbo[] = 'Sursuroeste';
$rumbo[] = 'Sur';
$rumbo[] = 'Sursureste';
$rumbo[] = 'Sureste';
$rumbo[] = 'Estesureste';
$rumbo[] = 'Parado';

//Consulta parametro de ajuste de horas con respecto al GPS

$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();

$consulta = " SELECT * FROM ctg_vehiculos where veh_uposicion > '20180801 00:49:36'";
//$consulta = "SELECT * FROM ctg_vehiculos c
//	join monitoreo.tb_vehiculos v on c.veh_nserie = v.num_serie_veh";
//	 order by fec_posicion_veh asc";

$query = $conn->prepare($consulta);
$query->execute();
$contador = 1;
while ($registro = $query->fetch()) {
    $serie = $registro["veh_nserie"];
    $consulta1 = "SELECT * FROM tb_vehiculos WHERE num_serie_veh=?";
    $query1 = $conn->prepare($consulta1);
    $query1->bindParam(1, $serie);
    $query1->execute();
    $encuentra = 0;
    while ($registro1 = $query1->fetch()) {
        $encuentra = 1;
        if ($registro1["num_latitud_veh"] != "Infinity")
            $latitud_ant = $registro1["num_latitud_veh"];
        else
            $latitud_ant = 0;
        if ($registro1["num_longitud_veh"] != "Infinity")
            $longitud_ant = $registro1["num_longitud_veh"];
        else
            $longitud_ant = 0;
        $fecha_ant = $registro1["fec_posicion_veh"];
        $zriesgo_ant = $registro1['num_zonariesgo_veh'];
        $economico = $registro1['txt_economico_veh'];
        $veh_zpinteres = $registro1['txt_zonapinteres_veh'];
        $veh_sespecial = $registro1['num_seguimientoespecial_veh'];
   //     $unidadenzonaderiesgo = $registro1['fk_clave_zon'];
       
    }

    echo "<p>" . $contador . ") ";
    if ($encuentra) {
        $orienta = $rumbo[16];
        $latitud = $registro['veh_latitud'];
        $longitud = $registro['veh_longitud'];
        $fecha = date('Y-m-d H:i:s', strtotime('-' . $ajustegps . ' hour', strtotime($registro["veh_uposicion"])));
        //checa si cambio la posición
        echo $longitud . "," . round($longitud_ant, 6) . "," . round($latitud_ant, 6) . "<br>";
        if ($longitud != round($longitud_ant, 6) or $latitud != round($latitud_ant, 6)) {
            $indice = orientacion($longitud_ant, $latitud_ant, round($longitud, 6), round($latitud, 6));
            if ($indice >= 0)
                $orienta = $rumbo[$indice];
           /* $ignicion = 2;
            switch ($registro['veh_ignicion']):
                case 0:
                    $ignicion = 2;
                    break;
                case 1:
                    $ignicion = 1;
                    break;
            endswitch;*/
//	    echo " --- ".$registro['veh_ignicion']. " --- ";
	    if($registro['veh_ignicion']!=0){
             $ignicion = 1;                   
            }else{
             $ignicion = 2;
            }

            $ubicacion = georeferencia($latitud, $longitud, $conn);
            $ubicacionpi = georeferencia_pi($latitud, $longitud, $conn);

            $combtot = $registro['veh_combtot'];
            $odometro = $registro['veh_odometro'];

            $zona = checazonaprioridad($latitud, $longitud, $conn);

            /* Fin cambios */
         //   echo "ZONA:" . $zona . ", ";
            // Genera alertas en entrando y saliendo de zonas de interes
            //Checa si esta entrando a zona de interes
            $hoy = date('Y/m/d H:i:s', time());
            $nombrezinteres = "";

            if ($zona) {
                $consulta6 = "SELECT * FROM tb_zonas WHERE pk_clave_zon=?";
                $query6 = $conn->prepare($consulta6);
                $query6->bindParam(1, $zona);
                $query6->execute();
                $registro6 = $query6->fetch();
                $nombrezinteres = $registro6["txt_nombre_zon"];
                if ($registro6["fk_clave_num"] == 1)    
                    $tipozona = "INTERES";
                if ($registro6["fk_clave_num"] == 2)
                    $tipozona = "SEGURO";
                if ($registro6["fk_clave_num"] == 3)
                    $tipozona = "RIESGO";

                if ($veh_zpinteres != $nombrezinteres) {  //Si la zona es diferente a la actual
                    // Inserta la alerta de entrada
                    echo "INSERTA ALERTA ENTRADA A ZONA DE INTERES,";

                    $consulta4 = "SELECT * FROM tb_tiposdealertas WHERE txt_nombre_tipa='Entrando Zona Riesgo' ";
                    $query4 = $conn->prepare($consulta4);
                    $query4->execute();
                    $registro4 = $query4->fetch();

                    if ($veh_sespecial) {
                        $consulta7 = "INSERT INTO tb_alertas 
			(fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        $query7 = $conn->prepare($consulta7);
                        $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                        $query7->bindParam(2, $fecha);
                        $query7->bindParam(3, $ubicacion);
                        $query7->bindParam(4, $economico);
                        $query7->bindParam(5, $ignicion);
                        $query7->bindParam(6, $nombrezinteres);
                        $query7->bindParam(7, $tipozona);
                        $query7->bindParam(8, $nombrezinteres);
                        $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                        $query7->bindParam(10, $latitud);
                        $query7->bindParam(11, $longitud);
                        $query7->bindParam(12, $ubicacionpi);
                        $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                        $query7->execute();
                    } else {
                        $consulta7 = "INSERT INTO tb_alertas 
			(fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale,num_estatus_ale,fk_clave_usu,fec_post_ale,txt_comentarios_ale)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1,49,?,'Cerrada por sistema')";
                        $query7 = $conn->prepare($consulta7);
                        $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                        $query7->bindParam(2, $fecha);
                        $query7->bindParam(3, $ubicacion);
                        $query7->bindParam(4, $economico);
                        $query7->bindParam(5, $ignicion);
                        $query7->bindParam(6, $nombrezinteres);
                        $query7->bindParam(7, $tipozona);
                        $query7->bindParam(8, $nombrezinteres);
                        $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                        $query7->bindParam(10, $latitud);
                        $query7->bindParam(11, $longitud);
                        $query7->bindParam(12, $ubicacionpi);
                        $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                        $query7->bindParam(14, $hoy);
                        $query7->execute();
                    }

                    if ($veh_zpinteres != "") {  // Si tenia zona , inserta salida
                        //Inserta salida
                        echo "INSERTA ALERTA DE SALIDA,";
                        $consulta4 = "SELECT * FROM tb_tiposdealertas WHERE txt_nombre_tipa='Saliendo Zona' ";
                        $query4 = $conn->prepare($consulta4);
                        $query4->execute();
                        $registro4 = $query4->fetch();

                        if ($veh_sespecial) {
                            $consulta7 = "INSERT INTO tb_alertas 
								               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
								               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $query7 = $conn->prepare($consulta7);
                            $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                            $query7->bindParam(2, $fecha);
                            $query7->bindParam(3, $ubicacion);
                            $query7->bindParam(4, $economico);
                            $query7->bindParam(5, $ignicion);
                            $query7->bindParam(6, $nombrezinteres);
                            $query7->bindParam(7, $tipozona);
                            $query7->bindParam(8, $nombrezinteres);
                            $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                            $query7->bindParam(10, $latitud);
                            $query7->bindParam(11, $longitud);
                            $query7->bindParam(12, $ubicacionpi);
                            $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                            $query7->execute();
                        } else {
                            $consulta7 = "INSERT INTO tb_alertas 
								               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale,num_estatus_ale,fk_clave_usu,fec_post_ale,txt_comentarios_ale)
								               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1,49,?,'Cerrada por sistema')";
                            $query7 = $conn->prepare($consulta7);
                            $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                            $query7->bindParam(2, $fecha);
                            $query7->bindParam(3, $ubicacion);
                            $query7->bindParam(4, $economico);
                            $query7->bindParam(5, $ignicion);
                            $query7->bindParam(6, $nombrezinteres);
                            $query7->bindParam(7, $tipozona);
                            $query7->bindParam(8, $nombrezinteres);
                            $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                            $query7->bindParam(10, $latitud);
                            $query7->bindParam(11, $longitud);
                            $query7->bindParam(12, $ubicacionpi);
                            $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                            $query7->bindParam(14, $hoy);
                            $query7->execute();
                        }
                    }
                }
            } else {  // Saliendo de zona
                if ($veh_zpinteres != "") { // Checa si tenia registrada una zona para insertar salida
                    //Inserta salida de zona de interes
                    echo "INSERTA ALERTA DE SALIDA,";

                    $consulta4 = "SELECT * FROM tb_tiposdealertas
								   WHERE txt_nombre_tipa='Saliendo Zona' ";
                    $query4 = $conn->prepare($consulta4);
                    $query4->execute();
                    $registro4 = $query4->fetch();

                    if ($veh_sespecial) {
                        $consulta7 = "INSERT INTO tb_alertas 
						               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
						               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        $query7 = $conn->prepare($consulta7);
                        $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                        $query7->bindParam(2, $fecha);
                        $query7->bindParam(3, $ubicacion);
                        $query7->bindParam(4, $economico);
                        $query7->bindParam(5, $ignicion);
                        $query7->bindParam(6, $nombrezinteres);
                        $query7->bindParam(7, $tipozona);
                        $query7->bindParam(8, $nombrezinteres);
                        $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                        $query7->bindParam(10, $latitud);
                        $query7->bindParam(11, $longitud);
                        $query7->bindParam(12, $ubicacionpi);
                        $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                        $query7->execute();
                    } else {
                        $consulta7 = "INSERT INTO tb_alertas 
						               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,txt_campo1_ale,txt_campo2_ale,txt_campo3_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale,num_estatus_ale,fk_clave_usu,fec_post_ale,txt_comentarios_ale)
						               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1,49,?,'Cerrada por sistema')";
                        $query7 = $conn->prepare($consulta7);
                        $query7->bindParam(1, $registro4["pk_clave_tipa"]);
                        $query7->bindParam(2, $fecha);
                        $query7->bindParam(3, $ubicacion);
                        $query7->bindParam(4, $economico);
                        $query7->bindParam(5, $ignicion);
                        $query7->bindParam(6, $nombrezinteres);
                        $query7->bindParam(7, $tipozona);
                        $query7->bindParam(8, $nombrezinteres);
                        $query7->bindParam(9, $registro4["num_prioridad_tipa"]);
                        $query7->bindParam(10, $latitud);
                        $query7->bindParam(11, $longitud);
                        $query7->bindParam(12, $ubicacionpi);
                        $query7->bindParam(13, $registro4["num_tipo_tipa"]);
                        $query7->bindParam(14, $hoy);
                        $query7->execute();
                    }
                }
            }
            // Finaliza proceso de generación de alertas entrando y saliendo de zonas de interes



            /* cambios 10/04/2018 */

            //       if(  $tipozona=='RIESGO'){
   /*         $consultasinpos = "select * from monitoreo.vw_unidades_no_reportando_riesgo where economico = ? limit 1";
            $querysinpos = $conn->prepare($consultasinpos);
            $querysinpos->bindParam(1, $economico);
            $querysinpos->execute();
            $registrosinpos = $querysinpos->fetch();
            if (empty($registrosinpos["economico"])) {
                
            } else {
                $consultaultale = "select * from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 201 and fec_fecha_ale > now() - interval '60 minute' limit 1";
                $queryultale = $conn->prepare($consultaultale);
                $queryultale->bindParam(1, $economico);
                $queryultale->execute();
                $registroultale = $queryultale->fetch();
                if (empty($registroultale["txt_economico_veh"])) {

                    $consultanp = " INSERT INTO tb_alertas 
                    (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                    VALUES (201,now(),'prueba','90001',1,3,19.0,-99.0,'prueba2',0)";
                    $querynp = $conn->prepare($consultanp);
                    $querynp->execute();
                }
            }*/
            /*
              $consultanp  = "INSERT INTO tb_alertas
              (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
              VALUES (201,now(),?,?,?,3,?,?,?,0)";
              $querynp = $conn->prepare($consultanp);
              $querynp->bindParam(1, $ubicacion);
              $querynp->bindParam(2, $economico);
              $querynp->bindParam(3, $ignicion);
              $querynp->bindParam(4, $latitud);
              $querynp->bindParam(5, $longitud);
              $querynp->bindParam(6, $ubicacionpi);
              $querynp->execute();
              //     }
              } */
            //        }
            /* fin cambios 10/04/2018 */

            //Actualización de Zona de riesgo 
   /*         $zriesgo = checazona($latitud, $longitud, 3, $conn);

            echo "RIESGO:" . $zriesgo . ", ";
            if ($zriesgo_ant == 0 and $zriesgo > 0) {
                echo "Inserta alerta 'Entrando Zona Riesgo':" . $ubicacionpi . ", ";
                $consulta4 = "SELECT * FROM tb_tiposdealertas WHERE txt_nombre_tipa='Entrando Zona Riesgo' ";
                $query4 = $conn->prepare($consulta4);
                $query4->execute();
                $registro4 = $query4->fetch();
                $consulta5 = "INSERT INTO tb_alertas 
					               (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
					               VALUES (?,?,?,?,?,?,?,?,?,?)";
                $query5 = $conn->prepare($consulta5);
                $query5->bindParam(1, $registro4["pk_clave_tipa"]);
                $query5->bindParam(2, $fecha);
                $query5->bindParam(3, $ubicacion);
                $query5->bindParam(4, $economico);
                $query5->bindParam(5, $ignicion);
                $query5->bindParam(6, $registro4["num_prioridad_tipa"]);
                $query5->bindParam(7, $latitud);
                $query5->bindParam(8, $longitud);
                $query5->bindParam(9, $ubicacionpi);
                $query5->bindParam(10, $registro4["num_tipo_tipa"]);
                $query5->execute();
            }*/

            //Actualiza tabla de tb_vehiculos
            $consulta2 = " UPDATE tb_vehiculos SET fec_posicion_veh=?, txt_posicion_veh=?, txt_upsmart_veh=?, num_latitud_veh=?,
			num_longitud_veh=?, num_ignicion_veh=?, txt_combtot_veh=?, txt_odometro_veh=?, txt_zonapinteres_veh = ?,
			txt_orientacion_veh=?, fk_clave_zon=? WHERE num_serie_veh=? ";
            $query2 = $conn->prepare($consulta2);
            $query2->bindParam(1, $fecha);
            $query2->bindParam(2, $ubicacion);
            $query2->bindParam(3, $ubicacionpi);
            $query2->bindParam(4, $latitud);
            $query2->bindParam(5, $longitud);
            $query2->bindParam(6, $ignicion);
            $query2->bindParam(7, $combtot);
            $query2->bindParam(8, $odometro);
            $query2->bindParam(9, $nombrezinteres);
            $query2->bindParam(10, $orienta);
            $query2->bindParam(11, $zona);
            $query2->bindParam(12, $serie);
            $query2->execute();
            echo "Se actualizó con éxito, serie:" . $serie . ", ubicación:" . $ubicacion . ", " . $ubicacionpi . ", fecha-hora:" . $fecha . "</p>";

      } else {
            $consulta2 = " UPDATE tb_vehiculos SET fec_posicion_veh=? WHERE num_serie_veh=? ";
            $query2 = $conn->prepare($consulta2);
            $query2->bindParam(1, $fecha);
            $query2->bindParam(2, $serie);
            $query2->execute();
            echo " Sin cambio de posición, serie:" . $serie . ", solo se actualizó la fecha y hora: " . $fecha . "</p>";
        }
        // Sacar la ciudad donde se encuentra la unidad
    //    $inserta_geocerca = "update monitoreo.geocercasporunidad set geo1=7 WHERE EXISTS (SELECT 1 FROM monitoreo.geocercasporunidad WHERE economico = 90000 and geo1 = 6 ) and economico = 90000 ";
   //     $queryig = $conn->prepare($inserta_geocerca);
   //            $query2->bindParam(1, $economico);
   //            $query2->bindParam(2, $zona);
   //     $queryig->execute();
   //      $queryig->closeCursor();
        // Genera alertas en entrando y saliendo de zonas de interes     
    }
    else
        echo "No se encontró, serie:" . $serie . "</p>";
    $contador++;
}
$query->closeCursor();
$query1->closeCursor();
$query2->closeCursor();
$query4->closeCursor();
$query5->closeCursor();
$query6->closeCursor();
$query7->closeCursor();
//$queryultale->closeCursor();
//$querynp->closeCursor();
//$querysinpos->closeCursor();
?>