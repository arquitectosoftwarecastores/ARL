<?php

  $registros=$_POST["registros"]; 
  $observaciones=$_POST['observaciones'];

  for($i=0; $i<sizeof($registros); $i++)
   { 
      $idalerta=$registros[$i]; 

      $consulta  = " SELECT *, date_trunc('day', fec_fecha_ale) as dia FROM tb_alertas WHERE pk_clave_ale=?";  
      $query = $conn->prepare($consulta);
      $query->bindParam(1, $idalerta);
      $query->execute();
      $registro = $query->fetch();
      $economico=$registro["txt_economico_veh"];
      $dia=$registro["dia"];
      $tipoalerta=$registro["fk_clave_tipa"];

      //echo $idalerta.",".$economico.",".$dia.",".$tipoalerta."<br>";
      //echo "======================"."<br>";

      /*
      $consulta2  = "SELECT *, date_trunc('day', fec_fecha_ale) as dia FROM tb_alertas WHERE fk_clave_tipa=? AND num_estatus_ale=0 AND txt_economico_veh=? AND date_trunc('day', fec_fecha_ale)=?";  
      $query2 = $conn->prepare($consulta2);
      $query2->bindParam(1, $tipoalerta);
      $query2->bindParam(2, $economico);
      $query2->bindParam(3, $dia);
      $query2->execute();
      while($registro2 = $query2->fetch()) {
        echo "Actualiza: ".$registro2["pk_clave_ale"].",".$registro2["txt_economico_veh"].",".$dia.",".$registro2["fk_clave_tipa"]."<br>";
      }
*/

      $consulta1  = " UPDATE tb_alertas SET txt_comentarios_ale=? , num_estatus_ale=1 , fk_clave_usu=?, fec_verifica_ale = now() WHERE fk_clave_tipa=? AND num_estatus_ale=0 AND txt_economico_veh=? AND date_trunc('day', fec_fecha_ale)=?";  
      $query1 = $conn->prepare($consulta1);
      $query1->bindParam(1, $observaciones);
      $query1->bindParam(2, $_SESSION["id"]);
      $query1->bindParam(3, $tipoalerta);
      $query1->bindParam(4, $economico);
      $query1->bindParam(5, $dia);
      $query1->execute();

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