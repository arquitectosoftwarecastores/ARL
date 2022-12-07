<?php
  $consulta  = " UPDATE tb_empresas SET 
   txt_nombre_emp=?,
   txt_direccion_emp=?,
   txt_colonia_emp=?,
   txt_cp_emp=?,
   fk_clave_mun=?,
   txt_telefono_emp=?
   WHERE pk_clave_emp=? ";  
  
  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $direccion);
  $query->bindParam(3, $colonia);
  $query->bindParam(4, $cp);
  $query->bindParam(5, $ciudad);
  $query->bindParam(6, $telefono);
  $query->bindParam(7, $id);

  $nombre=$_POST["nombre"];
  $direccion=$_POST["direccion"];
  $colonia=$_POST["colonia"];
  $cp=$_POST["cp"];
  $ciudad=$_POST["ciudad"];
  $telefono=$_POST["telefono"];
  $id=$_POST["id"];

  $query->execute();    
  $redireccionar="?seccion=".$seccion."&accion=lista";
?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>