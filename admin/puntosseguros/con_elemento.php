<?php 

	$strSQL  = " SELECT * FROM tb_puntosseguros, tb_municipios, tb_estados WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo ";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

	if (isset($_GET["nombre"]))  
		$strSQL .= " AND txt_nombre_pun ='".$_GET["nombre"]."' ";	

	if (isset($_GET["ciudad"]))  
		$strSQL .= " AND pk_clave_mun ='".$_GET["ciudad"]."' ";

	if (isset($_GET["estado"]))  
		$strSQL .= " AND pk_clave_edo ='".$_GET["estado"]."' ";

	if (isset($_GET["tipo"]))  
		$strSQL .= " AND num_tipo_pun ='".$_GET["tipo"]."' ";	

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