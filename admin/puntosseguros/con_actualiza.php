<?php
  $id=$_POST["id"];
  $nombre=$_POST["nombre"];  
  $latitud=$_POST["latitud"];
  $longitud=$_POST["longitud"];
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];

  $consulta  = "UPDATE tb_puntosseguros
				SET 
				txt_nombre_pun=?,
        num_latitud_pun=?,
        num_longitud_pun=?,
        fk_clave_mun=?,
        num_tipo_pun=?
				WHERE pk_clave_pun = ? ";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $latitud);
  $query->bindParam(3, $longitud);
  $query->bindParam(4, $ciudad);
  $query->bindParam(5, $tipo);
  $query->bindParam(6, $id);

  $query->execute();    

  $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>