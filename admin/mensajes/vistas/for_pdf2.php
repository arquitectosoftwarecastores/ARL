<!-- Con <page> se define una hoja con los márgenes que
   que se muestran -->
<style type="text/css">
<!--
#encabezado {padding:10px 0; border-top: 1px solid; border-bottom: 1px solid; width:100%;}
#encabezado .fila #col_1 {width: 100%; text-align:center;}
#encabezado .fila #col_2 {text-align:center; width: 100%}
 
#encabezado .fila td img {width:150px; text-align:center;}
#encabezado .fila #col_2 #span1{font-size: 15px;}
#encabezado .fila #col_2 #span2{font-size: 12px; color: #4d9;}
 
#footer {padding-top:5px 0; border-top: 1px solid; width:100%;}
#footer .fila td {text-align:center; width:100%;}
#footer .fila td span {font-size: 10px; color: #f5a;}

#table {background-color: #ffffff; text-align:center; width: 100%;}
#table .tabla #col_3 {background-color: #f5f5f5; border: 1px solid #ddd; text-align:center;}


#margen tr td {width:38%;  height:25;}
#datos_header {margin:auto; width:100%; border-top:3px solid #ddd; border-left:3px solid #ddd; border-right:3px solid #ddd; border-top-left-radius:20px; border-top-right-radius:20px;}
#datos_header tr td {border:1px solid #ddd; width:38%; text-align:center; height:25;}
#datos_header .fila #col_4 {border:1px solid #ddd; background-color: #D8D8D8;}
#datos {border-bottom:3px solid #D8D8D8; border-right:3px solid #D8D8D8; border-left:3px solid #D8D8D8; margin:auto; width:100%; border-bottom-left-radius:20px; border-bottom-right-radius:20px;}
#datos .fila #col_3 {border:1px solid #ddd; background-color: #f2f2f2;}
#datos .fila #col_4 {border:1px solid #ddd; background-color: #D8D8D8;}
#datos .fila #col_5 {border:1px solid #ddd;}
#datos tr td {border:1px solid #ddd; width:38%; text-align:center; height:25;}
</style>

<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");

    include("/var/www/html/conexion/conexion.php");
    include("/var/www/html/funciones/distancia.php");
    include("/var/www/html/posiciones/app_referencia.php");

    $counter = 1;
    $count_rows = 1;

    $strSQL  = "SELECT *
            FROM tb_mensajesenviadossms, tb_tiposdemensajessms, tb_usuarios
            WHERE fk_clave_tipm=pk_clave_tipm AND pk_clave_usu=fk_clave_usu  ";

    if (isset($_GET["busca"]))
        $strSQL .= " AND ( txt_economico_veh LIKE'%".$_GET["busca"]."%')";

    if (isset($_GET["economico"]))
        if ($_GET["economico"]!=0)
            $strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

    if (isset($_GET["from"]))
       if($_GET["from"]!=0)
            $strSQL.= " AND DATE(fec_fecha_mene) >= '".date("Y/m/d", strtotime($_GET["from"]))."' ";
    
    if (isset($_GET["to"]))
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
                $strSQL .= " ORDER BY ORDER BY fec_fecha_mene DESC ";
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
                $strSQL .= " ORDER BY fec_fecha_mene DESC";
              break;
        }
    }
    else
        $strSQL .= " ORDER BY fec_fecha_mene DESC";

    if (isset($_GET["rxp"]))
        $rxp=$_GET["rxp"];
    else
    // Number of rows to display by default if isn't selected another rxp like 5, 10, 20, 50, 100 or 500
        $rxp=500; 

    if (isset($_GET["inicia"]))
        $strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
    else
        $strSQL .= " LIMIT ".$rxp." OFFSET 0";

    $query = $conn->prepare($strSQL);
    $query->execute();
    $cuentaalertas=0;
?>
<page backtop="49mm" backbottom="10mm" backleft="10mm" backright="10mm" footer="page">
<page_header>
        <table id="encabezado">
            <tr class="fila">
                <td id="col_1" >
                    <img src="/var/www/html/imagenes/logo.jpg">
                    <br>
                </td>
            </tr>
            <tr class="fila">
                <td id="col_2">
                    <p id="p1"><strong><?php echo ucwords(strtolower($_SESSION["nombre"]));?>, Reporte de mensajes, Fecha: <?php echo date('d/m/Y H:i:s', time())?></strong></p>
                 </td>
            </tr>
        </table>
        <table id="margen">
            <tr>
                <td></td>
            </tr>
        </table>

    <table id="datos_header">
        <tr class="tabla fila">
            <td id="col_4" width="75" style="border-top-left-radius: 20px;"><strong>Fecha</strong></td>
            <td id="col_4" width="75"><strong>Económico</strong></td>
            <td id="col_4" width="75"><strong>Mensaje</strong></td>
            <td id="col_4" width="200"><strong>Usuario</strong></td>
            <td id="col_4" width="150"><strong>Comentario</strong></td>
            <td id="col_4" width="240" style="border-top-right-radius: 20px;"><strong>Ubicación</strong></td>
        </tr>
    </table>
</page_header>
    <page_footer>
        <table id="footer">
            <tr class="fila">
                <td>
                    <br>
                </td>
            </tr>
        </table>
    </page_footer>
 
    <table id="datos">
        <?php
        $count_rows++;
        $flag_fill_color = "No";
        while ($registro = $query->fetch()) { // The query fetch the rows to be display 
            ?>
        <tr class="tabla fila" >
            <?php
            switch ($flag_fill_color) {
                case "No":
            ?>

                    <td id="col_5" width="75"><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])); ?></td>
                    <td id="col_5" width="75"><?php echo $registro["txt_economico_veh"]; ?></td>
                    <td id="col_5" width="75"><?php echo $registro["txt_nombre_tipm"];?></td>
                    <td id="col_5" width="200"><?php echo $registro["txt_nombre_usu"] ?></td>
                    <td id="col_5" width="150"><?php echo $registro["txt_comentario_mene"] ?></td>
                    <td id="col_5" width="240"><?php echo georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn); ?></td>
                <?php
                  $flag_fill_color = "Si";
                  break;
                case "Si":
                ?>
                    <td id="col_3" width="75"><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_mene"])); ?></td>
                    <td id="col_3" width="75"><?php echo $registro["txt_economico_veh"]; ?></td>
                    <td id="col_3" width="75"><?php echo $registro["txt_nombre_tipm"];?></td>
                    <td id="col_3" width="200"><?php echo $registro["txt_nombre_usu"] ?></td>
                    <td id="col_3" width="150"><?php echo $registro["txt_comentario_mene"] ?></td>
                    <td id="col_3" width="240"><?php echo georeferencia($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).",".georeferencia_pi($registro["num_latitud_mene"],$registro["num_longitud_mene"],$conn).";"; ?></td>
                    
                <?php
                  $flag_fill_color = "No";
                  break;
              default:
                  $flag_fill_color = "No";
                  break;

            }

                ?>

        </tr>
        <?php 
        $counter++;
        $count_rows++;
        }
        ?>
    </table>
</page>
