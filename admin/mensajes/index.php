<?php 
    if(isset($_SESSION["mensajes"])) 
    {
		include_once("admin/mensajes/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
		switch ($accion)
		{
			case "lista":				
					include_once("general/def_sesiones.php");
					include_once("admin/mensajes/for_controles.php");
					include_once("admin/mensajes/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/mensajes/for_elemento.php");
					include_once("general/imp_navegacion.php");
				break;	
			case "imprime":				
					include_once("general/def_sesiones.php");
					include_once("admin/mensajes/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/mensajes/for_imprime.php");
				break;
			case "pdf":				
					include_once("general/def_sesiones.php");
					include_once("admin/mensajes/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/mensajes/for_pdf.php");
				break;
			case "excel":				
					include_once("general/def_sesiones.php");
					include_once("admin/mensajes/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/mensajes/for_excel.php");
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