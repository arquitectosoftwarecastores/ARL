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
        <th>Alerta</th>
        <th>Prioridad</th>
        <th>Estatus</th>
        <th>Acumuladas</th>
        <th>Tiempo</th>
      </tr>
    </thead>
    <tbody>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
	  while ($registro = $query->fetch()) {
      switch ($registro["num_prioridad_tipa"]) {
        case 3:
          $prioridad="Alta";
          $color="fondorojo";
          break;
        case 2:
          $prioridad="Media";
          $color="fondoamarillo";
          break;
        case 1:
          $prioridad="Baja";
          $color="fondoverde";
          break;          
    }
            
    switch ($registro["num_estatus_ale"]) {
        case 0:
          $estatus="Sin atender";
          $colorestatus="rojo";
          break;
        case 1:
          $estatus="Atendida";
          $colorestatus="verde";
    }
	?>
	<tr>
		<td><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_ale"])); ?></td>
		<td><?php echo $registro["txt_economico_veh"]; ?></td>
		<td><?php echo $registro["txt_nombre_tipa"];?></td>
		<td><?php echo $prioridad ?></td>
	      <?php
	        $nombre="";
	        if($estatus=="Atendida") 
	        {
	          $consulta1  = " SELECT * FROM tb_usuarios
	                          WHERE pk_clave_usu=?";  
	          $query1 = $conn->prepare($consulta1);
	          $query1->bindParam(1, $registro["fk_clave_usu"]);
	          $query1->execute();
	          while($registro1 = $query1->fetch())          
	            { 
	              $nombre=$registro1["txt_nombre_usu"]; 
	              $estatus="";
	            }
	        }
	      ?>
		<td><?php echo $estatus.$nombre ?></td>
		<td><?php echo $registro["acumuladas"] ?></td>
		<td>
        <?php 
          $tiempo=str_replace("days","días",$registro["tiempo"]);
          $tiempo=substr($tiempo,0,strlen($tiempo)-10);
          $tiempo=str_replace(":"," hrs. ",$tiempo). " min.";
          echo $tiempo; 
        ?>	
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