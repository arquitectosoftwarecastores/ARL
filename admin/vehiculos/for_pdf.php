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
    <p style="text-align:center"><strong>Lista de vehículos, Fecha: <?php echo date('d/m/Y H:i:s',time())?></strong></p>
  </div>
  <table class="table table-striped table-bordered table-hover">
        <tr>   	
          <td>Económico</td>
          <td>Serie</td>
          <td>Circuito</td>
          <td>Especial</td>
          <td>Latitud,Longitud</td>
        </tr>
  	<?php 	
      $query = $conn->prepare($strSQL);
      $query->execute(); 
      $cuentaalertas=0;
  	  while ($registro = $query->fetch()) {
  	?>
  	<tr>
      <td><?php echo $registro["txt_economico_veh"]; ?></td>
      <td><?php echo $registro["num_serie_veh"];?></td>
      <td><?php echo $registro["txt_nombre_cir"] ?></td>
      <td><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No"; ?></td>
      <td>
        <?php echo $registro["num_latitud_veh"].",".$registro["num_longitud_veh"] ?>
      </td>   
    </tr>
  	<?php } ?>
  </table>
</div>
<?php $query->closeCursor(); ?>


 <script>
function descargapdf() {
    var pdf = new jsPDF('l', 'pt', 'letter');
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
        top: 10,
        bottom: 10,
        left: 10 
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
