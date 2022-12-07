<?php
function puntoseguro($lat,$lon,$conn){
	$bandera = 0;
	$consulta  = " SELECT * FROM tb_puntosseguros WHERE num_tipo_pun=2 ";  
	$query = $conn->prepare($consulta);
	$query->execute();
	while($registro = $query->fetch())
	{
		$bandera =0;
		$distancia = distancia($registro['num_latitud_pun'],$registro['num_longitud_pun'],$lat,$lon);
		if($distancia <=0 )
		{
			$bandera = 1;
			return $bandera;
		}
	}
	return $bandera;
}
?>