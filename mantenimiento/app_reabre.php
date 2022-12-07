 <?php

  $economico=$_POST["atendidaideconomico"];
  $dia=date("Y/m/d",strtotime($_POST["atendidaidfechahora"]));

  $tipoalerta=$_POST["atendidaidtipoalerta"];

  $consulta  = " UPDATE tb_alertas SET num_estatus_ale=0 , fk_clave_usu=0 WHERE fk_clave_tipa=? AND num_estatus_ale=1 AND txt_economico_veh=? AND date_trunc('day', fec_fecha_ale)=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $tipoalerta);
  $query->bindParam(2, $economico);
  $query->bindParam(3, $dia);
  $query->execute();

  $redireccionar="?seccion=alertas&accion=lista";

  if(isset($_POST["rxp"]))
    $redireccionar.="&rxp=".$_POST["rxp"];
  if(isset($_POST["orden"]))
    $redireccionar.="&orden=".$_POST["orden"];
  if(isset($_POST["busca"]))
    $redireccionar.="&busca=".$_POST["busca"];  
  if(isset($_POST["inicia"]))
    $redireccionar.="&inicia=".$_POST["inicia"];  
  if(isset($_POST["economico"]))
    $redireccionar.="&economico=".$_POST["economico"];
  if(isset($_POST["from"]))
    $redireccionar.="&from=".$_POST["from"];
  if(isset($_POST["to"]))
    $redireccionar.="&to=".$_POST["to"];
  if(isset($_POST["prioridad"]))
    $redireccionar.="&prioridad=".$_POST["prioridad"];
  if(isset($_POST["estatus"]))
    $redireccionar.="&estatus=".$_POST["estatus"]; 


?>
<script>
   window.location.href = "<?php echo  $redireccionar; ?>";
</script>