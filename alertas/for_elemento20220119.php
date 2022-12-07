﻿<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php include('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<?php include("funciones/autofiltronuevo.php") ?>
<?php include("posiciones/app_referencia.php") ?>

<head>
  <meta http-equiv="refresh" content="300">
</head>

<form action="?seccion=<?php echo $seccion ?>&amp;accion=verificaseleccionados" id="form" name="form" method="post">
  <div class="container-fluid">
    <div class="row renglon">
      <div class="col-md-1">
        <input type="checkbox" name="todos" id="verificatodos" />
      </div>
      <?php $variable = "fecha" ?>
      <div class="col-md-2 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Fecha
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "economico" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Económico
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "alerta" ?>
      <div class="col-md-1 centrado negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Alerta
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "prioridad" ?>
      <div class="col-md-1 centrado negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Prioridad
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "estatus" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Estatus
        <?php include("general/for_orden.php"); ?>
      </div>
      <?php $variable = "acumuladas" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        <span style="font-size:9px">Acumuladas</span>
        <?php include("general/for_orden.php"); ?>
      </div>
      <!--     -->
      <?php $variable = "fecha" ?>
      <div class="col-md-1 negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Fecha Cierre
        <?php include("general/for_orden.php"); ?>
      </div>

      <?php $variable = "tiempo" ?>
      <div class="col-md-2 centrado negritas" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
        Tiempo
        <?php include("general/for_orden.php"); ?>
      </div>
      <div class="col-md-1 negritas"><strong>Acciones</strong></div>
    </div>
    <div class="row renglon">
      <div class="col-md-1"></div>
      <div class="col-md-2">
        <?php
        if (isset($_GET["from"]))
          $from = $_GET["from"];
        else
          $from = "";
        if (isset($_GET["to"]))
          $to = $_GET["to"];
        else
          $to = "";
        ?>
        <table>
          <tr>
            <td>De:&nbsp;</td>
            <td><input type="text" class="filtro validate[required]" id="from" name="from" size="8" value="<?php echo $from ?>" /></td>
            <td>&nbsp;A:&nbsp;</td>
            <td><input type="text" class="filtro validate[required]" id="to" name="to" size="8" value="<?php echo $to ?>" /></td>
          </tr>
        </table>
      </div>

      <?php

      if ($_SESSION['rol'] != "59") {

      ?>
        <div class="col-md-1 centrado">
          <?php
          if (isset($_GET["busca"]))
            $_GET["economico"] = $_GET["busca"];
          ?>
          <?php autofiltro("txt_economico_rem", "tb_remolques", "economico", $conn) ?>
        </div>
        <div class="col-md-1 centrado">
          <?php autofiltroalerta("txt_nombre_tipa", "tb_tiposdealertas", "alerta", $conn) ?>
        </div>
        <div class="col-md-1 centrado">
          <?php
          $selectprioridad = 0;
          if (isset($_GET["prioridad"])) {
            if ($_GET["prioridad"] == 3)
              $selectprioridad = 3;
            if ($_GET["prioridad"] == 2)
              $selectprioridad = 2;
            if ($_GET["prioridad"] == 1)
              $selectprioridad = 1;
          }
          ?>
          <select name="prioridad" id="prioridad" class="filtro">
            <option value="0">Ver todos</option>
            <option value="3" <?php if ($selectprioridad == 3) echo "selected"; ?>>Alta</option>
            <option value="2" <?php if ($selectprioridad == 2) echo "selected"; ?>>Media</option>
            <option value="1" <?php if ($selectprioridad == 1) echo "selected"; ?>>Baja</option>
          </select>
        </div>
      <?php
      } else {
      ?>
        <div class="col-md-1 centrado">
        </div>
        <div class="col-md-1 centrado">
        </div>
        <div class="col-md-1 centrado">
        </div>
      <?php
      }
      ?>
      <div class="col-md-1 centrado">
        <?php
        $selectestatus = -1;
        if (isset($_GET["estatus"])) {
          if ($_GET["estatus"] == 0)
            $selectestatus = 0;
          if ($_GET["estatus"] == 1)
            $selectestatus = 1;
        }
        ?>
        <select name="estatus" id="estatus" class="filtro">
          <option value="-1" <?php if ($selectestatus == -1) echo "selected"; ?>>Ver todos</option>
          <option value="0" <?php if ($selectestatus == 0) echo "selected"; ?>>Sin atender</option>
          <option value="1" <?php if ($selectestatus == 1) echo "selected"; ?>>Atendida</option>
        </select>
      </div>
    </div>
    <?php
    // echo $strSQL;
    $query = $conn->prepare($strSQL);
    $query->execute();
    $cuentaalertas = 0;
    while ($registro = $query->fetch()) {
      switch ($registro["num_prioridad_tipa"]) {
        case 3:
          $prioridad = "Alta";
          $color = "fondorojo";
          break;
        case 2:
          $prioridad = "Media";
          $color = "fondoamarillo";
          break;
        case 1:
          $prioridad = "Baja";
          $color = "fondoverde";
          break;
      }

      switch ($registro["num_estatus_ale"]) {
        case 0:
          $estatus = "Sin atender";
          $colorestatus = "rojo";
          break;
        case 1:
          $estatus = "Atendida";
          $colorestatus = "verde";
      }
    ?>
      <div class="row renglon">
        <?php
        if ($registro["num_estatus_ale"] == 0) {
          $cuentaalertas++;
        ?>
          <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro["pk_clave_ale"] ?>" data-tipo="<?php echo $registro["pk_clave_tipa"] ?>" /></div>
        <?php } else { ?>
          <div class="col-md-1">&nbsp;</div>
        <?php } ?>
        <div class="col-md-2">
          <?php
          $fechaAle = new DateTime($registro["fec_fecha_ale"], new DateTimeZone('UTC'));
          $fechaAle = $fechaAle->setTimezone(new DateTimeZone('America/Mexico_City'));
          $fechaAle = date_format($fechaAle, 'Y-m-d H:i:s');
          echo $fechaAle;
          ?>
        </div>
        <div class="col-md-1 centrado"><?php echo $registro["txt_economico_rem"]; ?></div>
        <div class="col-md-1 centrado">
          <button type="button" class="btn btn-default btn-xs" onclick="verMapa('<?php echo $registro["txt_economico_rem"]; ?>')">
            <?php echo $registro["txt_nombre_tipa"]; ?>
          </button>

        </div>
        <div class="col-md-1 centrado">
          <div class="circulo <?php echo $color ?> blanco"><?php echo $prioridad ?></div>
        </div>
        <?php
        $nombre = "";
        $idGlobal = 0;
        $idGlobal2 = 0;
        if ($estatus == "Atendida") {
          $consulta1 = " SELECT * FROM tb_usuarios
                          WHERE pk_clave_usu=?";
          $query1 = $conn->prepare($consulta1);
          $query1->bindParam(1, $registro["fk_clave_usu"]);
          $query1->execute();
          while ($registro1 = $query1->fetch()) {
            $nombre = $registro1["txt_nombre_usu"];
            $estatus = "";
          }
        }
        ?>
        <div class="col-md-1 centrado <?php echo $colorestatus ?>">
          <strong><?php echo $estatus . $nombre ?></strong>
        </div>
        <div class="col-md-1 centrado">
          <?php echo $registro["acumuladas"] ?>
        </div>
        <!-- Modificado por Marco Sanchez para ver cuando se cierra un sistema -->
        <!--        <div class="col-md-1">< <!-- ?php echo date('d/m/Y H:i:s',strtotime($registro["fec_verifica_ale"])); ?></div> -->
        <!--    <div class="col-md-1"><<!--?php echo date('d/m/Y H:i:s',strtotime($registro["fec_verifica_ale"])); ?></div>-->
        <div class="col-md-1"><?php echo $registro["fec_verifica_ale"]; ?></div>
        <!-- Fin Modificaciones -->
        <div class="col-md-2 centrado">
          <?php
          $tiempo = str_replace("day", "día", $registro["tiempo"]);
          $tiempo = substr($tiempo, 0, strlen($tiempo) - 10);
          $tiempo = str_replace(":", " hrs. ", $tiempo) . " min.";
          echo $tiempo;
          ?>
        </div>
        <div class="col-md-1 centrado">
          <?php if ($registro["num_estatus_ale"] == "0") { ?>
            <button type="button" class="btn btn-primary btn-xs verifica" data-toggle="modal" data-target="#verifica" data-observaciones="<?php echo $registro["txt_comentarios_ale"]; ?>" data-fechahora="<?php echo $registro["fec_fecha_ale"] ?>" data-economico="<?php echo $registro["txt_economico_rem"] ?>" data-tipoalerta="<?php echo $registro["txt_nombre_tipa"] ?>" data-idtipoalerta="<?php echo $registro["pk_clave_tipa"] ?>" data-prioridad="<?php echo $prioridad ?>" data-estatus="<?php echo $estatus . $nombre ?>" data-ubicacion="<?php echo $registro["num_latitud_ale"] . ", " . $registro["num_longitud_ale"] ?>" data-unidad="<?php echo $registro["txt_campo1_ale"] ?>" data-oficina="<?php echo $registro["txt_campo4_ale"] ?>" data-viaje="<?php echo $registro["txt_campo2_ale"] ?>" data-motivo="<?php echo $registro["txt_campo5_ale"] ?>">VERIFICAR</button>
          <?php }
          ?>

          <?php
          if ($registro["num_estatus_ale"] == "1") {
            $idGlobal = $registro["txt_economico_rem"];
            $idGlobal2 = $registro["pk_clave_tipa"];

            /*
            $consultaalertas = "SELECT a.fec_fecha_ale as fecha, a.txt_ubicacion_ale as ubicacion, a.alerta_fecha_registro as registro, a.txt_comentarios_ale as comentarios,
                    a.fec_verifica_ale as verifica, u.txt_nombre_usu as usuario from monitoreo.tb_alertas a
                    left join monitoreo.tb_usuarios u on a.fk_clave_usu = u.pk_clave_usu
                    where a.txt_economico_rem = ? and a.fk_clave_tipa = 201 and a.fec_fecha_ale > (now() - '240:00:00'::interval) order by fec_fecha_ale desc limit 10";
            $queryalertas = $conn->prepare($consultaalertas);
            $queryalertas->bindParam(1, $registro["txt_economico_rem"]);
            // $queryalertas->bindParam(2, $idGlobal2);
            $queryalertas->execute();
            */

          ?>
            <button type="button" class="btn btn-default btn-xs atendida" data-toggle="modal" data-target="#atendida" data-observaciones="<?php echo $registro["txt_comentarios_ale"]; ?>" data-fechahora="<?php echo $registro["fec_fecha_ale"] ?>" data-economico="<?php echo $registro["txt_economico_rem"] ?>" data-tipoalerta="<?php echo $registro["txt_nombre_tipa"] ?>" data-idtipoalerta="<?php echo $registro["pk_clave_tipa"] ?>" data-prioridad="<?php echo $prioridad ?>" data-estatus="<?php echo $estatus . $nombre ?>" data-por="<?php echo $nombre ?>" data-idG="<?php echo $registro["txt_economico_rem"] ?>" data-ubicacion="<?php echo $registro["num_latitud_ale"] . "," . $registro["num_longitud_ale"] ?>">CONSULTAR</button>
          <?php // echo $registro["txt_economico_veh"];
          } ?>
        </div>
      </div>
    <?php } ?>
    <div class="row renglon">
      <div class="col-md-1">
        <?php if ($cuentaalertas > 0) { ?>
          <button type="button" class="btn btn-primary btn-xs" id="verificaseleccionados" data-toggle="modal" data-target="#modalverificatodos">VERIFICAR SELECCIONADOS</button>
        <?php } ?>
        <?php include("general/for_filtros.php"); ?>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalverificatodos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Verifica alertas en grupo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md negritas">
              Observaciones:
              <textarea class="validate[required] form-control" rows="6" name="observaciones"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="btnverificatodos" class="btn btn-primary">VERIFICA ALERTAS</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>

</form>


<script>
  $('#verificatodos').change(function() {
    var checkboxes = $(this).closest('form').find(':checkbox');
    if ($(this).is(':checked')) {
      checkboxes.prop('checked', true);
    } else {
      checkboxes.prop('checked', false);
    }
  });

  $('#verificaseleccionados').click(function() {
    checkedCount = 0;
    list = document.getElementsByName('registros[]')
    valor = 0;
    for (index = 0; index < list.length; ++index) {
      item = list[index];
      if (item.checked) {
        if ($(item).attr('data-tipo') != valor) {
          checkedCount++;
          valor = $(item).attr('data-tipo');
        }
      }
    }

    if (checkedCount > 1) {
      alert("SOLO PUEDE VERIFICAR ALERTAS SELECCIONADAS CUANDO SON DEL MISMO TIPO.");
      return false;
    }
  });

  $('#btnverificatodos').click(function() {
    var r = confirm("Esta seguro que desea VERIFICAR los registros seleccionados?");
    if (r == true)
      return true;
    else
      return false;
  });
</script>
</form>
<?php $query->closeCursor(); ?>
<?php include("general/jquery.php") ?>


<!-- Modal -->
<div class="modal fade" id="verifica" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Verifica Alerta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="?seccion=alertas&amp;accion=verifica" id="form1" method="post">
          <fieldset>
            <div class="row">
              <div class="col-md-2 negritas derecha">
                No. Económico:
              </div>
              <div class="col-md-2">
                <div id="verificaeconomico"></div>
              </div>
              <div class="col-md-2 negritas derecha">
                Coordenadas:
              </div>
              <div class="col-md-6">
                <div id="verificaubicacion"></div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2 negritas derecha">
                Fecha-hora:
              </div>
              <div class="col-md-2">
                <div id="verificafechahora"></div>
              </div>
              <div class="col-md-2 negritas derecha">
                Alerta:
              </div>
              <div class="col-md-3">
                <div id="verificatipoalerta"></div>
              </div>
              <div class="col-md-1 negritas derecha">
                Prioridad:
              </div>
              <div class="col-md-2">
                <div id="verificaprioridad"></div>
              </div>
            </div>


            <div class="row mt-2 mb-1" id="rowUnidad">
              <div class="col-md-2 negritas derecha">
                Unidad:
              </div>
              <div class="col-md-2" id="verificaunidad">
              </div>
              <div class="col-md-2 negritas derecha">
                Oficina:
              </div>
              <div class="col-md-2" id="verificaoficina">
              </div>
              <div class="col-md-2 negritas derecha">
                Viaje:
              </div>
              <div class="col-md-2" id="verificaviaje">
              </div>
            </div>

            <?php
            $puesto = substr($_SESSION['usuario'], 0, 4);
            if ($puesto != "1138") {
            ?>
              <div class="row">
                <div class="col-md-12">
                  <iframe id="iframemapa" src="../app_muestramapa.php?economico=95820" width="100%" height="300px"></iframe>
                </div>
              </div>
            <?php } ?>
            <div class="row">
              <div class="col-md-2 negritas derecha">
                <strong>Observaciones:</strong>
              </div>
              <div class="col-md-7">
                <textarea oncopy="return false" onpaste="return false" oncut="return false" class="validate[required] form-control" rows="3" id="verificaobservaciones" name="observaciones"> </textarea>
              </div>
              <div class="col-md-2 centrado">
                <?php include("general/for_filtros.php"); ?>
                <input type="hidden" id="num_economico" name="num_economico" value="" />
                <input type="hidden" id="alerta" name="alerta" value="" />
                <input type="hidden" id="idalerta" name="idalerta" value="" />
                <input type="hidden" id="fechahora" name="fechahora" value="" />
                <button type="submit" id="agrega" class="btn btn-primary">VERIFICAR</button>
              </div>
            </div>
          </fieldset>
        </form>
        <!--
        <h4> Historico de Alertas</h4>
        <div class="row">
          <div class="col-md-12">
            <iframe id="historicoAlertas" src="../info.php?economico=95820" width="100%" height="200px" frameBorder="0"></iframe>
          </div>
        </div>-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
      </div>
    </div>
  </div>
</div>


<script>
  $('.verifica').click(function() {
    var puesto = '<?php echo substr($_SESSION['usuario'], 0, 4) ?>';
    if (puesto !== '1138') {
      $('#iframemapa').attr('src', "alertas/app_muestramapaGMAPS.php?economico=" + $(this).data('economico'))
    }

    document.getElementById('rowUnidad').style.display = 'none';

    if (String($(this).data('unidad')).length > 0) {
      document.getElementById('rowUnidad').style.display = '';
    }

    $('#num_economico').val($(this).data('economico'));
    $('#alerta').val($(this).data('tipoalerta'));
    $('#idalerta').val($(this).data('idtipoalerta'));
    $('#fechahora').val($(this).data('fechahora'));
    //$('#verificaobservaciones').html($(this).data('observaciones'));
    $('#verificaeconomico').html($(this).data('economico'));
    $('#verificafechahora').html($(this).data('fechahora'));
    $('#verificatipoalerta').html($(this).data('tipoalerta'));
    $('#verificaprioridad').html($(this).data('prioridad'));
    $('#verificaubicacion').html($(this).data('ubicacion'));
    $('#verificaunidad').html($(this).data('unidad'));
    $('#verificaoficina').html($(this).data('oficina'));
    $('#verificaviaje').html($(this).data('viaje'));
    $('#historicoAlertas').attr('src', "alertas/info.php?economico=" + $(this).data('economico') + "&idtipoalerta=" + $(this).data('idtipoalerta') + "&fechahora=" + $(this).data('fechahora'))
  });
</script>


<!-- Modal -->
<div class="modal fade" id="atendida" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Alerta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="?seccion=alertas&amp;accion=reabre" id="form1" method="post">
          <fieldset>
            <fieldset>
              <div class="row">
                <div class="col-md-2 negritas derecha">
                  <strong>No. Económico:</strong>
                </div>
                <div class="col-md-2">
                  <div id="atendidaeconomico"></div>
                </div>
                <div class="col-md-1 negritas derecha">
                  <strong>Coordenadas:</strong>
                </div>
                <div class="col-md-7">
                  <div id="atendidaubicacion"></div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 negritas derecha">
                  <strong>Fecha-hora:</strong>
                </div>
                <div class="col-md-2">
                  <div id="atendidafechahora"></div>
                </div>
                <div class="col-md-1 negritas derecha">
                  <strong>Alerta:</strong>
                </div>
                <div class="col-md-3">
                  <div id="atendidatipoalerta"></div>
                </div>
                <div class="col-md-1 negritas derecha">
                  <strong>Prioridad:</strong>
                </div>
                <div class="col-md-3">
                  <div id="atendidaprioridad"></div>
                </div>
              </div>
              <?php
              $puesto = substr($_SESSION['usuario'], 0, 4);
              if ($puesto != "1138") {
              ?>
                <div class="row">
                  <div class="col-md-12">
                    <iframe id="atendidaiframemapa" src="./alertas/app_muestramapa.php?economico=95820" width="100%" height="300px"></iframe>
                  </div>
                </div>
              <?php } ?>
              <div class="row">
                <div class="col-md-2 negritas derecha">
                  <strong>Observaciones:</strong>
                </div>
                <div class="col-md-7">
                  <div id="atendidaobservaciones"></div>
                </div>
                <div class="col-md-2 centrado">
                  <p>&nbsp;</p>
                  <?php include("general/for_filtros.php"); ?>
                  <input type="hidden" id="atendidaideconomico" name="atendidaideconomico" value="" />
                  <input type="hidden" id="atendidaidtipoalerta" name="atendidaidtipoalerta" value="" />
                  <input type="hidden" id="atendidaidfechahora" name="atendidaidfechahora" value="" />
                  <!--      <button type="submit" id="agrega" class="btn btn-primary" disabled="true">RE-ABRIR</button>-->
                </div>
              </div>
            </fieldset>
        </form>
        <!--
        <h2> Historico de Alertas</h2>
        <div class="row">
          <div class="col-md-12">
            <iframe id="historicoAlerts" src="./alertas/info.php?economico=95820" width="100%" height="200px" frameBorder="0"></iframe>
          </div>
        </div>
      </div>
      -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>
</div>


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

  $('.atendida').click(function() {
    $('#atendidaiframemapa').attr('src', "./alertas/app_muestramapa.php?economico=" + $(this).data('economico'))
    $('#atendidaobservaciones').html($(this).data('observaciones'));
    $('#atendidafechahora').html($(this).data('fechahora'));
    $('#atendidaeconomico').html($(this).data('economico'));
    $('#atendidatipoalerta').html($(this).data('tipoalerta'));
    $('#atendidaidtipoalerta').val($(this).data('idtipoalerta'));
    $('#atendidaprioridad').html($(this).data('prioridad'));
    $('#atendidaestatus').html($(this).data('estatus'));
    $('#atendidapor').html($(this).data('por'));
    $('#atendidaubicacion').html($(this).data('ubicacion'));
    $('#atendidaideconomico').val($(this).data('economico'));
    $('#atendidaidfechahora').val($(this).data('fechahora'));
    $('#idG').val($(this).data('economico'));
    $('#historicoAlerts').attr('src', "./alertas/info.php?economico=" + $(this).data('economico') + "&idtipoalerta=" + $(this).data('idtipoalerta') + "&fechahora=" + $(this).data('fechahora'))
  });
</script>
<script>
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>


<script>
  $(function() {
    $("#from").datepicker({
      dateFormat: 'yy/mm/dd',
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function(selectedDate) {
        $("#to").datepicker("option", "minDate", selectedDate);
      }
    });
    $("#to").datepicker({
      dateFormat: 'yy/mm/dd',
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function(selectedDate) {
        $("#from").datepicker("option", "maxDate", selectedDate);
      }
    });
  });
</script>

<script>
  $.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '<Ant',
    nextText: 'Sig>',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
  };
  $.datepicker.setDefaults($.datepicker.regional['es']);
</script>

<hr>