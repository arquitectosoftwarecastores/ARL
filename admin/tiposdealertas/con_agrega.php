<?php

  $nombre=$_POST["nombre"];  
  $prioridad=$_POST["prioridad"];
  $tipo=$_POST["tipo"];

  $consulta  = "INSERT INTO tb_tiposdealertas
				        (txt_nombre_tipa,num_prioridad_tipa,num_ver_tipa)
		            VALUES (?,?,?)";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $prioridad);
  $query->bindParam(3, $tipo);
  $query->execute();    
  $query->closeCursor();

  $redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>