<?php

  $usuario=$_POST["usuario"];
  $contrasena=$_POST["password"];
  $nombre=$_POST["nombre"];  
  $correo=$_POST["correo"];
  if(isset($_POST["activo"]))
    $activo=1;
  else
    $activo=0;
  $rol=$_POST["rol"];
  $empresa=$_POST["empresa"];

  $consulta  = "INSERT INTO tb_usuarios
				(txt_usuario_usu,txt_contrasena_usu,txt_nombre_usu,txt_email_usu,num_activo_usu,fk_clave_rol,fk_clave_emp)
		        VALUES (?,?,?,?,?,?,?)";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $usuario);
  $query->bindParam(2, $contrasena);
  $query->bindParam(3, $nombre);
  $query->bindParam(4, $correo);
  $query->bindParam(5, $activo);
  $query->bindParam(6, $rol);
  $query->bindParam(7, $empresa);
  $query->execute();    
  $query->closeCursor();
  $redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>