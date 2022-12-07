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

      
   
<?php $query->closeCursor(); ?>


<?php 
	} // fin del if
?>
