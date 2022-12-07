<?php

include_once('../../config/conexion.php');
include_once('../class/HexString.php');

do {
  // Consulata Ultima Cadena
  $conCad = 'SELECT * 
            FROM avl_cadenas_g 
            WHERE cad_estatus = 1
            ORDER BY cad_id DESC
            LIMIT 1';
  $qryCad = $conn->prepare($conCad);
  $qryCad->execute();

  while ($regCad = $qryCad->fecth()) {
    $hexStr = new HexString($regCad);
  }

  if (isset($hexStr)) {
    

    unset($hexStr);
  } else {
    echo 'Sin Cadenas';
    sleep(10);
  }

} while (true);
