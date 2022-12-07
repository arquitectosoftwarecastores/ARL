<?PHP
// *********************************************************************************************************************
//  Genera XML con la informacion relacionada con el hist�rico de posiciones de acuerdo a los criterios seleccionados
// *********************************************************************************************************************
 	session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");
    include ('conexion.php');
    include ('../posiciones/app_referencia.php');
    include ('../funciones/distancia.php');
    include ('../funciones/validaconsultas.php');
	header('Content-Type: text/xml');
 	//include('calcula_distancia.php');
 	//include('referencia_geografica.php');
    $user = $_SESSION["usuario"];

    //Consulta parametro de ajuste de horas con respecto al GPS

	$consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
	$query0 = $conn->prepare($consulta0);
	$query0->execute();
	$registro0 = $query0->fetch();
	$ajustegps=$registro0["num_valor_par"];
	$query0->closeCursor();

    $id=$_GET['id'];
	$filtro=$_GET['filtro'];

	$fechainicial=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($_GET["ini"])));
	$fechafinal=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($_GET["fin"])));

	if ($filtro=="posiciones" or $filtro=="trayectoria" ){

   /*  realizaconsulta($user,14, $id, "historico");     */


		$consulta  = "SELECT num_serie_veh FROM tb_vehiculos WHERE txt_economico_veh =? ";
		$query = $conn->prepare($consulta);
		$query->bindParam(1, $id);
		$query->execute();
		$registro = $query->fetch();
		$nserie = $registro["num_serie_veh"];



            #--------------------------version 2
            if ($fechainicial < '2018-11-05 18:26:00') {

                $consulta1 = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
                FROM tb_posiciones_historico WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
                AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
            } elseif ($fechainicial < '2019-01-17 11:00:00'){
                $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
			FROM tb_posiciones_historico2 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
			AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";

            } elseif ($fechainicial < '2019-02-01 12:40:00'){
                $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
                FROM tb_posiciones_historico3 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
				AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
			
			} elseif ($fechainicial < '2019-11-05 14:45:00'){
				$consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
                FROM tb_posiciones_historico4 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
                AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
			} elseif ($fechainicial < '2020-02-07 12:00:00'){
				$consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
                FROM tb_posiciones_historico5 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
                AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
			} else {
                $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
                FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
                AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
            }



                /*
		$consulta1  = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud, num_longitud_pos as longitud,num_ignicion_pos as ignicion
		 			   FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
		 			   AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
		*/

                $query1 = $conn->prepare($consulta1);
		$query1->bindParam(1, $nserie);
		$query1->bindParam(2, $fechainicial);
		$query1->bindParam(3, $fechafinal);
		$query1->execute();

		$row_array= array ();
		$r_totales = 0;
		$distancia = 0;
		$comb_consumido = 0;
		$rendimiento_calc = 0;
		$ban = 0;

		while ($registro1 = $query1->fetch())
		{
	 		$icono = "images/posicion.png";
	 		//$fecha = $registro1['fecha'];
	 		$fecha = date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro1['fecha'])));
            $latitud = $registro1['latitud'];
			$longitud = $registro1['longitud'];
			$unidad_ubicacion = georeferencia($latitud,$longitud,$conn).",".georeferencia_pi($latitud,$longitud,$conn);
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

            if ($registro1['txt_odometro_pos'] != '')	$odometro = $registro1['txt_odometro_pos'];
            if ($registro1['txt_combtot_pos'] != '') $comb_total = $registro1['txt_combtot_pos'];
            if ($registro1['txt_descolgada_pos'] != '') $velocidad = $registro1['txt_descolgada_pos'];
            if ($registro1['txt_comboci_pos'] != '') $com_ocioso = $registro1['txt_comboci_pos'];
            if ($registro1['txt_taceite_pos'] != '') $temperatura = $registro1['txt_taceite_pos'];
            if ($registro1['txt_presion_aceite_pos'] != '') $presion_aceite = $registro1['txt_presion_aceite_pos'];
            if ($registro1['txt_rpm_pos'] != '') $rpm = $registro1['txt_rpm_pos'];
            if ($registro1['txt_velcruc_pos'] != '') $tiempo_crucero = $registro1['txt_velcruc_pos'];
            if ($registro1['txt_coderr_pos'] != '') $dtc = $registro1['txt_coderr_pos'];
            if ($registro1['txt_rendimiento_pos'] != '') $rendimiento = $registro1['txt_rendimiento_pos'];

         	if($ban == 0){
         		$odometro_anterior = $odometro;
         		$comb_anterior = $comb_total;
         		$ban = 1;
         	}else{
         		$distancia = $odometro - $odometro_anterior;
         		$comb_consumido = $comb_total -	$comb_anterior;
         		$rendimiento_calc = 0;
				if ($comb_consumido > 0){
					$rendimiento_calc = round($distancia / $comb_consumido,2);
				}
				$odometro_anterior = $odometro;
				$comb_anterior = $comb_total;
         	}

			$fila = array ( 'latitud'=>$latitud,
                            'longitud'=>$longitud,
                            'unidad'=>$id,
                            'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
                            'uposicion'=>$fecha,
                            'ignicion'=>$registro1['ignicion'],
                            'icono'=>$icono,
							'tipo'=>'Posicion',
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
	        $row_array[]= $fila;
	        $r_totales ++;

		}  // fin del while posiciones
    }  // fin del if filtro

       //-------------------------  extraccion de mensajes en el periodo dado ------------------------

   if ($_GET['filtro']=='eventos'){

		$consulta2  = "SELECT * FROM tb_alertas, tb_tiposdealertas WHERE txt_economico_veh =?
					   AND fk_clave_tipa=pk_clave_tipa AND fec_fecha_ale<=? AND fec_fecha_ale>=?
					   ORDER BY fec_fecha_ale ASC";
		$query2 = $conn->prepare($consulta2);
		$query2->bindParam(1, $id);
		$query2->bindParam(2, $fechainicial);
		$query2->bindParam(3, $fechafinal);
		$query2->execute();

		while ($registro2 = $query2->fetch())
		{
			switch ($registro2['txt_ignicion_ale']):
                    case 1:
                        $ignicion = 'Encendido';
                        break;
                    case 2:
                        $ignicion = 'Apagado';
                        break;
                    default:
                    	$ignicion = 'Desconocido';
                    	 break;
            endswitch;

			$unidad_ubicacion = "";
			//$unidad_ubicacion = referencia_geo($latitud,$longitud,$conecta_mysql,$database_smartfleet)."<br>[".referencia_geo_pi($latitud,$longitud,$conecta_mysql,$database_smartfleet)."]";

            switch ($registro2['txt_nombre_tipa']):
                case 'Entrada Punto':
                    	$icono = "images/entrada_punto.png";
                    break;
                case 'Deteccion Parada':
                    	$icono = "images/parada_na.png";
                    break;
                default:
                		$icono = "images/evento.png";
                	 break;
            endswitch;

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
	        $fila = array ( 'latitud'=>$registro2['num_latitud_ale'],
                            'longitud'=>$registro2['num_longitud_ale'],
                            'unidad'=>$id,
                            'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
                            'uposicion'=>$registro2['fec_fecha_ale'],
                            'ignicion'=>$ignicion,
                            'icono'=>$icono,
							'tipo'=>$registro2['num_tipo_ale'],
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
							'distancia_odo' => 0,
							'comb_consumido' => 0,
							'rendimiento_calc' => 0,
							'datos_motor' => 0);
	            $row_array[]= $fila;
	            $r_totales ++;
			}
	}

			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$xml .="<Registros>\n";


		if (count($row_array) > 0 ){

			foreach ($row_array as $llave => $fila) {
			    $uposicion[$llave]  = $fila['uposicion'];

			}

			array_multisort($uposicion, SORT_ASC,$row_array);

			$ii = 0;
	       foreach ($row_array as $filas) {

				$xml .="<Trackpoint>\n";
					$xml .="<Position>\n";
						$xml .="<Evento>\n";
							$xml .=$filas['tipo']."\n";
				 		$xml .="</Evento>\n";
				 		$xml .="<Posicion>";
							$xml .=$filas['posicion'];
				 		$xml .="</Posicion>";
				 		$xml .="<Latitude>\n";
				 			$xml .=$filas['latitud']."\n";
				 		$xml .="</Latitude>\n";
				 		$xml .="<Longitude>\n";
				 			$xml .=$filas['longitud']."\n";
				 		$xml .="</Longitude>\n";
				 		$xml .="<Altitude>\n";
				 			$xml .="1\n";
				 		$xml .="</Altitude>\n";
				 		$xml .="<Odometro>\n";
				 			$xml .=$filas['odometro']."\n";
				 		$xml .="</Odometro>\n";
				 		$xml .="<Combtot>\n";
				 			$xml .=$filas['comb_total']."\n";
				 		$xml .="</Combtot>\n";
				 		$xml .="<Descolgada>\n";
				 			$xml .=$filas['speed']."\n";
				 		$xml .="</Descolgada>\n";
				 		$xml .="<Comboci>\n";
				 			$xml .=$filas['com_ocioso']."\n";
				 		$xml .="</Comboci>\n";
				 		$xml .="<Temperatura>\n";
				 			$xml .=$filas['temperatura']."\n";
				 		$xml .="</Temperatura>\n";
				 		$xml .="<Presion>\n";
				 			$xml .=$filas['presion_aceite']."\n";
				 		$xml .="</Presion>\n";
				 		$xml .="<RPM>\n";
				 			$xml .=$filas['rpm']."\n";
				 		$xml .="</RPM>\n";
				 		$xml .="<Crucero>\n";
				 			$xml .=$filas['tiempo_crucero']."\n";
				 		$xml .="</Crucero>\n";
				 		$xml .="<Coderr>\n";
				 			$xml .=$filas['dtc']."\n";
				 		$xml .="</Coderr>\n";
				 		$xml .="<Rendimiento>\n";
				 			$xml .=$filas['rendimiento']."\n";
				 		$xml .="</Rendimiento>\n";
				 		$xml .="<Distancia_Odo>\n";
				 			$xml .=$filas['distancia_odo']."\n";
				 		$xml .="</Distancia_Odo>\n";
				 		$xml .="<CombConsumido>\n";
				 			$xml .=$filas['comb_consumido']."\n";
				 		$xml .="</CombConsumido>\n";
				 		$xml .="<RendimientoCalc>\n";
				 			$xml .=$filas['rendimiento_calc']."\n";
				 		$xml .="</RendimientoCalc>\n";
				 		$xml .="<DatosMotor>\n";
				 			$xml .=$filas['datos_motor']."\n";
				 		$xml .="</DatosMotor>\n";

				 	$xml .="</Position>\n";
				 	$xml .="<Time>\n";
				 		$xml .=$filas['uposicion']."\n";
				 	$xml .="</Time>\n";
				$xml .="</Trackpoint>\n";

				$ii = $ii + 1;
			}
	}else{
		$xml .="<Trackpoint>\n";
					$xml .="<Position>\n";
						$xml .="<Evento>\n";
								$xml .= "1\n";
				 		$xml .="</Evento>\n";
					$xml .="</Position>\n";
		$xml .="</Trackpoint>\n";


	}
		$xml .="</Registros>\n";
echo $xml;

 /* Almacenamos quien realiza consultas a las unidades*/
    $consulta33 ="insert into monitoreo.tb_consultas(usuario,fecha,modulo,unidad,submodulo) values (?,now(),'14',?,'historico')";
    $query33 = $conn->prepare($consulta33);
    $query33->bindParam(1, $user);
    $query33->bindParam(2, $id);
    $query33->execute();
    $query33->closeCursor();
    /* ****************************************** */
