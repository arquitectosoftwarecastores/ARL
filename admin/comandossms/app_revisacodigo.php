<?php 
  include ('../../conexion/conexion.php');
  $consulta1  = "SELECT * FROM tb_tiposdemensajessms WHERE pk_clave_tipm=? AND
  				 txt_codigo_tipm=?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $_GET["idmensaje"]);
  $query1->bindParam(2, $_GET["codigo"]);
  $query1->execute();
  $estatus=0;
  while ($registro1 = $query1->fetch())
  	$estatus=1;
  echo $estatus;
?>