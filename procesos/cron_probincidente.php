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
while (true) {
    echo "Iniciando...";
    echo "<br>";
    $consulta = " SELECT * FROM monitoreo.tb_vehiculos where txt_economico_veh <> '85507' or txt_economico_veh <> '60219' ";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        $banderasinposicionar = 0;
        $banderadesconexiondeantena = 0;
        $banderadesconexiondeeneregia = 0;
        $banderamonitoreodeseguridad = 0;
        $total = 0;
        $economico1 = $registro["txt_economico_veh"];
        echo " ***** ";
        echo "Economico: " . $economico1;
        $latitud1 = $registro["num_latitud_veh"];
        $longitud1 = $registro["num_longitud_veh"];
        /* Buscamos si en la ultima media hora tiene alguna alerta de sin posicionar */
        $consuluta_sinposicionar = "select count(*) as total from monitoreo.tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 201 and fec_fecha_ale > (now() - '00:20:00'::interval) limit 1";
        $query1 = $conn->prepare($consuluta_sinposicionar);
        $query1->bindParam(1, $economico1);
        $query1->execute();
        while ($registro1 = $query1->fetch()) {
            $banderasinposicionar = $registro1["total"];
            if($banderasinposicionar>1){
                $banderasinposicionar=1;
            }
        }
        echo " Sin posicionar: " . $banderasinposicionar;
        $query1->closeCursor();
        /* Buscamos si en la ultima media hora tiene alguna alerta de desconexion de antena */
        $consuluta_desconexiondeantena = "select count(*) as total from monitoreo.tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 103 and fec_fecha_ale > (now() - '00:20:00'::interval) limit 1";
        $query2 = $conn->prepare($consuluta_desconexiondeantena);
        $query2->bindParam(1, $economico1);
        $query2->execute();
        while ($registro2 = $query2->fetch()) {
            $banderadesconexiondeantena = $registro2["total"];
            if($banderadesconexiondeantena>1){
                $banderadesconexiondeantena=1;
            }
        }
        echo " Desconexion de Antena: " . $banderadesconexiondeantena;
        $query2->closeCursor();
        /* Buscamos si en la ultima media hora tiene alguna alerta de desconexion de antena */
        $consulta_desconexiondeenergia = "select count(*) as total from monitoreo.tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 7 and fec_fecha_ale > (now() - '00:20:00'::interval) limit 1";
        $query3 = $conn->prepare($consulta_desconexiondeenergia);
        $query3->bindParam(1, $economico1);
        $query3->execute();
        while ($registro3 = $query3->fetch()) {
            $banderadesconexiondeeneregia = $registro3["total"];
            if($banderadesconexiondeeneregia>1){
                $banderadesconexiondeeneregia=1;
            }
        }
        echo " Desconexion de Energia: " . $banderadesconexiondeeneregia;
        $query3->closeCursor();
        /* Buscamos si en la ultima media hora tiene alguna alerta de monitoreo de seguridad */
        $consulta_monitoreodeseguridad = "select count(*) as total from monitoreo.tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 200 and fec_fecha_ale > (now() - '00:20:00'::interval) limit 1";
        $query4 = $conn->prepare($consulta_monitoreodeseguridad);
        $query4->bindParam(1, $economico1);
        $query4->execute();
        while ($registro4 = $query4->fetch()) {
            $banderamonitoreodeseguridad = $registro4["total"];
            if($banderamonitoreodeseguridad>1){
                $banderamonitoreodeseguridad=1;
            }
        }
        echo " Monitoreo de Seguridad: " . $banderamonitoreodeseguridad;
        $query4->closeCursor();
        /* Total de Alertas Recibidas */
        $total = $banderadesconexiondeeneregia + $banderadesconexiondeantena + $banderasinposicionar + $banderamonitoreodeseguridad;
        echo " Total de Alertas: " . $total;
        if ($total > 1) {
            $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 207 and fec_fecha_ale > now() - interval '30 minute' limit 1";
            $queryultale = $conn->prepare($consultaultale);
            $queryultale->bindParam(1, $economico1);
            $queryultale->execute();
            $registroultale = $queryultale->fetch();
            if ($registroultale["bandera"] == 0) {
                echo " *** Se inserto alerta de posible incidencia";
                $consultanp = " INSERT INTO tb_alertas 
                                       (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale)
                                       VALUES (207,now(),?,?,?,3,?,?,?,0)";
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
        echo "<Br>";
    }
    $query->closeCursor();
}
?>