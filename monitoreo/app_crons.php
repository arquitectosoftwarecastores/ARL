<?php
session_name("ARL");
session_start();

$arrCron = array();

echo "Hola";
if (isset($_SESSION["id"])) {
  include_once('../conexion/conexion.php');
  $selCr = "SELECT tc.id , tc.nombre, tc.tiempo_ejecucion ,tec.ultimo_registro
            FROM tb_crones tc 
            INNER JOIN tb_estatus_crones tec 
              ON tc.id = tec.id_cron 
            WHERE tc.estatus = 1
            ORDER BY tc.id";
  $qryCr = $conn->query($selCr);
  $qryCr->execute();

  while ($row = $qryCr->fetch()) {
    $fechaRegistro = new DateTime($row["ultimo_registro"]);
    $fechaAhora = new DateTime();
    $tolerancia =  (int) ($row["tiempo_ejecucion"] + ($row["tiempo_ejecucion"] * 0.5));

    $fechaAhora = $fechaAhora->sub(new DateInterval("PT" . $tolerancia . "M"));

    $estatus = $fechaRegistro > $fechaAhora ? 1 : 0;
    $cron = array(
      "id" => $row["id"],
      "nombre" => $row["nombre"],
      "ultimo_registro" => $row["ultimo_registro"],
      "estatus" => $estatus,
    );
    array_push($arrCron, $cron);
  }
  $qryCr->closeCursor();
}
echo json_encode($arrCron);
