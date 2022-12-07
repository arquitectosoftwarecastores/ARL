<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
include('../conexion/conexion.php');
include('../funciones/distancia.php');
include('../funciones/puntoseguro.php');
include('../posiciones/app_referencia.php');
include('../funciones/checazona.php');

date_default_timezone_set("America/Mexico_City");
echo "*******************************************************************************************\n";
$year = date_format(new DateTime(), "Y");
echo "Iniciando el cron...\n";
$mensaje = '0';
$consulta = "SELECT txt_economico_rem
            FROM monitoreo.tb_remolques 
            WHERE 
                estatus = 1
            ORDER BY txt_economico_rem";
$query = $conn->prepare($consulta);
$query->execute();
while ($registro = $query->fetch()) {
    $economico1 = $registro["txt_economico_rem"];
	echo "Economico::: ".$economico1;
    // $latitud1 = $registro["num_latitud_rem"];
    // $longitud1 = $registro["num_longitud_rem"];
    //  $mantenimiento = checazona($latitud1, $longitud1, 2, $conn);

    $inserta_mantenimiento = "SELECT 
                              r.noeconomico, CONCAT (ao.fechafin,' ',ao.horafin) AS fecha,
                              COUNT(r.noeconomico) AS total, 
                              DATE_ADD(CURDATE(),INTERVAL 119 DAY) AS fechasig,
                              NOW() as fechareg
                            FROM siat.actividadorden2022 AS ao
                            JOIN siat.ordenservicio2022 AS o 
                                ON o.idordenservicio = ao.idordenservicio 
                            JOIN camiones.remolques r 
                                ON r.idremolque = o.unidad 
                            WHERE 
                              ao.fechafin = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND  ao.idactividad = 1638 AND
                              o.idtipounidad = 4 AND
                              r.noeconomico = ?";
    $query2 = $bdsiat->prepare($inserta_mantenimiento);
    $query2->bindParam(1, $economico1);
    $query2->execute();
    $registro2 = $query2->fetch();
    //echo "Mantenimiento: ".$mantenimiento; 
    if ($registro2["total"] > 0) {
        echo "Economico: " . $economico1 . " -> ";
        echo "Unidad se realizo mantenimiento el día de hoy\n";
        $eliminar_mantenimiento = "DELETE FROM tb_mantenimientos
                                WHERE economico_rem = ?";
        $query2 = $conn->prepare($eliminar_mantenimiento);
        $query2->bindParam(1, $economico1);
        $query2->execute();

        $counsulta_mantenimiento = "INSERT INTO tb_mantenimientos
                                    (economico_rem, usuarios, observacion, fecha_mtto,
                                    fecha_sigmtto, fecha_registro)
                                VALUES (?,1,'Automatico',?,?,?)";
        $query2 = $conn->prepare($counsulta_mantenimiento);
        $query2->bindParam(1, $economico1);
        $query2->bindParam(2, $registro2["fecha"]);
        $query2->bindParam(3, $registro2["fechasig"]);
        $query2->bindParam(4, $registro2["fechareg"]);
        $query2->execute();

        $counsulta_mantenimiento_hist = "INSERT INTO tb_mantenimientos_historico
                                        (economico_rem, usuarios,
                                        observacion, fecha_mtto,
                                        fecha_sigmtto, fecha_registro)
                                    VALUES (?,1,'Automatico',?,?,?)";
        $query2 = $conn->prepare($counsulta_mantenimiento_hist);
        $query2->bindParam(1, $economico1);
        $query2->bindParam(2, $registro2["fecha"]);
        $query2->bindParam(3, $registro2["fechasig"]);
        $query2->bindParam(4, $registro2["fechareg"]);
        $query2->execute();

        $query2->closeCursor();
    }
}
