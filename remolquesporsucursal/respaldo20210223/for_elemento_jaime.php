<?php include ('general/estilo.php') ?>
<?php include("funciones/autofiltronuevo.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form1" method="post">
<div class="container-fluid">
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos"/>
      </div>
      <?php $variable="economico"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
    	Ecónomico
		  <?php   include ("general/for_orden.php"); ?>
      </div>
      <?php $variable="serie"?>
      <div class="col-md-2 negritas centrado" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Serie
      <?php   include ("general/for_orden.php"); ?>
      </div>
      <?php $variable="circuito"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Circuito
      <?php   include ("general/for_orden.php"); ?>
      </div>
      <?php $variable="especial"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Especial
      <?php   include ("general/for_orden.php"); ?>
      </div>
      <div class="col-md-2 negritas centrado">
        Latitud,Longitud
      </div>
      <div class="col-md-1 negritas centrado">
        Mapa
      </div>
      <div class="col-md-3 negritas centrado"><strong>Acciones</strong></div>
    </div>
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-1">
        <?php autofiltronuevo("txt_economico_veh","tb_vehiculos","economico",$conn) ?>
      </div>
      <div class="col-md-2 centrado">
        <?php autofiltronuevo("num_serie_veh","tb_vehiculos","serie",$conn) ?>
      </div>
      <div class="col-md-1 centrado">
        <?php
          $consulta2  = " SELECT distinct(txt_nombre_cir) as campo FROM tb_circuitos ORDER BY txt_nombre_cir ASC ";
          $query2 = $conn->prepare($consulta2);
          $query2->execute();
          $seleccionado="";
        ?>
        <select id="#circuito" name="circuito" class="filtro">
          <option value="-1">Ver todos</option>
          <?php
            while ($registro2 = $query2->fetch()) {
                if(isset($_GET["circuito"]))
                    if($_GET["circuito"]==trim($registro2["campo"]))
                        $seleccionado="selected";
                    else
                        $seleccionado="";
          ?>
          <option value="<?php echo $registro2['campo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["campo"] ?></option>
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
      <div class="col-md-1"><?php echo $registro["txt_economico_veh"]; ?></div>
      <div class="col-md-2 centrado"><?php echo $registro["num_serie_veh"]?></div>
      <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_cir"]?></div>
      <div class="col-md-1 centrado"><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No"; ?></div>
      <div class="col-md-2 centrado"><?php echo $registro["num_latitud_veh"].",".$registro["num_longitud_veh"]?></div>
      <div class="col-md-1 centrado">
        <a href="index.php?seccion=mapa&amp;accion=muestra&amp;economico=<?php echo $registro['txt_economico_veh'] ?>" target="_blank">
          <button type="button" class="btn btn-xs btn-primary">VER MAPA</button>
        </a>
      </div>
      <?php if ($_SESSION['rol'] == 37 || $_SESSION['rol'] == 50) {?>
			<div class="col-md-1 centrado">
        <a href="?seccion=vehiculos&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_veh"]; ?>"><button type="button" class="btn btn-primary btn-xs edita">EDITAR</button></a>
      </div>
      <?php if (isset($_SESSION["altaybajadevehiculos"])) { ?>
      <div class="col-md-1 centrado">
        <button data-id="<?php echo $registro[$campoId];?>" type="button" class="btn btn-danger btn-xs borra">BORRAR</button>
      </div>
    <?php }  } ?>
    </div>


  <?php } ?>
    <div class="row renglon">
      <div class="col-md-1">
        <?php if ($_SESSION['rol'] == 37 || $_SESSION['rol'] == 50) {
           if (isset($_SESSION["altaybajadevehiculos"])) { ?>
        <button  type="submit" class="btn btn-danger btn-xs" id="borratodos">BORRAR SELECCIONADOS</button>
      <?php } } ?>
        <?php   include ("general/for_filtros.php"); ?>
      </div>
    </div>
</div>
</form>

<?php $query->closeCursor(); ?>

<?php include("admin/vehiculos/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>

<script>

  $('.adjuntarimagen').click(function () {
    $('#identrada').val($(this).data('id'));

  });

</script>
