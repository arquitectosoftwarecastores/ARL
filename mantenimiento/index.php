<?php
include_once("mantenimiento/def_variables.php");
$accion = (isset($_GET['accion']) && $_GET['accion'] != '') ? $_GET['accion'] : 'lista';
switch ($accion) {
	case "lista":
		include_once("general/def_sesiones.php");
		include_once("mantenimiento/for_controles.php");
		include_once("mantenimiento/con_elemento.php");
		include_once("general/def_orden.php");
		include_once("mantenimiento/for_elemento.php");
		include_once("general/imp_navegacion.php");
		break;
}
