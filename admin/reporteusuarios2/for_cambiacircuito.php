<?php
  $consulta1  = "SELECT txt_economico_veh FROM tb_vehiculos ORDER BY txt_economico_veh ASC";  
  $query1 = $conn->prepare($consulta1);
  $query1->execute();

  $consulta2 = "SELECT * FROM tb_circuitos ORDER BY txt_nombre_cir ASC";  
  $query2 = $conn->prepare($consulta2);
  $query2->execute();

?>
<form id="form1" action="?seccion=vehiculos&amp;accion=actualizacircuito" method="post">
<div class="container-fluid"> 
  <div class="container">
      <div class="row">
        <div class="col-md-12 centrado">
          <h1>Cambia circuitos a Vehículos</h1>      
        </div> 
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Ecónomico:</strong>
        </div>
        <div class="col-md-6 izquierda">
          <select name="economico" id="economico" class="form-control">
          <option value="0">Seleccione un No Económico</option> 
          <?php   
            while ($registro1 = $query1->fetch()) 
            {
          ?>
            <option value="<?php echo $registro1["txt_economico_veh"]?>"><?php echo $registro1["txt_economico_veh"]?></option> 
          <?php 
            } 
          ?>    
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-6">
           <div id="lista"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Circuito:</strong>
        </div>
        <div class="col-md-6 izquierda">
          <select name="circuito" id="circuito" class="form-control">
            <?php   
              while ($registro2 = $query2->fetch()) 
              {
            ?>
              <option value="<?php echo $registro2["pk_clave_cir"]?>"><?php echo $registro2["txt_nombre_cir"]?></option> 
            <?php 
              } 
            ?>    
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 centrado">
          <button type="submit" class="btn btn-primary confirmaenviar">GUARDAR CAMBIOS</button>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 centrado">
          <a href="#" onclick="history.go(-1); return false;">
            <button type="button"  class="btn btn-warning">CANCELAR</button>
          </a>
        </div>
      </div>
  </div>
</div>
<input type="hidden" value="0" id="numerodevehiculos" name="numerodevehiculos">
</form>

<script>
    
    var contador=0;
    var cuenta=0;
    $('#economico').change(function() {
      economico=$( "#economico option:selected" ).val();
      if(economico!="0")
      {
        var agregado=0;
   
        $(".vehiculos").each(function() {
            if(this.value==economico) {
             alert( "Ya se agregó el No Económico: "+economico);
             agregado=1;
            }
        });

        if(!agregado)
        {

          $("#lista").append('<input type="hidden" class="vehiculos" name="vehiculo'+contador+'" id="vehiculo'+contador+'" value="'+economico+'"/>');
          $("#lista").append('<button style="margin:5px;" type="button" class="btn btn-xs btn-danger" onclick="quita('+contador+')" id="btn'+contador+'" ><span class="glyphicon glyphicon-remove"></span> '+economico+'</button>');
          contador++;
          $('#numerodevehiculos').val(contador);
          cuenta++;
        }
      }

    });

    function quita(valor) {
      $("#vehiculo"+valor).remove();
      $("#btn"+valor).remove();
      cuenta--;
    };
 
    $('.confirmaenviar').click(function(){
        if(cuenta==0)
        {
          alert("Agregue al menos un No económico");
          return false;
        }
    });

</script>
