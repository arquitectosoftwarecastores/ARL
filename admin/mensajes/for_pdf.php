<?php
  include("funciones/distancia.php");
  include("posiciones/app_referencia.php");
?>
<div class="container">
  <div class="row">
    <div class="col-md-12 centrado">
      <button onclick="javascript:descargapdf();" class="btn btn-sm btn-success clearfix">Descargar PDF</button>
    </div>
  </div>
</div>

<div id="info">
  <div class="centrado">
    <img src="imagenes/logo.jpg" style="text-align:center"  width="150px">
    <p style="text-align:center"><strong>Reporte de mensajes, Fecha: <?php echo date('d/m/Y H:i:s',time())?></strong></p>
  </div>
  <table class="table table-striped table-bordered table-hover">
      <thead>
        <tr>   	
          <th width="200px">Fecha</th>
          <th width="200px">Económico</th>
          <th width="200px">Mensaje</th>
          <th width="150px">Usuario</th>
          <th width="100px">Comentario</th>
          <th width="200px">Ubicación</th>
        </tr>
      </thead>
      <tbody>
  	<?php 	
      $query = $conn->prepare($strSQL);
      $query->execute(); 
      $cuentaalertas=0;
  	  while ($registro = $query->fetch()) {
  	?>
  	<tr>
      <td><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])); ?></td>
      <td><?php echo $registro["txt_economico_veh"]; ?></td>
      <td><?php echo $registro["txt_nombre_tipm"];?></td>
      <td><?php echo $registro["txt_nombre_usu"] ?></td>
      <td><?php echo $registro["txt_comentario_mene"] ?></td>
      <td><?php echo georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).";"; ?>
        
      </td>        
    </tr>
  	<?php } ?>
    </tbody>  
  </table>
</div>
<?php $query->closeCursor(); ?>


 <script>
function descargapdf() {
    var pdf = new jsPDF('l', 'pt', 'a4');
    source = $('#info')[0];
    pdf.cellInitialize();
    pdf.setFontSize(10);
    specialElementHandlers = {
        '#bypassme': function (element, renderer) {
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