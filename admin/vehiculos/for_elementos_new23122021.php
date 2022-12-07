<table id="tbRemolques" data-toggle="table" class="table table-striped table-borderless" data-toolbar="#toolbarRemolques" data-toolbar-align="left" data-group-by="true" data-group-by-field="cliente" data-pagination="true" data-search="true" data-sortable="true">
  <thead class=" thead-light">
    <th class="centrado" data-field="economico" data-sortable="true">Economico</th>
    <th class="centrado" data-field="serie" data-sortable="true">Serie</th>
    <th class="centrado" data-field="circuito" data-sortable="true">Circuito</th>
    <th class="centrado" data-field="lat">Latitud</th>
    <th class="centrado" data-field="long">Longitud</th>
    <th class="centrado" data-field="tipo">Tipo</th>
    <th class="centrado" data-field="id" data-formatter="colAccionesRemolques">Acciones</th>
  </thead>
  <tbody>

  </tbody>
</table>

<script>
  var $tbRemolques = $('#tbRemolques')

  $.ajax({
    type: 'GET', //aqui puede ser igual get
    url: 'vehiculos/json.php', //aqui va tu direccion donde esta tu funcion php
    data: {},
    success: function(data) {
      const remolques = data.vehiculos
      $tbRemolques.bootstrapTable('refresh')
      $tbRemolques.bootstrapTable({
        data: remolques
      })
      $tbRemolques.bootstrapTable('load', remolques)
    },
    error: function(data) {
      //lo que devuelve si falla
      alert('la actualizacion fallo' + ' ' + data);
    },
    complete: function() {

    }
  });


  function colAccionesRemolques(value, row) {
    return '<a href="?seccion=vehiculos&accion=cambia&id=' + value + '" >' +
      '<button class="btn btn-sm btn-primary" value="' + value + '" >' +
      'EDITAR' +
      '</button></a>&nbsp;' +
      '<a href="?seccion=vehiculos&accion=borra&id=' + value + '" >' +
      '<button class="btn btn-sm btn-danger" value="' + value + '" >' +
      'ELIMINAR' +
      '</button></a>'
  }
</script>