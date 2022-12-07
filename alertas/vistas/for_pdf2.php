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

    $count_rows = 1;

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

    if (isset($_GET["rxp"]))
        $rxp=$_GET["rxp"];
    else
    // Number of rows to display by default if isn't selected another rxp like 5, 10, 20, 50 ... 
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
                    <p id="p1"><strong><?php echo ucwords(strtolower($_SESSION["nombre"]));?>, Reporte de Alertas, Fecha: <?php echo date('d/m/Y H:i:s', time())?></strong></p>
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
            <td id="col_4" width="125" style="border-top-left-radius: 20px;"><strong>Fecha</strong></td>
            <td id="col_4" width="85"><strong>Económico</strong></td>
            <td id="col_4" width="175"><strong>Alerta</strong></td>
            <td id="col_4" width="85"><strong>Prioridad</strong></td>
            <td id="col_4" width="90"><strong>Estatus</strong></td>
            <td id="col_4" width="85"><strong>Acumuladas</strong></td>
            <td id="col_4" width="160" style="border-top-right-radius: 20px;"><strong>Tiempo</strong></td>
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
            ?>
        <tr class="tabla fila" >
            <?php
            switch ($flag_fill_color) {
                case "No":
            ?>

                    <td id="col_5" width="125"><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_ale"])); ?></td>
                    <td id="col_5" width="85"><?php echo $registro["txt_economico_veh"]; ?></td>
                    <td id="col_5" width="175"><?php echo $registro["txt_nombre_tipa"];?></td>
                    <td id="col_5" width="85"><?php echo $prioridad ?></td>
                        <?php
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
                        ?>
                    <td id="col_5" width="90"><?php echo $estatus.$nombre ?></td>
                    <td id="col_5" width="85"><?php echo $registro["acumuladas"] ?></td>
                    <td id="col_5" width="160">
                        <?php
                            $tiempo=str_replace("days","días",$registro["tiempo"]);
                            $tiempo=substr($tiempo,0,strlen($tiempo)-10);
                            $tiempo=str_replace(":"," hrs. ",$tiempo). " min.";
                            echo trim($tiempo); 
                            ?>
                    </td>
                <?php
                  $flag_fill_color = "Si";
                  break;
                case "Si":
                ?>
                    
                    <td id="col_3" width="125"><?php echo date('d/m/Y H:i:s',strtotime($registro["fec_fecha_ale"])); ?></td>
                    <td id="col_3" width="85"><?php echo $registro["txt_economico_veh"]; ?></td>
                    <td id="col_3" width="175"><?php echo $registro["txt_nombre_tipa"];?></td>
                    <td id="col_3" width="85"><?php echo $prioridad ?></td>
                        <?php
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
                        ?>
                    <td id="col_3" width="90"><?php echo $estatus.$nombre ?></td>
                    <td id="col_3" width="85"><?php echo $registro["acumuladas"] ?></td>
                    <td id="col_3" width="160">
                        <?php
                            $tiempo=str_replace("days","días",$registro["tiempo"]);
                            $tiempo=substr($tiempo,0,strlen($tiempo)-10);
                            $tiempo=str_replace(":"," hrs. ",$tiempo). " min.";
                            echo trim($tiempo); 
                            ?>
                    </td>
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
        $count_rows++;
        }
        ?>
    </table>
</page>
