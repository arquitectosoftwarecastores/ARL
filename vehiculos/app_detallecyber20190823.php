<?php  
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");
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
              <td><?php echo $registro['fec_posicion_veh']?></td>
              <td>
                  <?php echo $registro['txt_posicion_veh']?>, <?php echo $registro['txt_upsmart_veh']?>, 
                  <?php echo $registro['num_latitud_veh']?>,<?php echo $registro['num_longitud_veh']?>
              </td>
              <td>
                <?php if ($registro['num_ignicion_veh']==1) echo "Encendido"; else echo "Apagado";?>
              </td>
            </tr>
            <!--<tr>
             <td><a href="historico/historico_tracker.php?id=<?php echo $registro["txt_economico_veh"] ?>" target="_blank">
              <button type="button"  class="btn btn-primary btn-xs">HISTORICO</button> 
              </a></td>
             <td><button type="button"  class="btn btn-primary btn-xs" onclick="toggleStreetView(<?php echo $registro['num_latitud_veh']?>,<?php echo $registro['num_longitud_veh']?>);">DE CALLE</button></td>
             <td>
		<a href="index.php?seccion=conductor&amp;economico=<?php echo $registro["txt_economico_veh"] ?>" target="_blank">
                  <button type="button"  class="btn btn-primary btn-xs">CONDUCTOR</button>
                </a>
             </td>
            </tr>-->
          </table>

      </div>


      <div id="tablero" class="tab-pane fade in">
          <table class="table table-striped table-bordered">
            <tr>
              <td class="centrado"><strong>Odómetro: </strong></td>
              <td class="centrado"><strong>Comb.Total: </strong></td>
              <td class="centrado"><strong>Velocidad: </strong></td>
              <td class="centrado"><strong>Rendimiento: </strong></td>
              <td class="centrado"><strong>Orientación (<?php echo $registro['txt_orientacion_veh']?>)</strong></td>
            </tr>
            <tr>
              <td class="centrado"><?php echo $registro['txt_odometro_veh']?>Kms.</td>
              <td class="centrado"><?php echo $registro['txt_combtot_veh']?></td>
              <td class="centrado">0 Kms./Hr.</td>
              <td class="centrado">0 Kms/Lt.</td>
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

<?php 
	} // fin del if
?>
