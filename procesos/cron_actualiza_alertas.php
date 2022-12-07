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
while(true){
echo "Iniciando...";
echo "<br>";
$consulta = " SELECT * FROM monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad gpu on gpu.economico = v.txt_economico_veh";
$query = $conn->prepare($consulta);
$query->execute();
while ($registro = $query->fetch()) {
    $economico1 = $registro["txt_economico_veh"];
    $ubicacion1 = $registro["txt_posicion_veh"];
    $ignicion1 = $registro["num_ignicion_veh"];
    $latitud1 = $registro["num_latitud_veh"];
    $longitud1 = $registro["num_longitud_veh"];
    $ubicacion2 = $registro["txt_upsmart_veh"];
    $riesgo = $registro["riesgo"];   
    echo "Economico: ".$economico1;
    echo ", Riesgo: ".$riesgo;

if($riesgo!=0){
    $consultasinpos = "select count(*) as bandera from monitoreo.vw_unidades_no_reportando_riesgo where economico = ? limit 1";
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

$consultadesvio = "select count(*) as bandera from monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad g on g.economico=v.txt_economico_veh where g.sucursal in (3563,3575,3576,3577,3578) and v.txt_economico_veh = ?";
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
echo "<br>";    
}
$query->closeCursor();
}
?>