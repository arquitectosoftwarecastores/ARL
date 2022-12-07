<?php
  
  $id=$_GET["id"];
  if(isset($_POST["puntos"]))
  {
    $consulta  = "DELETE FROM tb_detallezonas
                  WHERE fk_clave_zon=? ";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $query->execute(); 

    $puntos=$_POST["puntos"];
    for ($x = 1; $x <= $puntos; $x++) {
      $coordenadas = explode(",", $_POST["latlong$x"]);
      $consulta1  = "INSERT INTO tb_detallezonas
            (fk_clave_zon,num_latitud_zon,num_longitud_zon)
            VALUES (?,?,?)";
      $query1 = $conn->prepare($consulta1);
      $query1->bindParam(1, $id);
      $query1->bindParam(2, $coordenadas[0]);
      $query1->bindParam(3, $coordenadas[1]);
      $query1->execute();  
    }  
  }
 
  $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>
