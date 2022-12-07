<?php

  $nombre=$_POST["nombre"];  
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];

  echo $nombre."<br>";
  echo $ciudad."<br>";
  echo $tipo."<br>";

  $consulta  = "INSERT INTO tb_zonas
				(txt_nombre_zon,fk_clave_mun,fk_clave_tipz)
		        VALUES (?,?,?)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $nombre);
  $query->bindParam(2, $ciudad);
  $query->bindParam(3, $tipo);
  $query->execute();    
  $query->closeCursor();

  $consulta1  = "SELECT MAX(pk_clave_zon) as maximo FROM tb_zonas";
  $query1 = $conn->prepare($consulta1);
  $query1->execute();    
  $registro1 = $query1->fetch();

  $redireccionar="?seccion=zonas&accion=mapa&id=".$registro1["maximo"];
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>