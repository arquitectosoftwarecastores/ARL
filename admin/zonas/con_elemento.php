<?php 

	$strSQL  = " SELECT * FROM tb_zonas, tb_tiposdezona , tb_municipios, tb_estados WHERE fk_clave_tipz=pk_clave_tipz AND fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".strtoupper($_GET["busca"])."%' ";

	if (isset($_GET["nombre"]))  
		$strSQL .= " AND txt_nombre_zon ='".$_GET["nombre"]."' ";	

	if (isset($_GET["ciudad"]))  
		$strSQL .= " AND pk_clave_mun ='".$_GET["ciudad"]."' ";

	if (isset($_GET["estado"]))  
		$strSQL .= " AND txt_nombre_edo ='".$_GET["estado"]."' ";

	if (isset($_GET["tipo"]))  
		$strSQL .= " AND txt_nombre_tipz ='".$_GET["tipo"]."' ";	

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
			case "ciudad_up":
					set_sesionesdesplegar("ciudad_up");
					$strSQL .= " ORDER BY txt_nombre_mun ASC ";
				break;
			case "ciudad_do":
					set_sesionesdesplegar("ciudad_do");
					$strSQL .= " ORDER BY txt_nombre_mun DESC ";
				break;
			case "estado_up":
					set_sesionesdesplegar("estado_up");
					$strSQL .= " ORDER BY txt_nombre_edo ASC ";
				break;
			case "estado_do":
					set_sesionesdesplegar("estado_do");
					$strSQL .= " ORDER BY  txt_nombre_edo  DESC ";
				break;
			case "tipo_up":
					set_sesionesdesplegar("tipo_up");
					$strSQL .= " ORDER BY txt_nombre_tipz ASC ";
				break;
			case "tipo_do":
					set_sesionesdesplegar("tipo_do");
					$strSQL .= " ORDER BY  txt_nombre_tipz  DESC ";
				break;
			default:
					set_sesionesdesplegar("nombre_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
		}
		
	}
	else {
		set_sesionesdesplegar("nombre_up");
		$strSQL .= " ORDER BY pk_clave_zon DESC";
	}
	

	include_once("general/calc_navegacion.php");
	

	if (isset($_GET["inicia"])) {
		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
	}
	else {
		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
	}

?>