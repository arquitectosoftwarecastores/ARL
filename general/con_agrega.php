<?php

  $consulta  = " INSERT INTO ".$Tabla."(".$campoMostrar.") VALUES (?)";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $elemento);
  $elemento=$_POST[$elemento];
  $query->execute();    
  $redireccionar="?seccion=".$seccion."&accion=lista";

  if(isset($_POST["rxp"]))
  	$redireccionar.="&rxp=".$_POST["rxp"];
  if(isset($_POST["orden"]))
  	$redireccionar.="&orden=".$_POST["orden"];
  if(isset($_POST["inicia"]))
    $redireccionar.="&inicia=".$_POST["inicia"];  

  $redireccionar.="&busca=".$elemento;  
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>