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
        <?php //Despliega en la ventana un textfield con pidiendo el origen           
        ?>    
      <?php $variable="origen"?>
      <div class="col-md-2 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Origen 
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="destino"?>
      <div class="col-md-2 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Destino
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <div class="col-md-3 negritas centrado"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-2">
        <?php autofiltro("txt_nombre_rut","tb_rutas","nombre",$conn) ?>
      </div>    
      <div class="col-md-1 centrado">
      <?php
        $consulta7  = " SELECT  * FROM tb_zonas WHERE pk_clave_zon IN (SELECT fk_clave_zon1 FROM tb_rutas ) ORDER BY txt_nombre_zon ASC ";  
        $query7 = $conn->prepare($consulta7);
        $query7->execute();  
        $seleccionado="";
      ?>
        <select id="origen" name="origen" class="filtro form-control">
          <option value="0">Ver todos</option>                    
          <?php
            while ($registro7 = $query7->fetch()) {
                if(isset($_GET["origen"]))
                    if($_GET["origen"]==$registro7["txt_nombre_zon"])
                        $seleccionado="selected";
                    else
                        $seleccionado="";
          ?>
          <option value="<?php echo $registro7['pk_clave_zon']; ?>" <?php echo $seleccionado; ?>><?php echo $registro7["txt_nombre_zon"] ?></option>
          <?php } ?>
        </select>  
      </div> 
      <div class="col-md-1 centrado">
      <?php
        $consulta8  = " SELECT  * FROM tb_zonas WHERE pk_clave_zon IN (SELECT fk_clave_zon2 FROM tb_rutas ) ORDER BY txt_nombre_zon ASC ";  
        $query8 = $conn->prepare($consulta8);
        $query8->execute();  
        $seleccionado="";
      ?>
        <select id="destino" name="destino" class="filtro form-control">
          <option value="0">Ver todos</option>                    
          <?php
            while ($registro8 = $query8->fetch()) {
                if(isset($_GET["destino"]))
                    if($_GET["destino"]==$registro8["txt_nombre_zon"])
                        $seleccionado="selected";
                    else
                        $seleccionado="";
          ?>
          <option value="<?php echo $registro8['pk_clave_zon']; ?>" <?php echo $seleccionado; ?>><?php echo $registro8["txt_nombre_zon"] ?></option>
          <?php } ?>
        </select>  
      </div> 
    </div>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
		while ($registro = $query->fetch()) {
	?>
      <div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>			
      <div class="col-md-2"><?php echo $registro["txt_nombre_rut"]; ?></div>
      <div class="col-md-2 centrado"><?php echo $registro["origen"]?></div>      
      <div class="col-md-2 centrado"><?php echo $registro["destino"]?></div>  
      <div class="col-md-1 centrado">
        <a href="?seccion=rutas&amp;accion=mapa&amp;id=<?php echo $registro["pk_clave_rut"]; ?>">
          <button type="button" class="btn btn-info btn-xs">VER RUTA EN MAPA
          </button>
        </a>
      </div>      
      <div class="col-md-1 centrado">
        <a href="?seccion=rutas&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_rut"]; ?>"><button type="button" class="btn btn-primary btn-xs edita" >EDITAR</button></a>
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
<?php include("admin/rutas/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>