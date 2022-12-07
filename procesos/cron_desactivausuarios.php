<?php
include ('../conexion/conexion.php');

$consultaUs ="SELECT us.txt_usuario_usu, COUNT(cs.fecha_inicio) nums FROM tb_usuarios AS us LEFT JOIN control_sesiones AS cs ON us.txt_usuario_usu = cs.txt_usuario_usu WHERE cs.fecha_inicio > (NOW()-INTERVAL '1 WEEK') GROUP BY us.txt_usuario_usu,us.txt_nombre_usu ORDER BY us.txt_usuario_usu ASC";
$query = $conn->prepare($consultaUs);
$query->execute();

$consultaLi = "SELECT txt_usuario_usu FROM tb_usuarios ORDER BY txt_usuario_usu ASC;";
$query1 = $conn->prepare($consultaLi);
$query1->execute();

while ($consultaLi = $query->fetch()) {

    //	$consultaAct = usuarios Activos
    //	$consultaLi = Lista de usuarios

    while($consultaAct = $query1->fetch()){

        //print $consultaLi['txt_usuario_usu'].'<br>';

        if ($consultaAct['txt_usuario_usu'] == $consultaLi['txt_usuario_usu']) {

          $consultaHOY = "SELECT us.txt_usuario_usu, COUNT(cs.fecha_inicio) AS NUM FROM tb_usuarios AS us LEFT JOIN control_sesiones AS cs ON us.txt_usuario_usu = cs.txt_usuario_usu WHERE cs.fecha_inicio > (NOW()-INTERVAL '1 DAY') AND us.txt_usuario_usu = ? GROUP BY us.txt_usuario_usu,us.txt_nombre_usu ORDER BY us.txt_usuario_usu ASC;";
          $query3 = $conn->prepare($consultaHOY);
          $query3->bindParam(1, $consultaAct['txt_usuario_usu']);
          $query3->execute();

          $conHoy = 1;
          while($conHoy = $query3->fetch()){
            $contHoy =  $conHoy['num'];
            //print $contHoy.'<br>';
          }

          if (isset($contHoy)) {
            //print $consultaAct['txt_usuario_usu'].' Mantiene Sesion<br>';

          }else {
            //print $consultaAct['txt_usuario_usu'].' Sesion Cerrada<br>';

          	$actualizarUsu = "UPDATE tb_usuarios SET num_activo_usu = 1 WHERE txt_usuario_usu = ?";
          	$query2 = $conn->prepare($actualizarUsu);
          	$query2->bindParam(1, $consultaAct['txt_usuario_usu']);
          	$query2->execute();
          }
          unset($contHoy);


        	break;
        }else{
        	print $consultaAct['txt_usuario_usu'].' Deshabilitado<br>';

        	$actualizarUsu = "UPDATE tb_usuarios SET num_activo_usu = 0 WHERE txt_usuario_usu = ?";
        	$query2 = $conn->prepare($actualizarUsu);
        	$query2->bindParam(1, $consultaAct['txt_usuario_usu']);
          $query2->execute();

        }
    }
}
$query->closeCursor();
$query1->closeCursor();
$query2->closeCursor();
$query3->closeCursor();
?>
