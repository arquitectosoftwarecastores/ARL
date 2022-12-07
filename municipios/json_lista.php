<?php
  include ('../conexion/conexion.php');
  $consulta  = " SELECT * FROM tb_municipios WHERE fk_clave_edo=? ORDER BY txt_nombre_mun ASC";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $id=$_GET["id"];
  $query->execute();  
  
  $cadena="[ ";  
  while ($registro = $query->fetch()) {
    $cadena.='{"optionValue": '.$registro["pk_clave_mun"].', "optionDisplay": "'.$registro["txt_nombre_mun"].'"},';
  }
  $cadena=rtrim($cadena, ",");
  echo $cadena." ]";
?>