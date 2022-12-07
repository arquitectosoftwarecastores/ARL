<?php include ('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
<div class="container-fluid">  
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos"/>
      </div>
      <?php $variable="nombre"?>
      <div class="col-md-2 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
    	Nombre
		  <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="direccion"?>
      <div class="col-md-2 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Dirección 
      <?php   include ("general/for_orden.php"); ?>
      </div> 
      <?php $variable="colonia"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Colonia
      <?php   include ("general/for_orden.php"); ?>
      </div> 
      <?php $variable="ciudad"?>
      <div class="col-md-1 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Ciudad 
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="estado"?>
      <div class="col-md-1 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Estado
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <div class="col-md-4 negritas centrado"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div> 
      <div class="col-md-2">
        <?php autofiltro("txt_nombre_emp","tb_empresas","nombre",$conn) ?>
      </div>   
      <div class="col-md-2"></div> 
      <div class="col-md-1"></div> 
      <div class="col-md-1 centrado">
      <?php
        $consulta7  = " SELECT  pk_clave_mun, txt_nombre_mun FROM tb_municipios WHERE pk_clave_mun IN (SELECT fk_clave_mun FROM tb_empresas ) ORDER BY txt_nombre_mun ASC ";  
        $query7 = $conn->prepare($consulta7);
        $query7->execute();  
        $seleccionado="";
      ?>
        <select id="ciudad" name="ciudad" class="filtro form-control">
          <option value="0">Ver todos</option>                    
          <?php
            while ($registro7 = $query7->fetch()) {
                if(isset($_GET["ciudad"]))
                    if($_GET["ciudad"]==$registro7["txt_nombre_mun"])
                        $seleccionado="selected";
                    else
                        $seleccionado="";
          ?>
          <option value="<?php echo $registro7['pk_clave_mun']; ?>" <?php echo $seleccionado; ?>><?php echo $registro7["txt_nombre_mun"] ?></option>
          <?php } ?>
        </select>  
      </div> 
      <div class="col-md-1 centrado">
        <?php autofiltro("txt_nombre_edo","tb_estados","estado",$conn) ?>
      </div> 
    </div>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
		while ($registro = $query->fetch()) {
	?>
		<div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>			
      <div class="col-md-2"><?php echo $registro["txt_nombre_emp"]; ?></div>
      <div class="col-md-2 centrado"><?php echo $registro["txt_direccion_emp"]?></div>      
      <div class="col-md-1 centrado"><?php echo $registro["txt_colonia_emp"]?></div>
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_mun"]?></div>      
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_edo"]?></div>      
      <div class="col-md-2"></div>
			<div class="col-md-1 centrado">
        <a href="?seccion=empresas&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_emp"]; ?>"><button type="button" class="btn btn-primary btn-xs edita">EDITAR</button></a>
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

<?php include("admin/empresas/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>
