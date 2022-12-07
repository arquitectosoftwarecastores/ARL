<?php 
	$strSQL  = " SELECT * FROM monitoreo.usuariospermisos ";	
//	if (isset($_GET["busca"]))  
//		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";
//
//	if (isset($_GET["economico"]))  
//		$strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";
//
//	if (isset($_GET["serie"]))  
//		$strSQL .= " AND num_serie_veh ='".$_GET["serie"]."' ";
// 
//	if (isset($_GET["circuito"]))  
//		if ($_GET["circuito"]!="-1") 
//			$strSQL .= " AND txt_nombre_cir ='".$_GET["circuito"]."'";
//
//	if (isset($_GET["orden"])) {
//			$orden = $_GET["orden"];
//		switch ($orden) {			
//			case "economico_up":
//					set_sesionesdesplegar("economico_up");
//					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
//				break;
//			case "economico_do":
//					set_sesionesdesplegar("economico_do");
//					$strSQL .= " ORDER BY ".$campoMostrar." DESC ";
//				break;
//			case "serie_up":
//					set_sesionesdesplegar("serie_up");
//					$strSQL .= " ORDER BY num_serie_veh ASC ";
//				break;
//			case "serie_do":
//					set_sesionesdesplegar("serie_do");
//					$strSQL .= " ORDER BY num_serie_veh DESC ";
//				break;
//			case "circuito_up":
//					set_sesionesdesplegar("circuito_up");
//					$strSQL .= " ORDER BY fk_clave_cir ASC ";
//				break;
//			case "circuito_do":
//					set_sesionesdesplegar("circuito_do");
//					$strSQL .= " ORDER BY fk_clave_cir DESC ";
//				break;
//			default:
//					set_sesionesdesplegar("numero_up");
//					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
//				break;
//		}
//		
//	}
//	else {
//		set_sesionesdesplegar("numero_up");
//		$strSQL .= " ORDER BY ".$campoMostrar."  ASC ";
//	}
	

//	include_once("general/calc_navegacion.php");
//	
//
//	if (isset($_GET["inicia"])) {
//		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
//	}
//	else {
//		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
//	}

?>