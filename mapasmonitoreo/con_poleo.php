<?php
session_name("ARL");
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
include('../conexion/conexion.php');
include('../posiciones/app_referencia.php');
include('../funciones/distancia.php');
//  Variables
$id = $_GET['id'];
$valUni = true;
$row_array = array();
# Valida si el usuario es de Inhouse
/*if (isset($_SESSION["rol"])) {
} else {
	$valUni = false;
}*/
if ($valUni) {
	//Consulta parametro de ajuste de horas con respecto al GPS
	$consulta0 = "SELECT * 
									FROM tb_parametros 
									WHERE txt_nombre_par = 'ajustegps'";
	$query0 = $conn->prepare($consulta0);
	$query0->execute();
	$registro0 = $query0->fetch();
	$ajusteGPS = $registro0["num_valor_par"];
	$query0->closeCursor();
	# Variable
	$filtro = $_GET['filtro'];
	# Realiza Ajuste de Fechas
	$fechainicial = new DateTime($_GET["ini"], new DateTimeZone('America/Mexico_City'));
	$fechainicial = $fechainicial->setTimezone(new DateTimeZone('UTC'));
	$fechainicial = date_format($fechainicial, 'Y-m-d H:i:s');

	$fechafinal = new DateTime($_GET["fin"], new DateTimeZone('America/Mexico_City'));
	$fechafinal = $fechafinal->setTimezone(new DateTimeZone('UTC'));
	//$fechafinal->add(new DateInterval('PT' . $ajusteGPS . 'H'));
	$fechafinal = date_format($fechafinal, 'Y-m-d H:i:s');
	if ($filtro == "posiciones" or $filtro == "trayectoria") {
		$conSer = "SELECT txt_nserie_rem 
									FROM tb_usuarios AS tu
								INNER JOIN tb_circuitosxusuario AS tcxu
									ON tu.pk_clave_usu = tcxu.fk_clave_usu
								INNER JOIN tb_remolques AS tv
									ON tcxu.fk_clave_cir = tv.fk_clave_cir
								WHERE 
									txt_usuario_usu = ? AND
									txt_economico_rem = ?";
		$qrySer = $conn->prepare($conSer);
		$qrySer->bindParam(1, $_SESSION["usuario"]);
		$qrySer->bindParam(2, $id);
		$qrySer->execute();
		$regSer = $qrySer->fetch();
		$nSer = trim($regSer["txt_nserie_rem"]);
		$nPri = trim($regSer["txt_nserie_rem"]);
		$nSec = trim($regSer["txt_nserie_rem"]);
		$qrySer->closeCursor();
		$unidad = array(
			'id' => $id,
			'nSerie' => $nSer,
			'fecIn' => $fechainicial,
			'fecFn' => $fechafinal,
			'ajusteGPS' => $ajusteGPS
		);
		# Consulta Poleos
		$row_array = consultarPosiciones($unidad, $conn);
		# Valida si No encontro Poleos con el IMEI por Default
		if (sizeof($row_array) == 0) {
			# Valida con cual IMEI buscó anteriormente
			if ($nSer === $nPri) {
				$nSer = $nSec;
			} else {
				$nSer = $nPri;
			}
			$unidad = array(
				'id' => $id,
				'nSerie' => $nSer,
				'fecIn' => $fechainicial,
				'fecFn' => $fechafinal,
				'ajusteGPS' => $ajusteGPS
			);
			# Consulta Poleos
			$row_array = consultarPosiciones($unidad, $conn);
		}
	}
}
$myJSON = json_encode($row_array);
echo $myJSON;

