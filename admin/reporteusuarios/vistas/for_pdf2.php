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

#datos {border:3px solid #D8D8D8; margin-left:20px; width:50%; border-radius:20px;}
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

    $strSQL = "SELECT * FROM tb_vehiculos, tb_circuitos WHERE fk_clave_cir=pk_clave_cir";
    if (isset($_GET["busca"]))
        $strSQL .= " AND ".$campoMostrar." LIKE'%".$_GET["busca"]."%' ";

    if (isset($_GET["economico"]))
        $strSQL .= " AND txt_economico_veh='".$_GET["economico"]."' ";

    if (isset($_GET["serie"]))
        $strSQL .= " AND num_serie_veh ='".$_GET["serie"]."' ";
    
    if (isset($_GET["circuito"]))
        if ($_GET["circuito"]!="-1")
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
        $strSQL .= " ORDER BY txt_economico_veh ASC ";

    if (isset($_GET["rxp"]))
        $rxp=$_GET["rxp"];
    else
    // Number of rows to display by default if isn't selected another rxp like 5, 10, 20, 50, 100, 1000 or 2000
        $rxp=500; 

    if (isset($_GET["inicia"]))
        $strSQL .= " LIMIT ".$rxp." OFFSET ".$_GET["inicia"];
    else
        $strSQL .= " LIMIT ".$rxp." OFFSET 0";

    $query = $conn->prepare($strSQL);
    $query->execute();
    $cuentaalertas=0;
?>
<page backtop="40mm" backbottom="10mm" backleft="10mm" backright="20mm" footer="page">
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
                    <p id="p1"><strong><?php echo ucwords(strtolower($_SESSION["nombre"]));?>, Lista de vehículos, Fecha: <?php echo date('d/m/Y H:i:s', time())?></strong></p>
                 </td>
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
        <tr class="tabla fila">
            <td id="col_4" style="border-top-left-radius: 20px;"><strong>Económico</strong></td>
            <td id="col_4"><strong>Serie</strong></td>
            <td id="col_4" width="150"><strong>Circuito</strong></td>
            <td id="col_4" width="70"><strong>Especial</strong></td>
            <td id="col_4" width="170" style="border-top-right-radius: 20px;"><strong>Latitud, Longitud</strong></td>
        </tr>
        <?php
        $count_rows++;
        $flag_fill_color = "No";
        while ($registro = $query->fetch()) { // The query fetch the rows to be display 
            if (fmod($counter, 23) == 0) { ?>
            <tr class="tabla fila">
                <td id="col_4" style="border-top-left-radius: 20px;"><strong>Económico</strong></td>
                <td id="col_4"><strong>Serie</strong></td>
                <td id="col_4" width="150"><strong>Circuito</strong></td>
                <td id="col_4" width="70"><strong>Especial</strong></td>
                <td id="col_4" width="170" style="border-top-right-radius: 20px;"><strong>Latitud, Longitud</strong></td>
            </tr>
                
            <?php 
            $counter++;
            $count_rows++;
            }
            ?>
        <tr class="tabla fila" >
            <?php
            switch ($flag_fill_color) {
                case "No":
                    if (fmod($count_rows, 23) == 0) {
            ?>
                    <td id="col_5" style="border-bottom-left-radius: 20px;" background-color="#F2F2F2"><?php echo $registro["txt_economico_veh"];?></td>
                    <td id="col_5"><?php echo $registro["num_serie_veh"];?></td>
                    <td id="col_5" width="150"><?php echo $registro["txt_nombre_cir"];?></td>
                    <td id="col_5" width="70"><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No";?></td>
                    <td id="col_5" width="170" style="border-bottom-right-radius: 20px;"><?php echo $registro["num_latitud_veh"].", ".$registro["num_longitud_veh"];?></td>
                <?php
                    }
                    else
                    {
                ?>
                    <td id="col_5"><?php echo $registro["txt_economico_veh"];?></td>
                    <td id="col_5"><?php echo $registro["num_serie_veh"];?></td>
                    <td id="col_5" width="150"><?php echo $registro["txt_nombre_cir"];?></td>
                    <td id="col_5" width="70"><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No";?></td>
                    <td id="col_5" width="170"><?php echo $registro["num_latitud_veh"].", ".$registro["num_longitud_veh"];?></td>

                <?php
                    }
                  $flag_fill_color = "Si";
                  break;
                case "Si":
                    if (fmod($count_rows, 23) == 0) {
                ?>
                    <td id="col_3" style="border-bottom-left-radius: 20px;" background-color="#F2F2F2"><?php echo $registro["txt_economico_veh"];?></td>
                    <td id="col_3"><?php echo $registro["num_serie_veh"];?></td>
                    <td id="col_3" width="150"><?php echo $registro["txt_nombre_cir"];?></td>
                    <td id="col_3" width="70"><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No";?></td>
                    <td id="col_3" width="170" style="border-bottom-right-radius: 20px;"><?php echo $registro["num_latitud_veh"].", ".$registro["num_longitud_veh"];?></td>
                <?php
                    }
                    else
                    {
                ?>
                    <td id="col_3"><?php echo $registro["txt_economico_veh"];?></td> 
                    <td id="col_3"><?php echo $registro["num_serie_veh"];?></td> 
                    <td id="col_3" width="150"><?php echo $registro["txt_nombre_cir"];?></td>
                    <td id="col_3" width="70"><?php if($registro["num_seguimientoespecial_veh"]) echo "Sí"; else echo "No";?></td>
                    <td id="col_3" width="170"><?php echo $registro["num_latitud_veh"].", ".$registro["num_longitud_veh"];?></td>
                    
                <?php
                    }
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
