<?php
header('Content-type: application/json');
include('../config/conexion.php');
session_name('ARL');
session_start();

$cuenta = 0;
$remolques = array();
$consulta  = 'SELECT *
              FROM tb_usuarios AS tu 
              INNER JOIN tb_circuitosxusuario AS tc 
                ON tu.pk_clave_usu = tc.fk_clave_usu 
              INNER JOIN tb_remolques AS tr 
                ON tc.fk_clave_cir = tr.fk_clave_cir 
              LEFT JOIN tb_circuitos AS c
                ON tr.fk_clave_cir = c.pk_clave_cir
              LEFT JOIN geocercasporunidad AS gc 
                ON tr.txt_economico_rem = gc.economico 
              WHERE 
                tu.pk_clave_usu = ? AND
                tr.estatus = 1
              ORDER BY tr.txt_economico_rem ASC';
$query = $conn->prepare($consulta);
$query->bindParam(1, $_SESSION['id']);
$query->execute();

while ($registro = $query->fetch()) {
  $valor = 0;
  if ($registro['sucursal'] == 0) {
    $valor = 'Fuera de Sucursal';
  } else {
    $consulta2 = "SELECT txt_nombre_zon as nombre 
                  from monitoreo.tb_zonas 
                  where pk_clave_zon = " . $registro['sucursal'];
    $query2 = $conn->prepare($consulta2);
    $query2->execute();
    $registro2 = $query2->fetch();
    if (isset($registro2['nombre'])) {
      $valor = $registro2['nombre'];
    } else {
      $valor =  '---';
    }
    // $query2.close();
  }

  $remolque = array(
    'id' => $registro['pk_clave_rem'],
    'economico' => $registro['txt_economico_rem'],
    'serie' => $registro['txt_nserie_rem'],
    'lat' => $registro['num_latitud_rem'],
    'long' => $registro['num_longitud_rem'],
    'circuito' => $registro['txt_nombre_cir'],
    'numcircuito' => $registro['pk_clave_cir'],
    'fec_posicion' => $registro['fec_posicion_rem'],
    'georeferencia' => $registro['txt_georeferencia_cas'],
    'sucursal' => $valor,
    'zonaderiesgo' => 0,
    'zonaderiesgoreal' => 0,
    'color' => $registro['num_icono_rem'],
    'especial' => 0,
    'inmovilizada' => 0,
    'frontera' => $registro['zonaroja'],
  );

  array_push($remolques, $remolque);
}

$vehiculos = array('vehiculos' => $remolques);
$query->closeCursor();

echo json_encode($vehiculos);
