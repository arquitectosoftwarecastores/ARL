﻿<!-- Con <page> se define una hoja con los márgenes que
   que se muestran -->
<style type="text/css">
<!--
#encabezado {padding:10px 0; border-top: 1px solid; border-bottom: 1px solid; width:100%;}
#encabezado .fila #col_1 {width: 100%; text-align:center;}
#encabezado .fila #col_2 {text-align:center; width: 100%}

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

    include("/var/www/html/conexion/conexion.php");
    include ('../posiciones/app_referencia.php');
    include ('../funciones/distancia.php');
    include ('../funciones/diferenciaenmin.php');


    $vehiculo=$_GET['vehiculo'];
    $from=$_GET['from'];
    $to=$_GET['to'];
  if (isset($_GET["txr"]))
    $txr=$_GET["txr"];
  else
  // Default time in minutes between rows to being displayed.
    $txr=5;

  switch ($txr):
    case 5:
        // The range of minutes is the same in the filters.
        $rangoMin = 5;
          break;
    case 10:
        $rangoMin = 10;
          break;
    case 20:
        $rangoMin = 20;
          break;
    case 30:
        $rangoMin = 30;
          break;
    default:
        $rangoMin = 5;
          break;
  endswitch;

    //$count_rows = 1;

    $consulta1  = " SELECT * FROM tb_vehiculos
                   WHERE txt_economico_veh=?";
    $query1 = $conn->prepare($consulta1);
    $query1->bindParam(1, $vehiculo);
    $query1->execute();
    $serie=0;
    while($registro1 = $query1->fetch())
       $serie=$registro1["num_serie_veh"];
    if($serie==0)
    {
      echo "<p>No se encontró el vehículo.</p>";
      exit();
    }

    $consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
    $query0 = $conn->prepare($consulta0);
    $query0->execute();
    $registro0 = $query0->fetch();
    $ajustegps=$registro0["num_valor_par"];
    $query0->closeCursor();

    $fechainicial=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($from)));
    $fechafinal=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($to)));

    if($fechainicial < '2018-11-05 18:26:00'){
        $strSQL  = " SELECT * FROM tb_posiciones_historico WHERE num_nserie_pos='".$serie."' 
        AND fec_ultimaposicion_pos >= '".$fechainicial."'
        AND fec_ultimaposicion_pos <= '".$fechafinal."' 
        ORDER BY fec_ultimaposicion_pos ASC";
      }elseif($fechainicial < '2019-01-17 11:00:00' ){
        $strSQL  = " SELECT * FROM tb_posiciones_historico2 WHERE num_nserie_pos='".$serie."' 
        AND fec_ultimaposicion_pos >= '".$fechainicial."'
        AND fec_ultimaposicion_pos <= '".$fechafinal."' 
        ORDER BY fec_ultimaposicion_pos ASC";
    
      }elseif($fechainicial < '2019-02-01 12:40:00'){
        $strSQL  = " SELECT * FROM tb_posiciones_historico3 WHERE num_nserie_pos='".$serie."' 
        AND fec_ultimaposicion_pos >= '".$fechainicial."'
        AND fec_ultimaposicion_pos <= '".$fechafinal."' 
        ORDER BY fec_ultimaposicion_pos ASC";
      }else{
        $strSQL  = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='".$serie."' 
        AND fec_ultimaposicion_pos >= '".$fechainicial."'
        AND fec_ultimaposicion_pos <= '".$fechafinal."' 
        ORDER BY fec_ultimaposicion_pos ASC";
      }
