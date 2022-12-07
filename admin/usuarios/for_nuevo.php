<!-- Fortaleza de contraseñas -->
<script type="text/javascript">
  var b = document.getElementById("agrega");
  var x = document.getElementById("msjAdv");
  x.style.display = "none";
  // b.disabled = false;

  function verificarUsuario(str) {
    if (str.length > 2) {
      var x = document.getElementById("msjAdv");
      var b = document.getElementById("agrega");
      var usuario = str;

      $.ajax({
        type: 'POST', //aqui puede ser igual get
        url: 'admin/usuarios/con_verifica.php', //aqui va tu direccion donde esta tu funcion php
        data: {
          usuario
        }, //aqui tus datos
        success: function(data) {
          //lo que devuelve
          //alert(data);

          if (data > 0) {
            x.style.display = "none";
            b.disabled = false;

          } else {
            document.getElementById("checkAceptar").checked = false;
            x.style.display = "block";
            b.disabled = true;

          }
        },
        error: function(data) {
          //lo que devuelve si falla
          alert('Error de conexion. ' + ' ' + data);

        }
      });

    }

  }


  function aceptarUsuario() {
    var b = document.getElementById("agrega");
    if (document.getElementById("checkAceptar").checked) {
      b.disabled = false;
    } else {
      b.disabled = true;
    }
  }
</script>

<?php

$consulta1  = "SELECT * FROM tb_roles ORDER BY txt_nombre_rol ASC";
$query1 = $conn->prepare($consulta1);
$query1->execute();

$consulta2  = "SELECT * FROM tb_empresas ORDER BY txt_nombre_emp ASC";
$query2 = $conn->prepare($consulta2);
$query2->execute();

?>
<!-- Modal -->
<div class="modal fade" id="nuevo" tabindex="-1" aria-labelledby="usuarioNuevo" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nuevo Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="?seccion=<?php echo $seccion; ?>&amp;accion=agrega" id="form1" method="post">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              Nombre:
              <input type="text" name="nombre" id="nombre" class="form-control" size="120" maxlength="120"  required/>
            </div>
          </div>
          <div class="row" id="pwd-container">
            <div class="col-md-6">
              Usuario:<input type="text" name="usuario" id="usuario" onfocusout="// verificarUsuario(this.value)" class="form-control" size="20" maxlength="20" required />
            </div>
            <div class="col-md-6" id="pwd-container">
              Contraseña:
              <input type="password" name="password" id="password" class="form-control" size="25" maxlength="25" required />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              Correo:
              <input type="email" name="correo" id="correo" class="form-control" required />
            </div>
            <div class="col-md-6">
              <p>&nbsp;</p>
              Usuario activo:&nbsp;<input type="checkbox" name="activo" id="activo" checked value="1" class="text-input text" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              Rol:
              <select name="rol" id="rol" class="form-control">
                <?php while ($registro1 = $query1->fetch()) {  ?>
                  <option value="<?php echo $registro1["pk_clave_rol"]; ?>"><?php echo $registro1["txt_nombre_rol"]; ?></option>
                <?php  } ?>
              </select>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col">
            <h6>Circuitos</h6>
            <hr />
            </div>
          </div>
          <div class="row">
            <?php
            $consulta3  = "SELECT * FROM  tb_circuitos 
                            ORDER BY txt_nombre_cir ASC";
            $query3 = $conn->prepare($consulta3);
            $query3->execute();
            while ($registro3 = $query3->fetch()) {
            ?>
              <div class="col-md-4">
                <input type="checkbox" name="circuitos[]" value="<?php echo $registro3["pk_clave_cir"] ?>" /> &nbsp;<?php echo $registro3["txt_nombre_cir"] ?>
              </div>
            <?php
            }
            ?>
          </div>

          <!--
          <hr />

          <div class="row">
            <div class="col-md-12">
              Usuario maestro:&nbsp;<input type="checkbox" name="maestro" id="maestro" value="1" class="text-input text" /><br />
              Acceso externo:&nbsp;<input type="checkbox" name="acceso_externo" id="acceso_externo" value="1" class="text-input text" />
            </div>
          </div>

          <div id="msjAdv" class="row">
            <div class="col-md-12">
              <p style="color: red;">*El usuario no se encuentra en la base de datos de Castores. ¿Desea registrar al usuario?
              </p>
              <input type="checkbox" id="checkAceptar" value="1" onclick="aceptarUsuario()" required>Aceptar
            </div>
          </div>

          -->
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