<?php include ('estilo.php') ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
<div class="container-fluid">
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos"/>
      </div>
      <?php $variable="nombre"?>
      <div class="col-md-8 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
    	Nombre
		<?php   include ("general/for_orden.php"); ?>						    		
      </div>  

      <div class="col-md-2 centrado negritas"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-8">
        <?php include("for_autofiltro.php") ?> 
      </div>       
    </div>
	<?php 	
    	$query = $conn->prepare($strSQL);
    	$query->execute(); 
		while ($registro = $query->fetch()) {
	?>
		<div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>
			<div class="col-md-8"><?php echo $registro[$campoMostrar]; ?></div>
			<div class="col-md-1"><button type="button" class="btn btn-primary btn-xs edita" data-id="<?php echo $registro[$campoId]; ?>" data-valor="<?php echo $registro[$campoMostrar]; ?>" data-toggle="modal" data-target="#edita">EDITAR</button></div>
			<div class="col-md-1"><button data-id="<?php echo $registro[$campoId];?>" type="button" class="btn btn-danger btn-xs borra">BORRAR</button></div>
		</div>
 	
		<?php } ?>
    <div class="row renglon">
      <div class="col-md-1">
        <button  type="submit" class="btn btn-danger btn-xs" id="borratodos">BORRAR SELECCIONADOS</button>
        <?php   include ("general/for_filtros.php"); ?>                        
      </div>
    </div>
</div>
</form>

<?php include("for_nuevo.php") ?>
<?php include("for_cambia.php") ?>
<?php include("jquery.php") ?>
