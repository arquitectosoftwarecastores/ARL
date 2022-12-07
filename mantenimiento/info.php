<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style>
    #customers {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 14px;
        border-collapse: collapse;
        width: 99.99%;
    }

    #customers td, #customers th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    #customers tr:nth-child(even){background-color: #f2f2f2;}

    #customers tr:hover {background-color: #ddd;}

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        background-color: #4CAF50;
        color: white;
    }
</style>

<?php
include ('conexion.php');
//$idGlobal = $_POST["idG"];
$idGlobal = $_GET["economico"];
//echo "fechahora " . substr($fechahora,0,10);
$consultaalertas = " SELECT fecha_alta, (SELECT txt_nombre_usu AS usuario_alta FROM tb_usuarios WHERE txt_usuario_usu = usuario_alta) , descripcion_alta, fecha_baja, (SELECT txt_nombre_usu AS usuario_baja FROM tb_usuarios WHERE txt_usuario_usu = usuario_baja), descripcion_baja  FROM tb_mantenimientos WHERE economico = ? ORDER BY fecha_alta DESC";
$queryalertas = $conn->prepare($consultaalertas);
$queryalertas->bindParam(1, $idGlobal);
$queryalertas->execute();
$cont = 0;
?>
<table id="customers">
    <thead>
        <tr>
            <th>Fecha de Registro</th>
            <th>Usuario de Registro</th>
            <th>Comentario</th>
            <th>Fecha de Verificación</th>
            <th>Usuario Cierre</th>
            <th>Comentario</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($registroalertas = $queryalertas->fetch()) {
            $cont++;
            $fecha_a = $registroalertas["fecha_alta"];
            $fecha_a = substr($fecha_a,0,19);
            $usuario_a = $registroalertas["usuario_alta"];
            $descripcion_a = $registroalertas["descripcion_alta"];
            $fecha_b = $registroalertas["fecha_baja"];
            $fecha_b = substr($fecha_b,0,19);
            $usuario_b = $registroalertas["usuario_baja"];
            $descripcion_b = $registroalertas["descripcion_baja"];
            ?>

            <tr>
                <td><?php echo $fecha_a; ?></td>
                <td style=""><?php echo $usuario_a; ?></td>
                <td style="text-align: left;"><?php echo $descripcion_a; ?></td>
                <td><?php echo $fecha_b; ?></td>
                <td><?php echo $usuario_b; ?></td>
                <td style="text-align: left;"><?php echo $descripcion_b; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
