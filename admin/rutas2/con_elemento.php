<?php 

	$strSQL  = " SELECT r.txt_nombre_rut, r.pk_clave_rut, r.pk_clave_rut,r.fk_clave_zon1 AS cveorigen,zo.txt_nombre_zon AS origen,
				 r.fk_clave_zon2 AS cvedestino,zd.txt_nombre_zon AS destino  
				 FROM tb_rutas AS r, tb_zonas AS zo, tb_zonas AS zd
				 WHERE r.fk_clave_zon1=zo.pk_clave_zon AND r.fk_clave_zon2=zd.pk_clave_zon ";	
	if (isset($_GET["busca"]))  
		$strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

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
			default:
					set_sesionesdesplegar("nombre_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
		}
		
	}
	else {
		set_sesionesdesplegar("nombre_up");
		$strSQL .= " ORDER BY pk_clave_rut DESC";
	}
	

	include_once("general/calc_navegacion.php");
	

	if (isset($_GET["inicia"])) {
		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
	}
	else {
		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
	}

?>