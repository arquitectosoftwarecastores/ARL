<?php include('general/estilo.php') ?>
<?php include("funciones/autofiltronuevo.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form1" name="form1" method="post">
  <div class="container-fluid">
    <div class="row renglon">
      <?php $variable = "economico" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Ecónomico
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "fecharegistro" ?>
      <div class="col-md-2 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Fecha de Ingreso
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "fechaactual" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Dias en USA
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "latitud" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Latitud
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "longitud" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up"  or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Longitud
        <?php include("general/for_orden.php"); ?>
      </div>
    </div>
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-1">
        <?php// autofiltronuevo("txt_economico_rem", "tb_remolques", "economico", $conn) ?>
      </div>
      <div class="col-md-2 centrado">
        <?php //autofiltronuevo("txt_nserie_rem", "tb_remolques", "serie", $conn) ?>
      </div>
    </div>
    <?php
    $query = $conn->prepare($strSQL);
    $query->execute();
    while ($registro = $query->fetch()) {
    ?>
      <div class="row renglon">
        <div class="col-md-1"><?php echo $registro["noeconomico"]; ?></div>        
        <div class="col-md-2"><?php echo $registro["fecha_ingreso"]; ?></div>        
        <div class="col-md-1"><?php echo $registro["dias"]; ?></div>  
        <div class="col-md-1"><?php echo $registro["latitud"]; ?></div>        
        <div class="col-md-1"><?php echo $registro["longitud"]; ?></div>  
        </div>
      
    <?php } ?>
      </div>
</form>

<?php $query->closeCursor(); ?>

<?php include("admin/vehiculos/for_nuevo.php") ?>
<?php include("general/jquery.php") ?>

<script>
  function verMapa(unidad) {
    var form = document.createElement("form");
    var element1 = document.createElement("input");

    form.method = "POST";
    form.action = "?seccion=mapa&accion=muestra";

    element1.value = unidad;
    element1.name = "economico";
    form.appendChild(element1);

    document.body.appendChild(form);

    form.submit();
  }

  $('.adjuntarimagen').click(function() {
    $('#identrada').val($(this).data('id'));

  });
</script>