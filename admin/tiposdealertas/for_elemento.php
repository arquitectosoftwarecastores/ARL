<?php include ('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
<div class="container-fluid">  
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos"/>
      </div>
      <?php $variable="nombre"?>
      <div class="col-md-3 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
    	Nombre
		  <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="prioridad"?>
      <div class="col-md-2 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Prioridad
      <?php   include ("general/for_orden.php"); ?>
      </div> 
      <?php $variable="ver"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Ver
      <?php   include ("general/for_orden.php"); ?>
      </div> 
      <?php $variable="tipo"?>
      <div class="col-md-1 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Tipo
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="global"?>
      <div class="col-md-1 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Global
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <div class="col-md-3 negritas centrado"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-3">
        <?php autofiltro("txt_nombre_tipa","tb_tiposdealertas","nombre",$conn) ?>
      </div>  
    </div>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
		while ($registro = $query->fetch()) {

      switch ($registro["num_prioridad_tipa"]) {
        case 3:
          $prioridad="Alta";
          break;
        case 2:
          $prioridad="Media";
          break;
        case 1:
          $prioridad="Baja";
          break;          
      }

      switch ($registro["num_ver_tipa"]) {
        case 1:
          $ver="Sí";
          break;
        case 2:
          $ver="No";
          break;        
      }

      switch ($registro["num_tipo_tipa"]) {
        case 0:
          $tipo="Monitoreo";
          break;
        case 1:
          $tipo="Seguridad";
          break;        
      }

      switch ($registro["num_global_tipa"]) {
        case 0:
          $global="No";
          break;
        case 1:
          $global="Sí";
          break;        
      }

	?>
		<div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>			
      <div class="col-md-3"><?php echo $registro["txt_nombre_tipa"]; ?></div>
      <div class="col-md-2 centrado"><?php echo $prioridad?></div>      
      <div class="col-md-1 centrado"><?php echo $ver?></div>
      <div class="col-md-1 centrado"><?php echo $tipo?></div>      
      <div class="col-md-1 centrado"><?php echo $global?></div>   
			<div class="col-md-1 centrado">
        <a href="?seccion=tiposdealertas&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_tipa"]; ?>"><button type="button" class="btn btn-primary btn-xs edita">EDITAR</button></a>
      </div>
      <div class="col-md-1 centrado">
        <button data-id="<?php echo $registro[$campoId];?>" type="button" class="btn btn-danger btn-xs borra">BORRAR</button>
      </div>
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

<?php $query->closeCursor(); ?>

<?php include("admin/tiposdealertas/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>