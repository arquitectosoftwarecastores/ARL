<?php
header('Content-type: application/json');
include('../config/conexion.php');
session_start();
$consulta  = 'SELECT  
                  pk_clave_rem,
                  txt_economico_rem,
                  txt_nserie_rem,
                  num_latitud_rem,
                  num_longitud_rem,
                  fec_posicion_rem,
                  txt_nombre_cir,
                  pk_clave_cir,
                  txt_georeferencia_cas,
                  num_icono_rem,
                  zonaroja,
                  sucursal,
                  nombre AS tipo
                FROM tb_remolques AS r 
                LEFT JOIN tb_circuitos AS c
                  ON r.fk_clave_cir = c.pk_clave_cir
                LEFT join geocercasporunidad gc 
                  ON r.txt_economico_rem = gc.economico 
                INNER JOIN tb_tiposderemolque AS tr
                  ON r.num_tipo_rem = tr.idtipo
                WHERE r.estatus = 1
                ORDER BY pk_clave_rem';

$query = $conn->prepare($consulta);
//$query->bindParam(1,$_GET['idusuario']);
$query->execute();
$cuenta = 0;
$remolques = array();

while ($registro = $query->fetch()) {
$valor=0;
  if($registro['sucursal']==0){
    $valor='Fuera de Sucursal';          
  }else{
    $consulta2 = "SELECT txt_nombre_zon as nombre 
                  FROM monitoreo.tb_zonas 
                  WHERE pk_clave_zon = ".$registro['sucursal'];
		$query2 = $conn->prepare($consulta2);
		$query2->execute();
    $registro2 = $query2->fetch();
    if (isset($registro2['nombre'])){
    $valor = $registro2['nombre'];
    }else{
      $valor =  '---';
    }
   // $query2.close();
  }

  $fecha = new DateTime($registro['fec_posicion_rem'], new DateTimeZone('UTC'));
  $fecha = $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
  $fecha = date_format($fecha, 'Y-m-d H:i:s');

  $remolque = array(
    'id' => $registro['pk_clave_rem'],
    'economico' => $registro['txt_economico_rem'],
    'serie' => $registro['txt_nserie_rem'],
    'lat' => $registro['num_latitud_rem'],
    'long' => $registro['num_longitud_rem'],
    'circuito' => $registro['txt_nombre_cir'],
    'numcircuito' => $registro['pk_clave_cir'],
    'fec_posicion' => $fecha,
    'georeferencia' => $registro['txt_georeferencia_cas'],
    'sucursal' => $valor,
    'zonaderiesgo' => 0,
    'zonaderiesgoreal' => 0,
    'color' => $registro['num_icono_rem'],
    'especial' => 0,
    'inmovilizada' => 0,
    'frontera' => $registro['zonaroja'],
    'tipo' => $registro['tipo'],
  );

  array_push($remolques, $remolque);
}

$vehiculos = array('vehiculos' => $remolques);
$query->closeCursor();

echo json_encode($vehiculos);

