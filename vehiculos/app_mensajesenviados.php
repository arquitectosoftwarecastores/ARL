<?php 
  include ('../conexion/conexion.php');
?>
<table class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th>Unidad</th>
      <th>Mensaje</th>
      <th>Fecha-Hora</th>
    </tr> 
  </thead>
  <tbody>       
  <?php 
    $consulta  = " SELECT * FROM tb_mensajesenviadossms, tb_tiposdemensajessms WHERE pk_clave_tipm=fk_clave_tipm ORDER BY fec_fecha_mene DESC LIMIT 100";  
    $query = $conn->prepare($consulta);
    $query->execute();
    while($registro = $query->fetch())
    {
  ?>
    <tr>
      <td><?php echo $registro["txt_economico_veh"]?></td>
      <td><?php echo $registro["txt_nombre_tipm"]?></td>
      <td><?php echo  date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"]))?></td>
    </tr>
  <?php
    }
  ?>
</table>
<?php $query->closeCursor(); ?>