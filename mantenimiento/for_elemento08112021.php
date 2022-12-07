﻿<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php include('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<?php include("funciones/autofiltronuevo.php") ?>
<?php include("posiciones/app_referencia.php") ?>

<head>
    <meta http-equiv="refresh" content="300">
</head>
<div class="container-fluid">
    <div class="row renglon">
        <?php $variable = "economico" ?>
        <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Económico
            <?php include("general/for_orden.php"); ?>
        </div>
        <?php $variable = "fecha" ?>
        <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Último Mantenimiento
            <?php include("general/for_orden.php"); ?>
        </div>
        <?php $variable = "fecha" ?>
        <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Próximo Mantenimiento
            <?php include("general/for_orden.php"); ?>
        </div>
        <?php $variable = "dd" ?>
        <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Días Faltantes
            <?php include("general/for_orden.php"); ?>
        </div>
        <div class="col-md-1 negritas centrado">
            Estatus
        </div>
        <?php $variable = "usuario" ?>
        <div class="col-md-2 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Usuario Registro
            <?php include("general/for_orden.php"); ?>
        </div>
        <?php $variable = "Fecha" ?>
        <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Fecha Registro
            <?php include("general/for_orden.php"); ?>
        </div>
        <?php $variable = "Ubicación" ?>
        <div class="col-md-2 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do")) echo "class='ordenado'" ?>>
            Ubicación Remolque
            <?php include("general/for_orden.php"); ?>
        </div>
        <div class="col-md-1 negritas centrado">
            Acciones
        </div>
    </div>
    <?php
    $query = $conn->prepare($strSQL);
    $query->execute();
    $cuentaalertas = 0;
    while ($registro = $query->fetch()) {
        // Extrae Datos
        $economico = $registro['txt_economico_rem'];
        $fecha_mtto = substr($registro["fecha_mtto"], 0, 16);;
        $fecha_sigmtto = substr($registro["fecha_sigmtto"], 0, 10);
        $estatus = "";
        $dias = $registro["dd"];
        $nombre = $registro["txt_nombre_usu"];
        $fecha_reg = substr($registro["fecha_registro"], 0, 16);
        $georeferencia = $registro["txt_georeferencia_cas"];
        $fecha_proxModal = substr($registro["fecha_proxmodal"], 0, 10);

        if (isset($registro["dd"])) {
            $dias = $registro["dd"];
        } else {
            $dias = "0";
        }

        if (isset($registro["dd"])) {
            if ($dias < 0) {
                $estatus = "Desfasado";
            } elseif ($dias < 30) {
                $estatus = "Agendar";
            } else {
                $estatus = "En Tiempo";
            }
        } else {
            $estatus = "Sin Registro";
        }

    ?>
        <div class="row renglon">
            <div class="col-md-1 centrado"><?php echo $economico; ?></div>
            <div class="col-md-1 centrado"><?php echo $fecha_mtto; ?></div>
            <div class="col-md-1 centrado"><?php echo $fecha_sigmtto; ?></div>
            <div class="col-md-1 centrado"><?php echo $dias; ?></div>
            <div class="col-md-1 centrado"><?php echo $estatus; ?></div>
            <div class="col-md-2 centrado"><?php echo $nombre; ?></div>
            <div class="col-md-1 centrado"><?php echo $fecha_reg; ?></div>
            <div class="col-md-2 centrado"><?php echo $georeferencia; ?></div>
            <div class="col-md-1 centrado">
                <button type="button" class="btn btn-primary btn-sm verifica" data-toggle="modal" data-target="#verifica" data-fecha_mtto="<?php echo $fecha_mtto ?>" data-fecha_sigmtto="<?php $fecha_sigmtto; ?>" data-economico="<?php echo $economico ?>" data-usuario_a="<?php echo $nombre ?>" data-fecha_prox="<?php echo $fecha_proxModal ?>" data-tipo="<?php echo $estatus ?>">REGISTRAR</button>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<!-- Modal -->
<div id="modalverificatodos" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Verifica Mantenimientos en grupo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 negritas derecha">
                        <strong>Descripción:</strong>
                    </div>
                    <div class="col-md-9">
                        <textarea class="validate[required] form-control" rows="6" name="observaciones" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p>&nbsp;</p>
                        <button type="submit" id="btnverificatodos" class="btn btn-primary">VERIFICA MANTENIMIENTOS</button>
                    </div>
                </div>
                </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>
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

<?php $query->closeCursor(); ?>
<?php include("general/jquery.php") ?>

<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="verifica" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="./mantenimiento/con_mantenimiento.php" id="form1" method="post">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Mantenimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-3 negritas derecha">
                                <strong>No. Económico:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="verificaeconomico" name="usuario"></div>
                            </div>
                            <div class="col-md-2 negritas derecha">
                                <strong>Estatus:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="estatus_mnt"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 negritas derecha">
                                <strong>Usuario:</strong>
                            </div>
                            <div class="col-md">
                                <div id="usuario_alta"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 negritas derecha">
                                <strong>Ultimo Mant.:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="fecha_mtto"></div>
                                <input type="text" name="fecha_mtto" id="fecha_mtto_h" hidden>
                            </div>
                            <div class="col-md-2 negritas derecha">
                                <strong>Prox. Mant.:</strong>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" id="fecha_sigmtto" name="fecha_sigmtto" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 negritas derecha">
                                <strong>Observaciones:</strong>
                            </div>
                            <div class="col-md-12">
                                <textarea class="validate[required] form-control" rows="3" id="descripcion_b" name="observacion" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 centrado">
                                <?php include("./general/for_filtros.php"); ?>
                                <input type="hidden" id="num_economico" name="economico" value="" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5>Historico de Mantenimientos</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <iframe id="historicoMantenimientos" src="" frameborder="0" width="100%" height="200px"></iframe>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="agrega" class="btn btn-primary">VERIFICAR</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    $('.verifica').click(function() {
        $('#num_economico').val($(this).data('economico'));
        $('#fecha_mtto').html($(this).data('fecha_mtto'));
        $('#fecha_mtto_h').val($(this).data('fecha_mtto'));
        $('#fecha_sigmtto').val($(this).data('fecha_prox'));
        $('#verificaeconomico').html($(this).data('economico'));
        $('#usuario_alta').html($(this).data('usuario_a'));
        $('#verificafechahora').html($(this).data('fechahora'));
        $('#estatus_mnt').html($(this).data('tipo'));
        $('#verificaprioridad').html($(this).data('prioridad'));
        $('#verificaubicacion').html($(this).data('ubicacion'));
        $('#historicoMantenimientos').attr('src', "./mantenimiento/app_table_historico.php?economico=" + $(this).data('economico'))
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

        $("#fecha_sigmtto").datepicker({
            dateFormat: 'yy-mm-dd',
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(selectedDate) {
                $("#fecha_sigmtto").datepicker("option", "minDate", selectedDate);
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