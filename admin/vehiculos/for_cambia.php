<?php

$id = $_GET["id"];
$consulta1  = " SELECT * FROM tb_remolques 
                 WHERE pk_clave_rem = ?";
$query1 = $conn->prepare($consulta1);
$query1->bindParam(1, $id);
$query1->execute();
$registro1 = $query1->fetch();

$consulta2  = "SELECT * FROM tb_circuitos 
                ORDER BY txt_nombre_cir ASC";
$query2 = $conn->prepare($consulta2);
$query2->execute();

$consulta1  = "SELECT * 
                FROM tb_tiposderemolque 
                ORDER BY idtipo ASC";
$qryTR = $conn->prepare($consulta1);
$qryTR->execute();


?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h1>Modifica Remolque</h1>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <form action="?seccion=vehiculos&amp;accion=actualiza&amp;id=<?php echo $id; ?>" id="form1" method="post">
        <fieldset>
          <div class="row">
            <div class="col-md-12">
              Número ecónomico:
              <input type="text" name="numero" id="numero" class="form-control" size="120" maxlength="120" value="<?php echo $registro1["txt_economico_rem"] ?>" <?php if (!isset($_SESSION["altaybajadevehiculos"])) echo "readonly"; ?> />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              Serie:<input type="text" name="serie" id="serie" class="form-control" size="25" maxlength="20" value="<?php echo $registro1["txt_nserie_rem"] ?>" <?php if (false
                                                                                                                                                                  /**!isset($_SESSION["altaybajadevehiculos"])*/
                                                                                                                                                                ) echo "readonly"; ?> />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              Circuito:
              <select name="circuito" id="circuito" class="form-control">
                <?php while ($registro2 = $query2->fetch()) {
                  if ($registro1['fk_clave_cir'] == $registro2["pk_clave_cir"])
                    $seleccionado = "selected";
                  else
                    $seleccionado = "";
                ?>
                  <option value="<?php echo $registro2["pk_clave_cir"]; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["txt_nombre_cir"]; ?></option>
                <?php  } ?>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              Tipo:
              <select name="tipo" id="tipo" class="form-control">
                <?php while ($resTR = $qryTR->fetch()) {
                  if ($resTR['idtipo'] == $registro1["num_tipo_rem"])
                    $seleccionado = "selected";
                  else
                    $seleccionado = "";
                ?>
                  <option value="<?php echo $resTR["idtipo"]; ?>" <?php echo $seleccionado; ?>><?php echo $resTR["nombre"]; ?></option>
                <?php  } ?>
              </select>
            </div>
          </div>


          <div class="row">
            <div class="col-md-12 centrado renglon">
              <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 centrado">
              <a href="?seccion=vehiculos">
                <button type="button" class="btn btn-warning">REGRESAR</button>
              </a>
            </div>
          </div>

        </fieldset>
      </form>
    </div>
  </div>
</div>
</div>
<script>
  $("#circuito").change(function() {

    if ($(this).val() == "30") {
      $("#div_carga").css("display", "block");
      $("#num_carga").addClass("validate[required] text-input text form-control");
      $("#num_carga").css("display", "block");
    } else {
      $("#div_carga").css("display", "none");
      $("#num_carga").css("display", "none");
      $("#num_carga").removeClass("validate[required] text-input text form-control");
    }

  });

  window.addEventListener('load', initNumCarga, false);

  function initNumCarga() {
    if ($("#num_carga").val() === "") {
      $("#div_carga").css("display", "none");
      $("#num_carga").css("display", "none");
      $("#num_carga").removeClass("validate[required] text-input text form-control");

    } else {
      $("#div_carga").css("display", "block");
      $("#num_carga").css("display", "block");
      $("#num_carga").addClass("validate[required] text-input text form-control");

    }
  }
</script>
<?php $query1->closeCursor(); ?>
<?php $query2->closeCursor(); ?>