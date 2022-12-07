<?php
 
  $numerodevehiculos=$_POST["numerodevehiculos"];
  $circuito=$_POST["circuito"];

  for ($i = 0; $i < $numerodevehiculos; $i++) {
      if(isset($_POST["vehiculo$i"]))
      {
       
      $id=$_POST["vehiculo$i"];
      $consulta  = " UPDATE tb_vehiculos SET fk_clave_cir=? WHERE txt_economico_veh=?";  
      $query = $conn->prepare($consulta);
      $query->bindParam(1, $circuito);
      $query->bindParam(2, $id);
      $query->execute();  
      
      }
  } 

  $redireccionar="?seccion=".$seccion."&accion=lista";

  if(isset($_POST["rxp"]))
    $redireccionar.="&rxp=".$_POST["rxp"];
  if(isset($_POST["orden"]))
    $redireccionar.="&orden=".$_POST["orden"];
  if(isset($_POST["busca"]))
    $redireccionar.="&busca=".$_POST["busca"];  
  if(isset($_POST["inicia"]))
    $redireccionar.="&inicia=".$_POST["inicia"];  

?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>