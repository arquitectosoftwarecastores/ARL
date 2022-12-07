<?php

  $consulta3  = " SELECT * FROM tb_zonas ORDER BY txt_nombre_zon ASC ";  
  $query3 = $conn->prepare($consulta3);
  $query3->execute();   

  $consulta4  = " SELECT * FROM tb_zonas ORDER BY txt_nombre_zon ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();  

?>

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
                <p>&nbsp;</p>
                <button type="submit" id="agrega"  class="btn btn-primary">AGREGAR Y VER RUTA</button>              
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