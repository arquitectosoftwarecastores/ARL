<?php
//session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
?>

<?php
if (isset($_GET["id"])) {
  $id = $_GET["id"];
  include('../conexion/conexion.php');
  $consulta  = " SELECT * FROM tb_remolques WHERE pk_clave_rem=?";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $cuenta = 0;
  $registro = $query->fetch();
?>




  <div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#unidad" aria-controls="home" role="tab" data-toggle="tab">
          <strong>
            <?php echo $registro['txt_economico_rem'] ?>
          </strong>
        </a>
      </li>
      <li role="presentation"><a href="#tablero" aria-controls="tablero" role="tab" data-toggle="tab">Tablero</a></li>
      <!--
      <li role="presentation"><a href="#cercanas" aria-controls="cercanas" role="tab" data-toggle="tab">Cercanas</a></li>
      -->
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="unidad">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <td class="centrado"><strong>Fecha-Hora</strong></td>
            <td class="centrado"><strong>Posición</strong></td>
            <td class="centrado"><strong>Ignición</strong></td>
          <tr>
            <td><?php echo $registro['fec_posicion_rem'] ?></td>
            <td>
              <?php echo $registro['txt_georeferencia_mun'] ?>, <?php echo $registro['txt_georeferencia_cas'] ?>,
              <?php echo $registro['num_latitud_rem'] ?>,<?php echo $registro['num_longitud_rem'] ?>
            </td>
            <td>
              <?php if ($registro['num_ignicion_rem'] == 1) echo "Encendido";
              else echo "Apagado"; ?>
            </td>
          </tr>
        </table>
      </div>



      <div role="tabpanel" class="tab-pane" id="tablero">
        <table class="table table-striped table-bordered">
          <tr>
            <td class="centrado"><strong>Odómetro: </strong></td>
            <td class="centrado"><strong>Comb.Total: </strong></td>
            <td class="centrado"><strong>Velocidad: </strong></td>
            <td class="centrado"><strong>Rendimiento: </strong></td>
            <td class="centrado"><strong>Orientación (<?php echo $registro['txt_orientacion_rem'] ?>)</strong></td>
          </tr>
          <tr>
            <td class="centrado"><?php //echo $registro['txt_odometro_veh'] ?>Kms.</td>
            <td class="centrado"><?php //echo $registro['txt_combtot_veh'] ?></td>
            <td class="centrado">0 Kms./Hr.</td>
            <td class="centrado">0 Kms/Lt.</td>
            <?php
            switch ($registro['txt_orientacion_rem']) {
              case 'Norte':
                $imagenorientacion = "norte.jpg";
                break;
              case 'Sur':
                $imagenorientacion = "sur.jpg";
                break;
              case 'Este':
                $imagenorientacion = "este.jpg";
                break;
              case 'Oeste':
                $imagenorientacion = "oeste.jpg";
                break;
              case 'Noreste':
                $imagenorientacion = "noreste.jpg";
                break;
              case 'Sureste':
                $imagenorientacion = "noroeste.jpg";
                break;
              case 'Suroeste':
                $imagenorientacion = "suroeste.jpg";
                break;
              case 'Noroeste':
                $imagenorientacion = "noroeste.jpg";
                break;
              case 'Nornoreste':
                $imagenorientacion = "nornoreste.jpg";
                break;
              case 'Estenoreste':
                $imagenorientacion = "estenoreste.jpg";
                break;
              case 'Estesureste':
                $imagenorientacion = "estesureste.jpg";
                break;
              case 'Estesureste':
                $imagenorientacion = "estesureste.jpg";
                break;
              case 'Sursureste':
                $imagenorientacion = "sursureste.jpg";
                break;
              case 'Sursuroeste':
                $imagenorientacion = "sursuroeste.jpg";
                break;
              case 'Oestesuroeste':
                $imagenorientacion = "oestesuroeste.jpg";
                break;
              case 'Oestenoroeste':
                $imagenorientacion = "oestenoroeste.jpg";
                break;
              case 'Nornoroeste':
                $imagenorientacion = "nornoroeste.jpg";
                break;
              case 'Parado':
                $imagenorientacion = "parado.jpg";
                break;
            }
            ?>
            <td class="centrado"><img style="width:30%" src="imagenes/<?php echo $imagenorientacion ?>"></td>
          </tr>
        </table>
      </div>



      <div role="tabpanel" class="tab-pane" id="cercanas">
        <?php
        $latitud = $registro['num_latitud_rem'];
        $longitud = $registro['num_longitud_rem'];
        // include("app_cercanas.php");
        ?>

      </div>

  </div>


  <?php

  $query->closeCursor(); 
} // fin del if
  ?>