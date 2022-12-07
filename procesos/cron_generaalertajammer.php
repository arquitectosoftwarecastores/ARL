<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
date_default_timezone_set("America/Mexico_City");
while (true) {
    echo "Iniciando...";
    echo "<br>";
    $consulta = " SELECT * FROM monitoreo.tb_vehiculos order by txt_economico_veh asc ";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $economico1 = $registro["txt_economico_veh"];
        $latitud1 = $registro["num_latitud_veh"];
        $longitud1 = $registro["num_longitud_veh"];
        $serie = $registro["num_serie_veh"];
        echo "Economico: " . $economico1;
        echo ", Serie: " . $serie;
        $consulta_jammer = "select (fec_ultimaposicion_pos - interval '360 minute') as fecha,* from monitoreo.tb_posiciones where txt_tipo_pos like '%ALT' and fec_ultimaposicion_pos > now() + interval '350 minute' and 
        (txt_cadena_pos like '%000;50;%' or txt_cadena_pos like '%010;50;%') and num_nserie_pos = ? ";
        $query2 = $conn->prepare($consulta_jammer);
        $query2->bindParam(1, $serie);
        $query2->execute();
        while ($registrojammer = $query2->fetch()) {
            $fechaalerta = $registrojammer['fecha'];
            echo "Fecha: ".$fechaalerta;
            $consultaultale = "select count(*) as total from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 150 and alerta_fecha_registro > now() - interval '10 minute' ";
            $queryultale = $conn->prepare($consultaultale);
            $queryultale->bindParam(1, $economico1);
            $queryultale->execute();
            $registroultale = $queryultale->fetch();
            if ($registroultale['total']==0) {
                    echo " ******************* Se inserto alerta de jammer ****************************";
                    $consultanp = " INSERT INTO tb_alertas 
                                       (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                       VALUES (150,?,?,?,?,3,?,?,?,0)";
                    $querynp = $conn->prepare($consultanp);
                    $querynp->bindParam(1, $fechaalerta);
                    $querynp->bindParam(2, $ubicacion1);
                    $querynp->bindParam(3, $economico1);
                    $querynp->bindParam(4, $ignicion1);
                    $querynp->bindParam(5, $latitud1);
                    $querynp->bindParam(6, $longitud1);
                    $querynp->bindParam(7, $ubicacion2);
                    $querynp->execute();
                    $querynp->closeCursor();
                }
                $queryultale->closeCursor();
            }
            echo "<br>";
            $query2->closeCursor();	
        }
            $query->closeCursor();
	    $eliminarcadenas = "delete from monitoreo.avl_cadenas_g where cad_estatus = 1 and cad_tipo like '%ALT' and 
            (cad_string like '%0;34;%' or cad_string like '%0;33;%' or cad_string like '%0;68;%' or cad_string like '%0;69;%' or cad_string like '%0;47;%' or cad_string like '%0;48;%' or cad_string like '%0;1;%' or cad_string like '%0;2;%')";
            $query3 = $conn->prepare($eliminarcadenas);
            $query3->execute();
            
	    $eliminarcadenas = "delete from monitoreo.avl_cadenas_g where cad_estatus = 1 and cad_tipo like '%EVT' and (cad_string like '%0;4;%' or cad_string like '%0;5;%' or cad_string like '%0;6;%')";
            $query3 = $conn->prepare($eliminarcadenas);
            $query3->execute();
        
            $eliminarcadenas = "delete from monitoreo.avl_cadenas_g where (cad_tipo like '%CMD' or cad_tipo like '%HTE' or cad_tipo like '%ALV') and cad_estatus = 1";
            $query3 = $conn->prepare($eliminarcadenas);
            $query3->execute();
	 
	    $eliminarcadenas = "delete from monitoreo.avl_cadenas_g where cad_estatus = 1 and (cad_tipo like '%EMG' or cad_tipo like '%EVT') and cad_fechahora < now() - interval '10 minute'";
            $query3 = $conn->prepare($eliminarcadenas);
            $query3->execute();

	    $eliminarcadenas = "delete from monitoreo.tb_posiciones WHERE pk_clave_pos IN (
    	    SELECT unnest(array_remove(all_ctids, actid) ) FROM ( SELECT 
      	    min(b.pk_clave_pos)  AS actid, 
            array_agg(pk_clave_pos) AS all_ctids 
 	    FROM monitoreo.tb_posiciones b
            where pk_clave_pos > 1688000000
     	    GROUP BY txt_tipo_pos, num_nserie_pos,fec_ultimaposicion_pos, num_latitud_pos, num_longitud_pos
     	    HAVING count(*) > 1) c);";
            $query3 = $conn->prepare($eliminarcadenas);
            $query3->execute();	    
            $query3->closeCursor();

            $eliminaralertas = "delete from monitoreo.tb_alertas
				WHERE pk_clave_ale IN (
    				SELECT unnest(array_remove(all_ctids, actid) ) 
    				FROM (
    				 SELECT 
     				 min(b.pk_clave_ale)  AS actid, 
      				array_agg(pk_clave_ale) AS all_ctids 
     				FROM monitoreo.tb_alertas b
    				 where pk_clave_ale  > 112400000
     				GROUP BY txt_economico_veh,num_latitud_ale,num_longitud_ale,fec_fecha_ale,num_tipo_ale
    				 HAVING count(*) > 1) c) ";
	    $query3 = $conn->prepare($eliminaralertas);
            $query3->execute();	    
            $query3->closeCursor();

	      /* Inserta Fecha de Ejecución */
    $actualiza_cron_historico = "insert into monitoreo.tb_estatus_crones_historico(id_cron,fecha_registro) values (3,now())";
    $query3 = $conn->prepare($actualiza_cron_historico);
    $query3->execute();
    $query3->closeCursor();    
    
    $actualiza_cron = "update monitoreo.tb_estatus_crones set ultimo_registro=now() where id_cron=3";
    $query4 = $conn->prepare($actualiza_cron);
    $query4->execute();
    $query4->closeCursor();
    }
?>