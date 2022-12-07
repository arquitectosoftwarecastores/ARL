<?php

$id = $_GET["id"];
$nombre = $_POST["nombre"];
$consulta  = "UPDATE tb_roles SET txt_nombre_rol=?  WHERE pk_clave_rol=? ";
$query = $conn->prepare($consulta);
$query->bindParam(1, $nombre);
$query->bindParam(2, $id);
$query->execute();


$consulta1  = " DELETE FROM tb_modulosxrol
                  WHERE fk_clave_rol=?";
$query1 = $conn->prepare($consulta1);
$query1->bindParam(1, $id);
$query1->execute();

$modulos = $_POST["modulos"];
for ($i = 0; $i < sizeof($modulos); $i++) {
   $consulta2  = " INSERT INTO tb_modulosxrol
                    (fk_clave_rol,fk_clave_mod)
                    VALUES(?,?)";
   $query2 = $conn->prepare($consulta2);
   $query2->bindParam(1, $id);
   $query2->bindParam(2, $modulos[$i]);
   $query2->execute();
}

$query1->closeCursor();

$consulta3  = " DELETE FROM tb_alertas_rol WHERE rol=?";
$query3 = $conn->prepare($consulta3);
$query3->bindParam(1, $id);
$query3->execute();

$alertas = $_POST["alertas"];
for ($i = 0; $i < sizeof($alertas); $i++) {
   $consulta4  = " INSERT INTO tb_alertas_rol
                     (rol,alerta)
                     VALUES(?,?)";
   $query4 = $conn->prepare($consulta4);
   $query4->bindParam(1, $id);
   $query4->bindParam(2, $alertas[$i]);
   $query4->execute();
}

$redireccionar = "?seccion=roles&accion=lista";
?>
<script>
   window.location.href = "<?php echo  $redireccionar; ?>";
</script>