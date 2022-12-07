<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include("../posiciones/app_referencia.php");
include("../funciones/distancia.php");
include("../funciones/orientacion.php");
include("../funciones/checazona.php");
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

while(true){
//Consulta parametro de ajuste de horas con respecto al GPS
$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();
/* where c.veh_nserie = '008074302'  */
$consulta = "SELECT * FROM ctg_vehiculos c join monitoreo.tb_vehiculos v on c.veh_nserie = v.num_serie_veh order by fec_posicion_veh asc limit 300";
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
    }
    echo "<p>" . $contador . ") ";
    if ($encuentra) {
        $orienta = $rumbo[16];
        $latitud = $registro['veh_latitud'];
        $longitud = $registro['veh_longitud'];
        $fecha = date('Y-m-d H:i:s', strtotime('-' . $ajustegps . ' hour', strtotime($registro["veh_uposicion"])));
        //checa si cambio la posición
        echo $longitud . "," . round($longitud_ant, 6) . "," . round($latitud_ant, 6) . "<br>";
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
            $nombrezinteres = "";
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
    }
    else
        echo "No se encontró, serie:" . $serie . "</p>";
    $contador++;
}
$query->closeCursor();
$query1->closeCursor();
$query2->closeCursor();

$consulta_no_existe = "select pk_clave_veh,num_serie_veh,txt_odometro_veh,txt_combtot_veh,fec_posicion_veh,txt_economico_veh
                       from monitoreo.tb_vehiculos tb where not exists(select num_serie_veh from monitoreo.lectura_tablero le 
                 where tb.txt_economico_veh = le.txt_economico_veh)";


//consultamos si hay registros nuevos en tb.vehiculos que no existan en lectura_tablero
//si hay registros nuevos, los insertamos en lectura_tablero
//si no existen unidades nuevas, no entra al while y sigue con el proceso

$query0 = $conn->prepare($consulta_no_existe);
$query0->execute();
while ($registro = $query0->fetch()) {
    try {
    $query_inserta = "insert into monitoreo.lectura_tablero values (?,?,0,0,?,?)";
     $query1 = $conn->prepare($query_inserta);
     
     $query1->bindParam(1,$registro["pk_clave_veh"] );
     $query1->bindParam(2,$registro["num_serie_veh"] );
     $query1->bindParam(3,$registro["fec_posicion_veh"] );
     $query1->bindParam(4,$registro["txt_economico_veh"] );
     $query1->execute();
    
     $query1->closeCursor();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
}

$query0->closeCursor();
//consultamos la unidades que tengsn el mayor odometro en compración al odometro insertado en lectura_tablero
//Si hay registros los actualizamos en la tabla de lectura_tablero
//si no hay registros, no entra al while y sigue el proceso
$consulta_odometro = "select tb.txt_economico_veh,tb.num_serie_veh,tb.txt_odometro_veh,tb.txt_combtot_veh,tb.fec_posicion_veh from monitoreo.tb_vehiculos tb 
                    where  exists(select num_serie_veh from monitoreo.lectura_tablero le 
                where tb.txt_economico_veh = le.txt_economico_veh
                and CAST(tb.txt_odometro_veh AS money) > CAST(le.txt_odometro_veh AS money))";

$query0 = $conn->prepare($consulta_odometro);


$query0->execute();
while($registro2 = $query0->fetch()){
    
    try {
        
        $query_update = "update monitoreo.lectura_tablero set txt_odometro_veh = ?,txt_combtot_veh = ?,fec_ultimo_registro = ? where txt_economico_veh = ?";
        $query2 = $conn->prepare($query_update);
        
        $query2->bindParam(1,$registro2["txt_odometro_veh"]);
        $query2->bindParam(2,$registro2["txt_combtot_veh"]);
        $query2->bindParam(3,$registro2["fec_posicion_veh"]);
        $query2->bindParam(4,$registro2["txt_economico_veh"]);
        $query2->execute();
        $query2->closeCursor();
       
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }        
}

 $actualiza_cron_historico = "insert into monitoreo.tb_estatus_crones_historico(id_cron,fecha_registro) values (4,now())";
    $query3 = $conn->prepare($actualiza_cron_historico);
    $query3->execute();
    $query3->closeCursor();    
    
    $actualiza_cron = "update monitoreo.tb_estatus_crones set ultimo_registro=now() where id_cron=4";
    $query4 = $conn->prepare($actualiza_cron);
    $query4->execute();
    $query4->closeCursor();
$query->closeCursor();
$query0->closeCursor();

}
?>