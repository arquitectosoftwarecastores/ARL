<?php

function consultaMaps ($modulo, $unidad, $conn){
    
    $inConsulta = "INSERT INTO tb_consultas (usuario, modulo, unidad, fecha, submodulo) VALUES (?, ?, ?, NOW(), 'MAP')";
    $qryIn = $conn->prepare($inConsulta);
    $qryIn->bindParam(1, $_SESSION['usuario']);
    $qryIn->bindParam(2, $modulo);
    $qryIn->bindParam(3, $unidad);
    $qryIn->execute();
    $qryIn->closeCursor();

}

?>