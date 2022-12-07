<?php
$puntointermedio=array();
$puntointermedio=$_POST["puntointermedio"];
 
for ($i = 2; $i <= sizeof($puntointermedio); $i++) {
     $consulta  = "INSERT INTO puntos_intermedios (clave_rut,clave_zon) VALUES (?,?)";
     $query = $conn->prepare($consulta);
     $query->bindParam(1, $puntointermedio[1]);
     $query->bindParam(2, $puntointermedio[i]);
     $query->execute();    
     $query->closeCursor();     
}

  //Obtiene el último registro de la tabla tb_rutas
  $consulta1  = "SELECT MAX(pk_clave_rut) as maximo FROM tb_rutas";
  $query1 = $conn->prepare($consulta1);
  $query1->execute();    
  $registro1 = $query1->fetch();
  echo "El registro guardado es: ".$registro1; 
  
  $redireccionar="?seccion=rutas&accion=lista";
  
  ?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>