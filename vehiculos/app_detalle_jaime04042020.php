<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");
    include("../posiciones/app_referencia.php");
?>



<?php
  if(isset($_GET["id"]))
  {
  $id=$_GET["id"];
  include ('../conexion/conexion.php');




  $consulta  = " SELECT * FROM tb_vehiculos WHERE pk_clave_veh=?";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $cuenta=0;
  $registro = $query->fetch();

    //añadido
  $consulta_tablero  = " SELECT * FROM lectura_tablero WHERE pk_clave_veh=?";
  $query_tablero = $conn->prepare($consulta_tablero);
  $query_tablero->bindParam(1, $id);
  $query_tablero->execute();

  $registro_tablero = $query_tablero->fetch();

   /*  */
   $posfecha="a";
   $poslat ="b";
   $noserie = trim($registro['num_serie_veh']);
   $consultada = " SELECT * FROM ctg_vehiculos where veh_nserie = ?";
   $query8 = $conn->prepare($consultada);
   $query8->bindParam(1, $noserie);
   $query8->execute();
   $registro8 = $query8->fetch();
            $poslat = $registro8['veh_latitud'];
            $poslon = $registro8['veh_longitud'];
            $posubicacion= georeferencia2($poslat, $poslon, $conn);
            $posubicacionpi = georeferencia_pi2($poslat, $poslon, $conn);
            $posfecha = date('Y-m-d H:i:s', strtotime('-' . 6 . ' hour', strtotime($registro8["veh_uposicion"])));

  /*   */

  //verificar si la unidad esta desactivada o no
  //añadido;
  $consulta5 = "SELECT  num_seguimientoespecial_veh as identificador,txt_tperdida_veh from tb_vehiculos where pk_clave_veh=?";
  $query9 = $conn->prepare($consulta5);
  $query9->bindParam(1, $id);
  $query9->execute();
  $registro9 = $query9->fetch();
  $identificador = $registro9['identificador'];
  $perdida = $registro['txt_tperdida_veh'];




  if($_SESSION["nombrerol"]<>"Usuario externo")
    {
?>
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#vehiculo" class="negrita"><?php echo $registro['txt_economico_veh']?></a></li>
      <li><a data-toggle="tab" href="#tablero" class="negrita">Tablero</a></li>
      <li><a data-toggle="tab" href="#unidades" class="negrita">Cercanas</a></li>
      <li><a data-toggle="tab" href="#autoridades" class="negrita">Autoridades</a></li>
      <li><a data-toggle="tab" href="#mensajes" class="negrita">Mensajes</a></li>
      <li><a data-toggle="tab" href="#alertas" class="negrita">Alertas</a></li>
    </ul>
    <div class="tab-content"  style="min-height:140px;">
      <div id="vehiculo" class="tab-pane fade in active">
          <table class="table table-striped table-bordered table-hover">
            <tr>
              <td class="centrado"><strong>Fecha-Hora</strong></td>
              <td class="centrado"><strong>Posición</strong></td>
              <td class="centrado"><strong>Ignición</strong></td>
            <tr>
                <td><?php echo $posfecha;?></td>
              <td>
                  <?php echo $posubicacion, $posubicacionpi?>,
                  <?php echo $poslat ?>,<?php echo $poslon?>
              </td>
              <td>
                <?php if ($registro8['veh_ignicion']>=1000) echo "Encendido "; else echo "Apagado ";?>
              </td>
            </tr>
            <tr>
             <td><a href="http://69.172.241.230/?seccion=historico&id=<?php echo $registro["txt_economico_veh"] ?>" target="_blank">
              <button type="button"  class="btn btn-primary btn-xs">HISTORICO</button>
              </a></td>
             <td>
             <button type="button"  class="btn btn-primary btn-xs" onclick="toggleStreetView(<?php echo $registro['num_latitud_veh']?>,<?php echo $registro['num_longitud_veh']?>);">DE CALLE</button>
                 <!--añadido.... si la unidad esta desactivada, muestra 'activar', caso contrario muestra 'desactivar' -->
                  <?php
		$texto="";
                if (strlen($registro['txt_tperdida_veh']) != 0){
                  $texto;
                  $valor_button;

                  if($identificador == '2'){
                  $texto = 'ACTIVAR';
                  $valor_button = 0;

                  }elseif($identificador == '0'){
                    $texto = 'DESACTIVAR';
                    $valor_button = 2;

                  }elseif($perdida == ''){
                    $type= 'hidden';

                  }
                  ?>

              <button type="button" id="btnUpdate"  class="btn btn-primary btn-xs"  style="background-color: #DC143C" value="<?php echo $identificador ?>" onclick="actualizar(<?php echo $id ?>,<?php echo $valor_button ?>)"> <?php echo $texto ?> </button>

               <?php } ?>

             </td>
             <td>
                <a href="http://69.172.241.230/index.php?seccion=conductor&amp;economico=<?php echo $registro["txt_economico_veh"] ?>" target="_blank">
                  <button type="button"  class="btn btn-primary btn-xs">CONDUCTOR</button>
                </a>
             </td>
            </tr>
          </table>
      </div>

      <div id="tablero" class="tab-pane fade in">
          <table class="table table-striped table-bordered">
            <tr>
              <td class="centrado"><strong>Odómetro: </strong></td>
              <td class="centrado"><strong>Comb.Total: </strong></td>
              <td class="centrado"><strong>Velocidad: </strong></td>
              <td class="centrado"><strong>Rendimiento: </strong></td>
              <td class="centrado"><strong>ultima fecha de registro </strong></td>
              <td class="centrado"><strong>Orientación (<?php echo $registro['txt_orientacion_veh']?>)</strong></td>
            </tr>
            <tr>
              <td class="centrado"><?php echo $registro_tablero['txt_odometro_veh']?>Kms.</td>
              <td class="centrado"><?php echo $registro_tablero['txt_combtot_veh']?></td>
              <td class="centrado"><?php echo (float) $registro8['veh_velocidad']?> Kms./Hr.</td>
              <td class="centrado">0 Kms/Lt.</td>
              <td class="centrado"><?php echo $registro_tablero['fec_ultimo_registro']?></td>
              <?php
                switch ($registro['txt_orientacion_veh']) {
                  case 'Norte':
                    $imagenorientacion="norte.jpg";
                    break;
                  case 'Sur':
                    $imagenorientacion="sur.jpg";
                    break;
                  case 'Este':
                    $imagenorientacion="este.jpg";
                    break;
                  case 'Oeste':
                    $imagenorientacion="oeste.jpg";
                    break;
                  case 'Noreste':
                    $imagenorientacion="noreste.jpg";
                    break;
                  case 'Sureste':
                    $imagenorientacion="noroeste.jpg";
                    break;
                  case 'Suroeste':
                    $imagenorientacion="suroeste.jpg";
                    break;
                  case 'Noroeste':
                    $imagenorientacion="noroeste.jpg";
                    break;
                  case 'Nornoreste':
                    $imagenorientacion="nornoreste.jpg";
                    break;
                  case 'Estenoreste':
                    $imagenorientacion="estenoreste.jpg";
                    break;
                  case 'Estesureste':
                    $imagenorientacion="estesureste.jpg";
                    break;
                  case 'Estesureste':
                    $imagenorientacion="estesureste.jpg";
                    break;
                  case 'Sursureste':
                    $imagenorientacion="sursureste.jpg";
                    break;
                  case 'Sursuroeste':
                    $imagenorientacion="sursuroeste.jpg";
                    break;
                  case 'Oestesuroeste':
                    $imagenorientacion="oestesuroeste.jpg";
                    break;
                  case 'Oestenoroeste':
                    $imagenorientacion="oestenoroeste.jpg";
                    break;
                  case 'Nornoroeste':
                    $imagenorientacion="nornoroeste.jpg";
                    break;
                  case 'Parado':
                    $imagenorientacion="parado.jpg";
                    break;
                }
              ?>
              <td class="centrado"><img style="width:30%" src="imagenes/<?php echo $imagenorientacion?>"></td>


            </tr>
          </table>
      </div>

      <div id="unidades" class="tab-pane fade">
          <?php
            $latitud= $registro['num_latitud_veh'];
            $longitud=$registro['num_longitud_veh'];
            include("app_cercanas.php");
          ?>
      </div>

      <div id="autoridades" class="tab-pane fade">
         <table class="table table-striped table-bordered table-hover">
         <?php
          $consulta2  = " SELECT *, SQRT(
                POW(69.1 * (num_latitud_aut - ?), 2) +
                POW(69.1 * (? - num_longitud_aut) * COS(num_latitud_aut / 57.3), 2)) AS distance
            FROM tb_autoridades, tb_municipios, tb_estados WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo  ORDER BY distance LIMIT 5;";

/*
          echo " SELECT ST_Distance('POINT(num_longitud_aut num_latitud_aut)':: geography, 'POINT(".$longitud." ".$latitud.")':: geography)
                 FROM tb_autoridades, tb_municipios, tb_estados WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo  ORDER BY distance LIMIT 5; ";
*/

          $query2 = $conn->prepare($consulta2);
          $query2->bindParam(1, $latitud);
          $query2->bindParam(2, $longitud);
          $query2->execute();
          while($registro2 = $query2->fetch()) {
              if($registro2["distance"]>0)
              {
                ?>
                    <tr>
                      <td width="30%">
                        <strong><?php echo $registro2["txt_nombre_aut"]?></strong>
                      </td>
                      <td class="centrado" width="10%">
                        <?php echo sprintf('%0.2f', $registro2["distance"])?> Kms.
                      </td>
                      <td width="30%">
                        Tel1:<?php echo $registro2["txt_telefono1_aut"]?>, Tel2:<?php echo $registro2["txt_telefono2_aut"]?>
                      </td>
                      <td width="30%">
                        <?php echo $registro2["txt_nombre_mun"]." / ".$registro2["txt_nombre_edo"]?>
                      </td>
                    </tr>
              <?php
              }
          }
          ?>
          </table>
      </div>

      <div id="mensajes" class="tab-pane fade">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Mensaje</th>
              <th>Fecha-Hora</th>
            </tr>
          </thead>
          <tbody>
         <?php
          $consulta3  = " SELECT * FROM tb_mensajesenviadossms, tb_tiposdemensajessms WHERE pk_clave_tipm=fk_clave_tipm AND txt_economico_veh=? ORDER BY fec_fecha_mene DESC LIMIT 5 ";
          $query3 = $conn->prepare($consulta3);
          $query3->bindParam(1,$registro["txt_economico_veh"]);
          $query3->execute();
          while($registro3 = $query3->fetch()) {
                ?>
              <tr>
                <td><?php echo $registro3["txt_economico_veh"]?></td>
                <td><?php echo $registro3["txt_nombre_tipm"]?></td>
                <td><?php echo  date('d/m/Y H:i:s',strtotime($registro3["fec_fecha_mene"]))?></td>
              </tr>
              <?php
          }
          ?>
          </tbody>
        </table>
      </div>

      <div id="alertas" class="tab-pane fade">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Alerta</th>
              <th>Prioridad</th>
              <th>Estatus</th>
              <th>Acumuladas</th>
            </tr>
          </thead>
          <tbody>
         <?php
          $consulta4  = " SELECT fk_clave_tipa,fec_fecha_ale,num_prioridad_ale,num_tipo_ale,num_estatus_ale,txt_nombre_tipa,num_prioridad_tipa,COUNT(*) as acumuladas
                          FROM tb_alertas, tb_tiposdealertas
                          WHERE fk_clave_tipa=pk_clave_tipa
                          AND txt_economico_veh=?
                          GROUP BY  fk_clave_tipa,fec_fecha_ale,num_prioridad_ale,num_tipo_ale,num_estatus_ale,txt_nombre_tipa,num_prioridad_tipa
                          ORDER BY fec_fecha_ale DESC LIMIT 5 ";
          $query4 = $conn->prepare($consulta4);
          $query4->bindParam(1,$registro["txt_economico_veh"]);
          $query4->execute();
          while($registro4 = $query4->fetch()) {
            $prioridad="";
            $estatus="";
            switch ($registro4["num_prioridad_tipa"]) {
              case 3:
                $prioridad="Alta";
                $color="rojo";
                break;
              case 2:
                $prioridad="Media";
                $color="amarillo";
                break;
              case 1:
                $prioridad="Baja";
                $color="verde";
                break;
            }
            switch ($registro4["num_estatus_ale"]) {
              case 0:
                $estatus="Sin atender";
                $colorestatus="rojo";
                break;
              case 1:
                $estatus="Ok";
                $colorestatus="verde";
                break;
            }
                ?>
              <tr>
                <td><?php echo date('d/m/Y H:i:s',strtotime($registro4["fec_fecha_ale"])) ?></td>
                <td><?php echo $registro4["txt_nombre_tipa"] ?></td>
                <td><div class="centrado circulo <?php echo $color?> blanco"><?php echo $prioridad ?></div></td>
                <td><div class="centrado <?php echo $colorestatus?> blanco"><?php echo $estatus ?></div></td>
                <td><?php echo $registro4["acumuladas"] ?></td>
              </tr>
              <?php
          }
          ?>
          </tbody>
        </table>
      </div>
<?php $query3->closeCursor(); ?>
<?php $query4->closeCursor(); ?>
    </div>
<?php $query->closeCursor(); ?>
<?php $query2->closeCursor(); ?>
<?php $query8->closeCursor(); ?>
<?php
	} // fin del if
        else
                {
?>
            <table class="table table-striped table-bordered table-hover">
                  <tr>
                     <td class="centrado"><strong>Unidad: <?php echo $registro['txt_economico_veh']?></strong></td>
                  </tr>
             </table>
<?php
}
               }

?>
