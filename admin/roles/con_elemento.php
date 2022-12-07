<?php 

	$strSQL  = " SELECT * FROM tb_usuarios, tb_roles, tb_empresas WHERE fk_clave_rol=pk_clave_rol AND fk_clave_emp=pk_clave_emp ";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

	if (isset($_GET["nombre"]))  
		$strSQL .= " AND txt_nombre_usu LIKE'%".$_GET["nombre"]."%' ";

	if (isset($_GET["usuario"]))  
		$strSQL .= " AND txt_usuario_usu ='".$_GET["usuario"]."' ";
 
	if (isset($_GET["rol"]))  
		$strSQL .= " AND txt_nombre_rol ='".$_GET["rol"]."' ";

	if (isset($_GET["empresa"]))  
		$strSQL .= " AND txt_nombre_emp ='".$_GET["empresa"]."' ";

	if (isset($_GET["orden"])) {
			$orden = $_GET["orden"];
		switch ($orden) {			
			case "nombre_up":
					set_sesionesdesplegar("nombre_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
			case "nombre_do":
					set_sesionesdesplegar("nombre_do");
					$strSQL .= " ORDER BY ".$campoMostrar." DESC ";
				break;
			case "usuario_up":
					set_sesionesdesplegar("usuario_up");
					$strSQL .= " ORDER BY txt_usuario_usu ASC ";
				break;
			case "usuario_do":
					set_sesionesdesplegar("usuario_do");
					$strSQL .= " ORDER BY txt_usuario_usu DESC ";
				break;
			case "rol_up":
					set_sesionesdesplegar("rol_up");
					$strSQL .= " ORDER BY fk_clave_rol ASC ";
				break;
			case "rol_do":
					set_sesionesdesplegar("rol_do");
					$strSQL .= " ORDER BY fk_clave_rol DESC ";
				break;
			case "activo_up":
					set_sesionesdesplegar("activo_up");
					$strSQL .= " ORDER BY num_activo_usu ASC ";
				break;
			case "activo_do":
					set_sesionesdesplegar("activo_do");
					$strSQL .= " ORDER BY num_activo_usu DESC ";
				break;
			case "empresa_up":
					set_sesionesdesplegar("empresa_up");
					$strSQL .= " ORDER BY txt_nombre_emp ASC ";
				break;
			case "empresa_do":
					set_sesionesdesplegar("empresa_do");
					$strSQL .= " ORDER BY txt_nombre_emp DESC ";
				break;
			default:
					set_sesionesdesplegar("nombre_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
		}
		
	}
	else {
		set_sesionesdesplegar("nombre_up");
		$strSQL .= " ORDER BY ".$campoMostrar."  ASC ";
	}
	

	include_once("general/calc_navegacion.php");
	

	if (isset($_GET["inicia"])) {
		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
	}
	else {
		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
	}

?>