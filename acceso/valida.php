<?php

function getRealIP()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP']))
    return $_SERVER['HTTP_CLIENT_IP'];

  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    return $_SERVER['HTTP_X_FORWARDED_FOR'];

  return $_SERVER['REMOTE_ADDR'];
}

$redireccionar = "?seccion=acceso&accion=ingresa&mensaje=novalido";

$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
$consulta = "SELECT * 
              FROM tb_usuarios AS tu
              LEFT JOIN tb_roles AS tr
                ON tu.fk_clave_rol = tr.pk_clave_rol
              WHERE 
                txt_usuario_usu = ? AND 
                txt_contrasena_usu = ? AND
                num_activo_usu = 1 AND
                status = 1";
$query = $conn->prepare($consulta);
$query->bindParam(1, $usuario);
$query->bindParam(2, $contrasena);
$query->execute();
$cuenta = false;

while ($regUsr = $query->fetch()) {
  // Verifica si el usuario esta activo en la bd Personal
  $consulta = "SELECT COUNT(*) AS existe
              FROM personal.personal
              WHERE 
                idusuario = ? AND
                status = 1";
  $qryEx = $bd13->prepare($consulta);
  $qryEx->bindParam(1, $usuario);
  $qryEx->execute();
  $exist = $qryEx->fetch();


  if ($exist > 0) {
    // Crea la sesion del usuario
    $_SESSION["id"] = $regUsr["pk_clave_usu"];
    $_SESSION["usuario"] = $regUsr["txt_usuario_usu"];
    $_SESSION["nombre"] = $regUsr["txt_nombre_usu"];
    $_SESSION["rol"] = $regUsr["fk_clave_rol"];
    $_SESSION["nombrerol"] = $regUsr["txt_nombre_rol"];
    $_SESSION["maestro"] = $regUsr["maestro"];

    $conMod = " SELECT * 
                FROM  tb_modulos, tb_modulosxrol 
                WHERE 
                  fk_clave_mod=pk_clave_mod AND
                  fk_clave_rol=?";
    $query1 = $conn->prepare($conMod);
    $query1->bindParam(1, $_SESSION["rol"]);
    $query1->execute();
    $lista = "";
    while ($registro1 = $query1->fetch()) {
      switch ($registro1["txt_nombre_mod"]) {
        case 'monitoreo':
          $_SESSION["monitoreo"] = 1;
          break;
      }
    }

    /*  Almacenamos intento de Inicio de sesión */
    $consulta5 = "INSERT INTO monitoreo.control_sesiones(txt_usuario_usu,fecha_inicio,ip) VALUES (?,now(),?)";
    $query5 = $conn->prepare($consulta5);
    $query5->bindParam(1, $_SESSION["usuario"]);
    $query5->bindParam(2, $ipv);
    $query5->execute();
    $query5->closeCursor();
    /* */

    $redireccionar = "?seccion=bienvenido&accion=muestra";
  }

  $consulta1 = " SELECT * 
                  FROM  tb_modulosxrol AS mxr
                  INNER JOIN tb_modulos AS tm
                    ON mxr.fk_clave_mod = tm.pk_clave_mod
                  WHERE 
                    fk_clave_rol = ?";
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $_SESSION["rol"]);
  $query1->execute();

  while ($regMod = $query1->fetch()) {
    $modulo = strtolower($regMod['txt_nombre_mod']);
    $_SESSION[$modulo] = 1;
  }
}
?>
<script>
  window.location.href = "<?php echo $redireccionar; ?>";
</script>