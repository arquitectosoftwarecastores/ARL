<?php
  $nombre=$_POST["nombre"];  
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];
  echo $nombre."<br>";
  echo $ciudad."<br>";
  echo $tipo."<br>";

  /* Inserta en tb_zonas */
  $consulta  = "INSERT INTO tb_zonas (txt_nombre_zon,fk_clave_mun,fk_clave_tipz,num_latitudcen_zon, num_longitudcen_zon) VALUES (?,?,?,19.5,-99.5)";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $nombre);
  $query->bindParam(2, $ciudad);
  $query->bindParam(3, $tipo);
  $query->execute();    
  $ultimoinsertado = $conn -> lastInsertId();
  $query->closeCursor();
  
  // Obtenemos el útlimo id de tb_zonas
  $consulta1  = "SELECT MAX(pk_clave_zon) as maximo FROM tb_zonas";
  $query1 = $conn->prepare($consulta1);
  $query1->execute();    
  $registro1 = $query1->fetch();
  echo $registro1["maximo"]."<br>";  

  /* Inserta en tb puntos seguros */
  $consulta2  = "INSERT INTO tb_puntosseguros (txt_nombre_pun,num_latitud_pun,num_longitud_pun, num_tipo_pun,fk_clave_mun,pk_clave_zon) VALUES (?,19.5,-99.5,?,?,?)";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $nombre);
  $query2->bindParam(2, $tipo);
  $query2->bindParam(3, $ciudad);
  $query2->bindParam(4, $registro1["maximo"]);
  $query2->execute();    
 
    /* Inserta en tb detalle zonas */
    $consulta3  = "INSERT INTO tb_detallezonas (fk_clave_zon,num_latitud_zon,num_longitud_zon) 
    VALUES (?,19,-99)";
    $query3 = $conn->prepare($consulta3);
    $query3->bindParam(1, $registro1["maximo"]);
    $query3->execute();

    $consulta4  = "INSERT INTO tb_detallezonas (fk_clave_zon,num_latitud_zon,num_longitud_zon) 
    VALUES (?,19,-100)";
    $query4 = $conn->prepare($consulta4);
    $query4->bindParam(1, $registro1["maximo"]);
    $query4->execute();
    
    $consulta5  = "INSERT INTO tb_detallezonas (fk_clave_zon,num_latitud_zon,num_longitud_zon) 
    VALUES (?,20,-100)";
    $query5 = $conn->prepare($consulta5);
    $query5->bindParam(1, $registro1["maximo"]);
    $query5->execute();
    
    $consulta6  = "INSERT INTO tb_detallezonas (fk_clave_zon,num_latitud_zon,num_longitud_zon) 
    VALUES (?,20,-99)";
    $query6 = $conn->prepare($consulta6);
    $query6->bindParam(1, $registro1["maximo"]);
    $query6->execute();    

    $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>