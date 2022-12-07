﻿<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php include ('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<?php include("funciones/autofiltronuevo.php") ?>
<?php include("posiciones/app_referencia.php") ?>
<head>
<meta http-equiv="refresh" content="300">
</head>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=verificaseleccionados" id="form" method="post">
    <div class="container-fluid">
        <div class="row renglon">
            <?php $variable = "economico" ?>
            <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                <span style="font-size:12px">Económico</span>
                <?php include ("general/for_orden.php"); ?>
            </div>
            <?php $variable = "fecha" ?>
            <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Último Mantenimiento
                <?php include ("general/for_orden.php"); ?>
            </div>
            <?php $variable = "fecha" ?>
            <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Próximo Mantenimiento
                <?php include ("general/for_orden.php"); ?>
            </div>
            <?php $variable = "dd" ?>
            <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Días Faltantes
                <?php include ("general/for_orden.php"); ?>
            </div>
            <div class="col-md-1 negritas centrado">
              <strong>Estatus</strong>
            </div>
            <?php $variable = "usuario" ?>
            <div class="col-md-2 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Usuario Registro
                <?php include ("general/for_orden.php"); ?>
            </div>
            <?php $variable = "Fecha" ?>
            <div class="col-md-1 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Fecha Registro
                <?php include ("general/for_orden.php"); ?>
            </div>
            <?php $variable = "Ubicación" ?>
            <div class="col-md-2 negritas centrado" <?php if (isset($_GET["orden"]) && ($_GET["orden"] == $variable . "_up" or $_GET["orden"] == $variable . "_do" )) echo "class='ordenado'" ?> >
                Ubicación Remolque
                <?php include ("general/for_orden.php"); ?>
            </div>
            <div class="col-md-1 negritas centrado">
              <strong>Acciones</strong>
            </div>
        </div>
        <div class="row renglon">
            <div class="col-md-1 centrado">
                <?php
                if (isset($_GET["busca"]))
                    $_GET["economico"] = $_GET["busca"];
                ?>
                <?php autofiltro("txt_economico_rem", "tb_remolques", "txt_economico_rem", $conn) ?>
            </div>
            <div class="col-md-1">
                <?php
                if (isset($_GET["from"]))
                    $from = $_GET["from"];
                else
                    $from = "";
                if (isset($_GET["from"]))
                    $to = $_GET["from"];
                else
                    $to = "";
                ?>
                <table>
                    <tr>
                    <!--     <td>De:&nbsp;</td>  -->
                        <td><input type="text" class="filtro validate[required]" id="from" name="from" size="8" value="<?php echo $from ?>" /></td>
                    <!--    <td>&nbsp;A:&nbsp;</td>
                        <td><input type="text" class="filtro validate[required]" id="to" name="to" size="8" value="<?// php echo $to ?>" /></td> -->
                    </tr>
                </table>
            </div>
            <div class="col-md-1">
                <?php
                if (isset($_GET["from"]))
                    $from = $_GET["from"];
                else
                    $from = "";
                if (isset($_GET["from"]))
                    $to = $_GET["from"];
                else
                    $to = "";
                ?>
                <table>
                    <tr>
                    <!--     <td>De:&nbsp;</td>  -->
                        <td><input type="text" class="filtro validate[required]" id="from" name="from" size="8" value="<?php echo $from ?>" /></td>
                    <!--    <td>&nbsp;A:&nbsp;</td>
                        <td><input type="text" class="filtro validate[required]" id="to" name="to" size="8" value="<?// php echo $to ?>" /></td> -->
                    </tr>
                </table>
            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-1 centrado">
              <select name="alerta" id="#alerta" class="filtro">
                <option value="0">Ver Todos</option>
                <?php
                $selectTipo = 0;
                if (isset($_GET["alerta"])) {
                  $selectTipo = $_GET["alerta"];
                }
                $conF = "SELECT * FROM tb_tiposdefallas";
                $queryF = $conn->prepare($conF);
                $queryF->execute();
                while ($reF = $queryF->fetch()) {
                    $nomF = $reF["txt_nombre_man"];
                    $idF = $reF["pk_clave_man"];
                ?>
                  <option value="<?php echo $idF; ?>" <?php if($selectTipo == $idF ) echo "selected"; ?>>
                    <?php echo $nomF; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-2 centrado">
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
                    <option value="0" <?php if ($selectestatus == 0) echo "selected"; ?>>En Mantenimiento</option>
                    <option value="1" <?php if ($selectestatus == 1) echo "selected"; ?>>Cierre Mantenimiento</option>
                </select>
            </div>
        </div>
        <?php
        $query = $conn->prepare($strSQL);
        $query->execute();
        $cuentaalertas = 0;
        while ($registro = $query->fetch()) {
            ?>
            <div class="row renglon">
                <div class="col-md-1 centrado"><?php echo $registro["txt_economico_rem"]; ?></div>
                <div class="col-md-1 centrado"><?php echo date('d/m/Y H:i:s', strtotime($registro["fecha_mtto"])); ?></div>
                <div class="col-md-1 centrado"><?php echo date('d/m/Y H:i:s', strtotime($registro["fecha_sigmtto"])); ?></div>
                <div class="col-md-1 centrado"><?php 
                if(isset($registro["dd"])){
                    echo $registro["dd"]; 
                }else{
                    echo "0";
                }
                ?></div>
                <div class="col-md-1 centrado"><?php 
                $estatus = "";
                    if(isset($registro["dd"])){
                        if($registro["dd"]< 0){
                            echo "Desfasado";
                            $estatus = "Desfasado";
                        }elseif($registro["dd"]< 30){
                            echo "Agendar";
                            $estatus = "Agendar";
                        }else{
                            echo "En Tiempo";
                            $estatus = "En Tiempo";
                        }                         
                    }else{
                        echo "Sin Registro";
                        $estatus = "Sin Registro";
                    }
                ?></div>

                <?php
                $nombre = "";
                $idGlobal = 0;
                $idGlobal2 = 0;
                    $consulta1 = " SELECT * FROM tb_usuarios
                          WHERE txt_usuario_usu=?";
                    $query1 = $conn->prepare($consulta1);
                    $query1->bindParam(1, $registro["usuarios"]);
                    $query1->execute();
                    while ($registro1 = $query1->fetch()) {
                        $nombre = $registro1["txt_nombre_usu"];
                       // $estatus = "";
                    }                  
                  ?>  
                 <div class="col-md-2 centrado"> 
                  <?php echo $nombre //$nombre ?>
                  </div>    
                  <div class="col-md-1 centrado"><?php echo date('d/m/Y H:i:s', strtotime($registro["fecha_registro"])); ?></div>     
                  <div class="col-md-2 centrado"><?php echo $registro["txt_georeferencia_cas"]; ?></div>
                <div class="col-md-1 centrado">
                    <?php if (/*$registro["usuario_baja"] == NULL*/true) { ?>
                        <button type="button" class="btn btn-primary btn-xs verifica" data-toggle="modal" data-target="#verifica"
                                data-observaciones="<?php echo $registro["observacion"]; ?>"
                                data-fechahora="<?php echo date('d/m/Y H:i:s', strtotime($registro["fecha_registro"])); ?>"
                                data-economico="<?php echo $registro["txt_economico_rem"] ?>"
                                data-usuario_a="<?php echo $nombre ?>"
                                data-tipo="<?php echo $estatus ?>"
                                >REGISTRAR</button>
                            <?php } ?>
                </div> 
            </div>
        <?php } ?>
        <div class="row renglon">
            <div class="col-md-1">
                <?php if ($cuentaalertas > 0) { ?>
                    <button  type="button" class="btn btn-primary btn-xs" id="verificaseleccionados" data-toggle="modal" data-target="#modalverificatodos" >VERIFICAR SELECCIONADOS</button>
                <?php } ?>
                <?php include ("general/for_filtros.php"); ?>
            </div>
        </div>
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
                            <textarea class="validate[required] form-control" rows="6" name="observaciones"maxlength="1000"></textarea>
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
            if($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
        });
        $('#verificaseleccionados').click(function() {
            checkedCount = 0;
            list = document.getElementsByName('registros[]')
            valor=0;
            for (index = 0; index < list.length; ++index) {
                item = list[index];
                if (item.checked) {
                    if($(item).attr('data-tipo')!=valor)
                    {
                        checkedCount++;
                        valor=$(item).attr('data-tipo');
                    }
                }
            }
            if (checkedCount>1)
            {
                alert("SOLO PUEDE VERIFICAR ALERTAS SELECCIONADAS CUANDO SON DEL MISMO TIPO.");
                return false;
            }
        });
        $('#btnverificatodos').click(function(){
            var r=confirm("Esta seguro que desea VERIFICAR los registros seleccionados?");
            if (r==true)
                return true;
            else
                return false;
        });
    </script>
</form>
<?php $query->closeCursor(); ?>
<?php include("general/jquery.php") ?>

<!-- Modal -->
<div id="verifica" class="modal fade" role="dialog">
    <div class="modal-dialog"  style="width: 60%;">
      <form action="mantenimiento/con_mantenimiento.php" id="form1" method="post">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mantenimiento</h4>
            </div>
            <div class="modal-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-2 negritas derecha">
                                <strong>No. Económico:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="verificaeconomico" name="usuario"></div>
                            </div>
                            <div class="col-md-2 negritas derecha">
                                <strong>Mantenimiento:</strong>
                            </div>
                            <div class="col-md-3">
                              <div id="tipo_mantenimiento"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 negritas derecha">
                                <strong>Usuario:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="usuario_alta"></div>
                            </div>
                            <div class="col-md-2 negritas derecha">
                                <strong>Fecha-Hora:</strong>
                            </div>
                            <div class="col-md-3">
                                <div id="verificafechahora"></div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-2 negritas derecha">
                                <strong>Observaciones:</strong>
                            </div>
                            <div class="col-md-10">
                              <div id="verificaobservaciones"></div>
                            </div>
                            <div class="col-md-12">
                                <textarea class="validate[required] form-control" rows="2" id="descripcion_b" name="descripcion"></textarea>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-2 centrado">
                              <?php include ("general/for_filtros.php"); ?>
                              <input type="hidden" id="num_economico" name="num_economico" value="" />
                          </div>
                        </div>
                    </fieldset>
            </div>
            <div class="modal-footer">
                <button type="submit" id="agrega"  class="btn btn-primary">VERIFICAR</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
      </form>
    </div>
</div>

<script>
    $('.verifica').click(function () {
        $('#iframemapa').attr('src', "alertas/app_muestramapa.php?economico="+$(this).data('economico'))
        $('#num_economico').val($(this).data('economico'));
        $('#fechahora').val($(this).data('fechahora'));
        $('#verificaobservaciones').html($(this).data('observaciones'));
        $('#verificaeconomico').html($(this).data('economico'));
        $('#usuario_alta').html($(this).data('usuario_a'));
        $('#verificafechahora').html($(this).data('fechahora'));
        $('#tipo_mantenimiento').html($(this).data('tipo'));
        $('#verificaprioridad').html($(this).data('prioridad'));
        $('#verificaubicacion').html($(this).data('ubicacion'));
        $('#historicoAlertas').attr('src', "alertas/info.php?economico="+$(this).data('economico')+"&idtipoalerta="+$(this).data('idtipoalerta')+"&fechahora="+$(this).data('fechahora'))
    });
</script>

<!-- Modal -->
<div id="atendida" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 85%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h1><strong>Detalles Mantenimiento</strong></h1>
            </div>
            <div class="modal-body">
                <form action="?seccion=mantenimiento&amp;accion=reabre" id="form1" method="post">
                    <fieldset>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-2 negritas derecha">
                                    <strong>No. Económico:</strong>
                                </div>
                                <div class="col-md-2">
                                    <div id="atendidaeconomico"></div>
                                </div>
                                <div class="col-md-2 negritas derecha">
                                  <strong>Mantenimiento:</strong>
                                </div>
                                <div class="col-md-2 ">
                                  <div id="tipo"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2 negritas derecha">
                                    <strong>Fecha-Hora Inicio:</strong>
                                </div>
                                <div class="col-md-2">
                                    <div id="atendidafechahora"></div>
                                </div>
                                <div class="col-md-2 negritas derecha">
                                  <strong>Usuario:</strong>
                                </div>
                                <div class="col-md-4">
                                  <div id="usuario_altaDet"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 negritas derecha">
                                    <strong>Observaciones:</strong>
                                </div>
                                <div class="col-md-10">
                                    <div id="atendidaobservaciones"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2 negritas derecha">
                                    <strong>Fecha-Hora Cierre:</strong>
                                </div>
                                <div class="col-md-2">
                                    <div id="fechahora_bajaDet"></div>
                                </div>
                              <div class="col-md-2 negritas derecha">
                                <strong>Usuario Cierre:</strong>
                              </div>
                              <div class="col-md-3">
                                <div id="usuario_bajaDet"></div>
                              </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 negritas derecha">
                                    <strong>Observaciones:</strong>
                                </div>
                                <div class="col-md-10">
                                    <div id="observaciones_bajaDet"></div>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <h2> Historico de Mantenimientos</h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <iframe id="historicoMante" src="" width="100%" height="200px" frameBorder="0"></iframe>
                                </div>
                            </div>
                        </fieldset>
                </form>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.atendida').click(function () {
        $('#atendidaiframemapa').attr('src', "alertas/app_muestramapa.php?economico="+$(this).data('economico'))
        $('#atendidaobservaciones').html($(this).data('observaciones'));
        $('#atendidafechahora').html($(this).data('fechahora'));
        $('#atendidaeconomico').html($(this).data('economico'));
        $('#atendidaideconomico').val($(this).data('economico'));
        $('#atendidaidfechahora').val($(this).data('fechahora'));
        $('#idG').val($(this).data('economico'));
        $('#usuario_altaDet').html($(this).data('usuario_a'));
        $('#usuario_bajaDet').html($(this).data('usuario_b'));
        $('#tipo').html($(this).data('tipo'));
        $('#fechahora_bajaDet').html($(this).data('fechahora_b'));
        $('#observaciones_bajaDet').html($(this).data('observaciones_b'));
        $('#historicoMante').attr('src', "mantenimiento/info.php?economico="+$(this).data('economico'))
    });
</script>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    $(function() {
        $( "#from" ).datepicker({
            dateFormat: 'yy/mm/dd',
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                $( "#to" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#to" ).datepicker({
            dateFormat: 'yy/mm/dd',
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                $( "#from" ).datepicker( "option", "maxDate", selectedDate );
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
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
</script>