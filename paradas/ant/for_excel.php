<?PHP
// *********************************************************************************************************************
//  Genera XML con la informacion relacionada de las paradas de los vehículos
//  de acuerdo a los criterios seleccionados
// *********************************************************************************************************************
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  date_default_timezone_set("America/Mexico_City");


  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename=ReporteDeParadas.csv");

    include ('../conexion/conexion.php');
    //include ('../conexion/conexion.php');
    //include ('../posiciones/app_referencia.php');
    //include ('../funciones/distancia.php');

	include("../funciones/diferenciaenhoras.php");
	include("../funciones/distancia.php");
	include("../posiciones/app_referencia.php");

	$consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
	$query0 = $conn->prepare($consulta0);
	$query0->execute();
	$registro0 = $query0->fetch();
	$ajustegps=$registro0["num_valor_par"];
	$query0->closeCursor();


	$vehiculo=$_GET['vehiculo'];
	$from=$_GET['from'];
	$to=$_GET['to'];


	$strSQL  = " SELECT * FROM tb_posiciones WHERE num_nserie_pos=".$nserie."
		AND DATE(fec_ultimaposicion_pos) >= '".date("Y/m/d", strtotime($from))."'
		AND DATE(fec_ultimaposicion_pos) <= '".date("Y/m/d", strtotime($to))."'
		ORDER BY fec_ultimaposicion_pos ";

	$query = $conn->prepare($strSQL);
	$query->execute();

	$fecha_inicio     = '';
	$fecha_fin      = '';
	$ubicacion_inicio   = '';
	$lat_actual = 0;
	$lon_actual = 0;
	$lat_pibote = 0;
	$lon_pibote = 0;
	$ban = 0;


   echo "Económico;Fecha de entrada;Fecha de salida;Tiempo permanencia;Ubicación;Geocerca\n";

		while ($registro = $query->fetch())
		{
			$fecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["fec_ultimaposicion_pos"])));
			$fecha_ok=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["fec_ultimaposicion_pos"])));
			$lat_actual = $registro["num_latitud_pos"];
			$lon_actual = $registro["num_longitud_pos"];
			$unidad_ubicacion = georeferencia($lat_actual,$lon_actual,$conn).", ";
			$unidad_ubicacion .= georeferencia_pi($lat_actual,$lon_actual,$con);
			if ($ban == 1)
			{
				$distancia_pibote = distancia($lat_actual,$lon_actual,$lat_pibote,$lon_pibote);
				//echo $distancia_pibote.", ";
				if ($distancia_pibote <= $distancia)
					$fecha_fin = $fecha_ok;
				else
				{
					if($fecha_fin!="")
					{
						$geocerca = "Trayecto";
						//echo $geocerca.", ";
						//echo "Busca alerta de entrada..., ";
						$consulta_alertas  = "SELECT fec_fecha_ale, txt_campo1_ale,txt_nombre_tipa
								FROM tb_alertas, tb_tiposdealertas
								WHERE fk_clave_tipa=pk_clave_tipa
								AND txt_economico_veh = '".$eco."' AND
								(txt_nombre_tipa = 'Entrando Zona' OR txt_nombre_tipa = 'Entrada Geocerca') AND
								fec_fecha_ale <= '".$fecha_inicio."' ORDER BY fec_fecha_ale DESC LIMIT 1";
						$query2 = $conn->prepare($consulta_alertas);
						$query2->execute();
						$row_alertas = $query2->fetch();
						if($row_alertas["txt_nombre_tipa"] != "")
						{
							//echo "Encuentra alerta de entrada..., ";
							//echo "Busca alerta de salida..., ";
							$tipo_buscar = "Saliendo Zona";
							if($row_alertas["txt_nombre_tipa"] == "Entrada Geocerca")
								$tipo_buscar = "Salida Geocerca";
							$consulta_salidas = "SELECT fec_fecha_ale, txt_campo1_ale,txt_nombre_tipa
									FROM tb_alertas, tb_tiposdealertas
									WHERE fk_clave_tipa=pk_clave_tipa
									AND txt_economico_veh = '".$eco."' AND
									txt_nombre_tipa = '".$tipo_buscar."' AND
									fec_fecha_ale >= '".$row_alertas['fec_fecha_ale']."' and
									txt_campo1_ale = '".$row_alertas['txt_campo1_ale']."'
									ORDER BY fec_fecha_ale ASC LIMIT 1";
							//echo $consulta_salidas."\n";
							$query3 = $conn->prepare($consulta_alertas);
							$query3->execute();
							$row_salidas = $query3->fetch();

							if($row_salidas['alerta_timestamp'] == "")
							{
								// se encuentra dentro
								if($tipo_buscar == "Saliendo Zona")
									$geocerca = "N/D";
								if($tipo_buscar == "Salida Geocerca")
									$geocerca = "HW";
								if($row_alertas['txt_campo1_ale'] != "")
									$geocerca = $row_alertas["txt_campo1_ale"];
							}
							else
							{
								if($fecha_inicio<=$row_salidas['fec_fecha_ale'])
								{
									// se encuentra dentro
									if($tipo_buscar == "Saliendo Zona")
										$geocerca = "N/D";
									if($tipo_buscar == "Salida Geocerca")
										$geocerca = "HW";
									if($row_alertas['txt_campo1_ale'] != "")
										$geocerca = $row_alertas['txt_campo1_ale'];
								}
							} // fin del if $row_salidas['alerta_timestamp'] == ""
						} // fin del if $row_alertas["txt_nombre_tipa"] != ""
						//echo "Fin:".$fecha_fin.", inicio:".$fecha_inicio.", ";
					// $tiempo = calcula_diferencia($fecha_fin,$fecha_inicio);

					$date_a = new DateTime($fecha_fin);
					$date_b = new DateTime($fecha_inicio);
					$interval = date_diff($date_a,$date_b);
					$tiempo = $interval->format('%a días %H hr. %I min.');

						if ($tiempo!="0 días 00 hr. 00 min.")
						{
							
							echo $eco.";";
							echo $fecha_inicio.";";
							echo $fecha_fin.";";
							echo $tiempo.";";
							echo $trim($ubicacion_inicio).";";
							echo $trim($geocerca).";";
							echo "\n";
						}
					} // fin del if $fecha_fin!=""
					// actualiza el pibote para la siguiente comparacion
					$fecha_inicio = $fecha_ok;
					$fecha_fin = "";
					$ubicacion_inicio = $unidad_ubicacion;
					$lat_pibote = $registro['num_latitud_pos'];
					$lon_pibote = $registro['num_longitud_pos'];
					//echo $fecha_inicio."<br>";
				} // fin del if $distancia_pibote <= $distancia
			} // fin del if $ban=1
			else
			{
				$fecha_inicio = $fecha_ok;
				$ubicacion_inicio = $unidad_ubicacion;
				$lat_pibote = $registro['num_latitud_pos'];
				$lon_pibote = $registro['num_longitud_pos'];
				$ban = 1;
				//echo $fecha_inicio."<br>";	
			} // fin del else  if $ban=1
		} // fin del ciclo
