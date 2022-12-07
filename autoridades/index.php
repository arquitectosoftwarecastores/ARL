<?php
 error_reporting(E_ALL);
    ini_set('display_errors', true);
    include ('../conexion/conexion.php');	

	$xml = new SimpleXMLElement('<xml/>');


  	$consulta  = " SELECT * FROM tb_vehiculos";  
  	$query = $conn->prepare($consulta);
  	$query->bindParam(1, $usuario);
  	$query->bindParam(2, $contrasena);
  	$query->execute();

  	while($registro = $query->fetch())
  	{
	  // AGREGA NODOS AL DOCUMENTO XML
  	  $track = $xml->addChild('marker');
	  $track->addChild("name", $registro['txt_economico_veh']);
	  $track->addChild("address", " ");
	  $track->addChild("lat", $registro['num_latitud_veh']);
	  $track->addChild("lng", $registro['num_longitud_veh']);
	  $track->addChild("type", " ");
  	}

	Header('Content-type: text/xml');
	print($xml->asXML());

?>