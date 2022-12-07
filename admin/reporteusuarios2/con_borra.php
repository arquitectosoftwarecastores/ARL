<?php
// exit();
 include("../funciones/checazona.php");
 $camion="";
 $user = $_SESSION["usuario"];  
 if(isset($_SESSION["altaybajadevehiculos"])) 
 {
 //Este método es para obtener el noeconomico de la unidad a borrar
    $id=$_GET["id"];
    $obtenid = "SELECT txt_economico_veh FROM tb_vehiculos WHERE pk_clave_veh= ?";
    $query = $conn -> prepare($obtenid);
    $query->bindParam(1, $id);
    $query->execute();
    $registroid = $query->fetch();
    if ($registroid == NULL){
        //echo "No se encontro informacion para la unidad " . $id;
        }else{
            $camion = $registroid["txt_economico_veh"];
            }
   $query->closeCursor();
            
//Este método es para eliminar el vehiculo
    $consulta  = " DELETE FROM ".$Tabla." WHERE ".$campoId."=?";  
    $query = $conn->prepare($consulta);
    $query->bindParam(1, $id);
    $id=$_GET["id"];
    //$camion=$_GET["economico"];
    //echo "El camion borrado es:".$camion;
    $query->execute();    
    $query->closeCursor();
    
//Este método es para eliminar informacion del vehiculo cambio el 9 de Abril del 2018
    $consulta2  = " DELETE FROM monitoreo.informacion_veh WHERE txt_numero_veh = ?";  
    $query2 = $conn->prepare($consulta2);
    $query2->bindParam(1, $camion);
    $query2->execute();    
    $query2->closeCursor();        
    
//Este método lo que hace es registrar el registro en usuariospermisos    
    $concatenandoconsulta = "DELETE FROM ".$Tabla." WHERE ".$campoId."=".$id." El carro eliminado fue: ".$camion;
    $nombrecorto = "baja vehiculo";
    $cambio = 'INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto) VALUES (?,?,?,?)' ;    
    $query = $conn->prepare($cambio);
    $query->bindParam(1, $user);
    $query->bindParam(2, $concatenandoconsulta);
    $query->bindParam(3, date('Y-m-d H:i:s'));
    $query->bindParam(4, $nombrecorto );
    $query->execute();  
    $query->closeCursor();  

//Este método lo que hace es cambiar el estatus de la informacion vehiculo    
    $cambioestatus = 'UPDATE informacion_veh SET status=0 WHERE txt_numero_veh=?';
    $query = $conn->prepare($cambioestatus);
    $query->bindParam(1, $camion);
    $query->execute();  
    $query->closeCursor();  
 
 $redireccionar="?seccion=".$seccion."&accion=lista";
  if(isset($_GET["rxp"]))
  	$redireccionar.="&rxp=".$_GET["rxp"];
  if(isset($_GET["orden"]))
  	$redireccionar.="&orden=".$_GET["orden"];
  if(isset($_GET["busca"]))
  	$redireccionar.="&busca=".$_GET["busca"];  
  if(isset($_GET["inicia"]))
    $redireccionar.="&inicia=".$_GET["inicia"];    
?>
<script>
   window.location.href = "<?php echo  $redireccionar; ?>";
</script>

<?php
  }
  else
  {
     ?>
    <div class="container">
       <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Su usuario no tiene acceso a este módulo</strong>.
        </div>
    </div>
    <?php
  }
?>