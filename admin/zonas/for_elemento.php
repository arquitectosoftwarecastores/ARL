<?php include("funciones/autofiltro.php") ?>   
<?php include ('general/estilo.php') ?>
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
      <?php $variable="tipo"?>
      <div class="col-md-1 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Tipo
      <?php   include ("general/for_orden.php"); ?>
      </div>   
      <div class="col-md negritas centrado"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-2">
        <?php autofiltro("txt_nombre_zon","tb_zonas","nombre",$conn) ?>
      </div>    
      <div class="col-md-1 centrado">
      <?php
        $consulta7  = " SELECT  pk_clave_mun, txt_nombre_mun FROM tb_municipios WHERE pk_clave_mun IN (SELECT fk_clave_mun FROM tb_zonas ) ORDER BY txt_nombre_mun ASC ";  
        $query7 = $conn->prepare($consulta7);
        $query7->execute();  
        $seleccionado="";
      ?>
        <select id="ciudad" name="ciudad" class="filtro">
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
      <div class="col-md-1 centrado">
        <?php autofiltro("txt_nombre_tipz","tb_tiposdezona","tipo",$conn) ?>
      </div> 
    </div>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
		while ($registro = $query->fetch()) {
	?>
		<div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>			
      <div class="col-md-2"><?php echo $registro["txt_nombre_zon"]; ?></div>
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_mun"]?></div>      
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_edo"]?></div>  
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_tipz"]?></div>      
      <div class="col-md centrado">
        <a href="?seccion=zonas&amp;accion=mapa&amp;id=<?php echo $registro["pk_clave_zon"]; ?>">
          <button type="button" class="btn btn-info btn-xs">DIBUJAR
          </button>
        </a>
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


<?php include("admin/zonas/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>