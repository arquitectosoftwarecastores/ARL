<?php 
		//Módulo 3 = Remolques
		$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 3 and pk_clave_usu = ".$_SESSION['id'];
		$query = $conn->prepare($consulta);
		$query->execute();
		$registro = $query->fetch();
		$permiso = $registro['total'];
	
	  if($permiso>0) { 
		include_once("admin/vehiculos/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
		switch ($accion)
		{
			case "lista":				
					include_once("general/def_sesiones.php");
					include_once("admin/vehiculos/for_controles.php");
					include_once("admin/vehiculos/for_elementos_new.php");	 
					include_once("admin/vehiculos/for_nuevo.php");
					//include_once("general/def_orden.php");	
					//include_once("admin/vehiculos/for_elemento.php");
					//include_once("general/imp_navegacion.php");
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