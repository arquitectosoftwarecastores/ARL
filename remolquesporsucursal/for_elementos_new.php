<link rel="stylesheet" href="libs/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.css">

<style>
  .bootstrap-table-filter-control-sucursal {
    font-size: 14px;
    height: 30px;
    margin-left: 40px;
    width: 90% !important;
    margin: 0 auto;
  }

  .bootstrap-table-filter-control-circuito {
    font-size: 14px;
    height: 30px;
    margin-left: 40px;
    width: 95% !important;
    margin: 0 auto;
  }

  .bootstrap-table-filter-control-economico {
    height: 30px;
    width: 90% !important;
    margin: 0 auto;
  }
</style>

<script src="libs/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>

<table id="tbRemolques" data-toggle="table" class="table table-striped table-borderless" data-toolbar="#toolbarRemolques" data-pagination="true" data-filter-control="true">
  <thead class=" thead-light">
    <th class="centrado" data-field="economico" data-filter-control="input">Economico</th>
    <th class="centrado" data-field="circuito" data-filter-control="select">Circuito</th>
    <th class="centrado" data-field="sucursal" data-filter-control="select">Sucursal</th>
    <th class="centrado" data-field="fec_posicion">Fecha</th>
    <th class="centrado" data-field="georeferencia">Referencia</th>
    <th class="centrado" data-field="lat">Latitud</th>
    <th class="centrado" data-field="long">Longitud</th>
    <!--  <th class="centrado" data-field="id" data-formatter="colAccionesRemolques">Acciones</th> -->
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