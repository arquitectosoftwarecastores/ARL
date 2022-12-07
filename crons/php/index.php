<?php  session_start(); ?>
<!doctype html>
<html lang="es">
<?php
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");
    include ('./config/conexion.php');
    $latitudcentro=24.517002;
    $longitudcentro=-101.788702;
    $seccion = (isset($_GET['seccion']) && $_GET['seccion']!='') ? $_GET['seccion'] : 'acceso';
?>
<head>
<meta charset="utf-8" />
<meta name="description" content="" >
<meta name="author" content="" >
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="keywords" content=" " >
<meta name="robots" content="NOINDEX, NOFOLLOW" >
<meta name="geo.region" content="MX-GUA"/>
<meta name="geo.placename" content="León Gto, Guanajuato"/>
<meta name="geo.position" content=" "/>
<title></title>
    <!-- jQuery plugin -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="librerias/jquery.min.js"></script>
    <script src="admin/rutas/scripts/monitoreo_unidades.js"></script>
    <!-- Validation-Engine plugin -->
    <script src="librerias/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
    <script src="librerias/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="librerias/validationEngine.jquery.min.css" type="text/css"/>
    <!-- Bootstrap plugin -->
    <script src="librerias/jquery-ui.js"></script>
    <!-- AngularJS -->
    <script type="text/javascript" src="librerias/angular.min.js"></script>
    <!-- Bootstrap plugin -->
    <script src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <link href='librerias/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
    <!-- Funciones jquery -->
    <script type="text/javascript" src="jquery/jfunciones.js"></script>
    <!-- Google Maps API -->
    <?php include_once('./config/gmapskey.php') ?>
    <?php  include ('./libs/css/estilo.php');  ?>
</head>
<body>
<?php
   if($seccion=="acceso")
        include ("acceso/index.php");
    if(isset($_SESSION["id"]))
    {
        if($seccion!="historico"){
          include ("menu/index.php");
          include ("encabezado/index.php");
        }else {
          include ("historico/historico_trackerP.php");
        }
        switch ($seccion)
        {
        case "bienvenido":
            include ("bienvenido/index.php");
            break;
        case "usuarios":
            include ("admin/usuarios/index.php");
            break;
        case "roles":
            include ("admin/roles/index.php");
            break;
        case "modulos":
            include ("admin/modulos/index.php");
            break;
        case "empresas":
            include ("admin/empresas/index.php");
            break;
        case "autoridades":
            include ("admin/autoridades/index.php");
            break;
        case "grupos":
            include ("admin/grupos/index.php");
            break;
        case "circuitos":
            include ("admin/circuitos/index.php");
            break;
        case "vehiculos":
            include ("admin/vehiculos/index.php");
            break;
        case "operadores":
            include ("admin/operadores/index.php");
            break;
        case "puntosseguros":
            include ("admin/puntosseguros/index.php");
            break;
        case "ubicacion":
            include ("ubicacion/index.php");
            break;
        case "monitoreo":
            include ("monitoreo/index.php");
            break;
        case "monitoreo2":
            include ("monitoreo2/index.php");
            break;
       case "zonas":
            include ("admin/zonas/index.php");
            break;
        case "rutas":
            include ("admin/rutas/index.php");
            break;
        case "tiposdealertas":
            include ("admin/tiposdealertas/index.php");
            break;
        case "alertas":
            include ("alertas/index.php");
            break;
        case "alertasmodulos":
            include ("alertasmodulos/index.php");
            break;
	 case "mapacalor":
            include ("mapacalor/muestra.php");
            break;
        case "posiciones":
            include ("posiciones/index.php");
            break;
        case "paradas":
            include ("paradas/index.php");
            break;
        case "mapa":
            include ("monitoreo/app_mapa.php");
            break;
        case "cercanas":
            include ("cercanas/index.php");
            break;
        case "comandossms":
            include ("admin/comandossms/index.php");
            break;
        case "mensajes":
            include ("admin/mensajes/index.php");
            break;
        case "vehiculosporzona":
            include ("vehiculosporzona/index.php");
            break;
        case "reportedevehiculos":
            include ("reportedevehiculos/index.php");
            break;
        case "conductor":
            include ("vehiculos/app_conductor.php");
            break;
        case "reporteusuarios":
            include ("admin/reporteusuarios/index.php");
            break;
        case "mantenimiento":
            include ("mantenimiento/index.php");
            break;
        case "mapasmonitoreo":
            include ("mapasmonitoreo/index.php");
            break;
        }

        if(isset($_SESSION["botondepanico"]))
           include ("botondepanico/index.php");
    }
    else
    {
      if($seccion!="acceso")
      {
	if($seccion=="mapacyber")
	{
		include ("monitoreo/app_mapacyber.php");
	}
	else{
?>
        <script>
            window.location.href = "?seccion=acceso&accion=ingresa&finalizosesion=1";
        </script>
<?php
	      }
	}
    }
?>
</body>
</html>
