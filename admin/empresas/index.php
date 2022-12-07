<?php
    if(isset($_SESSION["empresas"])) 
    {
		include_once("admin/empresas/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
		switch ($accion)
		{
			case "lista":				
					include_once("general/def_sesiones.php");
					include_once("general/for_controles.php");
					include_once("admin/empresas/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/empresas/for_elemento.php");
					include_once("general/imp_navegacion.php");
				break;									
			case "agrega":						
				include ("admin/empresas/con_agrega.php");
				break;			
			case "cambia":						
				include ("admin/empresas/for_cambia.php");
				break;
			case "actualiza":
				include ("admin/empresas/con_actualiza.php");
				break;
			case "borra":
				include ("general/con_borra.php"); 			
			break;
			case "eliminaseleccionados":
				include ("general/app_eliminaseleccionados.php"); 			
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