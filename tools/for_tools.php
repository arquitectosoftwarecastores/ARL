<div class="container mt-2 mb-2">
  <div class="row">
    <div class="col-md derecha">
      <b>No. Economico / ESN</b>
    </div>
    <div class="col-md-3">
      <input type="text" name="txtId" id="txtId" onkeypress="clickPress(event)" class="form-control form-control-sm" maxlength="20">
    </div>

    <div class="col-md">
      <button class="btn btn-sm btn-primary" onclick="buscarCadenas()" id="btnBuscar">BUSCAR</button>
    </div>
  </div>

  <hr>

  <div class="row">
    <div class="col-sm-2 derecha">
      <b>No. Economico:</b>
    </div>
    <div class="col-sm">
      <label id="labEconomico"></label>
    </div>

    <div class="col-md derecha">
      <b>ESN:</b>
    </div>
    <div class="col-sm">
      <label id="labEsn"></label>
    </div>

    <div class="col-sm derecha">
      <b>Indicador:</b>
    </div>
    <div class="col-sm">
      <img src="" height="14px" id="imgIndicador">
    </div>
  </div>
</div>



<table id="tbRemolques" data-toggle="table" class="table table-striped" data-toolbar="#toolbarRemolques" data-toolbar-align="left" data-group-by="true" data-group-by-field="cliente" data-pagination="true" data-sortable="true">
  <thead class="thead-light" style="font-size: 14px;">
    <th class="centrado" data-field="ultimaposicion" data-sortable="true">Fecha MX</th>
    <th class="centrado" data-field="bdposicion" data-sortable="true">Fecha UTC</th>
    <th class="centrado" data-field="coordenadas">Coordenadas</th>
    <th class="centrado" data-field="event">Event Code</th>
    <th class="centrado" data-field="ignicion">Ignicion</th>
    <th class="centrado" data-field="satelites">Satelites</th>
    <th class="centrado" data-field="fixstatus">FixStatus</th>
    <th class="centrado" data-field="carrier">Carrier</th>
    <th class="centrado" data-field="rssi">RSSI</th>
    <th class="centrado" data-field="motion">Motion</th>
    <th class="centrado" data-field="power">Power State</th>
    <th class="centrado" data-field="vias">Volt. 7 Vías</th>
    <th class="centrado" data-field="voltaje">Volt. Batería</th>
  </thead>
  <tbody style="font-size: 14px;">

  </tbody>
</table>

<script>
  var $tbRemolques = $('#tbRemolques')

  function clickPress(event) {
    if (event.keyCode == 13) {
      buscarCadenas()
    }
  }

  function buscarCadenas() {
    const id = document.getElementById('txtId').value

    $tbRemolques.bootstrapTable('refresh')
    $tbRemolques.bootstrapTable({
      data: []
    })
    $tbRemolques.bootstrapTable('load', [])

    document.getElementById('txtId').disabled = true
    document.getElementById('btnBuscar').disabled = true

    $.ajax({
      type: 'POST', //aqui puede ser igual get
      url: 'tools/con_tools.php', //aqui va tu direccion donde esta tu funcion php
      data: {
        id: id
      },
      success: (data) => {
        const pos = JSON.parse(data)

        const remolque = pos.remolque
        setTimeout(() => {
          document.getElementById('labEconomico').innerHTML = remolque.noeconomico
          document.getElementById('labEsn').innerHTML = remolque.esn

          let color = remolque.indicador
          switch (color) {
            case 0:
              color = 'yellow'
              break

            case 1:
              color = 'green'
              break

            case 2:
              color = 'blue'
              break

            case 3:
              color = 'red'
              break

            default:
              color = null
              break
          }

          let dir = "http://maps.google.com/mapfiles/ms/icons/" + color + ".png"

          if (color === null) {
            dir = null
          }

          document.getElementById('imgIndicador').src = dir

          $tbRemolques.bootstrapTable('refresh')
          $tbRemolques.bootstrapTable({
            data: pos.cadenas
          })
          $tbRemolques.bootstrapTable('load', pos.cadenas)
        }, 100);

      },
      error: function(data) {
        //lo que devuelve si falla
        alert('la actualizacion fallo' + ' ' + data);
      },
      complete: function() {
        setTimeout(() => {
          document.getElementById('txtId').disabled = false
          document.getElementById('btnBuscar').disabled = false
        }, 100);
      }
    });
  }
</script>