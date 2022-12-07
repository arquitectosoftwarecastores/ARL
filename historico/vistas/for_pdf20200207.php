<!-- Con <page> se define una hoja con los márgenes que
   que se muestran -->
<style type="text/css">
    <!--
    #encabezado {padding:10px 0; border-top: 1px solid; border-bottom: 1px solid; width:100%;}
    #encabezado .fila #col_1 {width: 100%; text-align:center;}
    #encabezado .fila #col_2 {text-align:center; width: 100%}
    #encabezado .fila #col_3 {text-align:center; width: 38%}

    #encabezado .fila td img {width:150px; text-align:center;}
    #encabezado .fila #col_2 #span1{font-size: 15px;}
    #encabezado .fila #col_2 #span2{font-size: 12px; color: #4d9;}

    #footer {padding-top:5px 0; border-top: 1px solid; width:100%;}
    #footer .fila td {text-align:center; width:100%;}
    #footer .fila td span {font-size: 10px; color: #f5a;}

    #table {background-color: #ffffff; text-align:center; width: 100%;}
    #table .tabla #col_3 {background-color: #f5f5f5; border: 1px solid #ddd; text-align:center;}


    #margen tr td {width:38%;  height:25;}
    #datos_header {margin:auto; width:100%; border-top:3px solid #ddd; border-left:3px solid #ddd; border-right:3px solid #ddd; border-top-left-radius:20px; border-top-right-radius:20px;}
    #datos_header tr td {border:1px solid #ddd; width:38%; text-align:center; height:25;}
    #datos_header .fila #col_4 {border:1px solid #ddd; background-color: #D8D8D8;}
    #datos {border-bottom:3px solid #D8D8D8; border-right:3px solid #D8D8D8; border-left:3px solid #D8D8D8; margin:auto; width:100%; border-bottom-left-radius:20px; border-bottom-right-radius:20px;}
    #datos .fila #col_3 {border:1px solid #ddd; background-color: #f2f2f2;}
    #datos .fila #col_4 {border:1px solid #ddd; background-color: #D8D8D8;}
    #datos .fila #col_5 {border:1px solid #ddd;}
    #datos tr td {border:1px solid #ddd; width:38%; text-align:center; height:25;}
</style>

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");

include("conexion.php");
include ('../posiciones/app_referencia.php');
include ('../funciones/distancia.php');
include ("calcula_tiempo.php");


//Consulta parametro de ajuste de horas con respecto al GPS

$consulta0 = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
$query0 = $conn->prepare($consulta0);
$query0->execute();
$registro0 = $query0->fetch();
$ajustegps = $registro0["num_valor_par"];
$query0->closeCursor();

$id = $_GET['id'];
$filtro = $_GET['filtro'];
if (isset($_GET["txr"]))
    $txr = $_GET["txr"];
else
// Default time in minutes between rows to being displayed.
    $txr = 5;

switch ($txr):
    case 5:
        // Because we have only rows with a difference of 2 minutes,
        // we need an integer number to compare if the row is a multiple
        // of 2. This is with the purpose of display rows nearest to
        // the last specified date range.
        // In other way, using 3 or 2.5 you will be exceeding the date
        // range of the query and/or will not display the last record
        // close to the end date. 
        $num = 2;
        break;
    case 10:
        $num = 5;
        break;
    case 20:
        $num = 10;
        break;
    case 30:
        // The same case when  minutes is selected.
        $num = 14;
        break;
    default:
        $num = 2;
        break;
endswitch;

$fechainicial = date('Y-m-d H:i:s', strtotime($ajustegps . ' hour', strtotime($_GET["ini"])));
$fechafinal = date('Y-m-d H:i:s', strtotime($ajustegps . ' hour', strtotime($_GET["fin"])));

