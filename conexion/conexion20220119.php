<?php		
	$host ="69.172.241.228";
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

	// Conexion SIAT
	$hostsiat = '172.16.100.26';
	$usersiat = 'usuarioWin';
	$passsiat = 'windows';
	try {
		$bdsiat = new PDO('mysql:host=' . $hostsiat . ';port=3306;', $usersiat, $passsiat);
		$bdsiat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		echo 'Ocurrió algo con la base de datos: ' . $e->getMessage();
	}

?>