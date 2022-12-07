<?php
$strSQL = "SELECT txt_nombre_tipa,txt_economico_rem,num_estatus_ale,
		MIN(num_prioridad_ale) AS num_prioridad_tipa,
		MIN(pk_clave_tipa) AS pk_clave_tipa,
		MAX(fec_fecha_ale) AS fec_fecha_ale,
		(CURRENT_TIMESTAMP - MAX(fec_fecha_ale)) as tiempo,
		MIN(pk_clave_ale) AS pk_clave_ale,
		MIN(txt_ubicacion_ale) AS txt_ubicacion_ale,
		MIN(num_latitud_ale) AS num_latitud_ale,
                MIN(num_longitud_ale) AS num_longitud_ale,
		MIN(txt_upsmart_ale) AS txt_upsmart_ale,
		MIN(txt_comentarios_ale) AS txt_comentarios_ale,
		MAX(fk_clave_usu) AS fk_clave_usu,
 		COUNT(*) as acumuladas,
		MAX(fec_verifica_ale) AS fec_verifica_ale,
 		date_trunc('day', fec_fecha_ale) as dia
		FROM  tb_alertas_rol as tar 
        INNER JOIN tb_alertas as ta ON tar.alerta = ta.fk_clave_tipa
        INNER JOIN tb_tiposdealertas as ttda ON ta.fk_clave_tipa = ttda.pk_clave_tipa
        WHERE fk_clave_tipa=pk_clave_tipa AND  rol=".$_SESSION['rol']."
	    AND txt_economico_rem IN (
            SELECT tv.txt_economico_rem FROM tb_usuarios AS tu
	        LEFT JOIN tb_circuitosxusuario AS cxu ON tu.pk_clave_usu = cxu.fk_clave_usu
            INNER JOIN tb_remolques AS tv ON cxu.fk_clave_cir = tv.fk_clave_cir ";
            $strSQL .= " WHERE txt_usuario_usu = '".$_SESSION['usuario']."'";             
    $strSQL .= " ) ";

if (isset($_GET["busca"]))
    $strSQL .= " AND ( txt_economico_rem LIKE'%" . $_GET["busca"] . "%')";

if (isset($_GET["economico"]))
    if ($_GET["economico"] != 0)
        $strSQL .= " AND txt_economico_rem='" . $_GET["economico"] . "' ";

if (isset($_GET["alerta"])) {
        $strSQL .= " AND txt_nombre_tipa='" . $_GET["alerta"] . "' ";
}
if (isset($_GET["prioridad"]))
    if ($_GET["prioridad"] != 0)
        $strSQL .= " AND num_prioridad_tipa=" . $_GET["prioridad"];

if (isset($_GET["estatus"]))
    if ($_GET["estatus"] != -1)
        $strSQL .= " AND num_estatus_ale=" . $_GET["estatus"];

if (isset($_GET["from"]))
    if ($_GET["from"] != 0)
        $strSQL.= " AND DATE(fec_fecha_ale) >= '" . date("Y/m/d", strtotime($_GET["from"])) . "' ";

if (isset($_GET["to"]))
    if ($_GET["to"] != 0)
        $strSQL.= " AND DATE(fec_fecha_ale) <= '" . date("Y/m/d", strtotime($_GET["to"])) . "' ";

$strSQL .= " GROUP BY dia, pk_clave_tipa,txt_economico_rem,num_estatus_ale ";

if (isset($_GET["orden"])) {
    $orden = $_GET["orden"];
    switch ($orden) {
        case "fecha_up":
            set_sesionesdesplegar("fecha_up");
            $strSQL .= " ORDER BY fec_fecha_ale ASC ";
            break;
        case "fecha_do":
            set_sesionesdesplegar("fecha_do");
            $strSQL .= " ORDER BY fec_fecha_ale DESC ";
            break;
        case "economico_up":
            set_sesionesdesplegar("economico_up");
            $strSQL .= " ORDER BY txt_economico_rem ASC ";
            break;
        case "economico_do":
            set_sesionesdesplegar("economico_do");
            $strSQL .= " ORDER BY txt_economico_rem DESC ";
            break;
        case "alerta_up":
            set_sesionesdesplegar("alerta_up");
            $strSQL .= " ORDER BY txt_nombre_tipa ASC ";
            break;
        case "alerta_do":
            set_sesionesdesplegar("alerta_do");
            $strSQL .= " ORDER BY txt_nombre_tipa DESC ";
            break;
        case "prioridad_up":
            set_sesionesdesplegar("prioridad_up");
            $strSQL .= " ORDER BY num_prioridad_tipa ASC ";
            break;
        case "prioridad_do":
            set_sesionesdesplegar("prioridad_do");
            $strSQL .= " ORDER BY num_prioridad_tipa DESC ";
            break;
        case "estatus_up":
            set_sesionesdesplegar("estatus_up");
            $strSQL .= " ORDER BY num_estatus_ale ASC ";
            break;
        case "estatus_do":
            set_sesionesdesplegar("estatus_do");
            $strSQL .= " ORDER BY num_estatus_ale DESC ";
            break;
        case "acumuladas_up":
            set_sesionesdesplegar("acumuladas_up");
            $strSQL .= " ORDER BY acumuladas ASC ";
            break;
        case "acumuladas_do":
            set_sesionesdesplegar("acumuladas_do");
            $strSQL .= " ORDER BY acumuladas DESC ";
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
            $strSQL .= "  ORDER BY fec_fecha_ale DESC, num_prioridad_tipa DESC, num_estatus_ale ASC";
            break;
    }
} else {
    set_sesionesdesplegar("nombre_up");
    $strSQL .= " ORDER BY fec_fecha_ale DESC, num_prioridad_tipa DESC,num_estatus_ale ASC";
}
include_once("general/calc_navegacion.php");
if (isset($_GET["inicia"])) {
    $strSQL .= " LIMIT " . $rxp . " OFFSET " . $_GET["inicia"];
} else {
    $strSQL .= " LIMIT " . $rxp . " OFFSET 0";
}
?>