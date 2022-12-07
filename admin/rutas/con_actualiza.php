<?php
  $id=$_POST["id"];
  $nombre=$_POST["nombre"];  
  $origen=$_POST["origen"];
  $destino=$_POST["destino"];

  $consulta  = "UPDATE tb_rutas
                 SET 
                 txt_nombre_rut=?,
                 fk_clave_zon1=?,
                 fk_clave_zon2=?
                WHERE pk_clave_rut=? ";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $nombre);
  $query->bindParam(2, $origen);
  $query->bindParam(3, $destino);
  $query->bindParam(4, $id);
  $query->execute();   

  $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>