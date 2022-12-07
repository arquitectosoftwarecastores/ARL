<!-- Modal -->
<div id="edita" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cambia <?php echo $elemento;?></h4>
      </div>
      <div class="modal-body">

      <form action="?seccion=<?php echo $seccion;?>&amp;accion=actualiza" id="form1" method="post">      
        <fieldset>        
          <div class="row">   
              <div class="col-md-12">
                <input type="text" name="elemento" id="elemento" class="validate[required] text-input text form-control" value=""> 
                <input name="id" type="hidden" id="id" value="" >
            </div>
          </div>
          <div class="row">   
            <div class="col-md-12">
                <hr/>
                <p>&nbsp;</p>
                <button type="submit"  class="btn btn-primary">CAMBIAR</button>
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
<script>

  $('.edita').click(function () {
    $('#id').val($(this).data('id'));
    $('#elemento').val($(this).data('valor'));
  });

</script>