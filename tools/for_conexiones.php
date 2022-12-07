<div class="container mt-2">
  <div class="row">
    <div class="col-sm derecha">
      <b>Conexión:</b>
    </div>
    <div class="col-sm" id="txtConexiones">
      0
    </div>

    <div class="col-md derecha">
      <b>Desconectados:</b>
    </div>
    <div class="col-md" id="txtDesconectados">
      0
    </div>
  </div>

  <hr>
</div>



<table id="tbRemolques" data-toggle="table" class="table table-striped" data-toolbar="#toolbarRemolques" data-toolbar-align="left" data-group-by="true" data-group-by-field="cliente" data-pagination="true" data-sortable="true" data-search="true">
  <thead class="thead-light" style="font-size: 14px;">
    <th class="centrado" data-field="esn" data-sortable="true">ESN</th>
    <th class="centrado" data-field="noeconomico" data-sortable="true">No Economico</th>
    <th class="centrado" data-field="estatus" data-sortable="true">Estatus</th>
    <th class="centrado" data-field="fecha" data-sortable="true">Fecha</th>
  </thead>
  <tbody style="font-size: 14px;">

  </tbody>
</table>

<script>
  var $tbRemolques = $('#tbRemolques')

  consultaConexiones()

  setInterval(() => {
    consultaConexiones()
  }, 30000);

  function consultaConexiones() {

    $.ajax({
      type: 'POST', //aqui puede ser igual get
      url: 'tools/con_conexiones.php', //aqui va tu direccion donde esta tu funcion php
      data: {},
      success: (data) => {
        const conex = JSON.parse(data)
        const count_con = conex.count
        const conexiones = conex.conexiones

        document.getElementById('txtConexiones').innerHTML = count_con.conexiones
        document.getElementById('txtDesconectados').innerHTML = count_con.desconexiones

        $tbRemolques.bootstrapTable('refresh')
        $tbRemolques.bootstrapTable({
          data: conexiones
        })
        $tbRemolques.bootstrapTable('load', conexiones)


      },
      error: function(data) {
        //lo que devuelve si falla
        alert('la actualizacion fallo' + ' ' + data);
      },
      complete: function() {}
    });
  }
</script>