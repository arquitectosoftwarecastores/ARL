<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
require_once('xmlrpc.inc');
date_default_timezone_set("America/Mexico_City");
	echo "************************************************************************************************************************";
    echo "Iniciando el cron...";
    echo "<br>";
    $mensaje = '0';
    $consulta = "select * from monitoreo.tb_remolques where estatus = 1";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $economico1 = $registro["txt_economico_rem"];
        $latitud1 = $registro["num_latitud_rem"];
        $longitud1 = $registro["num_longitud_rem"];
      //  $mantenimiento = checazona($latitud1, $longitud1, 2, $conn);
	    echo "Economico: " . $economico1;
        echo "<br>";        
        $inserta_mantenimiento = "SELECT cr.noeconomico, CONCAT (os.fechafin,' ',os.horafin) AS fecha, COUNT(cr.noeconomico) AS total, 
        DATE_ADD(CURDATE(),INTERVAL 120 DAY) AS fechasig, now() as fechareg
        FROM siat.ordenservicio2021 os
        JOIN camiones.remolques cr ON cr.idremolque = os.unidad
        WHERE os.fechafin = CURDATE() AND os.idtipounidad = 4 AND cr.noeconomico = ?";
        $query2 = $bdsiat->prepare($inserta_mantenimiento);
        $query2->bindParam(1, $economico1);
        $query2->execute();
        $registro2 = $query2->fetch();
        //echo "Mantenimiento: ".$mantenimiento; 
        if ($registro2["total"] > 0) {
            echo "Unidad se realizo mantenimiento el día de hoy";
            echo "<br>";
            $eliminar_mantenimiento = "delete from tb_mantenimientos where economico_rem = ?";
            $query2 = $conn->prepare($eliminar_mantenimiento);
            $query2->bindParam(1, $economico1);
            $query2->execute();

            $counsulta_mantenimiento = "insert into tb_mantenimientos(economico_rem,usuarios,observacion,fecha_mtto,fecha_sigmtto, fecha_registro) values (?,1,'Automatico',?,?,?)";
            $query2 = $conn->prepare($counsulta_mantenimiento);
            $query2->bindParam(1, $economico1);
            $query2->bindParam(2, $registro2["fecha"]);
            $query2->bindParam(3, $registro2["fechasig"]);
            $query2->bindParam(4, $registro2["fechareg"]);
            $query2->execute();

            $counsulta_mantenimiento_hist = "insert into tb_mantenimientos_historico(economico_rem,usuarios,observacion,fecha_mtto,fecha_sigmtto, fecha_registro) values (?,1,'Automatico',?,?,?)";
            $query2 = $conn->prepare($counsulta_mantenimiento_hist);
            $query2->bindParam(1, $economico1);
            $query2->bindParam(2, $registro2["fecha"]);
            $query2->bindParam(3, $registro2["fechasig"]);
            $query2->bindParam(4, $registro2["fechareg"]);
            $query2->execute();
            
            $query2->closeCursor();
        } else {
            echo "Unidad no se aplico mantenimiento";
        }
        
    }

