<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");
//while (true) {
    echo "Iniciando... \n";
    echo "<br>";
    $consulta = " SELECT txt_economico_rem, num_latitud_rem, num_longitud_rem, sucursal FROM tb_remolques AS tv 
    LEFT JOIN geocercasporunidad AS gcpu ON tv.txt_economico_rem = gcpu.economico 
    WHERE txt_economico_rem NOT LIKE '00%' AND estatus = 1 and length(txt_economico_rem) < 6; ";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $economico1 = $registro["txt_economico_rem"];
        $latitud1 = $registro["num_latitud_rem"];
        $longitud1 = $registro["num_longitud_rem"];
        $sucursal1 = $registro["sucursal"];
        echo "Economico: " . $economico1."\n";
        $zona2 = checazona($latitud1, $longitud1, 2, $conn);
        if ($sucursal1 !== $zona2) {
            echo " Actualiza Sucursal: " . $zona2;
            echo "<br> \n";
            $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET sucursal = ? where economico = ?";
            $query2 = $conn->prepare($inserta_geocerca);
            $query2->bindParam(2, $economico1);
            $query2->bindParam(1, $zona2);
            $query2->execute();
            $query2->closeCursor();
            /*  Almacenamos histórico de movimientos en sucursales */
            $inserta_historico = "insert into monitoreo.movimientos_sucursales values (?,now(),1,?)";
            $query3 = $conn->prepare($inserta_historico);
            $query3->bindParam(1, $economico1);
            $query3->bindParam(2, $zona2);
            $query3->execute();
            $query3->closeCursor();
        }
    }
    /* Inserta Fecha de Ejecuci�n 
    $actualiza_cron_historico = "insert into monitoreo.tb_estatus_crones_historico(id_cron,fecha_registro) values (1,now())";
    $query3 = $conn->prepare($actualiza_cron_historico);
    $query3->execute();
    $query3->closeCursor();    
    
    $actualiza_cron = "update monitoreo.tb_estatus_crones set ultimo_registro=now() where id_cron=1";
    $query4 = $conn->prepare($actualiza_cron);
    $query4->execute();
    $query4->closeCursor();*/
    
    $query->closeCursor();
//}
?>