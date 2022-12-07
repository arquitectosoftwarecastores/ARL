<?php

  $nombre=$_POST["nombre"];  
  $origen=$_POST["origen"];
  $destino=$_POST["destino"];

  $consulta  = "INSERT INTO tb_rutas
				(txt_nombre_rut,fk_clave_zon1,fk_clave_zon2)
		        VALUES (?,?,?)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $nombre);
  $query->bindParam(2, $origen);
  $query->bindParam(3, $destino);
  $query->execute();    
  $query->closeCursor();

  $consulta1  = "SELECT MAX(pk_clave_rut) as maximo FROM tb_rutas";
  $query1 = $conn->prepare($consulta1);
  $query1->execute();    
  $registro1 = $query1->fetch();

 // $redireccionar="?seccion=rutas&accion=mapa&id=".$registro1["maximo"];

  $redireccionar="?seccion=rutas&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>