<?php 
  header('Content-type: application/json');
  include ('../conexion/conexion.php');
  $consulta  = " SELECT * FROM tb_autoridades";  
  $query = $conn->prepare($consulta);
  $query->execute();
  $cuenta=0;
?>
{
  "autoridades": [
<?php  
  while($registro = $query->fetch()) { ?>
    {
      "id":<?php echo $registro['pk_clave_aut']?>,            
      "lat":<?php echo $registro['num_latitud_aut']?>, 
      "long":<?php echo $registro['num_longitud_aut']?>,
      "tipo":<?php echo $registro['fk_clave_tipa']?>            
    },
<?php 
  } ?>    
  {
      "id": 0,         
      "lat":0.000000, 
      "long":0.000000,
      "tipo":1
    }]
}