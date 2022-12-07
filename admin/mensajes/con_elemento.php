<?php 

	$strSQL  = "SELECT *
 				FROM tb_mensajesenviadossms, tb_tiposdemensajessms, tb_usuarios
				WHERE fk_clave_tipm=pk_clave_tipm AND pk_clave_usu=fk_clave_usu ";

	if (isset($_GET["busca"]))  
		$strSQL .= " AND ( txt_economico_veh LIKE'%".$_GET["busca"]."%')";

	if (isset($_GET["economico"]))
		if ($_GET["economico"]!=0)
			$strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

    if(isset($_GET["from"]))
        if($_GET["from"]!=0)
            $strSQL.= " AND DATE(fec_fecha_mene) >= '".date("Y/m/d", strtotime($_GET["from"]))."' ";

    if(isset($_GET["to"]))
        if($_GET["to"]!=0)
            $strSQL.= " AND DATE(fec_fecha_mene) <= '".date("Y/m/d", strtotime($_GET["to"]))."' ";

	if (isset($_GET["usuario"]))
			$strSQL .= " AND txt_nombre_usu='".$_GET["usuario"]."' ";


	if (isset($_GET["orden"])) {
			$orden = $_GET["orden"];
		switch ($orden) {			
			case "fecha_up":
					set_sesionesdesplegar("fecha_up");
					$strSQL .= " ORDER BY fec_fecha_mene ASC ";
				break;
			case "fecha_do":
					set_sesionesdesplegar("fecha_do");
					$strSQL .= " ORDER BY fec_fecha_mene DESC ";
				break;
			case "economico_up":
					set_sesionesdesplegar("economico_up");
					$strSQL .= " ORDER BY txt_economico_veh ASC ";
				break;
			case "economico_do":
					set_sesionesdesplegar("economico_do");
					$strSQL .= " ORDER BY txt_economico_veh DESC ";
				break;
			case "mensaje_up":
					set_sesionesdesplegar("mensaje_up");
					$strSQL .= " ORDER BY txt_nombre_tipm ASC ";
				break;
			case "mensaje_do":
					set_sesionesdesplegar("mensaje_do");
					$strSQL .= " ORDER BY txt_nombre_tipm DESC ";
				break;
			case "usuario_up":
					set_sesionesdesplegar("usuario_up");
					$strSQL .= " ORDER BY txt_nombre_usu ASC ";
				break;
			case "usuario_do":
					set_sesionesdesplegar("usuario_do");
					$strSQL .= " ORDER BY txt_nombre_usu DESC ";
				break;
			default:
					set_sesionesdesplegar("nombre_up");
					$strSQL .= "  ORDER BY fec_fecha_mene DESC";
				break;
		}
		
	}
	else {
		set_sesionesdesplegar("nombre_up");
		$strSQL .= " ORDER BY fec_fecha_mene DESC";
	}

	include_once("general/calc_navegacion.php");

	if (isset($_GET["inicia"])) {
		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
	}
	else {
		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
	}



?>