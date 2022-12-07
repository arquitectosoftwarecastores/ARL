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
                Prioridad:
                <select name="prioridad" id="prioridad" class="form-control">
                  <option value="3" >Alta</option>
                  <option value="2" >Media</option>
                  <option value="1" >Baja</option>
                </select>   
            </div>
            <div class="col-md-6">
                Tipo:
                <select name="tipo" id="tipo" class="form-control">
                  <option value="0" >Seguridad</option>
                  <option value="1" >Operación</option>
                </select>  
            </div>            
          </div>
          <div class="row">   
            <div class="col-md-12">
                <p>&nbsp;</p>
                <button type="submit" id="agrega"  class="btn btn-primary">AGREGAR</button>
                <?php   include ("general/for_filtros.php"); ?>               
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
<?php $query1->closeCursor(); ?>