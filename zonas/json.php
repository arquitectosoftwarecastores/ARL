<!--<<!--?php  session_start(); ?>-->    

<?php 
    error_reporting(E_ALL);
    ini_set('display_errors', true);
?>
<?php 
  header('Content-type: application/json');
  include ('../conexion/conexion.php');
  $consulta  = " SELECT * FROM tb_zonas WHERE fk_clave_tipz=3 ORDER BY pk_clave_zon";  
  $query = $conn->prepare($consulta);
  $query->execute();

  $consulta3  = " SELECT COUNT(*) as total FROM tb_zonas WHERE fk_clave_tipz=3 "; 
  $query3 = $conn->prepare($consulta3);
  $query3->execute();
  $registro3 = $query3->fetch();

?>
{
  "zonas": [
<?php  
  $contador=1;
  while($registro = $query->fetch()) { 
    ?>
    {
      "id":<?php echo $registro['pk_clave_zon']?>,      
      "tipo":<?php echo $registro['fk_clave_tipz']?>, 
      "puntos": [
      <?php
        $consulta1  = " SELECT COUNT(*) as total FROM tb_detallezonas WHERE fk_clave_zon=? "; 
        $query1 = $conn->prepare($consulta1);
        $query1->bindParam(1, $registro["pk_clave_zon"]);         
        $query1->execute();
        $registro1 = $query1->fetch();

        $consulta2  = " SELECT * FROM tb_detallezonas WHERE fk_clave_zon=? ORDER BY fk_clave_zon, pk_clave_det ASC"; 
        $query2 = $conn->prepare($consulta2);        
        $query2->bindParam(1, $registro["pk_clave_zon"]); 
        $query2->execute();
        $cuenta=1;
        while($registro2 = $query2->fetch())
        {          
      ?>
          {
            "lat":<?php echo $registro2['num_latitud_zon']?>,
            "long":<?php echo $registro2['num_longitud_zon']?>
          }<?php if($cuenta<$registro1["total"]) echo ",";?>

      <?php
          $cuenta++;
        }
      ?> 
      ]    
    }<?php if($contador<$registro3["total"]) echo ",";?>
<?php
  $contador++; 
  } ?>    
  ]
}