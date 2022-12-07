<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
if (isset($_GET["id"])) {
  session_start();
  include("../posiciones/app_referencia.php");
  $id = $_GET["id"];
  include('../config/conexion.php');
  $consulta  = " SELECT * 
                FROM tb_remolques
                WHERE pk_clave_rem=?";
  $qryRem = $conn->prepare($consulta);
  $qryRem->bindParam(1, $id);
  $qryRem->execute();
  $cuenta = 0;
  $regRem = $qryRem->fetch();
  $eco = $regRem['txt_economico_rem'];
  $vel = $regRem['num_velocidad_rem'];
  $lat = $regRem['num_latitud_rem'];
  $lon = $regRem['num_longitud_rem'];

  $fecPos = new DateTime($regRem["fec_posicion_rem"], new DateTimeZone('UTC'));
  $fecPos = $fecPos->setTimezone(new DateTimeZone('America/Mexico_City'));
  $fecPos = date_format($fecPos, 'Y-m-d H:i:s');

  $ori = $regRem['txt_orientacion_rem'];
  $imgOri = $ori . '.jpg';
  $geoMun = $regRem['txt_georeferencia_mun'];
  $geoCas = $regRem['txt_georeferencia_cas'];
  $ign = $regRem['num_ignicion_rem'];
  $bat = $regRem['num_bateria_rem'];
  if ($ign == 1) {
    $ign = 'Encendido';
  } else {
    $ign = 'Apagado';
  }

  // Consulta Mantenimiento
  /*
  $conMan = "SELECT COUNT(*) AS mantenimiento 
              FROM tb_mantenimientos 
              WHERE economico = ? AND fecha_baja IS NULL";
  $queryMan = $conn->prepare($conMan);
  $queryMan->bindParam(1, $regRem['txt_economico_rem']);
  $queryMan->execute();
  $registroMan = $queryMan->fetch();
  $mantenimiento = $registroMan['mantenimiento'];
  $conAle  = " SELECT fk_clave_tipa, fec_fecha_ale, num_prioridad_ale,
                    num_tipo_ale,num_estatus_ale,txt_nombre_tipa,
                    num_prioridad_tipa, COUNT(*) as acumuladas
                  FROM tb_alertas, tb_tiposdealertas
                  WHERE fk_clave_tipa = pk_clave_tipa AND txt_economico_rem=?
                  GROUP BY  
                    fk_clave_tipa, fec_fecha_ale, num_prioridad_ale,
                    num_tipo_ale, num_estatus_ale, txt_nombre_tipa, 
                    num_prioridad_tipa
                  ORDER BY fec_fecha_ale DESC LIMIT 5 ";
  $qryAle = $conn->prepare($conAle);
  $qryAle->bindParam(1, $eco);
  $qryAle->execute();
  */
  if (true/*$_SESSION["nombrerol"] != "Externo"*/) {
?>
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-link active" id="nav-vehiculo-tab" data-toggle="tab" href="#nav-vehiculo" role="tab" aria-controls="nav-vehiculo" aria-selected="true">
          <strong>
            <?php echo $eco ?>
          </strong>
        </a>
        <a class="nav-link" id="nav-tablero-tab" data-toggle="tab" href="#nav-tablero" role="tab" aria-controls="nav-tablero" aria-selected="false">Tablero</a>
        <a class="nav-link" id="nav-unidades-tab" data-toggle="tab" href="#nav-unidades" role="tab" aria-controls="nav-unidades" aria-selected="false" hidden>Cercanas</a>
        <a class="nav-link" id="nav-mensajes-tab" data-toggle="tab" href="#nav-mensajes" role="tab" aria-controls="nav-mensajes" aria-selected="false" hidden>Mensajes</a>
        <a class="nav-link" id="nav-alertas-tab" data-toggle="tab" href="#nav-alertas" role="tab" aria-controls="nav-alertas" aria-selected="false" hidden>Alertas</a>
        <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false" hidden>Contact</a>
      </div>
    </nav>
    <div class="tab-content" id="nav-tabContent" style="width: 900px; max-width: 100%;">
      <div class="tab-pane fade show active" id="nav-vehiculo" role="tabpanel" aria-labelledby="nav-vehiculo-tab">
        <table class="table table-striped table-bordered table-hover">
          <tr>
            <td class="centrado" colspan="1"><strong>Fecha-Hora</strong></td>
            <td class="centrado" colspan="2"><strong>Posición</strong></td>
            <td class="centrado" colspan="1"><strong>Ignición</strong></td>
          </tr>
          <tr>
            <td colspan="1"><?php echo $fecPos; ?></td>
            <td colspan="2">
              <?php echo $geoMun . ',' .  $geoCas . ' (' . $lat . '' . $lon . ')' ?>
            </td>
            <td colspan="1">
              <?php echo $ign; ?>
            </td>
          </tr>
          <tr hidden>
            <td class="centrado" colspan="1"><strong>Zona de Riesgo</strong></td>
            <td class="centrado" colspan="1"><strong>Inmovilizador</strong></td>
            <td class="centrado" colspan="1"><strong>Entrada</strong></td>
            <td class="centrado" colspan="1"><strong>Salida</strong></td>
          </tr>
          <tr hidden>
            <td><?php if (isset($estatusIn) and $estatusIn == 1) echo "Si";
                else echo "No"; ?></td>
            <td>
              <?php if (isset($automatico) and $automatico == 1) echo "Si";
              else echo "No"; ?>
            </td>
            <td>
              <?php if (isset($fec_en)) echo $fec_en;
              else echo "No Aplica"; ?>
            </td>
            <td>
              <?php if (isset($fec_sa)) echo $fec_sa;
              elseif (isset($fec_en)) echo "En Zona de Riesgo";
              else echo "No Aplica"; ?>
            </td>
          </tr>
          <tr hidden>
            <td>
              <a href="http://localhost/?seccion=historico&id=<?php echo $regRem["txt_economico_rem"] ?>" target="_blank">
                <button type="button" class="btn btn-sm btn-primary">HISTORICO</button>
              </a>
            </td>
            <td colspan="2">
              <button type="button" class="btn btn-primary btn-sm" onclick="toggleStreetView(<?php echo $lat . ',' . $regRem['num_longitud_rem'] ?>);">DE CALLE</button>
            </td>
            <td>
              <a href="http://localhost/index.php?seccion=conductor&amp;economico=<?php echo $regRem["txt_economico_rem"] ?>" target="_blank">
                <button type="button" class="btn btn-primary btn-sm">CONDUCTOR</button>
              </a>
            </td>
          </tr>
        </table>
      </div>
      <div class="tab-pane fade" id="nav-tablero" role="tabpanel" aria-labelledby="nav-tablero-tab">
        <table class="table table-striped table-bordered">
          <tr>
            <td class="centrado"><strong>Velocidad</strong></td>
            <td class="centrado"><strong>Bateria</strong></td>
            <td class="centrado"><strong>Ultima Fecha de Registro </strong></td>
            <td class="centrado"><strong>Orientación (<?php echo $ori; ?>)</strong></td>
          </tr>
          <tr>
            <td class="centrado"><?php echo $vel ?> Kms./Hr.</td>
            <td class="centrado"><?php echo $bat ?>%</td>
            <td class="centrado"><?php echo $fecPos ?></td>
            <td class="centrado"><img src="assets/icons/<?php echo $imgOri ?>" height="64px" width="64px"></td>
          </tr>
        </table>
      </div>
      <div class="tab-pane fade" id="nav-unidades" role="tabpanel" aria-labelledby="nav-unidades-tab">
        <?php
        $latitud = $lat;
        $longitud = $lon;
        include("app_cercanas.php");
        ?>
      </div>
      <div class="tab-pane fade" id="nav-mensajes" role="tabpanel" aria-labelledby="nav-mensajes-tab">
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
            $consulta3  = " SELECT * 
                            FROM tb_mensajesenviadossms, tb_tiposdemensajessms 
                            WHERE 
                              pk_clave_tipm=fk_clave_tipm AND 
                              txt_economico_rem = ? 
                            ORDER BY fec_fecha_mene DESC 
                            LIMIT 5 ";
            $qryMsj = $conn->prepare($consulta3);
            $qryMsj->bindParam(1, $regRem["txt_economico_rem"]);
            $qryMsj->execute();
            while ($registro3 = $qryMsj->fetch()) {

              $fecha = new DateTime($registro3["fec_fecha_mene"], new DateTimeZone('UTC'));
              $fecha = $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
              $fecha = date_format($fecha, 'Y-m-d H:i:s');
            ?>
              <tr>
                <td><?php echo $registro3["txt_economico_rem"] ?></td>
                <td><?php echo $registro3["txt_nombre_tipm"] ?></td>
                <td><?php echo  date('d/m/Y H:i:s', strtotime($fecha)) ?></td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="tab-pane fade" id="nav-alertas" role="tabpanel" aria-labelledby="nav-alertas-tab">
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
            while ($regAle = $qryAle->fetch()) {
              $prioridad = "";
              $estatus = "";
              switch ($regAle["num_prioridad_tipa"]) {
                case 3:
                  $prioridad = "Alta";
                  $color = "rojo";
                  break;
                case 2:
                  $prioridad = "Media";
                  $color = "amarillo";
                  break;
                case 1:
                  $prioridad = "Baja";
                  $color = "verde";
                  break;
              }
              switch ($regAle["num_estatus_ale"]) {
                case 0:
                  $estatus = "Sin atender";
                  $colorestatus = "rojo";
                  break;
                case 1:
                  $estatus = "Ok";
                  $colorestatus = "verde";
                  break;
              }
            ?>
              <tr>
                <td><?php echo date('d/m/Y H:i:s', strtotime($regAle["fec_fecha_ale"])) ?></td>
                <td><?php echo $regAle["txt_nombre_tipa"] ?></td>
                <td>
                  <div class="centrado circulo <?php echo $color ?> blanco"><?php echo $prioridad ?></div>
                </td>
                <td>
                  <div class="centrado <?php echo $colorestatus ?> blanco"><?php echo $estatus ?></div>
                </td>
                <td><?php echo $regAle["acumuladas"] ?></td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
    </div>
  <?php
    $qryMsj->closeCursor();
    $qryAle->closeCursor();
  } // fin del if
  else {
  ?>
    <table class="table table-striped table-bordered table-hover">
      <tr>
        <td class="centrado">
          <strong>
            Unidad: <?php echo $eco ?>
          </strong>
        </td>
      </tr>
    </table>
<?php
  }
  $qryRem->closeCursor();
} else {
  echo 'NOT FOUND';
}
?>