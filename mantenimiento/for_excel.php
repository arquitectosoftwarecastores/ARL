<?php  
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  date_default_timezone_set("America/Mexico_City");

  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename=NombreArchivoExcel.csv");

  include ('../conexion/conexion.php');

  $strSQL  = "SELECT txt_nombre_tipa,txt_economico_veh,num_estatus_ale, 
        MIN(num_prioridad_tipa) AS num_prioridad_tipa,
        MIN(pk_clave_tipa) AS pk_clave_tipa,
        MIN(fec_fecha_ale) AS fec_fecha_ale,
        (CURRENT_TIMESTAMP - MIN(fec_fecha_ale)) as tiempo,
        MIN(pk_clave_ale) AS pk_clave_ale,
        MIN(txt_ubicacion_ale) AS txt_ubicacion_ale,
        MIN(txt_upsmart_ale) AS txt_upsmart_ale,
        MIN(txt_comentarios_ale) AS txt_comentarios_ale,
        MIN(fk_clave_usu) AS fk_clave_usu,
        COUNT(*) as acumuladas,
        date_trunc('day', fec_fecha_ale) as dia
        FROM tb_alertas, tb_tiposdealertas 
        WHERE fk_clave_tipa=pk_clave_tipa ";

  if (isset($_GET["busca"]))  
    $strSQL .= " AND ( txt_economico_veh LIKE'%".$_GET["busca"]."%')";

  if (isset($_GET["economico"]))
    if ($_GET["economico"]!=0)
      $strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

  if (isset($_GET["alerta"]))  
    if ($_GET["alerta"]!="")
      $strSQL .= " AND txt_nombre_tipa='".$_GET["alerta"]."' ";

  if (isset($_GET["prioridad"]))
    if ($_GET["prioridad"]!=0) 
      $strSQL .= " AND num_prioridad_tipa=".$_GET["prioridad"];

  if (isset($_GET["estatus"]))
    if ($_GET["estatus"]!=-1) 
      $strSQL .= " AND num_estatus_ale=".$_GET["estatus"];

    if(isset($_GET["from"]))
        if($_GET["from"]!=0)
            $strSQL.= " AND DATE(fec_fecha_ale) >= '".date("Y/m/d", strtotime($_GET["from"]))."' ";

    if(isset($_GET["to"]))
        if($_GET["to"]!=0)
            $strSQL.= " AND DATE(fec_fecha_ale) <= '".date("Y/m/d", strtotime($_GET["to"]))."' ";

    $strSQL  .= " GROUP BY dia, pk_clave_tipa,txt_economico_veh,num_estatus_ale ";  

  if (isset($_GET["orden"])) {
      $orden = $_GET["orden"];
    switch ($orden) {     
      case "fecha_up":
          $strSQL .= " ORDER BY fec_fecha_ale ASC ";
        break;
      case "fecha_do":
          $strSQL .= " ORDER BY fec_fecha_ale DESC ";
        break;
      case "economico_up":
          $strSQL .= " ORDER BY txt_economico_veh ASC ";
        break;
      case "economico_do":
          $strSQL .= " ORDER BY txt_economico_veh DESC ";
        break;
      case "alerta_up":
          $strSQL .= " ORDER BY txt_nombre_tipa ASC ";
        break;
      case "alerta_do":
          set_sesionesdesplegar("alerta_do");
          $strSQL .= " ORDER BY txt_nombre_tipa DESC ";
        break;
      case "prioridad_up":
          $strSQL .= " ORDER BY num_prioridad_tipa ASC ";
        break;
      case "prioridad_do":
          $strSQL .= " ORDER BY num_prioridad_tipa DESC ";
        break;
      case "estatus_up":
          $strSQL .= " ORDER BY num_estatus_ale ASC ";
        break;
      case "estatus_do":
          $strSQL .= " ORDER BY num_estatus_ale DESC ";
        break;
      case "acumuladas_up":
          $strSQL .= " ORDER BY acumuladas ASC ";
        break;
      case "acumuladas_do":
          $strSQL .= " ORDER BY acumuladas DESC ";
        break;
      case "tiempo_up":
          $strSQL .= " ORDER BY tiempo ASC ";
        break;
      case "tiempo_do":
          set_sesionesdesplegar("tiempo_do");
          $strSQL .= " ORDER BY tiempo DESC ";
        break;
      default:
          set_sesionesdesplegar("nombre_up");
          $strSQL .= "  ORDER BY fec_fecha_ale DESC, num_prioridad_tipa DESC, num_estatus_ale ASC";
        break;
    }
    
  }
  else
    $strSQL .= " ORDER BY fec_fecha_ale DESC, num_prioridad_tipa DESC,num_estatus_ale ASC";

//  if (isset($_GET["rxp"]))
//    $rxp=$_GET["rxp"];
//  else
//    $rxp=500;

//  if (isset($_GET["inicia"]))
//    $strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
//  else
//    $strSQL .= " LIMIT ".$rxp." OFFSET 0";

	echo "Fecha;Economico;Alerta;Prioridad;Estatus;Acumuladas;Tiempo\n";
    $query = $conn->prepare($strSQL);
    $query->execute(); 
    $cuentaalertas=0;
	  while ($registro = $query->fetch()) {
      switch ($registro["num_prioridad_tipa"]) {
        case 3:
          $prioridad="Alta";
          $color="fondorojo";
          break;
        case 2:
          $prioridad="Media";
          $color="fondoamarillo";
          break;
        case 1:
          $prioridad="Baja";
          $color="fondoverde";
          break;          
    }
            
    switch ($registro["num_estatus_ale"]) {
        case 0:
          $estatus="Sin atender";
          $colorestatus="rojo";
          break;
        case 1:
          $estatus="Atendida";
          $colorestatus="verde";
    }

    $nombre="";
    if($estatus=="Atendida") 
    {
      $consulta1  = " SELECT * FROM tb_usuarios
                      WHERE pk_clave_usu=?";  
      $query1 = $conn->prepare($consulta1);
      $query1->bindParam(1, $registro["fk_clave_usu"]);
      $query1->execute();
      while($registro1 = $query1->fetch())          
        { 
          $nombre=$registro1["txt_nombre_usu"]; 
          $estatus="";
        }
    }
    
  echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_ale"])).";";
	echo "\"".$registro["txt_economico_veh"]."\"".";";
	echo "\"".$registro["txt_nombre_tipa"]."\"".";";
	echo "\"".$prioridad."\"".";";
	echo "\"".$estatus.$nombre."\"".";";
	echo "\"".$registro["acumuladas"]."\"".";";
  $tiempo=str_replace("day","dia",$registro["tiempo"]);
	$tiempo=substr($tiempo,0,strlen($tiempo)-10);
	$tiempo=str_replace(":"," hrs. ",$tiempo). " min.;";
	echo "\"".$tiempo."\"".";"; 
 	echo "\n";
    }
?>
