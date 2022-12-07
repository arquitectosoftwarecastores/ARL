<!-- Modal -->
<div id="mensajesenviados" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Últimos mensajes enviados: </h4>
      </div>
      <div class="modal-body">
        <div id="infomensajesenviados"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  $("#btnmensajesenviados").click(function(){
    $.ajaxSetup({ cache: false });
    $.ajax({
      url: "vehiculos/app_mensajesenviados.php",
            cache: false
    })
      .done(function( data ) {                          
          $('#infomensajesenviados').html(data);                          
      });

  });
});
</script>