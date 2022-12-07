<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', false);

$valUni = true;



if ($valUni) {


  $consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";
  $query0 = $conn->prepare($consulta0);
  $query0->execute();
  $registro0 = $query0->fetch();
  $ajustegps=$registro0["num_valor_par"];
  $query0->closeCursor();

?>
<?php include("funciones/diferenciaenhoras.php");?>
<?php include("funciones/distancia.php");?>
<?php include("posiciones/app_referencia.php");?>
<div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div id="infodata">
          <table class="table table-striped table-bordered table-hover" id="info">
            <thead>
              <tr>
                <th>Económico</th>
                <th>Fecha de entrada</th>
                <th>Fecha de salida</th>
                <th>Tiempo permanencia</th>
                <th>Ubicación</th>
                <th>Geocerca</th>
              </tr>
            </thead>
            <tbody>
          	<?php
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
              $tofinal=date('Y-m-d H:i:s',strtotime(($ajustegps+24).' hour',strtotime($to.'00:00:00')));
              //echo "  ".$tofinal."  ";
          		while ($registro = $query->fetch())
              {
               
                $fecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["fec_ultimaposicion_pos"])));
                $fecha_ok=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["fec_ultimaposicion_pos"])));
                //$fecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["fec_ultimaposicion_pos"])));
                //$fecha_ok=date('Y-m-d H:i:s',strtotime($registro["fec_ultimaposicion_pos"]));
               //echo "fecha: ".$fecha." fecha_ok:".$fecha_ok." Reg:".$registro["fec_ultimaposicion_pos"];
              //if ($tofinal > date('Y-m-d H:i:s',strtotime($registro["fec_ultimaposicion_pos"])))
              //{
                $lat_actual = $registro["num_latitud_pos"];
                $lon_actual = $registro["num_longitud_pos"];
                $unidad_ubicacion = georeferencia($lat_actual,$lon_actual,$conn).", ";
                $unidad_ubicacion .= georeferencia_pi($lat_actual,$lon_actual,$conn);

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
                      $consulta_alertas  = "SELECT fec_fecha_ale, txt_campo1_ale, txt_nombre_tipa
                                  FROM tb_alertas, tb_tiposdealertas
                                  WHERE fk_clave_tipa=pk_clave_tipa
                                  AND txt_economico_rem = '".$eco."' AND
                                  -- (txt_nombre_tipa = 'Entrando Zona' OR txt_nombre_tipa = 'Entrada Geocerca') AND
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
                        $consulta_salidas = "SELECT fec_fecha_ale, txt_campo1_ale, txt_nombre_tipa
                                    FROM tb_alertas, tb_tiposdealertas
                                    WHERE fk_clave_tipa=pk_clave_tipa
                                    AND txt_economico_rem = '".$eco."' AND
                               --     txt_nombre_tipa = '".$tipo_buscar."' AND
                                    fec_fecha_ale >= '".$row_alertas['fec_fecha_ale']."' and
                                    txt_campo1_ale = '".$row_alertas['txt_campo1_ale']."'
                                    ORDER BY fec_fecha_ale ASC LIMIT 1";
                        //echo $consulta_salidas."\n";
                        $query3 = $conn->prepare($consulta_alertas);
                        $query3->execute();
                        $row_salidas = $query3->fetch();

                  /*      if($row_salidas['alerta_timestamp'] == "")
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
                        }  // fin del if $row_salidas['alerta_timestamp'] == ""*/

                      } // fin del if $row_alertas["txt_nombre_tipa"] != ""


                      //echo "Fin:".$fecha_fin.", inicio:".$fecha_inicio.", ";


                    // $tiempo = calcula_diferencia($fecha_fin,$fecha_inicio);


                    $date_a = new DateTime($fecha_fin);
                    $date_b = new DateTime($fecha_inicio);
                    $interval = date_diff($date_a,$date_b);
                    $tiempo = $interval->format('%a días %H hr. %I min.');

                      if ($tiempo!="0 días 00 hr. 00 min.")
                      {
                        echo "<tr>";
                        echo "<td>".$eco."</td>";
                        echo "<td>".$fecha_inicio."</td>";
                        echo "<td>".$fecha_fin."</td>";
                        echo "<td>".$tiempo."</td>";
                        echo "<td>".trim($ubicacion_inicio)."</td>";
                        echo "<td>".trim($geocerca)."</td>";
                        echo "</tr>";
                      }


                    }  // fin del if $fecha_fin!=""

                    // actualiza el pibote para la siguiente comparacion
                    $fecha_inicio = $fecha_ok;
                    $fecha_fin = "";
                    $ubicacion_inicio = $unidad_ubicacion;
                    $lat_pibote = $registro['num_latitud_pos'];
                    $lon_pibote = $registro['num_longitud_pos'];
                    //echo $fecha_inicio."<br>";
                  }   // fin del if $distancia_pibote <= $distancia


                }  // fin del if $ban=1
                else
                {
                  $fecha_inicio = $fecha_ok;
                  $ubicacion_inicio = $unidad_ubicacion;
                  $lat_pibote = $registro['num_latitud_pos'];
                  $lon_pibote = $registro['num_longitud_pos'];
                  $ban = 1;
                  //echo $fecha_inicio."<br>";
                }  // fin del else  if $ban=1
               //}  // fin if para fecha final
              } // fin del ciclo
	    } // ultima llave
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>
