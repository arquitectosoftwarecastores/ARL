<?php
include('../conexion/conexion.php');
session_start();
$usuario = $_SESSION['usuario'];
$descripcion = $_POST['descripcion'];
if (isset($_POST['economico'])) {
  $economico = $_POST['economico'];
}else {
  $economico = $_POST['num_economico'];
}
  // Inserta Mantenimiento
  $in_man = "INSERT INTO tb_mantenimientos VALUES (?,?,?,now(),now() + '90 day'::interval,now()";
  $queryIn = $conn->prepare($in_man);
  $queryIn->bindParam(1, $economico);
  $queryIn->bindParam(2, $usuario);
  $queryIn->bindParam(3, $descripcion);
  $queryIn->execute();
if ($_POST['num_economico']) {
  ?>
  <script type="text/javascript">
    location.href ="../?seccion=mantenimiento&accion=lista&estatus=0";
  </script>
  <?php
}
 ?>