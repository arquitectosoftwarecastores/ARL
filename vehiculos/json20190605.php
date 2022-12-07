<?php  
  header('Content-type: application/json');
  include ('../conexion/conexion.php');
  $consulta  = "SELECT * FROM monitoreo.tb_vehiculos as v 
                LEFT JOIN geocercasporunidad gpu on gpu.economico = v.txt_economico_veh
                LEFT JOIN vehiculos_inmovilizados vi ON vi.noeconomico = v.txt_economico_veh, 
                tb_circuitos as c,tb_circuitosxusuario as cxu WHERE v.fk_clave_cir=c.pk_clave_cir 
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
     if($registro["num_latitud_veh"]!="Infinity")
      $latitud=$registro["num_latitud_veh"];
     else
      $latitud=0;
     if($registro["num_longitud_veh"]!="Infinity")
      $longitud=$registro["num_longitud_veh"];
     else
      $longitud=0;
    ?>
    {
      "id":<?php echo $registro['pk_clave_veh']?>,      
      "economico":"<?php echo $registro['txt_economico_veh']?>",      
      "lat":<?php echo $latitud?>, 
      "long":<?php echo $longitud?>,
      "zonaderiesgo":<?php echo $registro['num_zonariesgo_veh']?>,
      "zonaderiesgoreal":"<?php echo $registro['fk_clave_zon']?>",
      "color":<?php if(strlen($registro['txt_tperdida_veh'])) echo 2; else echo 1; ?>,
      "especial":<?php echo $registro['num_seguimientoespecial_veh']?>,
      "inmovilizada":<?php if($registro['automatico']=='' or $registro['automatico']==null) echo 0; else echo 1; ?> 
    },
<?php 
  } ?>    
  {
      "id": 0,      
      "economico":" ",      
      "lat":0.000000, 
      "long":0.000000,
      "zonaderiesgoreal":0,
      "zonaderiesgo":0,
      "color":1,
      "especial":0, 
      "inmovilizada":0         
      }]
}
<?php $query->closeCursor(); ?>