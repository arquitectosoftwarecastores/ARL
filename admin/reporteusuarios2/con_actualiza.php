<?php
  $user = $_SESSION["usuario"];
  $id=$_GET["id"];
  echo "id: ".$id;
  //$numero=$_POST["numero"];
  //echo "numero: ".$numero;
  $serie=$_POST["serie"];
  echo "serie: ".$serie;
  $circuito=$_POST["circuito"]; 
  echo "circuito: ".$circuito;
  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //En la lineas de abajo recibimos el tipo de camion y su estatus para modificarlos 
  $estatuscamion=$_POST["estatuscamion"];
  echo "estatuscamion: ".$estatuscamion;
  $tipocamion=$_POST["tipocamion"];  
  echo "tipocamion".$tipocamion;
  if(isset($_POST["especial"]))
    $especial=1;
  else
    $especial=0;
  $consulta  = "UPDATE tb_vehiculos SET num_serie_veh=?, fk_clave_cir=?, num_seguimientoespecial_veh=? WHERE pk_clave_veh = ? ";
  $query = $conn->prepare($consulta);
//  $query->bindParam(1, $numero);
  $query->bindParam(1, $serie);
  $query->bindParam(2, $circuito);
  $query->bindParam(3, $especial);
  $query->bindParam(4, $id);
  $query->execute();   
  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //La consulta de abajo es para que inserte en la tabla informacion_veh los campos de tipo de camion por default el status = 1 
  $consulta2 = "UPDATE informacion_veh SET idtipounidad=?, status=? WHERE txt_numero_veh = ? ";
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $tipocamion);
  $query2->bindParam(2, $estatuscamion);
  $query2->bindParam(3, $numero);  
  $query2->execute();    
  $redireccionar="?seccion=".$seccion."&accion=lista";
  
  //Este método lo que hace es registrar el registro en usuariospermisos    
  $concatenandoconsulta = "Numero: ".$numero." Serie: ".$serie." circuito: "."$circuito"." especial: ".$especial." tipocamion ".$tipocamion." estatuscamion: ".$estatuscamion;
  $nombrecorto = "modificacion vehiculo";
  $cambio = 'INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto) VALUES (?,?,?,?)' ;    
  $query3 = $conn->prepare($cambio);
  $query3->bindParam(1, $user);
  $query3->bindParam(2, $concatenandoconsulta);
  $query3->bindParam(3, date('Y-m-d H:i:s'));
  $query3->bindParam(4, $nombrecorto );
  $query3->execute();  
  $query3->closeCursor();  
    
?>
<script>
   window.location.href = "<//?php echo  $redireccionar; ?>";
</script>