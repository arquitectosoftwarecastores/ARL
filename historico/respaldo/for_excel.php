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
  header("Content-Disposition: attachment; filename=NombreArchivoExcel.csv");
    include('../conexion/conexion.php');
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
  $fechainicial=date('Y-m-d H:i:s',strtotime($_GET["ini"]));
  $fechafinal=date('Y-m-d H:i:s',strtotime($_GET["fin"]));
  if ($filtro=="posiciones" or $filtro=="trayectoria" ){
    $consulta  = "SELECT txt_nserie_rem FROM tb_remolques WHERE txt_economico_rem =? ";  
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $query->execute();
    $registro = $query->fetch();
    $nserie = $registro["txt_nserie_rem"]; 
    $consulta1  = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,             
              num_longitud_pos as longitud,num_ignicion_pos as ignicion
              FROM tb_posiciones WHERE txt_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
              AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
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
      $fecha = date('Y-m-d H:i:s',strtotime($registro1['fecha']));
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
//            if ($registro1['txt_odometro_pos'] != '') $odometro = $registro1['txt_odometro_pos'];
$odometro = 0;
//            if ($registro1['txt_combtot_pos'] != '') $comb_total = $registro1['txt_combtot_pos'];
$comb_total = 0;
            if ($registro1['num_velocidad_pos'] != '') $velocidad = $registro1['num_velocidad_pos'];
//            if ($registro1['txt_comboci_pos'] != '') $com_ocioso = $registro1['txt_comboci_pos'];
$com_ocioso = 0;
//            if ($registro1['txt_taceite_pos'] != '') $temperatura = $registro1['txt_taceite_pos'];
$temperatura = 0;
//            if ($registro1['txt_presion_aceite_pos'] != '') $presion_aceite = $registro1['txt_presion_aceite_pos'];
$presion_aceite = 0;
//            if ($registro1['txt_rpm_pos'] != '') $rpm = $registro1['txt_rpm_pos'];
$rpm = 0;
//            if ($registro1['txt_velcruc_pos'] != '') $tiempo_crucero = $registro1['txt_velcruc_pos'];
$tiempo_crucero = 0;
            if ($registro1['txt_coderr_pos'] != '') $dtc = $registro1['txt_coderr_pos'];
//            if ($registro1['txt_rendimiento_pos'] != '') $rendimiento = $registro1['txt_rendimiento_pos'];
$rendimiento = 0;                  
        //  if($ban == 0){
      //      $odometro_anterior = $odometro;
    //        $comb_anterior = $comb_total;
  //          $ban = 1;
//          }else{
          //  $distancia = $odometro - $odometro_anterior;
        //    $comb_consumido = $comb_total - $comb_anterior;
      //      $rendimiento_calc = 0;
    //    if ($comb_consumido > 0){
  //        $rendimiento_calc = round($distancia / $comb_consumido,2);    
//        }               
        $odometro_anterior = $odometro;
        $comb_anterior = $comb_total;
          //}                  
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
              'comb_consumido' => 0,
              'rendimiento_calc' => 0,
              'datos_motor' => 0);
              $row_array[]= $fila;
              $r_totales ++;
      }
  } 
  echo "Fecha-hora;Posición;Tipo;Distancia;Comb. usado;Rendimiento;Tiempo;Velocidad\n";
    if (count($row_array) > 0 ){
      foreach ($row_array as $llave => $fila) {
          $uposicion[$llave]  = $fila['uposicion']; 
      }      
      array_multisort($uposicion, SORT_ASC,$row_array);      
      $ii = 0; 
         foreach ($row_array as $filas) {            
            echo $filas['uposicion'].";";
            echo $filas['posicion'].";";
            echo $filas['tipo'].";";
            echo $filas['distancia_odo'].";";
            echo $filas['comb_consumido'].";";
            echo $filas['rendimiento'].";";
            echo $filas['rendimiento_calc'].";";
            echo $filas['speed'].";";
            echo "\n";
        $ii = $ii + 1;
      }
  }else{
       echo "No se encontrarón registros;";
       echo "\n";
  }
?>