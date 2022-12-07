<?php
 $user = $_SESSION["usuario"];
  if(isset($_SESSION["altaybajadevehiculos"])) 
  {
  $numero=$_POST["numero"];
  $consulta1  = " SELECT * FROM tb_vehiculos WHERE txt_economico_veh = ?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $numero);
  $query1->execute();    
  $cuenta=0;
  while($registro1 = $query1->fetch())  
    $cuenta++;
  if($cuenta)
  {
?>
    <div class="container">
       <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>El número económico <?php echo $numero ?>ya ha sido registrado previamente.</strong>.
        </div>
    </div>
<?php 
   exit();    
  }
  
  $serie=$_POST["serie"];
  $circuito=$_POST["circuito"];
  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //En la linea de abajo recibimos el tipo de camion 
  $tipocamion=$_POST["tipocamion"];
  $especial=0;
  if(isset($_POST["especial"]))
    if($_POST["especial"]==1)
       $especial=1;
  
    function agregarunidad($num, $ser, $cir, $esp, $con, $tc){
//       echo "Si entra2!!!! ";    
  $consulta  = "INSERT INTO tb_vehiculos
				(txt_economico_veh,num_serie_veh,fk_clave_cir,num_seguimientoespecial_veh,num_latitud_veh,num_longitud_veh,num_zonariesgo_veh,txt_tperdida_veh,fec_posicion_veh)
		        VALUES (?,?,?,?,0,0,0,'',NOW())";
  $query = $con->prepare($consulta);
  $query->bindParam(1, $num);
  $query->bindParam(2, $ser);
  $query->bindParam(3, $cir);
  $query->bindParam(4, $esp);
  $query->execute();    
  
  //Autor: Marco Sánchez   Fecha //07/Septiembre/2017
  //La consulta de abajo es para que inserte en la tabla informacion_veh los campos de tipo de camion por default el status = 1 
  $consulta2  = "INSERT INTO informacion_veh (txt_numero_veh,idtipounidad,status) VALUES (?,?,1)";
  $query = $con->prepare($consulta2);
  $query->bindParam(1, $num);
  $query->bindParam(2, $tc);
  $query->execute(); 
 // $query2->closeCursor(); 
 
 $user = $_SESSION["usuario"];
 $concatenandoconsulta = " ".$consulta." ".$num." ".$ser." ".$cir; 
 $nombrecorto = "alta vehiculos";
 $cambio = 'INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto) VALUES (?,?,?,?)' ;    
 $query = $con->prepare($cambio);
 $query->bindParam(1, $user);
 $query->bindParam(2, $concatenandoconsulta);
 $query->bindParam(3, date('Y-m-d H:i:s'));
 $query->bindParam(4, $nombrecorto );
 $query->execute();  
 $query->closeCursor();
  
}
     // Hacemos la conexion para corroborar o advertir que esa unidad no esta dada de alta en la base de datos de castores
     // Marco Sánchez       
        $servidor="192.168.0.13";
        $usuar="usuarioWin";
        $passw = "windows";
	$bd = "camiones";
	try{
	 // Conexion y consulta en mysql
         $var=0;   
	 $con = new PDO("mysql:host=".$servidor.";dbname=".$bd."",$usuar,$passw);
    	 $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
         $consult = 'SELECT COUNT(*) FROM camiones where noeconomico = '. $numero;
         $datos = $con->query($consult);
         foreach($datos as $row){
            echo $row[0] . '<br/>';
            $var =  $row[0];
            }
      // echo "El numero de unidades que cumplen con el criterio de busqueda son: ".$var;
      // si no encontro ningun numero coincidente en la base de datos
            if($var==0){
                ?>
                <script type="text/javascript">
       //         if(confirm("la unidad no existe en la base de datos de castores, deseas agregarla?")==true){
         //           var jsVar1 = "1";
           //     }else{
             //       var jsVar1 = "0";                 
               // }
                    alert("la unidad no existe en la base de datos de castores");
                </script>                  
                <?php                
                // $variablePHP = "<script> document.write(jsVar1) </script>";                
                // echo "<script> alert(\"EYYYY \"".$variablePHP."\"); </script>";
                // echo "el valor de la variable actual es de: ".$variablePHP;
//                if( $variablePHP == "1"){
//                    echo "Si entra!!!! ";    
//                    agregarunidad($numero, $serie, $circuito, $especial, $conn, $tipocamion);                                                        
  //              }else{
   //                 echo "No entra!!!! ";                
     //          }
            }else{
                 agregarunidad($numero, $serie, $circuito, $especial, $conn, $tipocamion );                               
            }
        } catch(PDOException $e) {
	 // report error message
	 echo $e->getMessage();
	}
$redireccionar="?seccion=".$seccion."&accion=lista"; 
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