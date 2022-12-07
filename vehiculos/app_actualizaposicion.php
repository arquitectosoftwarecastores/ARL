<?php 
  header('Content-type: application/json');
  include ('../conexion/conexion.php');
  $consulta  = "SELECT * FROM monitoreo.tb_remolques as v, tb_circuitos as c,
                tb_circuitosxusuario as cxu WHERE v.fk_clave_cir=c.pk_clave_cir 
                AND c.pk_clave_cir=cxu.fk_clave_cir AND cxu.fk_clave_usu=? ";
  $query = $conn->prepare($consulta);
  $query->bindParam(1,$_GET["idusuario"]);
  $query->execute();
  $cuenta=0;
?>
{
  "vehiculos": [
<?php  
  while($registro = $query->fetch()) {

     if($registro["num_latitud_rem"]!="Infinity")
      $latitud=$registro["num_latitud_rem"];
     else
      $latitud=0;

     if($registro["num_longitud_rem"]!="Infinity")
      $longitud=$registro["num_longitud_rem"];
     else
      $longitud=0;


   ?>
    {
      "id":<?php echo $registro['pk_clave_rem']?>,          
      "lat":<?php echo $latitud?>, 
      "long":<?php echo $longitud?>
    },
<?php 
  } ?>    
  {
      "id": 0,          
      "lat":0.000000, 
      "long":0.000000
    }]
}
<?php $query->closeCursor(); ?>