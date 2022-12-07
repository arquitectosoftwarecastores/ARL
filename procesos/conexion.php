<?php

/*
	$host ="localhost";
	$usuario    = "smartfleet";
	$contrasena = "smartfleet";
	$basededatos = "db_monitoreo;castores_avl";
	try{
    	$conn = new PDO("mysql:host=".$host.";dbname=".$basededatos,$usuario, $contrasena,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	 	}
		catch(PDOException $e)
			{
    			echo "ERROR: " . $e->getMessage();
			}  
*/
			
	$host ="69.172.241.230";
	$usuario    = "monitoreo";
	$contrasena = "monitoreo";
	$basededatos = "db_monitoreo;";

	try{
	 // create a PostgreSQL database connection
	 $conn = new PDO("pgsql:host=".$host.";port=5432;dbname=".$basededatos.";user=".$usuario.";password=".$contrasena);
	 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch (PDOException $e){
	 // report error message
	 echo $e->getMessage();
	}

	// Conexion 13

$host13 = "192.168.0.13";
$user13 = "usuarioWin";
$pass13 = "windows";

try {
	$con13 = new PDO('mysql:host='.$host13.';port=3306;', $user13, $pass13);
	$con13->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	echo "Ocurrió algo con la base de datos: " . $e->getMessage();
}



?>