if ($filtro == "posiciones" or $filtro == "trayectoria") {

    $consulta = "SELECT num_serie_veh FROM tb_vehiculos WHERE txt_economico_veh =? ";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $query->execute();
    $registro = $query->fetch();
    $nserie = $registro["num_serie_veh"];

   #--------------------------version 2
   if($fechainicial < '2018-11-05 18:26:00') {
            
    $consulta1 = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
    FROM tb_posiciones_historico WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
    AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
}elseif($fechainicial < '2019-01-17 11:00:00'){
    $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
FROM tb_posiciones_historico2 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
}elseif($fechainicial < '2019-02-01 12:40:00'){
    $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
    FROM tb_posiciones_historico3 WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
    AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
}else{
    $consulta1 ="SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud,num_longitud_pos as longitud,num_ignicion_pos as ignicion
    FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=? 
    AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC"; 
}
    /*

      $consulta1  = "SELECT *,fec_ultimaposicion_pos as fecha, num_latitud_pos as latitud, num_longitud_pos as longitud,num_ignicion_pos as ignicion
      FROM tb_posiciones WHERE num_nserie_pos = ? AND fec_ultimaposicion_pos>=?
      AND fec_ultimaposicion_pos<=? ORDER BY fec_ultimaposicion_pos ASC";
     * 
     */
    $query1 = $conn->prepare($consulta1);
    $query1->bindParam(1, $nserie);
    $query1->bindParam(2, $fechainicial);
    $query1->bindParam(3, $fechafinal);
    $query1->execute();

    $row_array = array();
    $r_totales = 0;
    $distancia = 0;
    $comb_consumido = 0;
    $rendimiento_calc = 0;
    $ban = 0;
    $contador = 0;
    $distanciarecorrida = 0.0;

    while ($registro1 = $query1->fetch()) {
        $icono = "images/posicion.png";
        //$fecha = $registro1['fecha'];
        $fecha = date('Y-m-d H:i:s', strtotime('-' . $ajustegps . ' hour', strtotime($registro1['fecha'])));
        $latitud = $registro1['latitud'];
        $longitud = $registro1['longitud'];
        $unidad_ubicacion = georeferencia($latitud, $longitud, $conn) . "," . georeferencia_pi($latitud, $longitud, $conn);
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
            $distancia_veh = distancia($latitudanterior, $longitudanterior, $registro1['latitud'], $registro1['longitud']);
        else {
            $distancia_veh = 0.0;
        }
        $latitudanterior = $registro1["latitud"];
        $longitudanterior = $registro1["longitud"];
        $contador++;
        if (is_nan($distancia_veh)) {
            $distanciarecorrida += 0.0;
        } else {
            $distanciarecorrida += $distancia_veh;
        }

        if ($registro1['txt_odometro_pos'] != '')
            $odometro = $registro1['txt_odometro_pos'];
        if ($registro1['txt_combtot_pos'] != '')
            $comb_total = $registro1['txt_combtot_pos'];
        if ($registro1['txt_velocidad_pos'] != '')
            $velocidad = $registro1['txt_velocidad_pos'];
        if ($registro1['txt_comboci_pos'] != '')
            $com_ocioso = $registro1['txt_comboci_pos'];
        if ($registro1['txt_taceite_pos'] != '')
            $temperatura = $registro1['txt_taceite_pos'];
        if ($registro1['txt_presion_aceite_pos'] != '')
            $presion_aceite = $registro1['txt_presion_aceite_pos'];
        if ($registro1['txt_rpm_pos'] != '')
            $rpm = $registro1['txt_rpm_pos'];
        if ($registro1['txt_velcruc_pos'] != '')
            $tiempo_crucero = $registro1['txt_velcruc_pos'];
        if ($registro1['txt_coderr_pos'] != '')
            $dtc = $registro1['txt_coderr_pos'];
        if ($registro1['txt_rendimiento_pos'] != '')
            $rendimiento = $registro1['txt_rendimiento_pos'];

        if ($ban == 0) {
            $odometro_anterior = $odometro;
            $comb_anterior = $comb_total;
            $ban = 1;
        } else {
            $distancia = $odometro - $odometro_anterior;
            $comb_consumido = $comb_total - $comb_anterior;
            $rendimiento_calc = 0;
            if ($comb_consumido > 0) {
                $rendimiento_calc = round($distancia / $comb_consumido, 2);
            }
            $odometro_anterior = $odometro;
            $comb_anterior = $comb_total;
        }

        $fila = array('latitud' => $latitud,
            'longitud' => $longitud,
            'unidad' => $id,
            'posicion' => $unidad_ubicacion,
            'uposicion' => $fecha,
            'ignicion' => $registro1['ignicion'],
            'icono' => $icono,
            'tipo' => 'Posicion',
            'odometro' => $odometro,
            'comb_total' => $comb_total,
            'speed' => round($velocidad / 0.62137, 2),
//              'speed' => $velocidad, 
            'com_ocioso' => $com_ocioso,
            'temperatura' => $temperatura,
            'presion_aceite' => $presion_aceite,
            'rpm' => $rpm,
            'tiempo_crucero' => $tiempo_crucero,
            'dtc' => $dtc,
            'rendimiento' => $rendimiento,
            'distancia_odo' => $distancia,
            'distancia_recorrida' => round($distanciarecorrida, 2),
            'comb_consumido' => $comb_consumido,
            'rendimiento_calc' => $rendimiento_calc,
            'datos_motor' => 1
        );
        $row_array[] = $fila;
        $r_totales++;
    }  // fin del while posiciones
}  // fin del if filtro
//-------------------------  extraccion de mensajes en el periodo dado ------------------------

