<!DOCTYPE html>
<html lang="es">

<?php
session_name("scarlet");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Mexico_City');
mb_internal_encoding('UTF-8');

// Valida Sesion
if (!isset($_GET['modulo']) & !isset($_SESSION["scarlet_id"])) {
  $modulo = 'acceso';
} elseif (!isset($_GET['modulo'])) {
  $modulo = 'NO SE ENCONTRO LA PAGINA';
} else {
  $modulo = $_GET['modulo'];
}

// Obitne Titulo HTML

$htmlTitle = 'SCARLET';
if ($modulo != 'acceso') {
  $htmlTitle =  'SCARLET - ' . strtoupper($modulo);
}
?>

<head>

  <meta charset="UTF-8">
  <title><?php echo $htmlTitle; ?></title>

  <link rel="shortcut icon" href="./assets/img/icon.png" type="image/x-icon">


  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="NOINDEX, NOFOLLOW">

  <!-- Fonts -->
  <link rel="font/otf" href="./assets/fonts/Orbitron-Regular.otf">

  <!-- Bootstrap v4.5.2 -->
  <link rel="stylesheet" href="./libs/bootstrap/css/bootstrap.min.css">

  <!-- Bootrstrap Extension -->
  <link rel="stylesheet" href="./libs/bootstrap-table/extensions/group-by-v2/bootstrap-table-group-by.min.css">

  <!-- Bootstrap Table -->
  <link rel="stylesheet" href="./libs/bootstrap-table/bootstrap-table.min.css">

  <!-- Bootstrao Select -->
  <link rel="stylesheet" href="./libs/bootstrap-select/css/bootstrap-select.min.css">

  <!-- Date Range Picker -->
  <link rel="stylesheet" href="./libs/daterangepicker/daterangepicker.min.css">

  <?php
  include_once('./libs/custom/estilo.php');
  include_once('./src/db/conexion.php');
  ?>

</head>

<body>
  <!-- JavaScripts -->
  <script src="./libs/js/jquery-3.5.1.min.js"></script>
  <script src="./libs/popper/umd/popper.min.js"></script>
  <script src="./libs/daterangepicker/moment.min.js"></script>
  <script src="./libs/bootstrap/js/bootstrap.min.js"></script>
  <script src="./libs/bootstrap-table/bootstrap-table.min.js"></script>
  <script src="./libs/bootstrap-table/extensions/group-by-v2/bootstrap-table-group-by.min.js"></script>
  <script src="./libs/bootstrap-table/locale/bootstrap-table-es-MX.min.js"></script>
  <script src="./libs/bootstrap-select/js/bootstrap-select.min.js"></script>
  <script src="./libs/bootstrap-select/js/i18n/defaults-es_ES.min.js"></script>
  <script src="./libs/daterangepicker/daterangepicker.min.js"></script>

  <script src="./libs/custom/alerts.js"></script>
  <script src="./libs/custom/forms.js"></script>
  <script src="./libs/custom/tables.js"></script>
  <script src="./libs/custom/cookies.js"></script>
  <!-- JavaScripts -->

  <?php

  # Carga Vistas
  include_once('./src/index.php');

  ?>

</body>

<!--

       .---.
      @ @   )
      ^     |
     [|]    | ##
     /      |####
    (       |#IN#
     \| /   |#TI#
    / |.'   |###
   _\ ``\   )##
  /,,_/,,____#

-->


</html>