function consultarPosiciones($unidad, $conn)
{
	$arrayPol = array();
	$id = $unidad['id'];
	$ajusteGPS = $unidad['ajusteGPS'];
	$nSerie = (int)$unidad['nSerie'];
	$fecIn = $unidad['fecIn'];
	$fecFn = $unidad['fecFn'];
	$table = 'tb_posiciones';
	$conPol = "SELECT *,fec_ultimaposicion_pos as fecha, 
							num_latitud_pos as latitud,
							num_longitud_pos as longitud,
							num_ignicion_pos as ignicion
						FROM " . $table . " 
						WHERE 
						txt_nserie_pos = ? AND
						fec_ultimaposicion_pos >= ? AND
						fec_ultimaposicion_pos <= ? AND
						num_latitud_pos > 0
						ORDER BY fec_ultimaposicion_pos ASC";
	$qryPol = $conn->prepare($conPol);
	$qryPol->bindParam(1, $nSerie);
	$qryPol->bindParam(2, $fecIn);
	$qryPol->bindParam(3, $fecFn);
	$qryPol->execute();
	$r_totales = 0;
	$distancia = 0;
	$comb_consumido = 0;
	$rendimiento_calc = 0;
	$odometro_anterior = 0;
	$comb_anterior = 0;
	$ban = 0;
	while ($resPol = $qryPol->fetch()) {
		$odometro = 0;
		$comb_total = 0;
		$velocidad = 0;
		$com_ocioso = 0;
		$temperatura = 0;
		$presion_aceite = 0;
		$rpm = 0;
		$tiempo_crucero = 0;
		$dtc = 0;
		$rendimiento = 0;
		$o = "images/posicion.png";
		//$fecha = date('Y-m-d H:i:s', strtotime('-' . $ajusteGPS . ' hour', strtotime($resPol['fecha'])));
		$fecha = new DateTime($resPol['fecha'], new DateTimeZone('UTC'));
		$fecha = $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
		$fecha = date_format($fecha, 'Y-m-d H:i:s');

		$latitud = $resPol['latitud'];
		$longitud = $resPol['longitud'];
		$unidad_ubicacion = georeferencia($latitud, $longitud, $conn) . "," . georeferencia_pi($latitud, $longitud, $conn);
		//if ($resPol['txt_odometro_pos'] != '')	
		$odometro = '0';
		//if ($resPol['txt_combtot_pos'] != '') 
		$comb_total = '0';
		//if ($resPol['txt_coderr_pos'] != '') $velocidad = $resPol['num_velocidad_pos'];
		$velocidad = '0';
		//if ($resPol['txt_comboci_pos'] != '') 
		$com_ocioso = '0';
		//if ($resPol['txt_taceite_pos'] != '') 
		$temperatura = '0';
		//if ($resPol['txt_presion_aceite_pos'] != '') 
		$presion_aceite = '0';
		//if ($resPol['txt_rpm_pos'] != '') 
		$rpm = '0';
		//if ($resPol['txt_velcruc_pos'] != '') 
		$tiempo_crucero = '0';
		//if ($resPol['txt_coderr_pos'] != '') $dtc = $resPol['txt_coderr_pos'];
		$dtc = '0';
		//if ($resPol['txt_rendimiento_pos'] != '') 
		$rendimiento = '0';
		//$odometro_anterior = $odometro;
		//$comb_anterior = $comb_total;
		$fila = array(
			'latitud' => $latitud,
			'longitud' => $longitud,
			'unidad' => $id,
			'posicion' => $unidad_ubicacion,
			'uposicion' => $fecha,
			'ignicion' => $resPol['ignicion'],
			'icono' => '$icono',
			'tipo' => 'Posicion',
			'odometro' => $odometro,
			'comb_total' => $comb_total,
			'speed' => $velocidad,
			'com_ocioso' => $com_ocioso,
			'temperatura' => $temperatura,
			'presion_aceite' => $presion_aceite,
			'rpm' => $rpm,
			'tiempo_crucero' => $tiempo_crucero,
			'dtc' => $dtc,
			'rendimiento' => $rendimiento,
			'distancia_odo' => $distancia,
			'comb_consumido' => $comb_consumido,
			'rendimiento_calc' => $rendimiento_calc,
			'datos_motor' => 1
		);
		$arrayPol[] = $fila;
		$r_totales++;
	}
	$qryPol->closeCursor();
	return $arrayPol;
}

function checaCircuito($usuario, $idcircuito, $conn)
{
	$selCir = 'SELECT * 
				FROM tb_usuarios AS tu
				INNER JOIN tb_circuitosxusuario AS tcxu
					ON tu.pk_clave_usu = tcxu.fk_clave_usu
				WHERE 
					txt_usuario_usu LIKE ? ';
	$qryCir = $conn->prepare($selCir);
	$qryCir->bindParam(1, $usuario);
	$qryCir->execute();
	while ($resCir = $qryCir->fetch()) {
	}
}
