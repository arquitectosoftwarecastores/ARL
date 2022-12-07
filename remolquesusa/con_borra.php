<?php
// exit();
$economico = "";
$user = $_SESSION["usuario"];
if ( true /**isset($_SESSION["altaybajadevehiculos"])*/) {
  //Este método es para obtener el noeconomico de la unidad a borrar
  $id = $_GET["id"];
  $obtenid = "SELECT txt_economico_rem FROM tb_remolques 
              WHERE pk_clave_rem = ?";
  $qryEco = $conn->prepare($obtenid);
  $qryEco->bindParam(1, $id);
  $qryEco->execute();
  $registroid = $qryEco->fetch();
  if ($registroid == NULL) {
    //echo "No se encontro informacion para la unidad " . $id;
  } else {
    $economico = $registroid["txt_economico_rem"];
  }
  $qryEco->closeCursor();

  // Elimina el remolque
  $bajaVehiculo = "DELETE FROM tb_remolques 
                    WHERE pk_clave_rem = ? ";
  $qryRem = $conn->prepare($bajaVehiculo);
  $qryRem->bindParam(1, $id);
  $qryRem->execute();
  $qryRem->closeCursor();

  // Elimina Registro en la tabla geocercasporunidad
  $bajaGeo = "DELETE FROM geocercasporunidad 
              WHERE economico = ?";
  $qryGeo = $conn->prepare($bajaGeo);
  $qryGeo->bindParam(1, $economico);
  $qryGeo->execute();
  $qryGeo->closeCursor();

  $bajaGeo = "DELETE FROM lectura_tablero 
              WHERE txt_economico_veh = ?";
  $qryLT = $conn->prepare($bajaGeo);
  $qryLT->bindParam(1, $economico);
  $qryLT->execute();
  $qryLT->closeCursor();

  // Inserta en Bitacora la accion
  $accion = "Baja Vehiculo";
  $modulo = "8";
  $insertBi = "INSERT INTO bitacora_usuarios (txt_usuario_usu, txt_modificado, id_modulo, fecha, accion) VALUES (?, ?, ?, NOW(), ?)";
  $qrybi = $conn->prepare($insertBi);
  $qrybi->bindParam(1, $_SESSION["usuario"]);
  $qrybi->bindParam(2, $economico);
  $qrybi->bindParam(3, $modulo);
  $qrybi->bindParam(4, $accion);
  $qrybi->execute();
  $qrybi->closeCursor();


  $redireccionar = "?seccion=" . $seccion . "&accion=lista";
?>
  <script>
    window.location.href = "<?php echo  $redireccionar; ?>";
  </script>

<?php
} else {
?>
  <div class="container">
    <div class="alert alert-warning">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <strong>Su usuario no tiene acceso a este módulo</strong>.
    </div>
  </div>
<?php
}
?>