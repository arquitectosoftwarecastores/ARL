<?php
  $id=$_GET["id"];
  $nombre=$_POST["nombre"];  
  $prioridad=$_POST["prioridad"];
  $tipo=$_POST["tipo"];


  $consulta  = "UPDATE  tb_tiposdealertas
				SET 
				txt_nombre_tipa=?,
        num_prioridad_tipa=?,
        num_ver_tipa=?
				WHERE pk_clave_tipa = ? ";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $prioridad);
  $query->bindParam(3, $ver);  
  $query->bindParam(4, $id);
  $query->execute();    

  $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>