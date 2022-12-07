
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
include ('../conexion/conexion.php');
include ('../funciones/distancia.php');
include ('../funciones/puntoseguro.php');
include ('../posiciones/app_referencia.php');
include ('../funciones/checazona.php');
//include("../funciones/almacenaconsulta.php");
date_default_timezone_set("America/Mexico_City");
//while(true){
echo "Iniciando...";
echo "<br>";


function calDis($dLatDegrees1, $dLonDegrees1,$dLatDegrees2, $dLonDegrees2){

  $EARTH_RADIUS_MI    = 3963.2263272;
  $PI                 = 3.14159265358979323846;
  $dDistMiles         = 0;
  $ddistkm            = 0;

  $dDistMiles = sin ($dLatDegrees1* ($PI/180)) * sin ($dLatDegrees2* ($PI/180)) +
  cos ($dLatDegrees1 * ($PI/180)) * cos ($dLatDegrees2*($PI/180)) *
  cos (($dLonDegrees1 - $dLonDegrees2)*($PI/180));
  $dDistMiles = $EARTH_RADIUS_MI * acos ($dDistMiles);
  $ddistm = $dDistMiles * 1609.344;

  return $ddistm;
}


// Consulta Posiciones de las Unidades que no esten en sucursal
$consultaUn = "SELECT * FROM tb_vehiculos AS tv LEFT JOIN geocercasporunidad AS gu ON tv.txt_economico_veh = gu.economico WHERE gu.sucursal <> 0 AND tv.txt_tperdida_veh = '' AND tv.status = 1 ORDER BY tv.txt_economico_veh DESC";
$queryUn = $conn->prepare($consultaUn);
$queryUn->execute();
$x =0;

while ($vehiculo = $queryUn->fetch()) {

  $economico_veh = $vehiculo['txt_economico_veh'];
  $latitud_veh = $vehiculo['num_latitud_veh'];
  $longitud_veh = $vehiculo['num_longitud_veh'];

  echo "<br/>".$economico_veh;

  // Consulta ultima posicion
  $consultaPos = "SELECT * FROM monitoreo.ultimo_movimiento WHERE txt_economico_veh LIKE ? AND status = 0";
  $queryPos = $conn->prepare($consultaPos);
  $queryPos->bindParam(1, $economico_veh);
  $queryPos->execute();
  $posicion = $queryPos -> fetch();

  if (isset($posicion['txt_economico_veh']) && $posicion['status'] == 0) {

    $latitud_ale = $posicion['num_latitud'];
    $longitud_ale = $posicion['num_longitud'];

    $latitud_veh = $vehiculo['num_latitud_veh'];
    $longitud_veh = $vehiculo['num_longitud_veh'];
    $dis = (float)calDis($latitud_veh, $longitud_veh, $latitud_ale, $longitud_ale);

    print " Dis ".$dis;
    // Compara distancia
    if ($dis >= 10.00) {
      // Si cambio la posicion, actualiza fecha y posicion
      //print "<br>******************************Actualiza datos";

      $actuPos = " UPDATE monitoreo.ultimo_movimiento SET num_latitud = ?, num_longitud = ?, fec_ultimomovimiento = ? WHERE txt_economico_veh = ? AND status = 0";
      $queryAct = $conn->prepare($actuPos);
      $queryAct->bindParam(1, $latitud_veh);
      $queryAct->bindParam(2, $longitud_veh);
      $queryAct->bindParam(3, $vehiculo['fec_posicion_veh']);
      $queryAct->bindParam(4, $economico_veh);
      $queryAct->execute();

      echo "  Actualiza <br>";

    }else{
      // Si no cambio de posicion, verifica fecha que no sea mayor a 24hrs desde la ultima actualizacion
      $intervalo = date_diff(date_create($posicion['fec_ultimomovimiento']), date_create(date_default_timezone_get()));
      // Tiempo en horas
      $intervalo = $intervalo->format("%h.%i");
      echo " Intervalo ".$intervalo."hrs ";

      if ($intervalo >= 12.00) {


        $fechaalerta = $vehiculo['fec_posicion_veh'];
        $ubicacion1 = $vehiculo['txt_posicion_veh'];
        $economico1 = $vehiculo['txt_economico_veh'];
        $ignicion1 = $vehiculo['num_ignicion_veh'];
        $ubicacion2 = $vehiculo['txt_upsmart_veh'];


        $consultaultale = "select count(*) as bandera from tb_alertas where txt_economico_veh = ? and fk_clave_tipa = 212 and fec_fecha_ale > now() - interval '60 minute' limit 1";
        $queryultale = $conn->prepare($consultaultale);
        $queryultale->bindParam(1, $economico_veh);
        $queryultale->execute();
        $registroultale = $queryultale->fetch();

        if ($registroultale["bandera"] == 0) {
          $consultaunicon = "select count(*) as bandera from monitoreo.unidades_sin_posicionar where txt_economico_veh = ? and fecha_registro > now() - interval '720 minute' limit 1";
          $queryunicon = $conn->prepare($consultaunicon);
          $queryunicon->bindParam(1, $economico_veh);
          $queryunicon->execute();
          $registrounicon = $queryunicon->fetch();

          if ($registrounicon["bandera"] == 0) {

            print '  <--------------------- Alerta '.$x++.'<br>';


            $consultanp = " INSERT INTO tb_alertas
            (fk_clave_tipa, fec_fecha_ale, txt_ubicacion_ale, txt_economico_veh, txt_ignicion_ale, num_prioridad_ale, num_latitud_ale, num_longitud_ale, txt_upsmart_ale, num_tipo_ale)
            VALUES (212,?,?,?,?,3,?,?,?,0)";
            $querynp = $conn->prepare($consultanp);
            $querynp->bindParam(1, $fechaalerta);
            $querynp->bindParam(2, $ubicacion1);
            $querynp->bindParam(3, $economico1);
            $querynp->bindParam(4, $ignicion1);
            $querynp->bindParam(5, $latitud_veh);
            $querynp->bindParam(6, $longitud_veh);
            $querynp->bindParam(7, $ubicacion2);
            $querynp->execute();
            $querynp->closeCursor();

            $actuPos = " UPDATE monitoreo.ultimo_movimiento SET status = 1 WHERE txt_economico_veh = ?";
            $queryAct = $conn->prepare($actuPos);
            $queryAct->bindParam(1, $economico_veh);
            $queryAct->execute();
            $queryAct->closeCursor();
          }

        }

      }

    }

  }elseif (!(isset($posicion['txt_economico_veh']))) {

    // Si no encuentra vehiculo, Inserta en la tabla los datos del Vehiculo
    $economico_veh = $vehiculo['txt_economico_veh'];
    $fec_ultimomovimiento = $vehiculo['fec_posicion_veh'];

    //print "<br/>No se encontro el vehiculo.";
    $fec_ultimomovimiento = $vehiculo['fec_posicion_veh'];
    $insertPos = " INSERT INTO monitoreo.ultimo_movimiento(txt_economico_veh, num_latitud,num_longitud, fec_ultimomovimiento, status) VALUES (?,?,?,?,0)";
    $queryIns = $conn->prepare($insertPos);
    $queryIns->bindParam(1, $vehiculo['txt_economico_veh']);
    $queryIns->bindParam(2, $latitud_veh);
    $queryIns->bindParam(3, $longitud_veh);
    $queryIns->bindParam(4, $vehiculo['fec_posicion_veh']);
    $queryIns->execute();
    print '<br>Se añadio a la tabla.';
    $queryIns->closeCursor();

  }
}
$queryUn->closeCursor();
$queryPos->closeCursor();


//}
?>
