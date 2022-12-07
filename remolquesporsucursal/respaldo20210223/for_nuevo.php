<?php
$consulta1  = "SELECT * FROM tb_circuitos ORDER BY txt_nombre_cir ASC";
$query1 = $conn->prepare($consulta1);
$query1->execute();
?>

<script type="text/javascript" src="scripts/pwstrength.js"></script>
<script type="text/javascript">
  function verificarVehiculo(str) {
    if (str.length > 1) {
      var x = document.getElementById("msjAdv");
      var b = document.getElementById("agrega");
      var t = document.getElementById("tipocamion");
      var txt = document.getElementById("msjTXT");
      var numero = str;
      $.ajax({
        type: 'POST', //aqui puede ser igual get
        url: 'admin/vehiculos/con_verifica.php', //aqui va tu direccion donde esta tu funcion php
        data: {
          numero
        }, //aqui tus datos
        success: function(data) {
          //lo que devuelve
          data = jQuery.parseJSON(data);
          //alert(data['noeconomico']);
          var economico = data['noeconomico'];
          var tipo = data['tipounidad'];
          var status = data['status'];
          // Valida status
          if (status == 0) {
            if (economico != 0) {
              b.disabled = false;
              x.style.display = "none";
              document.getElementById('tipocamion').value = tipo;
              document.getElementById('msjTXT').innerHTML = '';
            } else {
              document.getElementById("checkAceptar").checked = false;
              x.style.display = "block";
              b.disabled = true;
              document.getElementById('msjTXT').innerHTML = '<br>* El vehiculo no se encuentra en la base de datos de Castores. ¿Desea registrar el vehiculo?';
              document.getElementById('tipocamion').value = '';
            }
          } else {
            b.disabled = true;
            x.style.display = "none";
            document.getElementById('msjTXT').innerHTML = '<br>El vehículo ya esta registrado.';
          }
        },
        error: function(data) {
          //lo que devuelve si falla
          alert('Error de conexion. ' + ' ' + data);

        }
      });
    }
  }

  function aceptarVehiculo() {
    var b = document.getElementById("agrega");
    if (document.getElementById("checkAceptar").checked) {
      b.disabled = false;
    } else {
      b.disabled = true;
    }
  }
</script>

<!-- Modal -->
<div class="modal fade" id="nuevo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Remolque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="?seccion=vehiculos&accion=agrega" method="post">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              Número ecónomico:
              <input type="text" name="numero" id="numero" class="form-control" maxlength="8" required />
            </div>
          </div>
          <div class="row">
            <div class="col">
              No de Serie:
              <input type="text" name="serie" id="serie" class="form-control" maxlength="20" required />
            </div>
          </div>
          <div class="row">
            <div class="col mb-2">
              Circuito:
              <select name="circuito" id="circuito" class="custom-select">
                <?php while ($registro1 = $query1->fetch()) {  ?>
                  <option value="<?php echo $registro1["pk_clave_cir"]; ?>"><?php echo $registro1["txt_nombre_cir"]; ?></option>
                <?php  } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
          <button type="submit" id="agrega" class="btn btn-success">AGREGAR</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $query1->closeCursor(); ?>