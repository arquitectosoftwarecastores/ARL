<?php 
    date_default_timezone_set("America/Mexico_City");
	include ('../config/conexion.php');
	include ('../posiciones/app_referencia.php');
	$id=$_GET["id"];
	$consulta  = " SELECT * FROM tb_remolques WHERE txt_economico_rem=?";  
	$query = $conn->prepare($consulta);
	$query->bindParam(1, $id);  
	$query->execute();
	$cuenta=0;
	$registro = $query->fetch();
	echo $registro["fec_posicion_rem"].", ";
	echo $registro["txt_georeferencia_cas"].", ";
	echo $registro["txt_georeferencia_mun"];
?>