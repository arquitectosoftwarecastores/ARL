<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");

header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=NombreArchivoExcel.csv");

include ('../conexion/conexion.php');


$strSQL = " select veh.txt_economico_veh as Economico_Primario from monitoreo.avl_secundario sec
join monitoreo.tb_vehiculos veh on veh.num_serie_veh = sec.sec_secundario
where  TO_TIMESTAMP(sec.sec_primarioupos,'YYYYMMDD HH24:MI:SS') < (now() - interval '12 hour')  
and  TO_TIMESTAMP(sec.sec_secundarioupos,'YYYYMMDD HH24:MI:SS') > (now() - interval '12 hour')
order by veh.txt_economico_veh ";

$strSQL2 = "select veh.txt_economico_veh as Economico_Secundario from monitoreo.avl_secundario sec
join monitoreo.tb_vehiculos veh on veh.num_serie_veh = sec.sec_primario
where  TO_TIMESTAMP(sec.sec_secundarioupos,'YYYYMMDD HH24:MI:SS') < (now() - interval '12 hour')  
and  TO_TIMESTAMP(sec.sec_primarioupos,'YYYYMMDD HH24:MI:SS') > (now() - interval '12 hour')
order by veh.txt_economico_veh";

echo "Primario sin reportar \n";
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;

	  while ($registro = $query->fetch()) {
    	echo "\"".$registro["economico_primario"]."\"".";";    	
     	echo "\n";
    }

echo "Secundario sin reportar \n";
    $query2 = $conn->prepare($strSQL2);
    $query2->execute();

    while ($registro2 = $query2->fetch()) {
    	echo "\"".$registro2["economico_secundario"]."\"".";";    	
     	echo "\n";
    }

?>
