<?php 
		//Módulo 1 = Usuarios
		$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 1 and pk_clave_usu = ".$_SESSION['id'];
		$query = $conn->prepare($consulta);
		$query->execute();
		$registro = $query->fetch();
		$permiso = $registro['total'];
	
	  if($permiso>0) { 
	
		include_once("admin/usuarios/def_variables.php");
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'lista';
		switch ($accion)
		{
			case "lista":				
					include_once("general/def_sesiones.php");
					include_once("general/for_controles.php");
					include_once("admin/usuarios/con_elemento.php");	 
					include_once("general/def_orden.php");	
					include_once("admin/usuarios/for_elemento.php");
					include_once("general/imp_navegacion.php");
				break;									
			case "agrega":						
				include ("admin/usuarios/con_agrega.php");
				break;			
			case "cambia":						
				include ("admin/usuarios/for_cambia.php");
				break;
			case "actualiza":
				include ("admin/usuarios/con_actualiza.php");
				break;
			case "borra":
				include ("admin/usuarios/con_borra.php"); 			
			break;
			case "eliminaseleccionados":
				include ("general/app_eliminaseleccionados.php"); 			
			break;
			case "estatus":
				include ("admin/usuarios/con_estatus.php"); 			
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