<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<form id="form1" class="ingresa" action="?seccion=paradas&amp;accion=lista" method="post">
<div class="container-fluid">
    <div class="row">
      <div class="col-md-2">
      	Número ecónomico:
        <input type="text" name="vehiculo" class="validate[required] form-control" value="<?php if(isset($_POST["vehiculo"])) echo $_POST["vehiculo"]; ?>" />
      </div>
      <div class="col-md-2">
        Distancia en Kms.:
        <input type="text" name="distancia" class="validate[required] form-control" value="<?php if(isset($_POST["distancia"])) echo $_POST["distancia"]; else echo "1";  ?>" />
      </div>
      <div class="col-md-2">
        Fecha inicial:
        <input type="text" class="validate[required] form-control" id="from" name="from" size="8" value="<?php if(isset($_POST["from"])) echo $_POST["from"]; else echo date("Y/m/d");?>">
      </div>
      <div class="col-md-2">
        Fecha final:
        <input type="text" class="validate[required] form-control" id="to" name="to" size="8" value="<?php if(isset($_POST["to"])) echo $_POST["to"]; else echo date("Y/m/d"); ?>">
      </div>
      <div class="col-md-1">
        <br/><button type="submit" id="buscar" class="btn btn-primary">BUSCAR</button>
      </div>
    <?php if(isset($_POST["vehiculo"])) { ?>
    <div class="col-md-2">
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-success btn-xs" id="imprime">IMPRIMIR</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success btn-xs" id="pdf">PDF</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success btn-xs" id="excel">EXCEL</button>
            </div>
        </div>
    </div>
    <?php } ?>
    </div>
</div>
</form>

<script>
$(function() {
  $( "#from" ).datepicker({
    dateFormat: 'yy/mm/dd',
    changeMonth: true,
    numberOfMonths: 1,
    onClose: function( selectedDate ) {
      $( "#to" ).datepicker( "option", "minDate", selectedDate );
    }
  });
  $( "#to" ).datepicker({
    dateFormat: 'yy/mm/dd',
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

  <script>
  $( "#imprime" ).click(function() {

     var divToPrint=document.getElementById("info");
     newWin= window.open("");
     newWin.document.write('<html><head><title>Reporte de paradas</title><link rel="stylesheet" href="/librerias/bootstrap/css/bootstrap.min.css"></head><body>');
     newWin.document.write('<p><strong>Reporte de paradas, Fecha: <?php echo date('d/m/Y H:i:s',time())?></strong></p>');
     newWin.document.write(divToPrint.outerHTML);
     newWin.print();
     newWin.close();

  });

$( "#pdf" ).click(function() {

      var url = "index.php?seccion=paradas&accion=pdf&vehiculo="+$_POST["vehiculo"]+"&distancia="+$_POST["distancia"]+"&from="+$_POST["from"]+"&to="+$_POST["to"];
      window.open(url);

});

$( "#excel" ).click(function() {

      var url = "paradas/for_excel.php?excel=1&vehiculo="+$_POST["vehiculo"]+"&distancia="+$_POST["distancia"]+"&from="+$_POST["from"]+"&to="+$_POST["to"];
      //var url = "paradas/for_excel.php?excel=1";
      window.open(url);
});
</script>

