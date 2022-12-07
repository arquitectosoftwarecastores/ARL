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
    <p style="text-align:center"><strong>Reporte de Alertas, Fecha: <?php echo date('d/m/Y H:i:s',time())?></strong></p>
  </div>
  <table class="table table-striped table-bordered table-hover">
      <thead>
        <tr>   	
          <th width="200px">Fecha</th>
          <th width="200px">Económico</th>
          <th width="200px">Alerta</th>
          <th width="150px">Prioridad</th>
          <th width="100px">Estatus</th>
          <th width="200px">Acumuladas</th>
          <th width="200px">Tiempo</th>
        </tr>
      </thead>
      <tbody>
  	<?php 	
      $query = $conn->prepare($strSQL);
      $query->execute(); 
      $cuentaalertas=0;
  	  while ($registro = $query->fetch()) {
        switch ($registro["num_prioridad_tipa"]) {
          case 3:
            $prioridad="Alta";
            $color="fondorojo";
            break;
          case 2:
            $prioridad="Media";
            $color="fondoamarillo";
            break;
          case 1:
            $prioridad="Baja";
            $color="fondoverde";
            break;          
      }
              
      switch ($registro["num_estatus_ale"]) {
          case 0:
            $estatus="Sin atender";
            $colorestatus="rojo";
            break;
          case 1:
            $estatus="Atendida";
            $colorestatus="verde";
      }
  	?>
  	<tr>
  		<td><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_ale"])); ?></td>
  		<td><?php echo $registro["txt_economico_veh"]; ?></td>
  		<td><?php echo $registro["txt_nombre_tipa"];?></td>
  		<td><?php echo $prioridad ?></td>
  	      <?php
  	        $nombre="";
  	        if($estatus=="Atendida") 
  	        {
  	          $consulta1  = " SELECT * FROM tb_usuarios
  	                          WHERE pk_clave_usu=?";  
  	          $query1 = $conn->prepare($consulta1);
  	          $query1->bindParam(1, $registro["fk_clave_usu"]);
  	          $query1->execute();
  	          while($registro1 = $query1->fetch())          
  	            { 
  	              $nombre=$registro1["txt_nombre_usu"]; 
  	              $estatus="";
  	            }
  	        }
  	      ?>
  		<td><?php echo $estatus.$nombre ?></td>
  		<td><?php echo $registro["acumuladas"] ?></td>
  		<td>
          <?php 
            $tiempo=str_replace("days","días",$registro["tiempo"]);
            $tiempo=substr($tiempo,0,strlen($tiempo)-10);
            $tiempo=str_replace(":"," hrs. ",$tiempo). " min.";
            echo trim($tiempo); 
          ?>	
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