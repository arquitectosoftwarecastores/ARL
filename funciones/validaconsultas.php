<?php
include ("/var/www/html/conexion/conexion.php");
function validaconsulta($user, $consulta, $nombrecorto, $tabla, $registro) {
    $cambio = 'INSERT INTO usuariospermisos (txt_usuario_usu, comandoejecutado, fecha, nombrecorto,tabla, registro) VALUES (?,?,?,?,?,?)' ;    
    $query = $conn->prepare($cambio);
    $query->bindParam(1, $user);
    $query->bindParam(2, $consulta);
    $query->bindParam(3, date('Y-m-d H:i:s'));
    $query->bindParam(4, $nombrecorto );
    $query->bindParam(5, $tabla);
    $query->bindParam(6, $registro );
    $query->execute();  
    $query->closeCursor();    
}

function realizaconsulta($user, $modulo, $unidad, $submodulo) {
    $consulta33 ="insert into monitoreo.tb_consultas(usuario,fecha,modulo,unidad,submodulo) values (?,now(),?,?,?)"; 
    $query33 = $conn->prepare($consulta33);
    $query33->bindParam(1, $user);
    $query33->bindParam(2, $modulo);
    $query33->bindParam(3, $unidad);
    $query33->bindParam(4, $submodulo);
    $query33->execute();
    $query33->closeCursor();
}
?>