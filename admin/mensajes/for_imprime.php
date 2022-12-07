<?php
  include("funciones/distancia.php");
  include("posiciones/app_referencia.php");
?>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the current printer page size */
        margin: 5mm;  /* this affects the margin in the printer settings */
    }

    body 
    {
        background-color:#FFFFFF; 
        margin: 0px;  /* the margin on the content before printing */
   }

 a[href]:after {
    content: none !important;
  }

</style>

<style type="text/css">
   #menuprincipal {
    display: none;
   }

   #botonmenu {
    display: none;
   }

   #logo {
    width: 150px;
   }
</style>

<table class="table table-striped table-bordered table-hover">
    <thead> 
      <tr>   	
        <th>Fecha</th>
        <th>Económico</th>
        <th>Mensaje</th>
        <th>Usuario</th>
        <th>Comentario</th>
        <th>Ubicación</th>
      </tr>
    </thead>
    <tbody>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
	  while ($registro = $query->fetch()) {
	?>
	<tr>
		<td><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])); ?></td>
		<td><?php echo $registro["txt_economico_veh"]; ?></td>
		<td><?php echo $registro["txt_nombre_tipm"];?></td>
		<td><?php echo $registro["txt_nombre_usu"] ?></td>
		<td><?php echo $registro["txt_comentario_mene"] ?></td>
		<td><?php echo georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).";"; ?>
      
    </td>        
    </tr>
	<?php } ?>
  </tbody>  
</table>
<?php $query->closeCursor(); ?>
<script type="text/javascript">
    window.onload = function () {
    window.print();
}
</script>