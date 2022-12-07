<?php include('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<?php include("funciones/autofiltronuevo.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
  <div class="container-fluid">
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="todos" />
      </div>
      <?php $variable = "nombre" ?>
      <div class="col-md-3 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Nombre
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "usuario" ?>
      <div class="col-md-2 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Usuario
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "rol" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Rol
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "activo" ?>
      <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Activo
        <?php include("general/for_orden.php"); ?>
      </div>
      <div class="col-md-3 negritas centrado"><strong>Acciones</strong></div>
    </div>
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-3">
        <?php autofiltronuevo("txt_nombre_usu", "tb_usuarios", "nombre", $conn) ?>
      </div>
      <div class="col-md-2 centrado">
        <?php autofiltronuevo("txt_usuario_usu", "tb_usuarios", "usuario", $conn) ?>
      </div>
      <div class="col-md-1 centrado">
        <?php autofiltro("txt_nombre_rol", "tb_roles", "rol", $conn) ?>
      </div>
    </div>
    <?php
    $query = $conn->prepare($strSQL);
    $query->execute();
    while ($registro = $query->fetch()) {
    ?>
      <div class="row renglon">
        <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>" /></div>
        <div class="col-md-3"><?php echo $registro["txt_nombre_usu"]; ?></div>
        <div class="col-md-2 centrado"><?php echo $registro["txt_usuario_usu"] ?></div>
        <div class="col-md-1 centrado"><?php echo $registro["txt_nombre_rol"] ?></div>
        <div class="col-md-1 centrado"><?php if ($registro["num_activo_usu"]) echo "Sí";
                                        else echo "No"; ?></div>
        <?php if ($registro["num_activo_usu"] == 1) { ?>
          <div class="col-md-1 centrado"><a href="?seccion=usuarios&amp;accion=estatus&amp;id=<?php echo $registro[$campoId]; ?>&amp;estatus=0"><button type="button" class="btn btn-sm btn-warning">DESACTIVAR</button></a></div>
        <?php } else { ?>
          <?php if ($registro["num_activo_usu"] == 2) { ?>
            <div class="col-md-1 centrado"><a href="?seccion=usuarios&amp;accion=estatus&amp;id=<?php echo $registro[$campoId]; ?>&amp;estatus=1"><button type="button" class="btn btn-sm btn-danger">CERRAR SESIÓN</button></a></div>
          <?php } else { ?>
            <div class="col-md-1 centrado"><a href="?seccion=usuarios&amp;accion=estatus&amp;id=<?php echo $registro[$campoId]; ?>&amp;estatus=1"><button type="button" class="btn btn-sm btn-info">ACTIVAR</button></a></div>
          <?php } ?>
        <?php } ?>

          <div class="col-md-1 centrado">
            <a href="?seccion=usuarios&amp;accion=cambia&amp;id=<?php echo $registro["pk_clave_usu"]; ?>"><button type="button" class="btn btn-primary btn-sm edita">EDITAR</button></a>
          </div>

        <div class="col-md-1 centrado">
          <button data-id="<?php echo $registro[$campoId]; ?>" type="button" class="btn btn-danger btn-sm borra">BORRAR</button>
        </div>
      </div>


    <?php } ?>
    <div class="row renglon">
      <div class="col-md-1">
        <button type="submit" class="btn btn-danger btn-sm" id="borratodos">BORRAR SELECCIONADOS</button>
        <?php include("general/for_filtros.php"); ?>
      </div>
    </div>
  </div>
</form>

<?php $query->closeCursor(); ?>

<?php include("admin/usuarios/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>

<script>
  $('.adjuntarimagen').click(function() {
    $('#identrada').val($(this).data('id'));

  });
</script>