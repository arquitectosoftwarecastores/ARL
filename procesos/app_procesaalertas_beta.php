<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('max_execution_time', 300);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");


// Consulta parametro de ajuste de horas con respecto al GPS
$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();
// Consulta la ultima alerta  
$consulta = " SELECT * FROM tb_configuracion ";
$query = $conn->prepare($consulta);
$query->execute();
$registro = $query->fetch();
echo "<p>Registro de última alerta: " . $registro["num_ultimaalerta_con"] . "</p>";
// Consulta las alertas
$consulta1 = " SELECT * FROM avl_alertas WHERE  alerta_id > ? ORDER BY alerta_id ASC LIMIT 10000";
$query1 = $conn->prepare($consulta1);
$query1->bindParam(1, $registro["num_ultimaalerta_con"]);
$query1->execute();
$total = 0;
$ultimo = 0;
$contador = 0;
while ($registro1 = $query1->fetch()) {
    echo "Alerta No: " . $registro1["alerta_id"] . ", ";
    $consulta2 = " SELECT * FROM tb_tiposdealertas WHERE txt_nombre_tipa=?";
    $query2 = $conn->prepare($consulta2);
    $query2->bindParam(1, $registro1["alerta_dat1"]);
    $query2->execute();
    $cuenta = 0;
    while ($registro2 = $query2->fetch()) {
        $mensaje = $registro2["txt_nombre_tipa"];
        $prioridad = $registro2["num_prioridad_tipa"];
        $clavealerta = $registro2["pk_clave_tipa"];
        $tipoalerta = $registro2["num_tipo_tipa"];
        $cuenta++;
    }
    if ($cuenta) {
        echo "Tipo de alerta:" . $mensaje . ", ";
        // inicializa variables para insertar registros
        $campo1 = '';
        $ignicion = '';
        $fecha = date('Y-m-d H:i:s', strtotime('-' . $ajustegps . ' hour', strtotime($registro1["alerta_timestamp"])));
        $latitud = $registro1['alerta_latitud'];
        $longitud = $registro1['alerta_longitud'];
        $ubicacion = georeferencia($latitud, $longitud, $conn);
        $upsmart = georeferencia_pi($latitud, $longitud, $conn);
        switch ($registro1['alerta_ignicion']) {
            case 0:
                $ignicion = 'Apagado';
                break;
            case 1:
                $ignicion = 'Encendido';
                break;
        }
        // Código para identificar el ID de geocerca
        if ($mensaje == "Dentro Geocerca" OR $mensaje == "Fuera Geocerca") {
            $id_geocerca = checazona($registro1['alerta_latitud'], $registro1['alerta_longitud'], 4, $conn);
            $consulta4 = " SELECT txt_nombre_zon FROM tb_zonas WHERE pk_clave_zon=?";
            $query4 = $conn->prepare($consulta4);
            $query4->bindParam(1, $id_geocerca);
            $query4->execute();
            while ($registro4 = $query4->fetch()) {
                $campo1 = $registro4["txt_nombre_zon"];
                echo "Zona:" . $campo1 . ", ";
            }
        }
        $consulta7 = " SELECT * FROM avl_secundario WHERE sec_primario =? OR sec_secundario=?";
        $query7 = $conn->prepare($consulta7);
        $query7->bindParam(1, $registro1["alerta_nserie"]);
        $query7->bindParam(2, $registro1["alerta_nserie"]);
        $query7->execute();
        $registro7 = $query7->fetch();
        //
        $consulta5 = " SELECT txt_economico_veh, fk_clave_cir FROM tb_vehiculos WHERE num_serie_veh =?  OR num_serie_veh =? ";
        $query5 = $conn->prepare($consulta5);
        $query5->bindParam(1, $registro7["sec_primario"]);
        $query5->bindParam(2, $registro7["sec_secundario"]);
        $query5->execute();
        $encontrovehiculo = 0;
        while ($registro5 = $query5->fetch()) {
            $encontrovehiculo++;
            $economico = $registro5['txt_economico_veh'];
        }
        if ($encontrovehiculo) {
            $consulta6 = "INSERT INTO 
		tb_alertas (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_upsmart_ale,txt_economico_veh,num_latitud_ale,num_longitud_ale,txt_campo1_ale,txt_ignicion_ale,num_prioridad_ale,num_tipo_ale,txt_full_ale) VALUES(?,?,?,?,?,?,?,?,?,?,?,?) ";
            $query6 = $conn->prepare($consulta6);
            $query6->bindParam(1, $clavealerta);
            $query6->bindParam(2, $fecha);
            $query6->bindParam(3, $ubicacion);
            $query6->bindParam(4, $upsmart);
            $query6->bindParam(5, $economico);
            $query6->bindParam(6, $latitud);
            $query6->bindParam(7, $longitud);
            $query6->bindParam(8, $campo1);
            $query6->bindParam(9, $ignicion);
            $query6->bindParam(10, $prioridad);
            $query6->bindParam(11, $tipoalerta);
            $query6->bindParam(12, $mensaje);
            $query6->execute();
            echo "Insertando alerta:" . $tipoalerta . ",Fecha menos 5 horas:" . $fecha . ", Fecha gps:" . $registro1["alerta_timestamp"] . ", " . $ubicacion . "," . $upsmart . "," . $economico . "," . $latitud . "," . $longitud . "," . $campo1 . "," . $ignicion . "," . $prioridad . "," . $mensaje . "<br/>";
            $total++;
        } else {
            echo ", <span style='color:#FF0000'>No se encontró las series :" . $registro7["sec_primario"] . ", " . $registro7["sec_secundario"] . ", serie:" . $registro1["alerta_nserie"] . "</span><br/>";
            $consulta8 = "SELECT * FROM seriesnoencontradas WHERE serie=?";
            $query8 = $conn->prepare($consulta8);
            $query8->bindParam(1, $registro1["alerta_nserie"]);
            $query8->execute();
            $serie = 0;
            while ($registro8 = $query8->fetch())
                $serie++;
            $query8->closeCursor();
            if (!$serie) {
                $consulta9 = "INSERT INTO seriesnoencontradas (serie) VALUES(?) ";
                $query9 = $conn->prepare($consulta9);
                $query9->bindParam(1, $registro1["alerta_nserie"]);
                $query9->execute();
                $query9->closeCursor();
                echo "Se inserto serie " . $registro1["alerta_nserie"] . "En tabla seriesnoencontradas, ";
            }
        }
    }
    else
        echo "<span style='color:#FF0000'>No se encontró:" . $registro1["alerta_dat1"] . "</span><br/>";
    $consulta3 = " UPDATE tb_configuracion SET num_ultimaalerta_con=?";
    $query3 = $conn->prepare($consulta3);
    $query3->bindParam(1, $registro1['alerta_id']);
    $query3->execute();
    $ultimo = $registro1['alerta_id'];
    $query3->closeCursor();
    $contador++;
}
/*
$consulta = " SELECT * FROM tb_vehiculos ";
$query = $conn->prepare($consulta);
$query->execute();
$contador = 1;
while ($registro = $query->fetch()) {

    $economico1 = $registro["txt_economico_veh"];
    $ubicacion1 = $registro["txt_posicion_veh"];
    $ignicion1 = $registro["num_ignicion_veh"];
    $latitud1 = $registro["num_latitud_veh"];
    $longitud1 = $registro["num_longitud_veh"];
    $ubicacion2 = $registro["txt_upsmart_veh"];
    echo "Economico: ".$economico1;
    /* 02/07/2018 verificar zona de riesgo de la ciudad */
 /*   $zona3 = checazona($latitud1, $longitud1, 3, $conn);
    $zona2 = checazona($latitud1, $longitud1, 2, $conn);
    $zona1 = checazona($latitud1, $longitud1, 0, $conn);   
    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET riesgo = ?, sucursal = ?, ciudad = ? where economico = ?";
    $query2 = $conn->prepare($inserta_geocerca);
    $query2->bindParam(4, $economico1);
    $query2->bindParam(3, $zona1);
    $query2->bindParam(2, $zona2);
    $query2->bindParam(1, $zona3);
    $query2->execute();
    $query2->closeCursor();*/
    
