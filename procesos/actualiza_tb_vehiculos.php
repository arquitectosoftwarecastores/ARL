<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
//ini_set('max_execution_time', 240);

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

$consulta = "SELECT * FROM ctg_vehiculos c join monitoreo.tb_vehiculos v on c.veh_nserie = v.num_serie_veh order by fec_posicion_veh asc limit 500";
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
            $hoy = date('Y/m/d H:i:s', time());
            $nombrezinteres = "";

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
    }
    else
        echo "No se encontró, serie:" . $serie . "</p>";
    $contador++;
}
$query->closeCursor();
$query1->closeCursor();
$query2->closeCursor();
}
?>
