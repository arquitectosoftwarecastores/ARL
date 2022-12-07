<?php

// Conexion ARL

$hostARL = '69.172.241.228';
$baseARL = 'db_monitoreo';
$userARL = 'monitoreo';
$passARL = 'monitoreo';

try {
  // Crea conexion al ARL
  $conn = new PDO('pgsql:host=' . $hostARL . ';port=5432;dbname=' . $baseARL . ';user=' . $userARL . ';password=' . $passARL);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // Problemade conexión
  echo $e->getMessage();
}

// Conexion 13

$host13 = '192.168.0.13';
$user13 = 'usuarioWin';
$pass13 = 'windows';

try {
  $bd13 = new PDO('mysql:host=' . $host13 . ';port=3306;', $user13, $pass13);
  $bd13->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  echo 'Ocurrió algo con la base de datos 13: ' . $e->getMessage();
}

// Conexion 23

$host23 = '192.168.0.23';
$user23 = 'usuarioWin';
$pass23 = 'windows';

try {
  $db23 = new PDO('mysql:host=' . $host23 . ';port=3306;', $user23, $pass23);
  $db23->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  echo 'Ocurrió algo con la base de datos 23: ' . $e->getMessage();
}
