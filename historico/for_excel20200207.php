<?PHP
// *********************************************************************************************************************
//  Genera XML con la informacion relacionada con el histórico de posiciones de acuerdo a los criterios seleccionados
// *********************************************************************************************************************
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  date_default_timezone_set("America/Mexico_City");

  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename=reporte_historico_de_posiciones.csv");

    include ('conexion.php');
    include ('../posiciones/app_referencia.php');
    include ('../funciones/distancia.php');


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

    $consulta  = "SELECT num_serie_veh FROM tb_vehiculos WHERE txt_economico_veh =? ";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $query->execute();
    $registro = $query->fetch();
    $nserie = $registro["num_serie_veh"];

    if ($fechainicial > '2018-11-05 18:26:00') {
        $consulta1 = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
		 			   FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
		 			   AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
    } elseif ($fechainicial < '2019-11-05 14:45:00'){
        $strSQL  = " SELECT * FROM tb_posiciones_historico4 WHERE num_nserie_pos='".$serie."' 
        AND fec_ultimaposicion_pos >= '".$fechainicial."'
        AND fec_ultimaposicion_pos <= '".$fechafinal."' 
        ORDER BY fec_ultimaposicion_pos ASC";
        
    } else {
        $consulta1 = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
		 			   FROM tb_posiciones_historico WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
		 			   AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
    }
    
    /*	
    $consulta1  = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,             num_longitud_pos as longitud,num_ignicion_pos as ignicion
             FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
             AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";*/   

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
    $contador=0;
    $distanciarecorrida=0;

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

            if ($contador)
                $distancia_veh=distancia($latitudanterior, $longitudanterior, $registro1['latitud'], $registro1['longitud']);
            else
            {
                $distancia_veh=0;
            }
            $latitudanterior=$registro1["latitud"];
            $longitudanterior=$registro1["longitud"];
            $contador++;
            $distanciarecorrida+=$distancia_veh;

            if ($registro1['txt_odometro_pos'] != '') $odometro = $registro1['txt_odometro_pos'];
            if ($registro1['txt_combtot_pos'] != '') $comb_total = $registro1['txt_combtot_pos'];
            if ($registro1['txt_velocidad_pos'] != '') $velocidad = $registro1['txt_velocidad_pos'];
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
            $comb_consumido = $comb_total - $comb_anterior;
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
                            'posicion'=>$unidad_ubicacion,
                            'uposicion'=>$fecha,
                            'ignicion'=>$registro1['ignicion'],
                            'icono'=>$icono,
              'tipo'=>'Posicion',
              'odometro' => $odometro,
              'comb_total' => $comb_total,
              'speed' => round($velocidad/0.62137,2),
              'com_ocioso' => $com_ocioso,
              'temperatura' => $temperatura,
              'presion_aceite' => $presion_aceite,
              'rpm' => $rpm,
              'tiempo_crucero' => $tiempo_crucero,
              'dtc' => $dtc,
              'rendimiento' => $rendimiento,
              'distancia_odo' => $distancia,
              'distancia_recorrida' => round($distanciarecorrida,2),
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
      if ($registro2['txt_ubicacion_ale'] != '') $unidad_ubicacion = $registro2['txt_ubicacion_ale'];
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

            if ($contador)
                $distancia_veh=distancia($latitudanterior, $longitudanterior, $registro2["num_latitud_ale"], $registro2['num_longitud_ale']);
            else
            {
                $distancia_veh=0;
            }
            $latitudanterior=$registro2["num_latitud_ale"];
            $longitudanterior=$registro2["num_longitud_ale"];
            $contador++;
            $distanciarecorrida+=$distancia_veh;

          $fila = array ( 'latitud'=>$registro2['num_latitud_ale'],
                            'longitud'=>$registro2['num_longitud_ale'],
                            'unidad'=>$id,
                            'posicion'=> $unidad_ubicacion,
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
              'distancia_recorrida' => round($distanciarecorrida,2),
              'comb_consumido' => 0,
              'rendimiento_calc' => 0,
              'datos_motor' => 0);
              $row_array[]= $fila;
              $r_totales ++;
      }
  }

  echo "Fecha-hora;Posición;Distancia;Velocidad;Ignición;Latitud;Longitud\n";


    if (count($row_array) > 0 ){

      foreach ($row_array as $llave => $fila) {
          $uposicion[$llave]  = $fila['uposicion'];

      }

      array_multisort($uposicion, SORT_ASC,$row_array);

      $ii = 0;
         foreach ($row_array as $filas) {

            echo "\"".$filas['uposicion']."\"".";";
            echo "\"".$filas['posicion']."\"".";";
            echo "\"".$filas['distancia_recorrida']." Kms."."\"".";";
            echo "\"".$filas['speed']." Km/hr. ".$filas['distancia_recorrida']."\"".";";
            if($filas['ignicion']) echo "\""."Encendido"."\"".";"; else echo "\""."Apagado"."\"".";";
            echo "\"".$filas['latitud']."\"".";";
            echo "\"".$filas['longitud']."\"".";";
            echo "\n";

        $ii = $ii + 1;
      }
  }else{
       echo "No se encontraron registros;";
       echo "\n";


  }

?>
