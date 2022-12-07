<?php

if (isset($_SESSION['tools'])) {
  if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
      case 'conexiones':
        include_once('tools/for_conexiones.php');
        break;

      case 'comandos':
        include_once('tools/for_comandos.php');
        break;
      
      default:
        include_once('tools/for_tools.php');
        break;
    }
  } else {
    include_once('tools/for_tools.php');
  }
} else {
  echo '<h2>No tiene permisos para acceder a este modulo.</h2>';
}