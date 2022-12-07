<?php
$accion = (isset($_GET['accion']) && $_GET['accion'] != '') ? $_GET['accion'] : 'ingresa';

$dir = __DIR__;

switch ($accion) {
  case 'ingresa':
    include_once($dir . '/ingresa.php');
    break;
  case 'registrate':
    include($dir . '/registrate.php');
    break;
  case 'valida':
    include($dir . '/valida.php');
    break;
  case 'salir':
    include($dir . '/salir.php');
    break;
  default:
    include_once($dir . '/ingresa.php');
    break;
}
