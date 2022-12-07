<div class="container">
  <div class="row">
    <div class="col-md-1">
      <p>Registros</p>
    </div>
    <div class="col-md-2">
      <select id="rxp" class="form-control form-control-sm">
        <option value="5" <?php if ($rxp == 5) { ?> selected="selected" <?php } ?>>5</option>
        <option value="10" <?php if ($rxp == 10) { ?> selected="selected" <?php } ?>>10</option>
        <option value="20" <?php if ($rxp == 20) { ?> selected="selected" <?php } ?>>20</option>
        <option value="50" <?php if ($rxp == 50) { ?> selected="selected" <?php } ?>>50</option>
        <option value="100" <?php if ($rxp == 100) { ?> selected="selected" <?php } ?>>100</option>
        <option value="500" <?php if ($rxp == 500) { ?> selected="selected" <?php } ?>>500</option>
        <option value="1000" <?php if ($rxp == 1000) { ?> selected="selected" <?php } ?>>1000</option>
        <option value="2000" <?php if ($rxp == 2000) { ?> selected="selected" <?php } ?>>2000</option>
        <option value="5000" <?php if ($rxp == 5000) { ?> selected="selected" <?php } ?>>5000</option>
      </select>
    </div>
    <div class="col-md-2">
      <a href="?seccion=<?php echo $seccion; ?>&amp;accion=lista"><button type="button" class="btn btn-primary btn-sm">VER TODOS</button></a>
    </div>

    <div class="col-md-1">
      <p>Estatus</p>
    </div>

    <?php
    $gEstatus = -1;
    if (isset($_GET["estatus"])) {
      $gEstatus = $_GET["estatus"];
    } else {
      $gEstatus = null;
    }
    ?>

    <div class="col-md-2">
      <select name="estatus" id="estatus" class="filtro form-control form-control-sm">
        <option value="" <?php if ($gEstatus == null) echo "selected"; ?>>Ver todos</option>
        <option value="Sin Registro" <?php if ($gEstatus == 'Sin Registro') echo "selected"; ?>>Sin Registro</option>
        <option value="Agendar" <?php if ($gEstatus == 'Agendar') echo "selected"; ?>>Agendar</option>
        <option value="Desfasado" <?php if ($gEstatus == 'Desfasado') echo "selected"; ?>>Desfasado</option>
        <option value="En Tiempo" <?php if ($gEstatus == 'En Tiempo') echo "selected"; ?>>En Tiempo</option>
      </select>
    </div>

    <div class="col-md-2">
      <input type="text" id="busca" class="form-control form-control-sm" value="<?php if (isset($_GET["busca"])) echo $_GET["busca"]; ?>" placeholder="No. Económico" />
    </div>
    <div class="col-md-1">
      <button type="button" id="buscar" class="btn btn-primary btn-sm">BUSCAR</button>
    </div>



  </div>
</div>