/*
  $vehiculo=$_POST['vehiculo'];
  $from=$_POST['from'];
  $to=$_POST['to'];
  $distancia=$_POST["distancia"];


  $consulta1  = " SELECT * FROM tb_vehiculos
                 WHERE txt_economico_veh=?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $vehiculo);
  $query1->execute();
  $nserie=0;
  while($registro1 = $query1->fetch())
	{
     $eco = $registro1['txt_economico_veh'];
     $nserie = $registro1["num_serie_veh"];
     $fecha_actual = date("Y/m/d H:i:s",time());
     $ubicacion_actual = $registro1['txt_posicion_veh'];
     $lat_actual = $registro1['num_latitud_veh'];
     $lon_actual = $registro1['num_longitud_veh'];
	}
  if($nserie==0)
  {
    echo "<p>No se encontró el vehículo.</p>";
    exit();
  }

  $strSQL  = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='".$nserie."'
               AND DATE(fec_ultimaposicion_pos) >= '".date("Y/m/d", strtotime($from))."'
               AND DATE(fec_ultimaposicion_pos) <= '".date("Y/m/d", strtotime($to))."'
               ORDER BY fec_ultimaposicion_pos ";
*/
?>


<?php
/*
   echo "Económico;Fecha de entrada;Fecha de salida;Tiempo permanencia;Ubicación;Geocerca\n";
  $query = $conn->prepare($strSQL);
  $query->execute();
  $contador=0;
  $distanciarecorrida=0;
  while ($registro = $query->fetch())
  {
    if($contador)
      $distancia=distancia($latitudanterior, $longitudanterior,$registro["num_latitud_pos"], $registro["num_longitud_pos"]);
    else
     {
      $distancia=0;
     }
    $latitudanterior=$registro["num_latitud_pos"];
    $longitudanterior=$registro["num_longitud_pos"];
    $contador++;
    $distanciarecorrida+=$distancia;

    echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))).";";
    echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn).",".georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn).";";
    echo round($registro["txt_velocidad_pos"]/0.62137,2)." Km/hr.;";
    echo round($distanciarecorrida,2).";";
    if ($registro["num_ignicion_pos"]) echo "Encendido;"; else echo "Apagado;";
    echo $registro['txt_odometro_pos'].";";
    echo $registro['txt_combtot_pos'].";";
    echo "\n";

  }
*/
?>
