<?php
if ($_SESSION["nombrerol"] == "Gestoria") {
                    $consulta7 = "and fk_clave_cir = 17 ";
                } else if ($_SESSION["nombrerol"] == "Cobranza") {
                    $consulta7 = "and fk_clave_cir = 56 ";
                } else if ($_SESSION["nombrerol"] == "Seguridad Custodia") {
                    $consulta7 = "and fk_clave_cir = 57 ";
                } else if ($_SESSION["nombrerol"] == "Ejecutivo de Ventas") {
                    $consulta7 = "and fk_clave_cir = 55 ";
                } else if ($_SESSION["nombrerol"] == "Paqueteria") {
                    $consulta7 = "and fk_clave_cir = 60";
                } else if ($_SESSION["nombrerol"] == "Coordinador GPS") {
                    $consulta7 = " ";
                } else {
                    $consulta7 = "and fk_clave_cir not in (17,55,56,57,60) ";
                }



	$strSQL  = " SELECT * FROM tb_remolques, tb_circuitos  WHERE estatus = 1 AND fk_clave_cir = pk_clave_cir " . $consulta7;

  if (!(isset($_SESSION["utilitarios"]))) {
    $strSQL = $strSQL." AND fk_clave_cir != 50  ";
  }

  
	if (isset($_GET["busca"]))
		$strSQL .= " AND ".$campoMostrar." LIKE '%".$_GET["busca"]."%' ";

	if (isset($_GET["economico"]))
		$strSQL .= " AND txt_economico_rem = '".$_GET["economico"]."' ";

	if (isset($_GET["serie"]))
		$strSQL .= " AND txt_nserie_rem ='".$_GET["serie"]."' ";

	if (isset($_GET["circuito"]))
		if ($_GET["circuito"]!="-1")
			$strSQL .= " AND txt_nombre_cir ='".$_GET["circuito"]."'";

	if (isset($_GET["orden"])) {
			$orden = $_GET["orden"];
		switch ($orden) {
			case "economico_up":
					set_sesionesdesplegar("economico_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
			case "economico_do":
					set_sesionesdesplegar("economico_do");
					$strSQL .= " ORDER BY ".$campoMostrar." DESC ";
				break;
			case "serie_up":
					set_sesionesdesplegar("serie_up");
					$strSQL .= " ORDER BY txt_serie_rem ASC ";
				break;
			case "serie_do":
					set_sesionesdesplegar("serie_do");
					$strSQL .= " ORDER BY txt_serie_rem DESC ";
				break;
			case "circuito_up":
					set_sesionesdesplegar("circuito_up");
					$strSQL .= " ORDER BY fk_clave_cir ASC ";
				break;
			case "circuito_do":
					set_sesionesdesplegar("circuito_do");
					$strSQL .= " ORDER BY fk_clave_cir DESC ";
				break;
			default:
					set_sesionesdesplegar("numero_up");
					$strSQL .= " ORDER BY ".$campoMostrar." ASC ";
				break;
		}

	}
	else {
		set_sesionesdesplegar("numero_up");
		$strSQL .= " ORDER BY txt_economico_rem  ASC ";
	}

	echo $strSQL;

	include_once("general/calc_navegacion.php");


	if (isset($_GET["inicia"])) {
		$strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
	}
	else {
		$strSQL .= " LIMIT ".$rxp." OFFSET 0";
	}
