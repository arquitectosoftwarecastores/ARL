<?php

  $nombre=$_POST["nombre"];  
  $latitud=$_POST["latitud"];
  $longitud=$_POST["longitud"];
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];

  $consulta  = "INSERT INTO tb_puntosseguros
				(txt_nombre_pun,num_latitud_pun,num_longitud_pun,fk_clave_mun,num_tipo_pun)
		        VALUES (?,?,?,?,?)";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $latitud);
  $query->bindParam(3, $longitud);
  $query->bindParam(4, $ciudad);
  $query->bindParam(5, $tipo);
  $query->execute();    
  $query->closeCursor();
  $redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>