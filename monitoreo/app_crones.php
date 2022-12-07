<?php
session_start(); 
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
include ('../conexion/conexion.php');
//arreglo donde guardaremos el json que regresara el ajax
$jsondata = array();
//consulta para obtener crones disponibles y activos (estatus 1)
$consulta_crones = "select * from tb_crones where estatus = 1 order by id asc";
$query1 = $conn->prepare($consulta_crones);
$query1->execute(); 
//iteramos cada registro
while ($resultado1 = $query1->fetch()) {
    $cron = array();

    //guardamos id del cron iterado
    $cron['id_cron'] = "Proceso: ".$resultado1['id'];

    //logica para saber si el cron se detuvo
    //obtenemos la ultima fecha 
    $consulta_fecha = 'select * from tb_estatus_crones where id_cron = ?' ;
    $query2 = $conn->prepare($consulta_fecha);
    $query2->bindParam(1,$resultado1['id']);
    $query2->execute();
    $resultado2 = $query2->fetch();  
    $fecha_bd = $resultado2['ultimo_registro'];
    $query2->closeCursor();

    //le damos formato a la fecha
    $fecha = date_create($fecha_bd);
    //obtenemos los minutos que tarda el cron en terminar
    $tiempo = $resultado1['tiempo_ejecucion'];
    //le agregamos los minutos a la fecha obtenida desde la bd
    date_add($fecha, date_interval_create_from_date_string( $tiempo.' minutes'));
    //añadimos  un formato especifico a la fecha final
    $result = $fecha->format('Y-m-d H:i:s');

    //obtenemos la fecha actual del sistema
    $date_actual = date('Y-m-d H:i:s');

    //comparamos las dos fechas
    //si la fecha result es mas grande que date_actual, el cron esta corriendo
    //si la fecha result es mas pequeña que date_actual, el cron se detuvo
    //si la fecha result es igual a 1970-01-01 00:00:00, el cron esta en espera
    // 1 -> corriendo , 2 -> se detuvo, 3 -> espera

    if ($result == date('Y-m-d H:i:s',0)) {
        $cron['estatus'] = 3;
    }
    else if($result < $date_actual){
        //añadimos un estatus para identificar el estado del cron
        $cron['estatus'] = 2;
    }else{
        $cron['estatus'] = 1;
    }

    $jsondata[] = $cron;
}

print_r(json_encode($jsondata));

?>