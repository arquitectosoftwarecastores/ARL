<?php
header('Content-type: application/json');
include('../config/conexion.php');

session_start();
$consulta  = 'SELECT * 
                FROM tb_remolques AS r 
                LEFT JOIN tb_circuitos AS c
                  ON r.fk_clave_cir = c.pk_clave_cir
                WHERE r.estatus = 1
                ORDER BY pk_clave_rem';

$query = $conn->prepare($consulta);
//$query->bindParam(1,$_GET['idusuario']);
$query->execute();
$cuenta = 0;

$remolques = array();



while ($registro = $query->fetch()) {

  $remolque = array(
    'id' => $registro['pk_clave_rem'],
    'economico' => $registro['txt_economico_rem'],
    'serie' => $registro['txt_nserie_rem'],
    'lat' => $registro['num_latitud_rem'],
    'long' => $registro['num_longitud_rem'],
    'circuito' => $registro['txt_nombre_cir'],
    'numcircuito' => $registro['pk_clave_cir'],
    'zonaderiesgo' => 0,
    'zonaderiesgoreal' => 0,
    'color' => $registro['num_icono_rem'],
    'especial' => 0,
    'inmovilizada' => 0
  );

  array_push($remolques, $remolque);
}

$vehiculos = array('vehiculos' => $remolques);
$query->closeCursor();

echo json_encode($vehiculos);

