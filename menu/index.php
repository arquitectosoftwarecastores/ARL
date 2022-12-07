<?php
?>

<div class="row" style="padding: -20px; ">
  <div class="col-sm-2">
    <a href="?modulo=bitacora">
      <img id="logo" src="./assets/img/logo.png" alt="GRUPO CASTORES" />
    </a>
  </div>
  <div class="col-sm-8 centrado">
    <h2>SISTEMA PARA MONITOREO DE REMOLQUES</h2>
    <p class="centrado" style="margin:0px;">Bienvenido <?php echo $_SESSION["nombre"]; ?>, <strong>Permiso:</strong> <?php echo $_SESSION["nombrerol"]; ?></p>
  </div>
  <div class="col-md-2 derecha">
    <button type="button" id="botonmenu">
      <span class="barramenu"></span>
      <span class="barramenu"></span>
      <span class="barramenu"></span>
    </button>
  </div>
</div>


<div id="menuprincipal">
  <ul>
    <a href="?seccion=bienvenido">
      <li>INICIO </li>
    </a>
    <?php //if(//isset($_SESSION["monitoreo"])) { 
    ?>
    <?php if (true) { ?>
      <a href="?seccion=monitoreo&amp;accion=lista" target="_blank">
        <li>MONITOREO</li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["alertas"])) { ?>
      <a href="?seccion=alertas&amp;accion=lista&amp;estatus=0&amp;prioridad=3&amp;orden=fecha_do" target="_blank">
        <li>MONITOR DE ALERTAS</li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["alertasmodulos"])) { ?>
      <a href="?seccion=alertasmodulos&amp;accion=lista&amp;estatus=0&amp;estatus2=0&amp;prioridad=3&amp;orden=fecha_do" target="_blank">
        <li>ALERTAS POR MODULOS</li>
      </a>
    <?php } ?>

    <?php if (isset($_SESSION["mapacalor"]) || isset($_SESSION["mapaalertas"]) || isset($_SESSION["mapasmonitoreo"])) { ?>
      <a data-toggle="collapse" data-target="#demo">
        <li>MAPAS</li>
      </a>
      <div id="demo" class="collapse">

        <?php if (isset($_SESSION["mapaalertas"])) { ?>
          <a href="mapaalertas/" target="_blank">
            <li>&ensp;MAPA DE ALERTAS</li>
          </a>
        <?php } ?>
        <?php if (isset($_SESSION["mapacalor"])) { ?>
          <a href="mapacalor/muestra.php" target="_blank">
            <li>&ensp;MAPA DE COBERTURA</li>
          </a>
        <?php } ?>
        <?php if (isset($_SESSION["mapasmonitoreo"])) { ?>
          <a href="?seccion=mapasmonitoreo" target="_blank">
            <li>&ensp;MAPAS DE MONITOREO</li>
          </a>
        <?php } ?>

      </div>
    <?php } ?>
    <?php if (isset($_SESSION["posiciones"])) { ?>
      <a href="?seccion=posiciones&amp;accion=captura" target="_blank">
        <li>HISTÓRICO DE POSICIONES</li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["cercanas"])) { ?>
      <a href="?seccion=cercanas" target="_blank">
        <li>UNIDADES CERCANAS</li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["mantenimiento"])) { ?>
      <a href="?seccion=mantenimiento&accion=lista&estatus=0" target="_blank">
        <li>MANTENIMIENTO</li>
      </a>
    <?php } ?>
    <?php if (true) { ?>
      <a href="?seccion=usuarios&amp;accion=lista" target="_blank">
        <li>USUARIOS <strong>(<?php echo $usuarios ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["roles"])) { ?>
      <a href="?seccion=roles&amp;accion=lista" target="_blank">
        <li>ROLES <strong>(<?php echo $roles ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["modulos"])) { ?>
      <a href="?seccion=modulos&amp;accion=lista" target="_blank">
        <li>MODULOS <strong>(<?php echo $modulos ?>)</strong></li>
      </a>
    <?php } ?>

    <?php if (isset($_SESSION["circuitos"])) { ?>
      <a href="?seccion=circuitos&amp;accion=lista" target="_blank">
        <li>CIRCUITOS <strong>(<?php echo $circuitos ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (true || isset($_SESSION["vehiculos"])) {
    ?>
      <a href="?seccion=vehiculos&amp;accion=lista" target="_blank">
        <li>VEHÍCULOS </li>
      </a>
    <?php
    } ?>
    <!--
      <?php if (isset($_SESSION["operadores"])) { ?>
      <a href="?seccion=operadores&amp;accion=lista" target="_blank"><li>OPERADORES <strong>(<?php echo $operadores ?>)</strong></li></a>
      <?php } ?>
      -->
    <?php if (isset($_SESSION["autoridades"])) { ?>
      <a href="?seccion=autoridades&amp;accion=lista" target="_blank">
        <li>AUTORIDADES <strong>(<?php echo $autoridades ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["empresas"])) { ?>
      <a href="?seccion=empresas&amp;accion=lista" target="_blank">
        <li>EMPRESAS <strong>(<?php echo $empresas ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["zonasderiesgo"])) { ?>
      <a href="?seccion=geocercaszonasderiesgo&amp;accion=muestra" target="_blank">
        <li>ZONAS DE RIESGO</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["zonas"])) { ?>
      <a href="?seccion=zonas&amp;accion=lista" target="_blank">
        <li>ZONAS <strong>(<?php echo $zonas ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["rutas"])) { ?>
      <a href="?seccion=rutas&amp;accion=lista" target="_blank">
        <li>RUTAS <strong>(<?php echo $rutas ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["tiposdealertas"])) { ?>
      <a href="?seccion=tiposdealertas&amp;accion=lista" target="_blank">
        <li>TIPOS DE ALERTAS <strong>(<?php echo $tiposdealertas ?>)</strong></li>
      </a>
    <?php } ?>
    <?php if (isset($_SESSION["ubicacion"])) { ?>
      <a href="?seccion=ubicacion" target="_blank">
        <li>UBICACIÓN</li>
      </a>
    <?php } ?>

    <?php if (isset($_SESSION["paradas"]) || isset($_SESSION["reporteusuarios"]) || isset($_SESSION["reportedevehiculos"])) { ?>
      <a data-toggle="collapse" data-target="#REPORTES">
        <li>REPORTES</li>
      </a>
      <div id="REPORTES" class="collapse">
        <?php if (isset($_SESSION["paradas"])) { ?>
          <a href="?seccion=paradas&amp;accion=captura" target="_blank">
            <li>&ensp;REPORTE DE PARADAS</li>
          </a>
        <?php } ?>
        <?php if (isset($_SESSION["reporteusuarios"])) { ?>
          <a href="?seccion=reporteusuarios&amp;accion=lista" target="_blank">
            <li>&ensp;REPORTE DE USUARIOS</li>
          </a>
        <?php } ?>
        <?php if (isset($_SESSION["reportedevehiculos"])) { ?>
          <a href="?seccion=reportedevehiculos&amp;accion=lista" target="_blank">
            <li>&ensp;REPORTE DE VEHICULOS</li>
          </a>
        <?php } ?>
      </div>
    <?php } ?>

    <?php if (isset($_SESSION["tools"]) || true) { ?>
      <a data-toggle="collapse" data-target="#TOOLS">
        <li>TOOLS</li>
      </a>
      <div id="TOOLS" class="collapse">
          <a href="?seccion=tools" target="_blank">
            <li>&ensp;POSICIONES</li>
          </a>
          <a href="?seccion=tools&amp;accion=comandos" target="_blank">
            <li>&ensp;CONEXIONES</li>
          </a>
      </div>
    <?php } ?>


    <?php if (isset($_SESSION["comandossms"]) || isset($_SESSION["mensajes"]) || isset($_SESSION["vehiculosporzona"])) { ?>
      <a data-toggle="collapse" data-target="#SMS">
        <li>SMS</li>
      </a>
      <div id="SMS" class="collapse">
        <?php if (isset($_SESSION["comandossms"])) { ?>
          <a href="?seccion=comandossms&amp;accion=captura" target="_blank">
            <li>&ensp;COMANDOS SMS</li>
          </a>
        <?php } ?>
        <?php if (isset($_SESSION["mensajes"])) { ?>
          <a href="?seccion=mensajes&amp;accion=lista" target="_blank">
            <li>&ensp;MENSAJES SMS</li>
          </a>
        <?php } ?>
      </div>
    <?php } ?>
    <?php if (isset($_SESSION["vehiculosporzona"])) { ?>
      <a href="?seccion=vehiculosporzona&amp;accion=lista" target="_blank">
        <li>VEHICULOS POR ZONA</li>
      </a>
    <?php } ?>
    <a href="?seccion=acceso&amp;accion=salir">
      <li>SALIR</li>
    </a>
  </ul>
</div>

<script>
  $("#botonmenu").click(function() {
    $("#menuprincipal").slideToggle("slow");
  });
</script>