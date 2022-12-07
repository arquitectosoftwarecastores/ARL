<?php
//Autor: Marco Sánchez   Fecha //07/Septiembre/2017
//Se agrega este archivo y funcion para solo contar las unidades con status 1 
  function cuentaRegistrosnuevo($tabla,$conn1) {

    $consulta1  =  "SELECT * FROM ".$tabla." veh join informacion_veh iv on veh.txt_economico_veh = iv.txt_numero_veh where iv.status = 1";  
    $query1 = $conn1->prepare($consulta1);
    $query1->execute();
    $cuenta=0;
    while($registro1 = $query1->fetch())
      $cuenta++;
    return $cuenta;
  }
?>