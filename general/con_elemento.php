<?php 

	$strSQL  = " SELECT * FROM ".$Tabla;	
	if (isset($_GET["busca"])) {
		$strSQL .= " WHERE ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

	}

	if (isset($_GET["orden"])) {
			$orden = $_GET["orden"];
		switch ($orden) {
			case "clave_up":
					set_sesionesdesplegar("clave_up");
					$strSQL .= " ORDER BY ".$campoId." ASC ";
				break;
			case "clave_do":
					set_sesionesdesplegar("clave_do");
					$strSQL .= " ORDER BY ".$campoId." DESC ";
				break;			
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