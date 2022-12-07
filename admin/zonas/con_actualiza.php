<?php
  $id=$_POST["id"];
  $nombre=$_POST["nombre"];
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];
  $consulta2  = "UPDATE tb_zonas SET txt_nombre_zon=?, fk_clave_tipz=?, fk_clave_mun=?,fecha_mod= NOW(), usuarioalta = ? WHERE pk_clave_zon=? ";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $nombre);
  $query2->bindParam(2, $tipo);
  $query2->bindParam(3, $ciudad);
  $query2->bindParam(4, $_SESSION['usuario']);
  $query2->bindParam(5, $id);
  $query2->execute();

  $redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>