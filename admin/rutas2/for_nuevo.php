<?php
  //Estas consultas sirven para llenar los select con las zonas dadas de alta
  $consulta3  = " SELECT * FROM tb_zonas ORDER BY txt_nombre_zon ASC ";  
  $query3 = $conn->prepare($consulta3);
  $query3->execute();   
  $consulta4  = " SELECT * FROM tb_zonas ORDER BY txt_nombre_zon ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();  
  $consulta5  = " SELECT * FROM tb_vehiculos ORDER BY txt_economico_veh ASC ";  
  $query5 = $conn->prepare($consulta5);
  $query5->execute();  
?>

<!-- Include Date Range Picker -->
<script src="../librerias/datetimepicker/moment.min.js"></script>
<script type="text/javascript" src="../librerias/datetimepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="../librerias/datetimepicker/daterangepicker.css" />

<!-- Modal -->
<div id="nuevo" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agrega <?php echo $elemento;?></h4>
      </div>
      <div class="modal-body">
      <form action="?seccion=<?php echo $seccion;?>&amp;accion=agrega" id="form1" method="post">    
        <fieldset>   
          <div class="row">   
            <div class="col-md-12">
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120"   />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              Origen:  
              <select name="origen" id="origen" class="text-input text form-control">
                <?php
                  while ($registro3 = $query3->fetch()) {
                ?>
                <option value="<?php echo $registro3['pk_clave_zon']; ?>"><?php echo $registro3['txt_nombre_zon']; ?></option>
                <?php } ?>                     
              </select>           
            </div>            
          </div>
          <div class="row">            
            <div class="col-md-12">
              Destino:<br>
              <select name="destino" id="destino" class="text-input text form-control" >
                <?php
                  while ($registro4 = $query4->fetch()) {
                ?>
                <option value="<?php echo $registro4['pk_clave_zon']; ?>"><?php echo $registro4['txt_nombre_zon']; ?></option>
                
                <?php } ?>
              </select>
            </div>  
          </div>     
           
         <div class="row">            
         <div class="col-md-12">
              Unidad a guardar su ruta:<br>
              <select name="unidadruta" id="unidadruta" class="text-input text form-control" >
                <?php
                  while ($registro5 = $query5->fetch()) {
                ?>
                <option value="<?php echo $registro5['txt_economico_veh']; ?>"><?php echo $registro5['txt_economico_veh']; ?></option>
                
                <?php } ?>
              </select>
            </div>
         </div>
         
           
 
           <div class="row">     
            <div class="col-md-12">
                Rango de fecha-hora: 
                <input type="text" name="daterange" id="daterange" class="validate[required] text-input text form-control">
            </div>
         </div> 
           
         <div class="row">   
            <div class="col-md-12">
                <p>&nbsp;</p>
                <button type="submit" id="agrega" class="btn btn-primary">GENERAR RUTA</button>                             
            </div>
          </div>   
        <input type="hidden" name="JsonPuntosIntermedios" id="JsonPuntosIntermedios" />  
          </fieldset>
        </form>  
          
<script>

$('#daterange').daterangepicker();


$('#daterange').daterangepicker({
    "timePicker": true,
    "timePicker24Hour": true,
    "startDate": "<?php  date_default_timezone_set('America/Monterrey'); echo date('m/d/Y H:i:s', (strtotime ("-35 Minute")))?>",
    "endDate": "<?php date_default_timezone_set('America/Monterrey'); echo date('m/d/Y H:i:s', (strtotime ("-5 Minute")))?>",

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
          
 
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>