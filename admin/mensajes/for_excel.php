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

  $strSQL  = "SELECT *
        FROM tb_mensajesenviadossms, tb_tiposdemensajessms, tb_usuarios
        WHERE fk_clave_tipm=pk_clave_tipm AND pk_clave_usu=fk_clave_usu  ";

  if (isset($_GET["busca"]))  
    $strSQL .= " AND ( txt_economico_veh LIKE'%".$_GET["busca"]."%')";

  if (isset($_GET["economico"]))
    if ($_GET["economico"]!=0)
      $strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

    if(isset($_GET["from"]))
        if($_GET["from"]!=0)
            $strSQL.= " AND DATE(fec_fecha_mene) >= '".date("Y/m/d", strtotime($_GET["from"]))."' ";

    if(isset($_GET["to"]))
        if($_GET["to"]!=0)
            $strSQL.= " AND DATE(fec_fecha_mene) <= '".date("Y/m/d", strtotime($_GET["to"]))."' ";

  if (isset($_GET["usuario"]))
      $strSQL .= " AND txt_nombre_usu='".$_GET["usuario"]."' "; 

  if (isset($_GET["orden"])) {
      $orden = $_GET["orden"];
    switch ($orden) {     
      case "fecha_up":
          $strSQL .= " ORDER BY fec_fecha_mene ASC ";
        break;
      case "fecha_do":
          $strSQL .= " ORDER BY fec_fecha_mene DESC ";
        break;
      case "economico_up":
          $strSQL .= " ORDER BY txt_economico_veh ASC ";
        break;
      case "economico_do":
          $strSQL .= " ORDER BY txt_economico_veh DESC ";
        break;
      case "mensaje_up":
          $strSQL .= " ORDER BY txt_nombre_tipm ASC ";
        break;
      case "mensaje_do":
          $strSQL .= " ORDER BY txt_nombre_tipm DESC ";
        break;
      case "usuario_up":
          $strSQL .= " ORDER BY txt_nombre_usu ASC ";
        break;
      case "usuario_do":
          $strSQL .= " ORDER BY txt_nombre_usu DESC ";
        break;
      default:
          $strSQL .= "  ORDER BY fec_fecha_mene DESC";
        break;
    }
    
  }
  else
    $strSQL .= " ORDER BY fec_fecha_mene DESC";


  if (isset($_GET["rxp"]))
    $rxp=$_GET["rxp"];
  else
    $rxp=500;

  if (isset($_GET["inicia"]))
    $strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
  else
    $strSQL .= " LIMIT ".$rxp." OFFSET 0";

	echo "Fecha;Económico;Mensaje;Usuario;Comentario;Ubicación\n";
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
	  while ($registro = $query->fetch()) {          
    echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])).";";
  	echo "\"".$registro["txt_economico_veh"]."\"".";";
  	echo "\"".$registro["txt_nombre_tipm"]."\"".";";
    echo "\"".$registro["txt_nombre_usu"]."\"".";";
    echo "\"".$registro["txt_comentario_mene"]."\"".";";
  	echo "\"".georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn)."\"".";";
   	echo "\n";
    }
?>
