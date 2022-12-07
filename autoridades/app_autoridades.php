<?php 
 	include ('../conexion/conexion.php');
  
  $consulta  = " SELECT * FROM tb_autoridades, tb_municipios, tb_estados, tb_tiposdeautoridades WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo AND fk_clave_tipa=pk_clave_tipa ORDER BY txt_nombre_edo, txt_nombre_mun ASC ";  
  $query = $conn->prepare($consulta);
  $query->execute();
?>
<table class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Teléfonos</th>
      <th>Tipo</th>
      <th>Estado</th>
      <th>Ciudad</th>
    </tr> 
  </thead>
  <tbody>       
  <?php 
    while($registro = $query->fetch())
    {
  ?>
    <tr>
      <td width="30%"><?php echo $registro["txt_nombre_aut"]?></td>
      <td width="40%"><?php echo $registro["txt_telefono1_aut"]."<br>".$registro["txt_telefono2_aut"]?></td>
      <td width="10%"><?php echo $registro["txt_nombre_tipa"]?></td>
      <td width="10%"><?php echo $registro["txt_nombre_edo"]?></td>
      <td width="10%"><?php echo strtoupper($registro["txt_abrev_edo"])?></td>                          
    </tr>
  <?php
    }
   $query->closeCursor();
  ?>
</table>