//    if($economico1 == 90001){
//            enviamensajeinmovilizador("89", "90001");
//        }
        
//    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET sucursal = ? where economico = ?";
//    $query2 = $conn->prepare($inserta_geocerca);
//    $query2->bindParam(2, $economico1);
//    $query2->bindParam(1, $zona2);
//    $query2->execute();
//    $query2->closeCursor();
//    if($zona3==2210 || 3614){	
//    $inserta_geocerca = "UPDATE monitoreo.geocercasporunidad SET ciudad = ? where economico = ?";
//    $query2 = $conn->prepare($inserta_geocerca);
//    $query2->bindParam(2, $economico1);
//    $query2->bindParam(1, $zona1);
//    $query2->execute();
//    $query2->closeCursor();
//    }
    /*  fin  */
/*if($zona3!=0){
    echo "Inserto";
    $consultasinpos = "select count(*) as bandera from monitoreo.vw_unidades_no_reportando_riesgo where economico = ? limit 1";
    $querysinpos = $conn->prepare($consultasinpos);
    $querysinpos->bindParam(1, $economico1);
    $querysinpos->execute();
    $registrosinpos = $querysinpos->fetch();
    if (($registrosinpos["bandera"]) == 1) {
        $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 201 and fec_fecha_ale > now() - interval '35 minute' limit 1";
        //                 $consultaultale  = "select count(*) from tb_alertas a join unidades_sin_posicionar u on a.txt_economico_veh = u.txt_economico_veh where a.txt_economico_veh = ? and fk_clave_tipa = 201 and a.fec_fecha_ale > now() - interval '30 minute' and u.fecha_registro > now() - interval '720 minute' limit 1";
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
            }
        }
    }
}*/
// }

/*
$consultadesvio = "select count(*) as bandera from monitoreo.tb_vehiculos v join monitoreo.geocercasporunidad g on g.economico=v.txt_economico_veh where g.sucursal in (3563,3575,3576,3577,3578) and v.txt_economico_veh = ?";
    $querydesvio = $conn->prepare($consultadesvio);
    $querydesvio->bindParam(1, $economico1);
    $querydesvio->execute();
    $registrosdesvio = $querydesvio->fetch();
    if (($registrosdesvio["bandera"]) == 1) {
        $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 203 and fec_fecha_ale > now() - interval '15 minute' limit 1";
        //                 $consultaultale  = "select count(*) from tb_alertas a join unidades_sin_posicionar u on a.txt_economico_veh = u.txt_economico_veh where a.txt_economico_veh = ? and fk_clave_tipa = 201 and a.fec_fecha_ale > now() - interval '30 minute' and u.fecha_registro > now() - interval '720 minute' limit 1";
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
            }
        }
    }      */  
//}

echo "<p>Se actualizaron: " . $total . " de " . $contador . " alertas de la No:" . $registro["num_ultimaalerta_con"] . " a la No: " . $ultimo . "</p>";
$query->closeCursor();
$query1->closeCursor();
$query2->closeCursor();
$query4->closeCursor();
$query5->closeCursor();
$query6->closeCursor();
$query7->closeCursor();

?>