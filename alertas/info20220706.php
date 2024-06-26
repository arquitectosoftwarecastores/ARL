<?php



?>


<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style>
    #customers {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #customers td,
    #customers th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #customers tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #customers tr:hover {
        background-color: #ddd;
    }

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<?php
include_once('../conexion/conexion.php');

$idGlobal = $_GET["economico"];
$idtipoalerta = $_GET["idtipoalerta"];
$fechahora = $_GET["fechahora"];
$fechahora = substr($fechahora, 0, 10);

$consultaalertas = "SELECT
                        a.fec_fecha_ale as fecha, a.txt_ubicacion_ale as ubicacion,
                        a.alerta_fecha_registro as registro, a.txt_comentarios_ale as comentarios,
                        a.num_latitud_ale as latitud, a.num_longitud_ale as longitud, 
                        a.fec_verifica_ale as verifica, u.txt_nombre_usu as usuario 
                    FROM monitoreo.tb_alertas a 
                    LEFT JOIN monitoreo.tb_usuarios u on a.fk_clave_usu = u.pk_clave_usu	
                    WHERE 
                        a.txt_economico_rem = ? AND fk_clave_tipa = ? AND
                        a.fec_fecha_ale > '$fechahora 00:00' and a.fec_fecha_ale < '$fechahora 23:59' 
                    ORDER BY fec_fecha_ale DESC";
$queryalertas = $conn->prepare($consultaalertas);
$queryalertas->bindParam(1, $idGlobal);
$queryalertas->bindParam(2, $idtipoalerta);

if (isset($_GET['economico']) & isset($_GET["fechahora"]) & isset($_GET["idtipoalerta"])) {
    $queryalertas->execute();
}

?>
<table id="customers">
    <thead>
        <tr>
            <th>Fecha de Registro</th>
            <th>Ubicación</th>
            <th>Fecha de Verificación</th>
            <th>Comentario</th>
            <th>Usuario Atendió</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($registroalertas = $queryalertas->fetch()) {

            $fec_fecha_ale = $registroalertas["fecha"];
            $txt_ubicacion_ale = $registroalertas["ubicacion"] . " " . $registroalertas["latitud"] . "," . $registroalertas["longitud"];
            $txt_comentarios_ale = $registroalertas["comentarios"];
            $txt_nombre_usu = $registroalertas["usuario"];
            $fec_verifica_ale = $registroalertas["verifica"];
        ?>

            <tr>
                <td><?php echo $fec_fecha_ale; ?></td>
                <td><?php echo $txt_ubicacion_ale; ?></td>
                <td><?php echo $fec_verifica_ale; ?></td>
                <td><?php echo $txt_comentarios_ale; ?></td>
                <td><?php echo $txt_nombre_usu; ?></td>
            </tr>
        <?php
        }
        $queryalertas->closeCursor();
        ?>
    </tbody>
</table>