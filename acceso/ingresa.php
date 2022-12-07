<style>
  html {
    background: url(./assets/img/background.jpg) no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    background-color: none !important;
  }
</style>


<div class="contenedoracceso">

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="blanco centrado">SISTEMA PARA MONITOREO DE REMOLQUES</h1>
      </div>
    </div>
  </div>

  <div class="container-fluid lineanegra"></div>

  <div class="container-fluid" style="background-color: #FFFFFF">

    <br>

    <div class="container">
      <?php

      # Valida si muestra Mensaje
      if (isset($_GET["mensaje"])) {

        if (strcmp($_GET["mensaje"], "novalido") == 0) {
          # Mensaje de usuario no valido
      ?>
          <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Por favor verifique su usuario y/o contraseña.</strong> Intente nuevamente.
          </div>
        <?php
        } elseif (strcmp($_GET["mensaje"], "terminada") == 0) {
          # Mensaje de sesion finalizada
        ?>
          <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Sesión Finzalizada.</strong> Su sesión ha finalizado por favor vuelva ingresar con su Usuario y Contraseña.
          </div>
        <?php
        } elseif (strcmp($_GET["mensaje"], "permiso") == 0) {
          # Mensaje de sesion finalizada
        ?>
          <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Acceso denegado.</strong> No tiene los permisos necesarios para acceder a este sistema.
          </div>
      <?php
        }
      }
      ?>

      <div class="row">
        <div class="col-md-4">
          <img id="logo" src="./assets/img/logo.jpg" alt="Grupo Castores" />
        </div>
        <div class="col-md-4 centrado">
          <form id="form1" class="ingresa" action="?seccion=acceso&amp;accion=valida" method="post">
            <fieldset>

              <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" required />

              <br>

              <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Contraseña" required />
              <br />
              <button type="submit" style="margin:0 auto;" class="btn btn-outline-dark">Entrar</button>
            </fieldset>
          </form>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
    <br>
  </div>
  <div class="container-fluid lineanegra"></div>

  <div class="container-fluid">
    <div class="container">
      <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
          <img id="vehiculo" class="centrado" src="./assets/img/vehiculo.png" alt="CASTORES" />
        </div>
      </div>
    </div>
  </div>

</div>