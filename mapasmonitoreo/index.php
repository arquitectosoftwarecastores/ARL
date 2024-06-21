<?php
//Módulo 6 = Búsqueda de Remolques
$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 6 and pk_clave_usu = ".$_SESSION['id'];
$query = $conn->prepare($consulta);
$query->execute();
$registro = $query->fetch();
$permiso = $registro['total'];
if($permiso>0) { ?>
<head>
    <!-- jQuery plugin -->
    <script src="librerias/jquery.min.js"></script>
    <link rel="stylesheet" href="librerias/maps.css">
    <script src="mapasmonitoreo/con_mapa.js"></script>
    <!-- Include Date Range Picker -->
    <script src="librerias/datetimepicker/moment.min.js"></script>
    <script type="text/javascript" src="librerias/datetimepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="librerias/datetimepicker/daterangepicker.css" />
    <!-- Google Maps API -->
    <?php include("googlemapsapi/key.php") ?>
    <!-- Estilos y Script -->
    <?php  include ('./css/estilo.php');  ?>
    <meta charset="UTF-8">
    <title>Mapas de Monitoreo</title>
</head>

<body>
    <div class="row" style="font-size: small; margin-top: -15px; margin-bottom: 0px; width:100%;">
        <div class="row form-inline">
            <div class="col-sm-4" style="align-content:right;">
                <input type="text" name="idrol" id="idrol" value="<?php echo $_SESSION['rol']?>" hidden>
                <label for="disabledInput" class="control-label" style="text-align: left"></label>
                No. Economico: <input type="text" class="form-control" id="noeconomico" name="noeconomico" maxlength="5" style="height: 25px; text-align: left;width: 25%;">
                <button id="btnBuscar" name="btnBuscar" class="btn btn-danger btn-md" style="height: 25px; font-size: smaller; line-height: 2px;font-weight: bold" onclick="buscarUnidadAhora();">
                    ACEPTAR
                </button>
            </div>
            <div class="col-sm-6" style="align-content:right;">               
                <label for="disabledInput" class="control-label">
                    
                </label>                
                Fecha - Hora: <input type="text" class="form-control" id="daterange" size="250" readonly style="height: 25px; text-align: left;width:50%;">                    
                    <button class="btn btn-warning" id="btnReloj" style="height: 25px;line-height: 0.5;align-items: center;" type="button" onclick="buscarUnidad();">
                        <img src="./assets/icons/time.png" height="15px" width="15px">
                    </button>
            </div>
            <div class="col-sm-2 derecha">
                <div class="input-group form-inline pull-right">
                    <button type="button" id="showStats"  class="btn btn-success btn-md" style="height: 25px; font-size: smaller; line-height: 2px;font-weight: bold;" onclick="muestraDetalles();">
                        DETALLES
                    </button>
                </div>
            </div>
        </div>
    </div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <div class="dropup" id="floating-info">
        <button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" style="font-size: 20px;font-weight: bolder; background-color: white;">?</button>
        <ul class="dropdown-menu">
            <li>&nbsp; &nbsp;Primera Mitad <a id="hgl"></a></li>
            <li>&nbsp; &nbsp;Segunda Mitad <a id="hrl"></a></li>
            <li class="divider"></li> 
            <li>&nbsp;&nbsp;<img src="historico/images/mm_20_green.png" />&nbsp;&nbsp;&nbsp;&nbsp;Inicio</li>
            <li> <img src="historico/images/posicion.png" />Checkpoint</li>
            <li>&nbsp;&nbsp;<img src="historico/images/mm_20_red.png" />&nbsp;&nbsp; &nbsp;Fin</li>
        </ul>
    </div>

    <div id="floating-player">
        <button class="btn btn-default" id="btnTimer" style="height: 30px;line-height: 0.5;align-items: center; background-color: white;" type="button" onclick="conTimer();" value="0" disabled>
            <img src="./assets/icons/play.png" height="15px" width="15px" />
        </button>
    </div>

    <div id="block-wrp">
        <!-- Mapas -->
        <div class="block-item">
            <div id="mapPoleo" class="map-item1"> </div>
        </div>
        <div class="block-item">
            <div id="mapUnidad" class="map-item2"> </div>
        </div>
        <!-- Barra de Carga -->
        <div id="loadstatus">
            <div id="information">
                <strong>Fecha/Hora</strong> : <span id="dateMessage"></span>&#183;
                <strong>Distancia</strong> : <span id="distanceMessage"></span>&#183;
                <strong>Distancia Acumulada </strong> : <span id="distanceacumMessage"></span>&#183;
                <strong>Tiempo</strong> : <span id="timeMessage"></span>&#183;
                <strong>Velocidad Promedio</strong> : <span id="speedMessage"></span>
            </div>
            <!-- information -->
            <div id="progress"></div>
        </div><!-- loadstatus -->
    </div>
    <div id="stats">
        <table class="table table-striped table-bordered table-hover">
          <tr>
              <td width="70%" class="form-inline">
                  Rango min. entre registros PDF:
                  <select name="txr" id="txr" class="form-control form-control-xs" style="height: 29px; font-size: small; text-align: left;">
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="30">30</option>
                  </select>
              </td>
              <td>
                  <button class="btn btn-success btn-sm" id="btnImprimir" onclick="generaImpresion();">IMPRIMIR</button>
              </td>
              <td>
                  <button class="btn btn-success btn-sm" id="btnPDF" onclick="generaPDF();" >PDF</button>
              </td>
              <td>
                   <button class="btn btn-success btn-sm" id="btnExcel" onclick="generaExcel();" hidden>EXCEL</button> 
              </td>
            </tr>
        </table>
        <div id="info">
            <table class="table table-striped table-bordered table-hover" id="statsTable">
            <thead>
                <tr><th width="150px" style="text-align: center;">Fecha/Hora</th>
                    <th style="text-align: center;">Posici&oacute;n</th>
                    <th style="text-align: center;">Tipo</th>
                    <th style="text-align: center;">Distancia</th>
                    <th style="text-align: center;">Comb.Usado</th>
                    <th style="text-align: center;">Rendimiento</th>
                    <th style="text-align: center;">Tiempo</th>
                    <th style="text-align: center;">Velocidad</th></tr>
            </thead>
            <tfoot></tfoot>
            <tbody id="tDetalle"></tbody>
            </table>
        </div>
	</div><!-- stats -->
</div>

    <!-- JavasScipt  -->
    <script type="text/javascript">
        $('#daterange').daterangepicker({
            "timePicker": true,
            "timePicker24Hour": true,
            "startDate": "<?php  date_default_timezone_set('America/Mexico_City'); echo date('m/d/Y H:i:s', (strtotime (" - 360 Minute")))?>",
            "endDate": "<?php date_default_timezone_set('America/Mexico_City'); echo date('m/d/Y H:i:s', (strtotime (" - 0 Minute")))?>",
            "locale": {
                "format": "MM/DD/YYYY HH:mm",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "Inicio",
                "toLabel": "Fin",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sab"
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
                "firstDay": 1
            }
        });
    </script>
    <script async defer
        src="http://maps.google.com/maps/api/js?key=<?php echo $gmk; ?>&language=es-MX&callback=drawMap"></script>
</body>
<?php 
include_once('helpers/LoggerApiGoogleMaps.php');
$log = new LoggerApiGoogleMaps($conn);
$log->saveLog($_SESSION["usuario"], 6, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
unset($log);
}else{
    echo "<h2>No tiene permiso para acceder a este módulo.</h2>";
}
 ?>