<?php 
    if(isset($_SESSION["vehiculos"])) 
    {
		include_once("admin/reporteusuarios/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
		switch ($accion)
		{
			case "lista":				
					include_once("general/def_sesiones.php");
					include_once("admin/reporteusuarios/for_controles.php");
					include_once("admin/reporteusuarios/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/reporteusuarios/for_elemento.php");
			  //		include_once("general/imp_navegacion.php");
				break;									
			case "agrega":						
				include ("admin/vehiculos/con_agrega.php");
				break;			
			case "cambia":						
				include ("admin/vehiculos/for_cambia.php");
				break;
			case "actualiza":
				include ("admin/vehiculos/con_actualiza.php");
				break;
			case "borra":
				include ("admin/vehiculos/con_borra.php"); 			
			break;
			case "eliminaseleccionados":
				include ("general/app_eliminaseleccionados.php"); 			
			break;	
			case "cambiacircuito":
				include ("admin/vehiculos/for_cambiacircuito.php"); 			
			break;
			case "actualizacircuito":
				include ("admin/vehiculos/con_actualizacircuito.php"); 			
			break;	
			case "imprime":				
					include_once("general/def_sesiones.php");
					include_once("admin/vehiculos/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/vehiculos/for_imprime.php");
				break;
			case "pdf":				
					include_once("general/def_sesiones.php");
					include_once("admin/vehiculos/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/vehiculos/for_pdf.php");
				break;
			case "excel":				
					include_once("general/def_sesiones.php");
					include_once("admin/vehiculos/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/vehiculos/for_excel.php");
				break;
	 	}
	}
	else
	{
     ?>
		<div class="container">
		   <div class="alert alert-warning">
		        <a href="#" class="close" data-dismiss="alert">&times;</a>
		        <strong>Su usuario no tiene acceso a este módulo</strong>.
		    </div>
		</div>
    <?php
	}
?>