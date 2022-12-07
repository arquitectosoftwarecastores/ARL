<?php
session_start(); 
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
include ('../conexion/conexion.php');
$user = $_SESSION["usuario"];
$id_rol = $_SESSION['rol'];
/*
$consulta = "SELECT txt_nombre_rol from tb_roles where pk_clave_rol = ?";
  $query1 = $conn->prepare($consulta);
  $query1->bindParam(1, $id_rol);  
  $query1->execute();
  $registro1 = $query1->fetch();  
  $rol = $registro1['txt_nombre_rol'];

if ($rol =='Administrador') {*/

    $valor = (int) $_POST["valor"];//VALOR PARA DESACTIVAR O ACTIVAR 
    $id = $_POST["id"];//id de la unidad
    

     $consulta12 = "UPDATE tb_remolques set num_seguimiento_rem = ? where pk_clave_rem = ?";
     $query12 = $conn->prepare($consulta12);
     $query12->bindParam(1, $valor);
     $query12->bindParam(2, $id);
     $query12->execute();
     $query12->closeCursor();

    echo 'Remolque actualizado';
/*}else{

    echo 'no tienes permiso para activar o desactivar unidades';
}*/

?>