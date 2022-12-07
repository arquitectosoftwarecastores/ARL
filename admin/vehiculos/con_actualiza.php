<?php
$id = $_GET["id"];
$numero = $_POST["numero"];
$serie = $_POST["serie"];
$circuito = $_POST["circuito"];
$tipo = $_POST["tipo"];

if (isset($_POST["especial"])) {
  $especial = 1;
} else {
  $especial = 0;
}

# Obtiene Ultimo número economico
$consulta2 = "SELECT * FROM tb_remolques 
              WHERE pk_clave_rem = ? ";
$query2 = $conn->prepare($consulta2);
$query2->bindParam(1, $id);
$query2->execute();

$conUnidad = $query2->fetch();
$oldEco = $conUnidad['txt_economico_rem'];

$consulta_nestle  = "UPDATE  tb_remolques
                      SET
                        txt_nserie_rem = ?,
                        fk_clave_cir = ?,
                        num_tipo_rem = ?
                      WHERE pk_clave_rem = ? ";
$query = $conn->prepare($consulta_nestle);
$query->bindParam(1, $serie);
$query->bindParam(2, $circuito);
$query->bindParam(3, $tipo);
$query->bindParam(4, $id);
$query->execute();
$query->closeCursor();


# Actualiza lectura_tablero
$consulta2 = "UPDATE lectura_tablero 
                  SET txt_economico_veh = ? 
                  WHERE txt_economico_veh = ? ";
$query2 = $conn->prepare($consulta2);
$query2->bindParam(1, $numero);
$query2->bindParam(2, $oldEco);
$query2->execute();


# Actualiza geocercasporunidad
$consulta2 = "UPDATE geocercasporunidad 
                  SET economico = ? 
                  WHERE economico = ? ";
$query2 = $conn->prepare($consulta2);
$query2->bindParam(1, $numero);
$query2->bindParam(2, $oldEco);
$query2->execute();
$query2->closeCursor();

// Bitacora Vehiculos
$accion = 'Modifico Vehiculo';
$modulo = '8';
$insertBi  = "INSERT INTO bitacora_usuarios (txt_usuario_usu, txt_modificado, id_modulo, fecha, accion) VALUES (?, ?, ?, NOW(), ?)";
$queryBi = $conn->prepare($insertBi);
$queryBi->bindParam(1, $_SESSION['usuario']);
$queryBi->bindParam(2, $numero);
$queryBi->bindParam(3, $modulo);
$queryBi->bindParam(4, $accion);
$queryBi->execute();
$queryBi->closeCursor();


$redireccionar = "?seccion=" . $seccion . "&accion=lista";

?>
<script>
  window.location.href = "<?php echo  $redireccionar; ?>";
</script>