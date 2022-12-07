<?php 

	$strSQL  = " SELECT * FROM tb_autoridades, tb_municipios, tb_estados, tb_tiposdeautoridades WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo AND fk_clave_tipa=pk_clave_tipa ";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

	if (isset($_GET["ciudad"]))  
		$strSQL .= " AND pk_clave_mun ='".$_GET["ciudad"]."' ";

	if (isset($_GET["estado"]))  
		$strSQL .= " AND pk_clave_edo ='".$_GET["estado"]."' ";

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
			case "telefono1_up":
					set_sesionesdesplegar("telefono1_up");
					$strSQL .= " ORDER BY txt_telefono1_aut ASC ";
				break;
			case "telefono1_do":
					set_sesionesdesplegar("telefono1_do");
					$strSQL .= " ORDER BY txt_telefono1_aut DESC ";
				break;	
			case "telefono2_up":
					set_sesionesdesplegar("telefono2_up");
					$strSQL .= " ORDER BY txt_telefono2_aut ASC ";
				break;
			case "telefono2_do":
					set_sesionesdesplegar("telefono2_do");
					$strSQL .= " ORDER BY txt_telefono2_aut DESC ";
				break;
			case "ciudad_up":
					set_sesionesdesplegar("ciudad_up");
					$strSQL .= " ORDER BY fk_clave_mun ASC ";
				break;
			case "ciudad_do":
					set_sesionesdesplegar("ciudad_do");
					$strSQL .= " ORDER BY fk_clave_mun DESC ";
				break;
			case "estado_up":
					set_sesionesdesplegar("estado_up");
					$strSQL .= " ORDER BY pk_clave_edo ASC ";
				break;
			case "estado_do":
					set_sesionesdesplegar("estado_do");
					$strSQL .= " ORDER BY pk_clave_edo DESC ";
				break;
			case "tipo_up":
					set_sesionesdesplegar("tipo_up");
					$strSQL .= " ORDER BY pk_clave_tipa ASC ";
				break;
			case "tipo_do":
					set_sesionesdesplegar("tipo_do");
					$strSQL .= " ORDER BY pk_clave_tipa DESC ";
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