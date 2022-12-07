<?php

// Conexion PostgreSQL
include ('../conexion/conexion.php');

// Conexion MySQL
$servidor="192.168.0.23";
$username="usuarioWin";
$password = "windows";

// Crea Conexion
$con = new mysqli($servidor, $username, $password);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
// echo "Connected successfully<br>";


/** Consulta idOficina y idCircuito de WALMART */
$arrCirOfi = getOficnas($conn);
// echo $arrCirOfi["oficinas"];

/** Actualiza a Todos los Circuitos */
updateZeroCircuitos($arrCirOfi['circuitos'], $conn);


/** Consulta Viajes Spot */
$conVS = 'SELECT cc.noeconomico, tv.idoficina, cc.idtipounidad , tv.idcatalogo_viajes
          FROM camiones.bitacora AS cb
          INNER JOIN talones.`viajes` AS tv ON cb.`idviaje` = tv.`idviaje`
          LEFT JOIN camiones.`camiones` AS cc ON tv.idunidad = cc.unidad
          WHERE tv.idcatalogo_viajes IN (1,11) AND cc.status = 1 
          AND tv.idcliente IN (16835, 60308, 1042) AND tv.estatus IN (1,2) 
          AND tv.idoficina IN ('.$arrCirOfi["oficinas"].')';
$result = $con->query($conVS);

if ($result->num_rows > 0) {

  while($row = $result->fetch_assoc()) {

    // Obtiene circuito de la unidad
    $circuito = getCircuito($row['idoficina'], $row['idtipounidad'], $row['idcatalogo_viajes'], $conn);

    if ($circuito != 0) {
      // Actualiza el Circuito de los vehiculos en Spot
      $upVeh = "UPDATE tb_vehiculos 
                SET fk_clave_cir = ? 
                WHERE txt_economico_veh = ?";
      $qryVeh = $conn->prepare($upVeh);
      $qryVeh->bindParam(1, $circuito);
      $qryVeh->bindParam(2, $row["noeconomico"]);
      $qryVeh->execute();
      $qryVeh->closeCursor();
      
      echo " * ".$circuito." - ". $row["noeconomico"]."<br>\n";
    }
    
  }

} else {
  echo "0 results";
}

echo "---------------------------";

// Unidades de renta walmart

$con->close();
$conn = null;


/** Funciones */

function updateZeroCircuitos ($circuitos, $conn) {

  // Actualiza los Circuitos a  'Todos Los Circuitos'
  $upVeh = "UPDATE tb_vehiculos SET fk_clave_cir = 0 
            WHERE fk_clave_cir IN (".$circuitos.") ";
  $queryVeh = $conn->prepare($upVeh);
  $queryVeh->execute();
  $queryVeh->closeCursor();
  echo "Todos a 0 <br>";

}

function getCircuito($idOficina, $tipo, $catalogo, $conn){

  if ($idOficina == 1418 || $idOficina == 1416) {
    // WALMART GUADALJARA

    if ($tipo == 1) {
      // Trailer
      return 39;

    } elseif ($tipo == 2) {
      // Torton
      return 51;
    }
    
  } elseif ($idOficina == 2504) {
    // WALMART CULIACAN
    
    if ($catalogo == 1) {
      // Completo -- Culican Dedicadas
      return 58;
    } else {
      // Redondo -- Culican
      return 12;
    }

  } elseif ($idOficina == 203 || $idOficina == 207) {
    // WALMART TIJUANA

    if ($catalogo == 11) {
      return 53;
    } else {
      return 0;
    }

  } elseif ($idOficina == 1911) {
    // WALMART MONTERREY

    if ($catalogo == 11) {
      return 46;
    } else {
      return 0;
    }
    
  } else {
    // Cualquier otro WALMART
    $conCir = "SELECT * FROM tb_spots WHERE idoficina = ?";
    $qryCir = $conn->prepare($conCir);
    $qryCir->bindParam(1, $idOficina);
    $qryCir->execute();
    $oficina = $qryCir -> fetch();
    $qryCir->closeCursor();

    return $oficina['idcircuito'];
  }
  
}


function getOficnas($conn) {
  // Variables
  $idOficinas = '';
  $idCircuitos = '';

  // Cuenta el numero de Oficinas
  $conOf = "SELECT COUNT(*) AS num FROM tb_spots";
  $qryOf = $conn->prepare($conOf);
  $qryOf->execute();
  $resOf = $qryOf -> fetch();

  $countOficina = $resOf['num'] ;

  // Obtiene oficinas
  $conOf = "SELECT * FROM tb_spots";
  $qryOf = $conn->prepare($conOf);
  $qryOf->execute();
  $i = 0;
  
  while ($oficina = $qryOf -> fetch() ) {
    
    if (($countOficina - 1) > $i) {
      $idOficinas = $idOficinas.', '.$oficina['idoficina'];
      $idCircuitos = $idCircuitos.', '.$oficina['idcircuito'];

    } else {
      $idOficinas = $oficina['idoficina'].$idOficinas;
      $idCircuitos = $oficina['idcircuito'].$idCircuitos;
    }
    $i++;
    
  }
  $qryOf->closeCursor();

  $arr = array(
    "oficinas" => $idOficinas,
    "circuitos" => $idCircuitos,
  );

  return $arr;
}

?>
