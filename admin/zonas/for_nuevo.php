<?php
  $consulta3  = " SELECT * FROM tb_estados ORDER BY txt_nombre_edo ASC ";  
  $query3 = $conn->prepare($consulta3);
  $query3->execute();   

  $consulta4  = " SELECT * FROM tb_municipios, tb_estados WHERE pk_clave_edo=fk_clave_edo AND fk_clave_edo=1 ORDER BY txt_nombre_mun ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();  

  $consulta5  = " SELECT * FROM tb_tiposdezona ";  
  $query5 = $conn->prepare($consulta5);
  $query5->execute();     
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
            <div class="col-md-6">
              Estado:  
              <select name="estado" id="estado" class="text-input text form-control">
                <?php
                  while ($registro3 = $query3->fetch()) {
                ?>
                <option value="<?php echo $registro3['pk_clave_edo']; ?>"><?php echo $registro3['txt_nombre_edo']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-6">
              Ciudad:<br>
              <select name="ciudad" id="ciudad" class="text-input text form-control" >
                <?php
                  while ($registro4 = $query4->fetch()) {
                ?>
                <option value="<?php echo $registro4['pk_clave_mun']; ?>"><?php echo $registro4['txt_nombre_mun']; ?></option>
                <?php } ?>
              </select>
            </div>  
          </div>
          <div class="row">   
            <div class="col-md-12">
              Estatus:<br>
              <select name="tipo" id="tipo" class="text-input text form-control" >
                <?php
                  while ($registro5 = $query5->fetch()) {
                ?>
                <option value="<?php echo $registro5['pk_clave_tipz']; ?>"><?php echo $registro5['txt_nombre_tipz']?></option>
                <?php } ?>
              </select>
            </div>
          </div>
            
          <div class="row">   
            <div class="col-md-12">
                <p>&nbsp;</p>
                <button type="submit" id="agrega"  class="btn btn-primary">AGREGAR Y DIBUJAR ZONA</button>              
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