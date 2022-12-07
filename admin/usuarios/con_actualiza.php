<?php
  $id=$_GET["id"];
  $usuario=$_POST["usuario"];
  $nombre=$_POST["nombre"];
  $correo=$_POST["correo"];
  $contrasena = $_POST['contrasena'];

  if(isset($_POST["activo"]))
    $activo=1;
  else
    $activo=0;

  if(isset($_POST["acceso_externo"]))
    $acceso=1;
  else
    $acceso=0;

  if(isset($_POST["maestro"]))
    $maestro=1;
  else
    $maestro=0;



  $rol=$_POST["rol"];


  $consulta  = "UPDATE  tb_usuarios
				SET
          txt_usuario_usu=?,
          txt_nombre_usu=?,
          txt_email_usu=?,
          num_activo_usu=?,
          fk_clave_rol=?,
          usuarioalta = ?,
          maestro = ?,
          acceso_externo = ?,
          txt_contrasena_usu = ?
				WHERE pk_clave_usu = ? ";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $usuario);
  $query->bindParam(2, $nombre);
  $query->bindParam(3, $correo);
  $query->bindParam(4, $activo);
  $query->bindParam(5, $rol);
  $query->bindParam(6, $_SESSION['usuario']);
  $query->bindParam(7, $maestro);
  $query->bindParam(8, $acceso);
  $query->bindParam(9, $contrasena);
  $query->bindParam(10, $id);

  $query->execute();

  // Inserta en bitacora
  $accion = "Modifico Usuario";
  $modulo = "1";

  $insertBi ="INSERT INTO bitacora_usuarios (txt_usuario_usu,txt_modificado, id_modulo,fecha, accion)
  VALUES (?,?,?,NOW(),?)";

  $queryBi = $conn->prepare($insertBi);
  $queryBi->bindParam(1, $_SESSION['usuario']);
  $queryBi->bindParam(2, $usuario);
  $queryBi->bindParam(3, $modulo);
  $queryBi->bindParam(4, $accion);
  $queryBi->execute();
  $queryBi->closeCursor();


  // Actualiza Campos en tabla de usuarios
  $actUsuario = "UPDATE tb_usuarios SET fecha_mod= NOW(), usuarioalta = ? WHERE txt_usuario_usu = ?";
  $queryUs = $conn -> prepare($actUsuario);

  $queryUs->bindParam(1, $_SESSION["usuario"]);
  $queryUs->bindParam(2, $usuario) ;
  $queryUs->execute();
  $queryUs->closeCursor();

  $consulta1  = " DELETE FROM tb_circuitosxusuario
                  WHERE fk_clave_usu=?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $id);
  $query1->execute();

  $circuitos=$_POST["circuitos"];
  for($i=0; $i<sizeof($circuitos); $i++)
   {

    $consulta2  = " INSERT INTO tb_circuitosxusuario
                    (fk_clave_usu,fk_clave_cir)
                    VALUES(?,?)";
    $query2 = $conn->prepare($consulta2);
    $query2->bindParam(1, $id);
    $query2->bindParam(2, $circuitos[$i]);
    $query2->execute();

   }

  $redireccionar="?seccion=".$seccion."&accion=lista";
  if (isset($_GET["rxp"]))
    $redireccionar .= "&rxp=".$_GET["rxp"];
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>
