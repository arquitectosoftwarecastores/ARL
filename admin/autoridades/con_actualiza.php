<?php
  $id=$_POST["id"];
  $nombre=$_POST["nombre"];  
  $telefono1=$_POST["telefono1"];
  if(isset($_POST["telefono2"]))
    $telefono2=$_POST["telefono2"];
  else
    $telefono2="";

  $latitud=$_POST["latitud"];
  $longitud=$_POST["longitud"];
  $ciudad=$_POST["ciudad"];
  $tipo=$_POST["tipo"];

  $consulta  = "UPDATE tb_autoridades
				SET 
				txt_nombre_aut=?,
        txt_telefono1_aut=?,
        txt_telefono2_aut=?,
        num_latitud_aut=?,
        num_longitud_aut=?,
        fk_clave_mun=?,
        fk_clave_tipa=?,
        fec_ultima_aut=NOW()
				WHERE pk_clave_aut = ? ";

  $query = $conn->prepare($consulta);

  $query->bindParam(1, $nombre);
  $query->bindParam(2, $telefono1);
  $query->bindParam(3, $telefono2);
  $query->bindParam(4, $latitud);
  $query->bindParam(5, $longitud);
  $query->bindParam(6, $ciudad);
  $query->bindParam(7, $tipo);
  $query->bindParam(8, $id);

  $query->execute();    

  $redireccionar="?seccion=".$seccion."&accion=lista";

?>
<script>
    window.location.href = "<?php echo  $redireccionar; ?>";
</script>