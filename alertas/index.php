<?php  
    //Módulo 7 = Alertas
	$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 7 and pk_clave_usu = ".$_SESSION['id'];
	$query = $conn->prepare($consulta);
	$query->execute();
	$registro = $query->fetch();
	$permiso = $registro['total'];

    if($permiso>0) 
    { 
		include_once("alertas/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
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