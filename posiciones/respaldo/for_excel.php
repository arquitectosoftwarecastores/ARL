<?PHP
// *********************************************************************************************************************
//  Genera XML con la informacion relacionada con el histórico de posiciones de acuerdo a los criterios seleccionados
// *********************************************************************************************************************
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  date_default_timezone_set("America/Mexico_City");


  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename=NombreArchivoExcel.csv");

    include ('../conexion/conexion.php'); 
    include ('../posiciones/app_referencia.php'); 
    include ('../funciones/distancia.php');
 

  $vehiculo=$_GET['vehiculo'];
  $from=$_GET['from']; 
  $to=$_GET['to'];


  $consulta1  = " SELECT * FROM tb_vehiculos
                 WHERE txt_economico_veh=?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $vehiculo);  
  $query1->execute();
  $serie=0;  
  while($registro1 = $query1->fetch())
     $serie=$registro1["num_serie_veh"];
  if($serie==0)
  {
    echo "<p>No se encontró el vehículo.</p>";
    exit();
  }

  $consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";  
  $query0 = $conn->prepare($consulta0);
  $query0->execute();
  $registro0 = $query0->fetch();
  $ajustegps=$registro0["num_valor_par"];
  $query0->closeCursor();

  $fechainicial=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($from)));
  $fechafinal=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($to)));

  $strSQL  = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='".$serie."' 
               AND fec_ultimaposicion_pos >= '".$fechainicial."'
               AND fec_ultimaposicion_pos <= '".$fechafinal."' 
               ORDER BY pk_clave_pos ASC";

?>
 
 
<?php  
   echo "Fecha-hora;Posición;Velocidad;Distancia;Ignición;Odómetro;Comb.Total\n";
  $query = $conn->prepare($strSQL);
  $query->execute(); 
  $contador=0;
  $distanciarecorrida=0;
  while ($registro = $query->fetch()) 
  {
    if($contador)
      $distancia=distancia($latitudanterior, $longitudanterior,$registro["num_latitud_pos"], $registro["num_longitud_pos"]);
    else
     {
      $distancia=0;
     }
    $latitudanterior=$registro["num_latitud_pos"];
    $longitudanterior=$registro["num_longitud_pos"]; 
    $contador++; 
    $distanciarecorrida+=$distancia;

    echo date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro['fec_ultimaposicion_pos']))).";";
    echo georeferencia($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn).",".georeferencia_pi($registro["num_latitud_pos"],$registro["num_longitud_pos"],$conn).";";
    echo round($registro["txt_velocidad_pos"]/0.62137,2)." Km/hr.;";
    echo round($distanciarecorrida,2).";";
    if ($registro["num_ignicion_pos"]) echo "Encendido;"; else echo "Apagado;";
    echo $registro['txt_odometro_pos'].";";
    echo $registro['txt_combtot_pos'].";";
    echo "\n";

  } 
?>