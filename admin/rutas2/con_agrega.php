<?php
 //session_start();
 $user = $_SESSION["usuario"];
 //Variables
 $nombre=$_POST["nombre"];  
 $origen=$_POST["origen"];
 $destino=$_POST["destino"];
//Comentado el 10 de Octubre para volver a api de puntos intermedios
 $unidadruta=$_POST["unidadruta"];
 $daterange =$_POST["daterange"];
 $fechainicial = substr($daterange,0,16);
 $fechafinal = substr($daterange,19,34); 
 echo "Si entra";
 echo " Como separo esta fecha ". $daterange;  
 echo " unidadruta: ".$unidadruta;
 echo " fecha inicial: ".$fechainicial;
 echo " fecha final: ".$fechafinal;
 echo "nombre" . $nombre;  
 echo "origen" . $origen;  
 echo "destino" . $destino;  

 $JsonPuntos=  json_decode($_POST["JsonPuntosIntermedios"]);
  
 //Agrega la nueva ruta en la tabla tb_rutas
 $consulta  = "INSERT INTO tb_rutas (txt_nombre_rut,fk_clave_zon1,fk_clave_zon2) VALUES (?,?,?)";
 $query = $conn->prepare($consulta);
 $query->bindParam(1, $nombre);
 $query->bindParam(2, $origen);
 $query->bindParam(3, $destino);
 $query->execute();  
 $ultimo =$conn->lastInsertId();  
 $query->closeCursor();
 $concatenandoconsulta = " ".$consulta." ".$nombre." ".$origen." ".$destino;
 echo "Usuario: ".$user;
 echo "lo que insertaria seria: ".$concatenandoconsulta; 
 $nombrecorto = "alta ruta";
 $cambio = 'INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto) VALUES (?,?,?,?)' ;    
 $query2 = $conn->prepare($cambio);
 $query2->bindParam(1, $user);
 $query2->bindParam(2, $concatenandoconsulta);
 $query2->bindParam(3, date('Y-m-d H:i:s'));
 $query2->bindParam(4, $nombrecorto );
 $query2->execute();  
 $query2->closeCursor();

 //Recuperamos todos los elementos de la consulta
 //Modificado el 10 de Octubre para permitir agregar puntos intermedios

 $consulta2 = "INSERT INTO monitoreo.puntos_rutas (ruta,longitud,latitud) 
SELECT ?, pos.num_longitud_pos, pos.num_latitud_pos FROM monitoreo.tb_posiciones pos 
JOIN monitoreo.tb_vehiculos veh ON pos.num_nserie_pos = veh.num_serie_veh::int 
WHERE pos.fec_ultimaposicion_pos > ?
AND pos.fec_ultimaposicion_pos < ?
AND veh.txt_economico_veh = ?";
 
echo "El ultimo registro insertado es ".$ultimo; 
echo "Numero economico ".$unidadruta;
$query2 = $conn->prepare($consulta2);
$query2->bindParam(1, $ultimo );
$query2->bindParam(2, $fechainicial);
$query2->bindParam(3, $fechafinal);
$query2->bindParam(4, $unidadruta);
$query2->execute();    
$query2->closeCursor();  
  
 /*
 // echo "La nueva ruta creada es ".$registro1['maximo']  
  foreach($JsonPuntos as $key => $value){
      echo "la zona es ".$value->id_zona."<br>";
      echo "la ruta es ".$ultimo."<br>";
      $consulta1  = "INSERT INTO puntos_intermedios(clave_rut,clave_zon) VALUES (?,?)";
      $query = $conn->prepare($consulta1);
      $query->bindParam(1, $ultimo);
      $query->bindParam(2, $value->id_zona);
      $query->execute();    
      $query->closeCursor();
  }
  */
 // $redireccionar="?seccion=rutas&accion=lista";

?>

<script>
 // window.location.href = "<//?php echo  $redireccionar; ?>";
</script>