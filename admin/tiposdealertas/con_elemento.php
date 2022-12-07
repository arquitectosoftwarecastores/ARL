<?php 

	$strSQL  = " SELECT * FROM tb_tiposdealertas ";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

	if (isset($_GET["nombre"]))  
		$strSQL .= " AND txt_nombre_tipa LIKE'%".$_GET["nombre"]."%' ";


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
			case "prioridad_up":
					set_sesionesdesplegar("prioridad_up");
					$strSQL .= " ORDER BY num_prioridad_tipa ASC ";
				break;
			case "prioridad_do":
					set_sesionesdesplegar("prioridad_do");
					$strSQL .= " ORDER BY num_prioridad_tipa DESC ";
				break;
			case "ver_up":
					set_sesionesdesplegar("ver_up");
					$strSQL .= " ORDER BY num_ver_tipa ASC ";
				break;
			case "ver_do":
					set_sesionesdesplegar("ver_do");
					$strSQL .= " ORDER BY num_ver_tipa DESC ";
				break;
			case "tipo_up":
					set_sesionesdesplegar("tipo_up");
					$strSQL .= " ORDER BY num_tipo_tipa ASC ";
				break;
			case "tipo_do":
					set_sesionesdesplegar("tipo_do");
					$strSQL .= " ORDER BY num_tipo_tipa DESC ";
				break;
			case "global_up":
					set_sesionesdesplegar("global_up");
					$strSQL .= " ORDER BY num_global_tipa ASC ";
				break;
			case "global_do":
					set_sesionesdesplegar("global_do");
					$strSQL .= " ORDER BY num_global_tipa DESC ";
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