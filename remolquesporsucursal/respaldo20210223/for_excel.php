<?php  
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  date_default_timezone_set("America/Mexico_City");

  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename=NombreArchivoExcel.csv");

  include ('../../conexion/conexion.php');
  include("../../funciones/distancia.php");
  include("../../posiciones/app_referencia.php");

  $strSQL  = " SELECT * FROM tb_vehiculos, tb_circuitos  WHERE fk_clave_cir=pk_clave_cir "; 
  if (isset($_GET["busca"]))  
    $strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

  if (isset($_GET["economico"]))  
    $strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

  if (isset($_GET["serie"]))  
    $strSQL .= " AND num_serie_veh ='".$_GET["serie"]."' ";
 
  if (isset($_GET["circuito"]))  
    //if ($_GET["circuito"]!="-1") 
      $strSQL .= " AND txt_nombre_cir ='".$_GET["circuito"]."'";

  if (isset($_GET["orden"])) {
      $orden = $_GET["orden"];
    switch ($orden) {     
      case "economico_up":
          $strSQL .= " ORDER BY txt_economico_veh ASC ";
        break;
      case "economico_do":
          $strSQL .= " ORDER BY txt_economico_veh DESC ";
        break;
      case "serie_up":
          $strSQL .= " ORDER BY num_serie_veh ASC ";
        break;
      case "serie_do":
          $strSQL .= " ORDER BY num_serie_veh DESC ";
        break;
      case "circuito_up":
          $strSQL .= " ORDER BY fk_clave_cir ASC ";
        break;
      case "circuito_do":
          $strSQL .= " ORDER BY fk_clave_cir DESC ";
        break;
      default:
          $strSQL .= " ORDER BY txt_economico_veh ASC ";
        break;
    }
    
  }
  else
    $strSQL .= " ORDER BY txt_economico_veh  ASC ";


  if (isset($_GET["rxp"]))
    $rxp=$_GET["rxp"];
  else
    $rxp=500;

  if (isset($_GET["inicia"]))
    $strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
  else
    $strSQL .= " LIMIT ".$rxp." OFFSET 0";

	echo "Económico;Serie;Circuito;Especial;Latitud-Longitud;\n";
     echo $strSQL;
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
	  while ($registro = $query->fetch()) {          
  	echo "\"".$registro["txt_economico_veh"]."\"".";";
  	echo "\"".$registro["num_serie_veh"]."\"".";";
    echo "\"".$registro["txt_nombre_cir"]."\"".";";
    if($registro["num_seguimientoespecial_veh"]) echo "\""."Sí"."\"".";"; else echo "\""."No"."\"".";";
  	echo $registro["num_latitud_veh"].",".$registro["num_longitud_veh"]."\n";
    }
?>
