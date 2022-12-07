<?php
  function cuentaRegistros($tabla,$conn1) {

    $consulta1  =  "SELECT * FROM ".$tabla;  
    $query1 = $conn1->prepare($consulta1);
    $query1->execute();
    $cuenta=0;
    while($registro1 = $query1->fetch())
      $cuenta++;
    return $cuenta;
  }
?>