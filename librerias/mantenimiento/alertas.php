<?php     
include_once("alertas/def_variables.php");
include_once("general/def_sesiones.php");
include_once("alertas/for_controles.php");
include_once("alertas/con_elemento.php");	 
include_once("general/def_orden.php");	
include_once("alertas/for_elemento.php");
include_once("general/imp_navegacion.php");     


/*	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
	switch ($accion)
	{		
           case "lista":				
		include_once("general/def_sesiones.php");
		include_once("alertas/for_controles.php");
		include_once("alertas/con_elemento.php");	 
                include_once("general/def_orden.php");	
		include_once("alertas/for_elemento.php");
		include_once("general/imp_navegacion.php");
                break;	
           case "imprime":				
		include_once("general/def_sesiones.php");
		include_once("alertas/con_elemento.php");	 
		include_once("general/def_orden.php");	
		include_once("alertas/for_imprime.php");
		break;
	   case "pdf":				
		include_once("general/def_sesiones.php");
		include_once("alertas/con_elemento.php");	 
		include_once("general/def_orden.php");	
		include_once("alertas/for_pdf.php");
		break;
	   case "verifica":				
		include("alertas/app_verifica.php");
                break;	
           case "reabre":				
		include("alertas/app_reabre.php");
		break;	
	   case "verificaseleccionados":				
		include("alertas/app_verificaseleccionados.php");
		break;	
	 	}
?*/