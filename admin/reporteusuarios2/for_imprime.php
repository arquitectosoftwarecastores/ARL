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
        <th>Económico</th>
        <th>Serie</th>
        <th>Circuito</th>
        <th>Especial</th>
        <th>Latitud,Longitud</th>
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
		<td><?php echo $registro["txt_economico_veh"]; ?></td>
		<td><?php echo $registro["num_serie_veh"];?></td>
		<td><?php echo $registro["txt_nombre_cir"] ?></td>
		<td><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No"; ?></td>
		<td>
      <?php echo $registro["num_latitud_veh"].",".$registro["num_longitud_veh"] ?>
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