if ($_GET['filtro'] == 'eventos') {

    $consulta2 = "SELECT * FROM tb_alertas, tb_tiposdealertas WHERE txt_economico_veh =?
             AND fk_clave_tipa=pk_clave_tipa AND fec_fecha_ale<=? AND fec_fecha_ale>=?
             ORDER BY fec_fecha_ale ASC";
    $query2 = $conn->prepare($consulta2);
    $query2->bindParam(1, $id);
    $query2->bindParam(2, $fechainicial);
    $query2->bindParam(3, $fechafinal);
    $query2->execute();

    while ($registro2 = $query2->fetch()) {
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
        if ($registro2['txt_ubicacion_ale'] != '')
            $unidad_ubicacion = $registro2['txt_ubicacion_ale'];
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
            $distancia_veh = distancia($latitudanterior, $longitudanterior, $registro2["num_latitud_ale"], $registro2['num_longitud_ale']);
        else {
            $distancia_veh = 0;
        }
        $latitudanterior = $registro2["num_latitud_ale"];
        $longitudanterior = $registro2["num_longitud_ale"];
        $contador++;
        if (is_nan($distancia_veh)) {
            $distanciarecorrida += 0.0;
        } else {
            $distanciarecorrida += $distancia_veh;
        }

        $fila = array('latitud' => $registro2['num_latitud_ale'],
            'longitud' => $registro2['num_longitud_ale'],
            'unidad' => $id,
            'posicion' => $unidad_ubicacion,
            'uposicion' => $registro2['fec_fecha_ale'],
            'ignicion' => $ignicion,
            'icono' => $icono,
            'tipo' => $registro2['num_tipo_ale'],
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
            'distancia_recorrida' => round($distanciarecorrida, 2),
            'comb_consumido' => 0,
            'rendimiento_calc' => 0,
            'datos_motor' => 0);
        $row_array[] = $fila;
        $r_totales++;
    }
}
?>
<page backtop="58mm" backbottom="10mm" backleft="10mm" backright="10mm" footer="page">
    <page_header>
        <table id="encabezado">
            <tr class="fila">
                <td id="col_1" >
                    <img src="/var/www/html/imagenes/logo.jpg">
                    <br>
                </td>
            </tr>
            <tr class="fila">
                <td id="col_3">
                    <strong><?php echo ucwords(strtolower($_SESSION["nombre"])); ?>, Histórico de posiciones, Fecha: <?php echo date('d/m/Y H:i:s', time()) ?></strong>
                </td>
            </tr>
            <tr class="fila">
                <td id="col_3">
                    <strong>Unidad: <?php echo $id; ?>, Rango de Fecha: <?php echo $_GET['ini']; ?> - <?php echo $_GET['fin']; ?></strong>
                </td>
            </tr>
        </table>
        <table id="margen">
            <tr>
                <td></td>
            </tr>
        </table>

        <table id="datos_header">
            <tr class="tabla fila">
                <td id="col_4" width="85" style="border-top-left-radius: 20px;"><strong>Fecha / hora</strong></td>
                <td id="col_4" width="210"><strong>Posición</strong></td>
                <td id="col_4" width="85"><strong>Distancia</strong></td>
                <td id="col_4" width="85"><strong>Velocidad</strong></td>
                <td id="col_4" width="85"><strong>Ignición</strong></td>
                <td id="col_4" width="85"><strong>Latitud</strong></td>
                <td id="col_4" width="85" style="border-top-right-radius: 20px;"><strong>Longitud</strong></td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table id="footer">
            <tr class="fila">
                <td>
                    <br>
                </td>
            </tr>
        </table>
    </page_footer>

    <table id="datos">
        <?php
        $count_rows = 0;
        $flag_fill_color = "No";
        if (count($row_array) > 0) {

            foreach ($row_array as $llave => $fila) {
                $uposicion[$llave] = $fila['uposicion'];
            }

            array_multisort($uposicion, SORT_ASC, $row_array);

            $ii = 0;
            $velocidad_anterior = 0;
            $distancia_anterior = 0;
            $pos_ant = 0;
            foreach ($row_array as $filas) {
                if ($ii == 0)
                    $first_date = $filas['uposicion'];
                ?>
                <tr class="tabla fila" >
                    <?php
                    if (($ii == 0) or ($ii % $num == 0)) {
                        switch ($flag_fill_color) {
                            case "No":
                                ?>
                                <td id="col_5" width="85"><?php echo $filas['uposicion'] ?></td>
                                <td id="col_5" width="210"><?php echo $filas['posicion'] ?></td>
                                <td id="col_5" width="85"><?php echo $filas['distancia_recorrida'] ?> Kms.</td>
                                <!-- <td id="col_5" width="85">< <!--?php echo $filas['speed']?> Km/hr.  <<php echo $filas['distancia_recorrida']?></td>  -->
                                <td id="col_5" width="85"><?php
                    if ($velocidad_anterior != 0) {
                        $distancia = $filas['distancia_recorrida'] - $distancia_anterior;
                        $last_date = $filas['uposicion'];
                        $total_time = tiempototal($pos_ant, $filas['uposicion']);
                        //calculates average speed
                        $timehours = datetohours($total_time);
                        if ($distancia != 0) {
                            $avspeed = averagespeed(round($distancia, 2), $timehours);
                        } else {
                            $avspeed = 0;
                        }
                        echo round($avspeed, 2);
                      //  echo $distancia_anterior;
                    }
                                ?> Km/hr.</td> 
                                <td id="col_5" width="85"><?php if ($filas['ignicion']) echo "Encendido"; else echo "Apagado"; ?></td>
                                <td id="col_5" width="85"><?php echo $filas['latitud'] ?></td>
                                <td id="col_5" width="85"><?php echo $filas['longitud'] ?></td>
                                <?php
                                $flag_fill_color = "Si";
                                break;
                            case "Si":
                                ?>
                                <td id="col_3" width="85"><?php echo $filas['uposicion'] ?></td>
                                <td id="col_3" width="210"><?php echo $filas['posicion'] ?></td>
                                <td id="col_3" width="85"><?php echo $filas['distancia_recorrida'] ?> Kms.</td> <!-- distancia_odo -->
                              <!--    <td id="col_3" width="85"><<!--?php echo $filas['speed']?> Km/hr.  <<!--?php echo $filas['distancia_recorrida']?></td>  -->
                                <td id="col_5" width="85"><?php
                    if ($velocidad_anterior != 0) {
                        $distancia = $filas['distancia_recorrida'] - $distancia_anterior;
                        $last_date = $filas['uposicion'];
                        $total_time = tiempototal($pos_ant, $filas['uposicion']);
                        //calculates average speed
                        $timehours = datetohours($total_time);
                        if ($distancia != 0) {
                            $avspeed = averagespeed(round($distancia, 2), $timehours);
                        } else {
                            $avspeed = 0;
                        }
                        echo round($avspeed, 2);
                     //   echo $distancia_anterior;
                    }
                                ?> Km/hr.</td>
                                <td id="col_3" width="85"><?php if ($filas['ignicion']) echo "Encendido"; else echo "Apagado"; ?></td>
                                <td id="col_3" width="85"><?php echo $filas['latitud'] ?></td>
                                <td id="col_3" width="85"><?php echo $filas['longitud'] ?></td>

                                <?php
                                $flag_fill_color = "No";
                                break;
                            default:
                                $flag_fill_color = "No";
                                break;
                        }
                    }
                    ?>

                </tr>
                <?php
                $ii = $ii + 1;
                $velocidad_anterior = $velocidad_anterior + 1;
                $distancia_anterior = $filas['distancia_recorrida'];
                $pos_ant = $filas['uposicion'];
            } // end of the foreach
            // calculates total time

            function endKey($array) {
                end($array);
                return key($array);
            }

            //echo endKey($fila);
            //echo end($fila[4]);
            //echo $filas['uposicion'][-1];
            $last_date = $filas['uposicion'];
            $total_time = tiempototal($first_date, $last_date);
            //calculates average speed
            $timehours = datetohours($total_time);
            $avspeed = averagespeed(round($distanciarecorrida, 2), $timehours);
        } else {
            ?> <p>No se encontraron registros</p><?php
    }
        ?>
    </table>
    <br>
    <table>
        <tr>
            <td>
                <strong><?php echo "Distancia Total: " . round($distanciarecorrida, 2) . " Kms."; ?></strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong><?php echo "Tiempo Total: " . $total_time; ?></strong>
            </td>
        </tr>
        <tr>
            <td>
                <strong><?php echo "Velocidad Promedio: " . round($avspeed, 2) . " Kms./Hr." ?></strong>
            </td>
        </tr>
    </table>

</page>
