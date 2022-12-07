<?php

  $consulta3  = " SELECT * FROM tb_estados ORDER BY txt_nombre_edo ASC ";  
  $query3 = $conn->prepare($consulta3);
  $query3->execute();   

  $consulta4  = " SELECT * FROM tb_municipios, tb_estados WHERE pk_clave_edo=fk_clave_edo AND fk_clave_edo=1 ORDER BY txt_nombre_mun ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();     

?>

    <!-- Lista de los municipios -->
    <script src="scripts/listamunicipios.js"></script>

<!-- Modal -->
<div id="nuevo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agrega empresa</h4>
      </div>
      <div class="modal-body">


      <form action="?seccion=<?php echo $seccion;?>&amp;accion=agrega" id="form1" method="post">
        <fieldset>  
          <div class="row">   
              <div class="col-md-12">
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control"  size="80" maxlength="80"   /> 
              </div>
          </div>
          <div class="row">   
              <div class="col-md-12">
                Dirección:<input type="text" name="direccion" id="direccion" class="validate[required] text-input text form-control"  size="80" maxlength="80"   /> 
              </div>              
          </div>  
          <div class="row">   
              <div class="col-md-12">
                Colonia:<input type="text" name="colonia" id="colonia" class="validate[required] text-input text form-control"  size="30" maxlength="30"   /> 
              </div>
          </div>
          <div class="row">           
              <div class="col-md-6">
                Código Postal:<input type="text" name="cp" id="cp" class="validate[required] text-input text form-control numericOnly"  size="6" maxlength="6"   /> 
              </div>
              <div class="col-md-6">
                Teléfono:<input type="text" name="telefono" id="telefono" class="validate[required] text-input text form-control numericOnly"  size="10" maxlength="10" placeholder="4771234567"  /> 
              </div>                           
          </div> 
          <div class="row"> 
            <?php include ("municipios/for_listanuevo.php"); ?>          
          </div>
          <div class="row">   
            <div class="col-md-12">
                <p>&nbsp;</p>
                <button type="submit" id="agrega"  class="btn btn-primary">AGREGAR</button>              
            </div>
          </div>   
          </fieldset>
        </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>
