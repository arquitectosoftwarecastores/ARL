<?php
 if(isset($_GET["id"]))
  {
  $id=$_GET["id"];
  include ('../conexion/conexion.php');
  $consulta  = " SELECT * FROM tb_autoridades, tb_municipios, tb_estados WHERE fk_clave_mun=pk_clave_mun AND fk_clave_edo=pk_clave_edo AND pk_clave_aut=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);  
  $query->execute();
  $registro = $query->fetch();
?>

    <table class="table table-striped table-bordered table-hover">
      <tr><td class="derecha"><strong>Nombre: </strong></td><td><?php echo $registro['txt_nombre_aut']?></td></tr>
      <tr><td class="derecha"><strong>Teléfono 1: </strong></td><td><?php echo $registro['txt_telefono1_aut']?></td></tr>
      <tr><td class="derecha"><strong>Teléfono 2: </strong></td><td><?php echo $registro['txt_telefono2_aut']?></td></tr>
      <tr><td class="derecha"><strong>Ciudad: </strong></td><td><?php echo $registro['txt_nombre_mun']?></td></tr>
      <tr><td class="derecha"><strong>Estado: </strong></td><td><?php echo $registro['txt_nombre_edo']?></td></tr>
      <tr>
        <td colspan="2" class="centrado">
              <button type="button"  class="btn btn-primary" onclick="toggleStreetView(<?php  echo $registro['num_latitud_aut']?>,<?php echo $registro['num_longitud_aut']?>);">VISTA DE CALLE</button>
        </td>
      </tr>
    </table>

<?php 
          $query->closeCursor();
	} // fin del if
?>