<?php

  include("funciones/cuentaregistros.php");  
  $usuarios=cuentaRegistros("tb_usuarios",$conn);
  $roles=cuentaRegistros("tb_roles",$conn);
  $modulos=cuentaRegistros("tb_modulos",$conn);
  //$grupos=cuentaRegistros("tb_grupos",$conn);
  $circuitos=cuentaRegistros("tb_circuitos",$conn);
  $vehiculos=cuentaRegistros("tb_vehiculos",$conn);
  $operadores=cuentaRegistros("tb_operadores",$conn);
  $autoridades=cuentaRegistros("tb_autoridades",$conn);
  $empresas=cuentaRegistros("tb_empresas",$conn);
  $zonas=cuentaRegistros("tb_zonas",$conn);
  $rutas=cuentaRegistros("tb_rutas",$conn);
  $tiposdealertas=cuentaRegistros("tb_tiposdealertas",$conn);

?>  

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-1">
        <img id="logo" src="imagenes/logo.jpg" alt="Sistema de Monitoreo" />
      </div>
      <div class="col-md-10 centrado">
        <h2>SISTEMA PARA MONITOREO DE UNIDADES</h2>
        <p class="centrado" style="margin:0px;">Bienvenido <?php echo $_SESSION["nombre"];?>, <strong>Empresa:</strong> <?php echo $_SESSION["nombreempresa"];?>, <strong>Permiso:</strong> <?php echo $_SESSION["nombrerol"];?></p>
      </div>
      <div class="col-md-1">
        <button type="button" id="botonmenu">
          <span class="barramenu"></span>
          <span class="barramenu"></span>
          <span class="barramenu"></span>
        </button>      
      </div>
    </div>
  </div>

  <div id="menuprincipal">
    <ul>
      <a href="?seccion=acceso&amp;accion=bienvenido"><li>INICIO </li></a>
      <?php if(isset($_SESSION["puntosseguros"])) { ?>
        <a href="?seccion=puntosseguros&amp;accion=lista" target="_blank"><li>PUNTOS SEGUROS</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["monitoreo"])) { ?>
        <a href="?seccion=monitoreo&amp;accion=lista" target="_blank"><li>MONITOREO</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["alertas"])) { ?>
        <a href="index.php?seccion=alertas&amp;accion=lista&amp;estatus=0&amp;prioridad=3&amp;orden=fecha_do" target="_blank"><li>MONITOR DE ALERTAS</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["posiciones"])) { ?>
        <a href="?seccion=posiciones&amp;accion=captura" target="_blank"><li>HISTÓRICO DE POSICIONES</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["cercanas"])) { ?>
        <a href="?seccion=cercanas" target="_blank"><li>UNIDADES CERCANAS</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["paradas"])) { ?>
        <a href="?seccion=paradas&amp;accion=captura" target="_blank"><li>REPORTE DE PARADAS</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["usuarios"])) { ?>
        <a href="?seccion=usuarios&amp;accion=lista" target="_blank"><li>USUARIOS <strong>(<?php echo $usuarios?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["roles"])) { ?>
      <a href="?seccion=roles&amp;accion=lista" target="_blank"><li>ROLES <strong>(<?php echo $roles?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["modulos"])) { ?>
      <a href="?seccion=modulos&amp;accion=lista" target="_blank"><li>MODULOS <strong>(<?php echo $modulos?>)</strong></li></a>
      <?php } ?>
      
      <?php if(isset($_SESSION["circuitos"])) { ?>
      <a href="?seccion=circuitos&amp;accion=lista" target="_blank"><li>CIRCUITOS <strong>(<?php echo $circuitos?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["vehiculos"])) { ?>
      <a href="?seccion=vehiculos&amp;accion=lista" target="_blank"><li>VEHÍCULOS <strong>(<?php echo $vehiculos?>)</strong></li></a>
      <?php } ?>
      <!--
      <?php if(isset($_SESSION["operadores"])) { ?>
      <a href="?seccion=operadores&amp;accion=lista" target="_blank"><li>OPERADORES <strong>(<?php echo $operadores?>)</strong></li></a>
      <?php } ?>
      -->
      <?php if(isset($_SESSION["autoridades"])) { ?>
      <a href="?seccion=autoridades&amp;accion=lista"  target="_blank"><li>AUTORIDADES <strong>(<?php echo $autoridades?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["empresas"])) { ?>
      <a href="?seccion=empresas&amp;accion=lista" target="_blank"><li>EMPRESAS <strong>(<?php echo $empresas?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["zonasderiesgo"])) { ?>
      <a href="?seccion=geocercaszonasderiesgo&amp;accion=muestra" target="_blank"><li>ZONAS DE RIESGO</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["zonas"])) { ?>                
      <a href="?seccion=zonas&amp;accion=lista" target="_blank"><li>ZONAS <strong>(<?php echo $zonas?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["rutas"])) { ?>                
      <a href="?seccion=rutas&amp;accion=lista" target="_blank"><li>RUTAS <strong>(<?php echo $rutas?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["tiposdealertas"])) { ?>                
      <a href="?seccion=tiposdealertas&amp;accion=lista" target="_blank"><li>TIPOS DE ALERTAS <strong>(<?php echo $tiposdealertas?>)</strong></li></a>
      <?php } ?>
      <?php if(isset($_SESSION["ubicacion"])) { ?>
      <a href="?seccion=ubicacion"  target="_blank"><li>UBICACIÓN</li></a>
      <?php } ?>    
      <?php if(isset($_SESSION["comandossms"])) { ?>
      <a href="?seccion=comandossms&amp;accion=captura"  target="_blank"><li>COMANDOS SMS</li></a>
      <?php } ?> 
      <?php if(isset($_SESSION["mensajes"])) { ?>
      <a href="?seccion=mensajes&amp;accion=lista"  target="_blank"><li>MENSAJES SMS</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["vehiculosporzona"])) { ?>
      <a href="?seccion=vehiculosporzona&amp;accion=lista"  target="_blank"><li>VEHICULOS POR ZONA</li></a>
      <?php } ?>
      <?php if(isset($_SESSION["reportedevehiculos"])) { ?>
      <a href="?seccion=reportedevehiculos&amp;accion=lista"  target="_blank"><li>REPORTE DE VEHICULOS</li></a>
      <?php } ?> 
      <a href="?seccion=acceso&amp;accion=salir"><li>SALIR</li></a>
    </ul>
  </div>

<script>
$("#botonmenu").click(function() { 
    $("#menuprincipal").slideToggle( "slow" );
});
</script>
