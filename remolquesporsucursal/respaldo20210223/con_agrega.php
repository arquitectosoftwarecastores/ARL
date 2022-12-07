<?php
$redireccionar = '?seccion=vehiculos';
if ( true /*isset($_SESSION["altaybajadevehiculos"])*/) {
  $numero = $_POST["numero"];
  $serie = $_POST["serie"];
  $circuito = $_POST["circuito"];
  $cuenta = 0;

  $conRem  = "SELECT * FROM tb_remolques 
              WHERE txt_economico_rem = ? LIMIT 1";
  $query1 = $conn->prepare($conRem);
  $query1->bindParam(1, $numero);
  $query1->execute();
  while ($regRem = $query1->fetch())
    $cuenta++;

  // VERIFICA SI YA ESTA REGISTRADO EL VEHICULO
  if ($cuenta > 0) {
    // VERIFICA SI ESTA DADA DE BAJA
    if ($regRem['estatus'] == 0) {
      $consulta  = "UPDATE tb_remolques 
                    SET 
                      num_serie_veh = ?, fk_clave_cir = ?, 
                      estatus = 1, fecha_mod = NOW(), usuario_mod = ? 
                    WHERE txt_economico_rem = ?";
      $query = $conn->prepare($consulta);
      $query->bindParam(1, $serie);
      $query->bindParam(2, $circuito);
      $query->bindParam(3, $_SESSION["usuario"]);
      $query->bindParam(4, $numero);
      $query->execute();
    } else {
?>
      <div class="container">
        <div class="alert alert-warning">
          <a href="#" class="close" data-dismiss="alert">&times;</a>
          <strong>El número económico <?php echo $numero ?> ya ha sido registrado previamente.</strong>.
        </div>
      </div>

  <?php
      exit();
    }
  } else {
    // Añade Vehiculo
    $consulta  = "INSERT INTO tb_remolques
                    (txt_economico_rem, txt_nserie_rem, fk_clave_cir,
                    num_latitud_rem, num_longitud_rem, fec_posicion_rem,
                    fecha_mod, usuario_mod)
                  VALUES (?, ?, ?, 0, 0, NOW(), NOW() ,?)";
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $numero);
    $query->bindParam(2, $serie);
    $query->bindParam(3, $circuito);
    $query->bindParam(4, $_SESSION["usuario"]);
    $query->execute();
  }

  $query->closeCursor();

  // Alta Geocerca por unidad
  $insertBi = "INSERT INTO geocercasporunidad 
                  (economico)
                VALUES (?)";
  $queryGc = $conn->prepare($insertBi);
  $queryGc->bindParam(1, $numero);
  $queryGc->execute();
  $queryGc->closeCursor();


  // Bitacora Alta Usuario
  $accion = "Alta Vehiculo";
  $modulo = "8";
  $insertBi = "INSERT INTO bitacora_usuarios 
              (txt_usuario_usu, txt_modificado, id_modulo, fecha, accion)
              VALUES (?, ?, ?, NOW(), ?)";
  $querybi = $conn->prepare($insertBi);
  $querybi->bindParam(1, $_SESSION["usuario"]);
  $querybi->bindParam(2, $numero);
  $querybi->bindParam(3, $modulo);
  $querybi->bindParam(4, $accion);
  $querybi->execute();
  $querybi->closeCursor();

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