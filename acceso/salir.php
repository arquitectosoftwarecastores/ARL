<?php
      //Cierra la sesión correctamente
      $consulta  = " UPDATE tb_usuarios SET num_activo_usu=1 
                      WHERE pk_clave_usu=? ";  

      $query = $conn->prepare($consulta);
      $query->bindParam(1,$_SESSION["id"]);
      $query->execute();
	  session_destroy();
?>
<script>
    window.location.href = "?seccion=acceso&accion=ingresa";
</script>