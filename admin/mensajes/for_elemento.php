<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php include ('general/estilo.php') ?>
<?php include("funciones/autofiltro.php") ?>
<?php include("funciones/distancia.php") ?>
<?php include("posiciones/app_referencia.php") ?>

<div class="container-fluid">  
    <div class="row renglon">   
      <?php $variable="fecha"?>
      <div class="col-md-2 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
    	Fecha
		  <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="economico"?>
      <div class="col-md-1 negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      <span style="font-size:10px">Económico</span>
      <?php   include ("general/for_orden.php"); ?>
      </div>       
      <?php $variable="mensaje"?>
      <div class="col-md-2 centrado negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Mensaje
      <?php   include ("general/for_orden.php"); ?>
      </div>  
      <?php $variable="usuario"?>
      <div class="col-md-2 centrado negritas" <?php if(isset($_GET["orden"]) && ($_GET["orden"]==$variable."_up"  or $_GET["orden"]==$variable."_do" )) echo "class='ordenado'"?> >
      Usuario
      <?php   include ("general/for_orden.php"); ?>
      </div>
      <div class="col-md-1 centrado negritas">
        Comentario
      </div>
      <div class="col-md-1 centrado negritas">
        Respuesta
      </div>  
      <div class="col-md-3 centrado negritas">
        Ubicación
      </div>      
    </div>  
    <div class="row renglon">
      <div class="col-md-2">
          <?php
            if(isset($_GET["from"]))
              $from=$_GET["from"];
            else
              $from="";
            if(isset($_GET["to"]))
              $to=$_GET["to"];
            else
              $to="";
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
      <div class="col-md-1 centrado">
        <?php if(isset($_GET["busca"]))
              $_GET["economico"]=$_GET["busca"];
        ?>
        <?php autofiltro("txt_economico_veh","tb_vehiculos","economico",$conn) ?>
      </div>
      <div class="col-md-2 centrado">
        <?php autofiltro("txt_nombre_tipm","tb_tiposdemensajessms","mensaje",$conn) ?>
      </div>
      <div class="col-md-2 centrado">
        <?php autofiltro("txt_nombre_usu","tb_usuarios","usuario",$conn) ?>
      </div>
    </div>
	<?php 	
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
		while ($registro = $query->fetch()) {
	?>
		<div class="row renglon">
      <div class="col-md-2"><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])); ?></div>
      <div class="col-md-1 centrado"><?php echo $registro["txt_economico_veh"]; ?></div>
      <div class="col-md-2 centrado">
        <?php echo $registro["txt_nombre_tipm"];?> 
      </div>
      <div class="col-md-2 centrado">
        <?php echo $registro["txt_nombre_usu"] ?>
      </div>
      <div class="col-md-1">
        <?php echo $registro["txt_comentario_mene"] ?>
      </div>
      <div class="col-md-1">
        <?php echo $registro["txt_respuesta_mene"] ?>
      </div>
      <div class="col-md-3 centrado">
        <?php echo georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn);?>
      </div>                    
    </div>
		<?php } ?>   
</div>


<?php $query->closeCursor(); ?>
<?php include("general/jquery.php") ?>


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