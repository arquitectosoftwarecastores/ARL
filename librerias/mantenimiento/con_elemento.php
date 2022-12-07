<?php
//$strSQL = "SELECT * FROM tb_mantenimientos WHERE ";
$strSQL = "select *, (fecha_sigmtto::date - fecha_mtto::date) as dd from monitoreo.tb_remolques r
left join monitoreo.tb_mantenimientos m on m.economico_rem::varchar = r.txt_economico_rem
WHERE length(r.txt_economico_rem) > 4 AND ";

$strEstatus = "";

if (isset($_GET["busca"])){
  $strBusca = " r.txt_economico_rem LIKE '%" . $_GET["busca"] . "%'";
}else {
  $strBusca = " r.txt_economico_rem NOT LIKE ' ' ";
}


if (isset($_GET["economico"])){
  if ($_GET["economico"] != 0){
    $strEconomico = " r.txt_economico_rem = '" . $_GET["economico"] . "' ";
  }
}
else {
  $strEconomico = " r.txt_economico_rem != ' ' ";
}

/*  
if (isset($_GET["alerta"])){
  $strTipo = " tipo = " . $_GET["alerta"] . " ";
  if ($_GET["alerta"] == 0) {
    $strTipo = "";
  }
}else {
  $strTipo = " tipo != 0 ";
}*/


if (isset($_GET["estatus"])){
	if ($_GET["estatus"] == 1)
		$strEstatus = " AND fecha_mtto IS NOT NULL";
	if ($_GET["estatus"] == 0)
	  $strEstatus .= " AND fecha_mtto IS NULL";
}


//$strSQL .= $strBusca." AND ".$strEconomico." AND ".$strTipo.$strEstatus;
$strSQL .= $strBusca." AND ".$strEconomico.$strEstatus;


if (isset($_GET["from"]))
    if ($_GET["from"] != 0)
        $strSQL.= " AND DATE(fecha_sigmtto) >= '" . date("Y/m/d", strtotime($_GET["from"])) . "' ";

if (isset($_GET["to"]))
    if ($_GET["to"] != 0)
        $strSQL.= " AND DATE(fecha_sigmtto) <= '" . date("Y/m/d", strtotime($_GET["to"])) . "' ";

//$strSQL .= " GROUP BY dia, pk_clave_tipa,txt_economico_veh,num_estatus_ale ";

if (isset($_GET["orden"])) {
    $orden = $_GET["orden"];
    switch ($orden) {
        case "fecha_up":
            set_sesionesdesplegar("fecha_up");
            $strSQL .= " ORDER BY fecha_alta ASC ";
            break;
        case "fecha_do":
            set_sesionesdesplegar("fecha_do");
            $strSQL .= " ORDER BY fecha_alta DESC ";
            break;
        case "economico_up":
            set_sesionesdesplegar("economico_up");
            $strSQL .= " ORDER BY r.txt_economico_rem ASC ";
            break;
        case "economico_do":
            set_sesionesdesplegar("economico_do");
            $strSQL .= " ORDER BY r.txt_economico_rem DESC ";
            break;
        case "usuarioa_up":
            set_sesionesdesplegar("usuarioa_up");
            $strSQL .= " ORDER BY usuario_alta ASC ";
            break;
        case "usuarioa_do":
            set_sesionesdesplegar("usuarioa_do");
            $strSQL .= " ORDER BY usuario_alta DESC ";
            break;
				case "usuarioab_up":
		        set_sesionesdesplegar("usuariob_up");
          	$strSQL .= " ORDER BY usuario_baja ASC ";
            break;
        case "usuariob_do":
            set_sesionesdesplegar("usuariob_do");
            $strSQL .= " ORDER BY usuario_baja DESC , fecha_alta DESC ";
            break;
        case "tiempo_up":
            set_sesionesdesplegar("tiempo_up");
            $strSQL .= " ORDER BY tiempo ASC ";
            break;
        case "tiempo_do":
            set_sesionesdesplegar("tiempo_do");
            $strSQL .= " ORDER BY tiempo DESC ";
            break;
        default:
            set_sesionesdesplegar("nombre_up");
            $strSQL .= "  ORDER BY fecha_mtto DESC";
            break;
    }
} else {
    set_sesionesdesplegar("nombre_up");
    $strSQL .= " ORDER BY txt_economico_rem ASC";
}

include_once("general/calc_navegacion.php");

if (isset($_GET["inicia"])) {
    $strSQL .= " LIMIT " . $rxp . " OFFSET " . $_GET["inicia"];
} else {
    $strSQL .= " LIMIT " . $rxp . " OFFSET 0";
}
?>
