<?php include ('general/estilo.php') ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
<div class="container-fluid">
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos"/>
      </div>
      <?php $variable="nombre"?>
      <div class="col-md-4 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Nombre
    <?php   include ("general/for_orden.php"); ?>                   
      </div>  
      <div class="col-md-4 negritas">
        Módulos a los que tiene acceso    
      </div>  
      <div class="col-md-2 centrado negritas"><strong>Acciones</strong></div>  
    </div>  
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-4">
        <?php include("general/for_autofiltro.php") ?> 
      </div>   
      <div class="col-md-4"></div>    
    </div>
  <?php   
      $query = $conn->prepare($strSQL);
      $query->execute(); 
    while ($registro = $query->fetch()) {
  ?>
    <div class="row renglon">
      <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>
      <div class="col-md-4"><?php echo $registro[$campoMostrar]; ?></div>
      <?php 
        $consulta2  = " SELECT * FROM  tb_modulos, tb_modulosxrol WHERE fk_clave_mod=pk_clave_mod AND fk_clave_rol=?";  
        $query2 = $conn->prepare($consulta2);
        $query2->bindParam(1, $registro["pk_clave_rol"]); 
        $query2->execute();
        $lista="";
        while ($registro2 = $query2->fetch()) 
             $lista=$lista.$registro2["txt_nombre_mod"]."<br>";
      ?>
      <div class="col-md-4"><?php echo $lista; ?></div>
      <div class="col-md-1">
        <a href="?seccion=roles&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_rol"]; ?>">
          <button type="button" class="btn btn-primary btn-xs edita">EDITAR</button>
        </a>
      </div>
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

<?php include("general/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>