?>
<page backtop="49mm" backbottom="10mm" backleft="10mm" backright="10mm" footer="page">
<page_header>
        <table id="encabezado">
            <tr class="fila">
                <td id="col_1" >
                    <img src="/var/www/html/imagenes/logo.jpg">
                    <br>
                </td>
            </tr>
            <tr class="fila">
                <td id="col_2">
                    <p id="p1"><strong><?php echo ucwords(strtolower($_SESSION["nombre"]));?>, Histórico de posiciones, Fecha: <?php echo date('d/m/Y H:i:s', time())?></strong></p>
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
            <td id="col_4" width="85" style="border-top-left-radius: 20px;"><strong>Fecha-hora</strong></td>
            <td id="col_4" width="210"><strong>Posición</strong></td>
            <td id="col_4" width="85"><strong>Velocidad</strong></td>
            <td id="col_4" width="75"><strong>Distancia Recorrida</strong></td>
            <td id="col_4" width="70"><strong>Ignición</strong></td>
            <td id="col_4" width="65"><strong>Odómetro</strong></td>
            <td id="col_4" width="80"><strong>Comb. Total</strong></td>
            <td id="col_4" width="75"><strong>Latitud</strong></td>
            <td id="col_4" width="75" style="border-top-right-radius: 20px;"><strong>Longitud</strong></td>
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

        $query = $conn->prepare($strSQL);
        $query->execute();
        $contador=0;
        $distanciarecorrida=0;


        //$count_rows++;
        $count_rows = 0;
        $flag_fill_color = "No";
        $primerFecha = "";
        $minutos = 0;
        while ($registro = $query->fetch()) { // The query fetch the rows to be display
            if ($count_rows == 0)
            {
            $primerFecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos'])));
            ?>
              <tr class="tabla fila" >
                    <td id="col_3" width="85"><?php echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))) ?></td>
                    <td id="col_3" width="210">
                        <?php echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                        <?php echo georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                    </td>
                    <td id="col_3" width="85"><?php echo round($registro["txt_velocidad_pos"]/0.62137,2)?> Km/hr.  <?php echo round($distanciarecorrida,2);?></td>
                    <td id="col_3" width="75"><?php echo round($distanciarecorrida,2);?> Km.</td>
                    <td id="col_3" width="70"><?php if($registro["num_ignicion_pos"]) echo "Encendido"; else echo "Apagado";?></td>
                    <td id="col_3" width="65"><?php echo $registro["txt_odometro_pos"]?></td>
                    <td id="col_3" width="80"><?php echo $registro["txt_combtot_pos"]?></td>
                    <td id="col_3" width="75"><?php echo $registro["num_latitud_pos"]?></td>
                    <td id="col_3" width="75"><?php echo $registro["num_longitud_pos"]?></td>
                </tr>
            <?php
            }
            else
            {

            if($contador)
                $distancia=distancia($latitudanterior, $longitudanterior,$registro["num_latitud_pos"], $registro["num_longitud_pos"]);
            else
            {
                $distancia=0;
            }
            $latitudanterior = $registro["num_latitud_pos"];
            $longitudanterior = $registro["num_longitud_pos"];
            $contador++;
            $distanciarecorrida+=$distancia;
            ?>
               <tr class="tabla fila" >
            <?php
            //if (($count_rows == 0) or ($count_rows % $num == 0))
            if ($primerFecha != "")
            {
                $segundaFecha =date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos'])));
                $difTiempo = tiempototal($primerFecha, $segundaFecha);
                $minutos = datetomin($difTiempo);
            }

            $cambiaFecha = "No";

            //if (($minutos != 0) and ($minutos >= $rangoMin))
            if ($minutos >= $rangoMin)
			{ 
            switch ($flag_fill_color) {
                case "No":
            ?>

                    <td id="col_5" width="85"><?php echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))) ?></td>
                    <td id="col_5" width="210">
                        <?php echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                        <?php echo georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                    </td>
                    <td id="col_5" width="85"><?php echo round($registro["txt_velocidad_pos"]/0.62137,2)?> Km/hr.  <?php echo round($distanciarecorrida,2);?></td>
                    <td id="col_5" width="75"><?php echo round($distanciarecorrida,2);?> Km.</td>
                    <td id="col_5" width="70"><?php if($registro["num_ignicion_pos"]) echo "Encendido"; else echo "Apagado";?></td>
                    <td id="col_5" width="65"><?php echo $registro["txt_odometro_pos"]?></td>
                    <td id="col_5" width="80"><?php echo $registro["txt_combtot_pos"]?></td>
                    <td id="col_5" width="75"><?php echo $registro["num_latitud_pos"]?></td>
                    <td id="col_5" width="75"><?php echo $registro["num_longitud_pos"]?></td>
                <?php
                  $cambiaFecha = "Si";
                  $flag_fill_color = "Si";
                  break;
                case "Si":
                ?>
                    <td id="col_3" width="85"><?php echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))) ?></td>
                    <td id="col_3" width="210">
                        <?php echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                        <?php echo georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn)?>
                    </td>
                    <td id="col_3" width="85"><?php echo round($registro["txt_velocidad_pos"]/0.62137,2)?> Km/hr.  <?php echo round($distanciarecorrida,2);?></td>
                    <td id="col_3" width="75"><?php echo round($distanciarecorrida,2);?> Km.</td>
                    <td id="col_3" width="70"><?php if($registro["num_ignicion_pos"]) echo "Encendido"; else echo "Apagado";?></td>
                    <td id="col_3" width="65"><?php echo $registro["txt_odometro_pos"]?></td>
                    <td id="col_3" width="80"><?php echo $registro["txt_combtot_pos"]?></td>
                    <td id="col_3" width="75"><?php echo $registro["num_latitud_pos"]?></td>
                    <td id="col_3" width="75"><?php echo $registro["num_longitud_pos"]?></td>

                <?php
                  $cambiaFecha = "Si";
                  $flag_fill_color = "No";
                  break;
              default:
                  $flag_fill_color = "No";
                  break;

            } //Case
			} // If Minutos
                ?>

        </tr>
<?php
        if ($cambiaFecha =="Si")
        {
            $primerFecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos'])));
        }
        }
        $count_rows++;
        } // While
        ?>
    </table>
</page>
