<!DOCTYPE html>
<html lang="es">
<?php
session_name("ARL");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Mexico_City");
include_once('./config/conexion.php');
$seccion = (isset($_GET['seccion']) && $_GET['seccion'] != '') ? $_GET['seccion'] : 'acceso';
// Obitne Titulo HTML
$htmlTitle = 'ARL';
if ($seccion != 'acceso') {
  $htmlTitle =  'ARL - ' . strtoupper($seccion);
}
?>

<head>
  <meta charset="UTF-8">
  <title><?php echo $htmlTitle; ?></title>
  <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="NOINDEX, NOFOLLOW">
  <!-- Fonts -->
  <link rel="font/otf" href="./assets/fonts/Orbitron-Regular.otf">
  <!-- Bootstrap v4.5.2 -->
  <link rel="stylesheet" href="./libs/bootstrap/css/bootstrap.min.css">
  <!-- Bootstrap Table -->
  <link rel="stylesheet" href="./libs/bootstrap-table/bootstrap-table.min.css">
  <?php
  include_once('./libs/custom/estilo.php');
  include_once('./config/gmapskey.php');
  ?>
  <!-- JavaScripts -->
  <script src="./libs/js/jquery-3.5.1.min.js"></script>
  <script src="./libs/popper/umd/popper.min.js"></script>
  <script src="./libs/daterangepicker/moment.min.js"></script>
  <script src="./libs/bootstrap/js/bootstrap.min.js"></script>
  <script src="./libs/custom/maps.js"></script>
  <script src="./libs/bootstrap-table/bootstrap-table.min.js"></script>
</head>

<body>
  <!-- JavaScripts -->
  <?php
  # Carga Vistas
  if($seccion == "mapacyber"){
  include ("monitoreo/app_mapacyber.php");
    exit;
  }
  if ($seccion == "acceso")
    include_once("acceso/index.php");
  if (isset($_SESSION['id'])) {
    include('./estructura/header.php');
    switch ($seccion) {
      case "bienvenido":
        include("./bienvenido/index.php");
        break;
      case "usuarios":
        include("./admin/usuarios/index.php");
        break;
      case "roles":
        include("./admin/roles/index.php");
        break;
      case "modulos":
        include("./admin/modulos/index.php");
        break;
      case "circuitos":
        include("./admin/circuitos/index.php");
        break;
      case "vehiculos":
        include("./admin/vehiculos/index.php");
        break;
      case "remolquesporsucursal":
        include("./remolquesporsucursal/index.php");
        break;
      case "remolquesusa":
          include("./remolquesusa/index.php");
          break;  
      case "posiciones":
          include("./posiciones/index.php");
          break;    
      case "paradas":
          include("./paradas/index.php");
          break;
        /* 
        case "puntosseguros":
        include("./admin/puntosseguros/index.php");
        break;
      case "ubicacion":
        include("./ubicacion/index.php");
        break;*/
      case "monitoreo":
        include("./monitoreo/index.php");
        break;
      case "zonas":
        include("./admin/zonas/index.php");
        break;/*
      case "rutas":
        include("./admin/rutas/index.php");
        break;
      case "tiposdealertas":
        include("./admin/tiposdealertas/index.php");
        break;*/
      case "alertas":
        include("./alertas/index.php");
        break;
        /*
      case "alertasmodulos":
        include("./alertasmodulos/index.php");
        break;
      case "mapacalor":
        include("./mapacalor/muestra.php");
        break;

      case "mapa":
        include("./monitoreo/app_mapa.php");
        break;
        */
      case "cercanas":
        include("./cercanas/index.php");
        break;
        /*
      case "comandossms":
        include("./admin/comandossms/index.php");
        break;
      case "mensajes":
        include("./admin/mensajes/index.php");
        break;
      case "vehiculosporzona":
        include("./vehiculosporzona/index.php");
        break;*/
      case "mantenimiento":
        include("./mantenimiento/index.php");
        break;
      case "mapasmonitoreo":
        include_once("./mapasmonitoreo/index.php");
        break;
      case "tools":
        include("./tools/index.php");
        break;
      default:
        include_once("./bienvenido/index.php");
        break;
    }
  } else {
    include_once('acceso/index.php');
  }
  ?>
</body>

</html>