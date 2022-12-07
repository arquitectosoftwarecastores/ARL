<?php
include('../conexion/conexion.php');
session_name("ARL");
session_start();

if (isset($_POST['economico'])) {
  $usuario = $_SESSION['usuario'];
  $economico = $_POST['economico'];

  $observacion = $_POST['observacion'];
  $fecha_mtto = date('Y-m-d', strtotime($_POST['fecha_mtto']));
  $fecha_sigmtto = date('Y-m-d', strtotime($_POST['fecha_sigmtto']));


  // Actualiza tabla de Mantenimientos
  $in_man = "UPDATE tb_mantenimientos 
              SET 
                usuarios = ?, observacion = ?, fecha_mtto = NOW(), 
                fecha_sigmtto = ?, fecha_registro = NOW()
              WHERE
                economico_rem = ?";
  $qryIn = $conn->prepare($in_man);
  $qryIn->bindParam(1, $usuario);
  $qryIn->bindParam(2, $observacion);
  $qryIn->bindParam(3, $fecha_sigmtto);
  $qryIn->bindParam(4, $economico);
  $qryIn->execute();

  $modRows = $qryIn->rowCount();

  if ($modRows == 0) {
    $in_man = "INSERT INTO tb_mantenimientos 
                VALUES (default, ?, ?, ?, NOW(), ?, NOW());";
    $qryIn = $conn->prepare($in_man);
    $qryIn->bindParam(1, $economico);
    $qryIn->bindParam(2, $usuario);
    $qryIn->bindParam(3, $observacion);
    $qryIn->bindParam(4, $fecha_sigmtto);
    $qryIn->execute();
  }


  // Inseta Mantenimiento Histoico
  $in_man = "INSERT INTO tb_mantenimientos_historico
              VALUES (default ,? ,? , ? ,NOW() ,NOW() + '120 day'::interval, NOW())";
  $qryIn = $conn->prepare($in_man);
  $qryIn->bindParam(1, $economico);
  $qryIn->bindParam(2, $usuario);
  $qryIn->bindParam(3, $observacion);
  $qryIn->execute();

  $qryIn->closeCursor();
}
?>
<script type="text/javascript">
  location.href = "../?seccion=mantenimiento&accion=lista";
</script>