 <?php
  $conocida = 0;
  $observaciones = $_POST['observaciones'];
  $economico = $_POST["num_economico"];
  $idalerta = $_POST['idalerta'];
  if (isset($_POST['conocida'])) {
    $conocida = $_POST['conocida'];
  }

  $fechaAle = new DateTime($_POST["fechahora"], new DateTimeZone('America/Mexico_City'));
  $fechaAle = $fechaAle->setTimezone(new DateTimeZone('UTC'));
  $fechaAle = date_format($fechaAle, 'Y-m-d H:i:s');
  $dia = date("Y/m/d", strtotime($fechaAle));

  $consulta  = "UPDATE tb_alertas SET txt_comentarios_ale=?, num_estatus_ale=1 , fk_clave_usu=?, num_dat0_ale = ?, fec_verifica_ale = CURRENT_TIMESTAMP 
                WHERE fk_clave_tipa=? AND num_estatus_ale=0 AND txt_economico_rem=? AND date_trunc('day', fec_fecha_ale)=?";
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $observaciones);
  $query->bindParam(2, $_SESSION["id"]);
  $query->bindParam(3, $conocida);
  $query->bindParam(4, $idalerta);
  $query->bindParam(5, $economico);
  $query->bindParam(6, $dia);
  $query->execute();
  $query->closeCursor();

  $redireccionar = "?seccion=alertas&accion=lista";

  if (isset($_POST["rxp"]))
    $redireccionar .= "&rxp=" . $_POST["rxp"];
  if (isset($_POST["orden"]))
    $redireccionar .= "&orden=" . $_POST["orden"];
  if (isset($_POST["busca"]))
    $redireccionar .= "&busca=" . $_POST["busca"];
  if (isset($_POST["inicia"]))
    $redireccionar .= "&inicia=" . $_POST["inicia"];
  if (isset($_POST["economico"]))
    $redireccionar .= "&economico=" . $_POST["economico"];
  if (isset($_POST["from"]))
    $redireccionar .= "&from=" . $_POST["from"];
  if (isset($_POST["to"]))
    $redireccionar .= "&to=" . $_POST["to"];
  if (isset($_POST["prioridad"]))
    $redireccionar .= "&prioridad=" . $_POST["prioridad"];
  if (isset($_POST["estatus"]))
    $redireccionar .= "&estatus=" . $_POST["estatus"];

  ?>
 <script>
   window.location.href = "<?php echo  $redireccionar; ?>";
 </script>