<!-- Modal -->
<div id="directorioautoridades" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">DIRECTORIO DE AUTORIDADES POR CIUDAD/ESTADO: </h4>
      </div>
      <div class="modal-body">
        <div id="infodirectorioautoridades"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  $("#btndirectorioautoridades").click(function(){
    $.ajaxSetup({ cache: false });
    $.ajax({
      url: "autoridades/app_autoridades.php",
            cache: false
    })
      .done(function( data ) {                          
          $('#infodirectorioautoridades').html(data);                          
      });

  });
});
</script>