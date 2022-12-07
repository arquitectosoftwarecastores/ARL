﻿<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<?php include('estilo.php') ?>
<?php
$host = "69.172.241.230";
$usuario = "monitoreo";
$contrasena = "monitoreo";
$basededatos = "db_monitoreo;";
try {
    // create a PostgreSQL database connection
    $conn = new PDO("pgsql:host=" . $host . ";port=5432;dbname=" . $basededatos . ";user=" . $usuario . ";password=" . $contrasena);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // report error message
    echo $e->getMessage();
}
?>  
<?php include('autofiltro.php'); 
 include('def_sesiones.php') ;
 include('for_controles.php');
 //include('def_orden.php');
 $fechainicial = $_POST['fechainicial'];
 $horainicial = $_POST['horainicial'];
 $fechafinal = $_POST['fechafinal'];
 $horafinal = $_POST['horafinal'];
 ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=eliminaseleccionados" id="form" method="post">
    <div class="row renglon">
        <div class="col-md-1">
            <input type="checkbox" name="todos" id="todos"/>
        </div>
            <?php // $variable = "usuario"  ?>             
        <div class="col-md-1 negritas centrado"> Matricula 
            <?php include ("for_orden.php");  ?>
        </div>  
        <div class="col-md-3 negritas centrado"> Usuario
            <?php include ("for_orden.php");  ?>
        </div> 
        <div class="col-md-2 negritas centrado"> Fecha             
        </div>
        <div class="col-md-1 negritas centrado"> Ip             
        </div>
    </div>  
<div class="row renglon">
    <div class="col-md-1 centrado">
        <?php
        $consulta2 = "select * from monitoreo.control_sesiones s join monitoreo.tb_usuarios u on s.txt_usuario_usu = u.txt_usuario_usu 
                     where s.fecha_inicio > '".$fechainicial." ".$horainicial."' and s.fecha_inicio < '".$fechafinal." ".$horafinal."'";
        $query = $conn->prepare($consulta2);
        $query->execute();
        while ($registro = $query->fetch()) {
            ?>
        </div>

        <div class="row renglon">
            <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[id] ?>"/></div>
            <div class="col-md-1"><?php echo $registro["txt_usuario_usu"]; ?></div>
            <div class="col-md-3"><?php echo $registro["txt_nombre_usu"]; ?></div>
            <div class="col-md-2 centrado"><?php echo $registro["fecha_inicio"] ?></div>
            <div class="col-md-1 centrado"><?php echo $registro["ip"] ?></div>
        </div>       
<?php } ?>
</div>      
</form>
<center>
    <?php
    $consulta3 = "select count(*) as total
                from monitoreo.control_sesiones s join monitoreo.tb_usuarios u on s.txt_usuario_usu = u.txt_usuario_usu 
                where s.fecha_inicio > '".$fechainicial." ".$horainicial."' and s.fecha_inicio < '".$fechafinal." ".$horafinal."'";
    $query3 = $conn->prepare($consulta3);
    $query3->execute();
    while ($registro3 = $query3->fetch()) {
        ?>        
        <b>Registros: 1-<?php echo $registro3["total"] ?> de <?php echo $registro3["total"] ?> Página: 1 de 1</b>
    </center>
    <?php
}
$query->closeCursor();
$query3->closeCursor();
?>


<script>

      $('.adjuntarimagen').click(function () {
       $('#identrada').val($(this).data('id'));

       });

</script>
