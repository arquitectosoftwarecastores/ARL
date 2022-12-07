<?PHP
// ***********************************************************************************************************
// HISTORICO DE POSICIONES Y EVENTOS LAS UNIDADES (RASTREO)
// ***********************************************************************************************************
if (!isset($_SESSION['id'])) {
  session_start();
}

/*include("./historico_tracker_load.php");*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<title>Histórico de Posiciones</title>

<?php
//include ('conexion/conexion.php');
?>

<!-- jQuery plugin -->
<!--
<script src="librerias/jquery.min.js"></script>
-->

<script src="historico/key.js"></script>
<script src="workoutTracker.js" type="text/javascript"></script>

<?php include("historico/api_Maps.php"); ?>
<script type="text/javascript" src="historico/ajax_upuntos.js"></script>
<script type="text/javascript" src="historico/historico_tracker_ventana_pdf_csv.js"></script>

<!-- Bootstrap plugin -->
<!--
<script src="librerias/bootstrap/js/bootstrap.min.js"></script>
<link href='librerias/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
-->

<!-- Include Date Range Picker -->
<script src="librerias/datetimepicker/moment.min.js"></script>
<script type="text/javascript" src="librerias/datetimepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="librerias/datetimepicker/daterangepicker.css" />



<style type="text/css">

body{
margin:0px;
padding:0px;
font-size:12px;
}

h1 {
font-size: 1em;
font-weight: bold;
}

#map{
width:100%;
height:450px;
margin:0;
padding:0px;
}

#loadstatus{
position:absolute;
bottom:0;
left:0;
z-index:1000;
width:100%;
line-height:30px;
height:30px;
border:1px solid #ccc;
text-align:left;
background-color:#fff;
}

#hideStats{
display:none;
}

#stats{
position:absolute;
top:130px;
left:0;
margin:0;
padding:0;
z-index:2000;
background-color:#fff;
border:1px solid #fff;
border-bottom:1px solid #ccc;
width:99%;
height:450px;
overflow:auto;
display:none;
}

#stats p{
margin:0 0 15px 0;
}

#information{
position:absolute;
bottom:0;
z-index:1500;
text-align:center;
width:100%;
}

#progress{
height:100%;
width:0%;
background-color:#DDEFA4;
}

#firstHalf{
width:45%;
margin:5px 0 15px 3px;
border-bottom:3px solid blue;
float:left;
}
#secondHalf{
width:45%;
margin:5px 3px 15px 0;
border-bottom:3px solid red;
float:right;
text-align:right;
}

</style>

</head>
<body>
<?php
    $id=0;
    if (isset($_GET['id'])) $id=$_GET['id'];

    $fecha_ini= date('Y/m/d H:i:s');
    if (isset($_POST['fecha_ini']))
        $fecha_ini=$_POST['fecha_ini'];

    $fecha_fin= "";
    if (isset($_POST['fecha_fin']))
        $fecha_fin=$_POST['fecha_fin'];
?>

<input type="hidden" id="user" name="user" value="<?php echo $_SESSION['id']?>" />
<input type="hidden" id="id" name="id" value="<?php echo $id?>" />

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>ECÓNOMICO: <?php echo $id;?></h1>
        </div>
    </div>
</div>

<form id="criterios" method="post" action="?seccion=historico&id=<?php echo $id; ?>" >
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <p id="firstHalf">Primera Mitad</p>
            <p id="secondHalf">Segunda Mitad</p>
        </div>
        <div class="col-md-2">
            <img src="historico/images/mm_20_green.png" />Inicio
            <img src="historico/images/posicion.png" />Checkpoint
            <img src="historico/images/mm_20_red.png" />Fin
        </div>
        <div class="col-md-3">
            Rango de fecha-hora:
            <input type="text" name="daterange" id="daterange" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="Radio" name="filtro" value="posiciones" <?PHP if (!isset($_POST['filtro'])) echo "checked";if ($_POST['filtro']=='posiciones') echo "checked";?>> Posiciones
            <input type="Radio" name="filtro" value="eventos" <?PHP if ($_POST['filtro']=='eventos') echo "checked";?> > Eventos
            <input type="Radio" name="filtro" value="trayectoria" <?PHP if ($_POST['filtro']=='trayectoria') echo "checked";?>> Trayectoria
        </div>
        <div class="col-md-2">
            <strong>Velocidad: </strong>
            <input type="Radio" name="velocidad" value="1.5" <?PHP if ($_POST['velocidad']=='1.5') echo "checked";?>>Baja
            <input type="Radio" name="velocidad" value="1" <?PHP if ($_POST['velocidad']=='1') echo "checked";?> >Media
            <input type="Radio" name="velocidad" value="0.5" <?PHP if (!isset($_POST['velocidad'])) echo "checked";if ($_POST['velocidad']=='0.5') echo "checked";?>>Alta
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-1">
            <input class="btn" type="button" name= "submit" id="submit" value="Consultar"/>
        </div>
        <div class="col-md-1">
            <input class="btn" id="pausar" type="button" value="Pausar" onclick=" stopCount();"/>
            <input id="contador" type="hidden" value="" />
        </div>
        <div class="col-md-1">
            <a id="showStats" class="btn btn-success">Detalle</a>
        </div>
        <div class="col-md-1">
            <a id="hideStats" class="btn btn-primary">Mapa</a>
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-2">
			Rango min. entre registros PDF:
        </div>
        <div class="col-md-1">
            <select id="txr" class="form-control">
				<option value="5" <?php if ($txr == 5) { ?> selected="selected" <?php } ?>>5</option>
				<option value="10" <?php if ($txr == 10) { ?> selected="selected" <?php } ?>>10</option>
				<option value="20" <?php if ($txr == 20) { ?> selected="selected" <?php } ?>>20</option>
				<option value="30" <?php if ($txr == 30) { ?> selected="selected" <?php } ?>>30</option>
			</select>
        </div>
    </div>
</div>
</form>

<div id="map"></div>

<div id="stats">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <td width="70%">&nbsp;</td>
            <td>
                <button class="btn btn-success btn-xs" id="imprime">IMPRIMIR</button>
            </td>
            <td>
                <button class="btn btn-success btn-xs" id="pdf">PDF</button>
            </td>
            <td>
                <button class="btn btn-success btn-xs" id="excel">EXCEL</button>
            </td>
          </tr>
        </table>
        <div id="info">
            <table class="table table-striped table-bordered table-hover" id="statsTable">
            <thead>
                <tr><th>Fecha/hora</th>
                    <th width="200px">Posici&oacute;n</th>
                    <th>Tipo</th>
                    <th>Distancia</th>
                    <th>Comb.Usado</th>
                    <th>Rendimiento</th>
                    <th>Tiempo</th>
                    <th>Velocidad</th></tr>
            </thead>
            <tfoot></tfoot>
            <tbody></tbody>
            </table>
        </div>
	</div><!-- stats -->
</div>

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


</body>
</html>
<!--

        <p>
            <strong>Unidad</strong> :<?PHP
                      if (isset($_GET['id'])) {
                        echo $_GET['id'];
                        echo "<input name = \"id\" type=\"hidden\" value = \"".$_GET['id']."\"/>";
                    } ?> &#183;
            <strong>Operador</strong> : <?PHP
                    if (isset($_GET['id'])) {
                        echo $operador;
                        echo "<input id=\"ope\" name = \"ope\" type=\"hidden\" value = \"".$operador."\"/>";
                    } ?> &#183;
            <strong>Exportar Informaci&oacute;n: </strong>
            <a href="javascript:crea_ventana_pdf()"><img src="images/Icono_Pdf.jpg" border="0" width="35" height="35"></a> &#183;
            <a href="javascript:crea_ventana_csv()"><img src="images/Icono_xls.jpg" border="0" width="35" height="35"></a> &#183;
        </p>

-->

<script>

$('#daterange').daterangepicker();


$('#daterange').daterangepicker({
    "timePicker": true,
    "timePicker24Hour": true,
    "startDate": "<?php  date_default_timezone_set('America/Monterrey'); echo date('m/d/Y H:i:s', (strtotime ("-31 Minute")))?>",
    "endDate": "<?php date_default_timezone_set('America/Monterrey'); echo date('m/d/Y H:i:s', (strtotime ("-1 Minute")))?>",

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

initCheckpoint();
    initButtons();
    onLoad();

$( "#submit" ).click(function() {

    initCheckpoint();
    initButtons();
    onLoad();
});

</script>

<script>

$('#imprime').click(function () {
   var divToPrint=document.getElementById("statsTable");
   newWin= window.open("");
   newWin.document.write('<html><head><title>Histórico de posiciones</title><link rel="stylesheet" href="librerias/bootstrap/css/bootstrap.min.css"></head><body>');
   newWin.document.write('<p><strong>Reporte Histórico de posiciones, Fecha: <?php echo date('d/m/Y H:i:s',time())?></strong></p>');
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
});

</script>


<div id="editor"></div>

 <script>
function descargapdf() {

    var pdf = new jsPDF('l', 'pt', 'a4');
    source = $('#info')[0];
    pdf.cellInitialize();
    pdf.setFontSize(10);
    specialElementHandlers = {
        '#editor': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 20,
        bottom: 20,
        left: 20
    };

    pdf.fromHTML(
    source, // HTML string or DOM elem ref.
    margins.left, // x coord
    margins.top, { // y coord
        'elementHandlers': specialElementHandlers
    },


    function (dispose) {
        pdf.save('archivo.pdf');
    }, margins);


}

</script>

<script src="scripts/jspdf.debug.js"></script>

<script>


$( "#excel" ).click(function() {

    fecharango=$('#daterange').val();
    fechas = fecharango.split("-");
    vini=fechas[0].trim();
    vfin=fechas[1].trim();

    var url = "for_excel.php?excel=1";
    url=url +"&id=<?php echo $_GET['id'];?>";
    url=url +"&filtro=posiciones";
    url=url +"&ini="+vini;
    url=url +"&fin="+vfin;
    window.open(url);

});
</script>

<script>


$( "#pdf" ).click(function() {

    fecharango=$('#daterange').val();
    fechas = fecharango.split("-");
    vini=fechas[0].trim();
    vfin=fechas[1].trim();

	var vtxr = document.getElementById("txr").value;
    var url = "for_pdf.php?pdf=1";
    url=url +"&id=<?php echo $_GET['id'];?>";
    url=url +"&filtro=posiciones";
    url=url +"&txr="+vtxr;
    url=url +"&ini="+vini;
    url=url +"&fin="+vfin;
    window.open(url);

});
</script>
