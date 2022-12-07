<?PHP
// *****************************************************************************
// Contiene funciones para determinar la cercanía a puntos o ciudades
// *****************************************************************************


function referencia_geo($latitud,$longitud,$conexion,$bd){
	mysql_select_db($bd,$conexion);
	
	$referencia_ok = "";
	$lat_rango1 = $latitud;
	$lat_rango2 = $latitud;
	$lon_rango1 = $longitud;
	$lon_rango2 = $longitud;
	while ($referencia_ok == ""){
		
		$lat_rango1 = $lat_rango1 + 0.25;
		$lat_rango2 = $lat_rango2 - 0.25;
		$lon_rango1 = $lon_rango1 + 0.25;
		$lon_rango2 = $lon_rango2 - 0.25;
		$colonia = "";
		$ciudad = "";
		$estado = "";
		
		$bandera = 0;
		$mas_cercano = 999999;
		$consulta = "select COLONY_NAME, LATITUDE, LONGITUDE, POSTAL_CODE, CITY_NAME, STATE_ABBR, STATE_NAME 
						from referencias_geo  
						where LONGITUDE>=".$lon_rango2." and LONGITUDE <= ".$lon_rango1." and 
						      LATITUDE>=".$lat_rango2." and LATITUDE <= ".$lat_rango1." order by LONGITUDE DESC limit 200";
		
		$result = mysql_query($consulta,$conexion);
		while ($row=mysql_fetch_row($result)){
			
			$distancia = distancia($row[1], $row[2], $latitud, $longitud);
			if ($bandera == 0){
				$mas_cercano = $distancia;
				$bandera = 1;
			}
			if ($distancia<=$mas_cercano){
				
				$mas_cercano = $distancia;
				$colonia = $row[0];
				$ciudad = $row[4];
				$estado = $row[5];
			}
			
		}
		if ($mas_cercano >0){
			if ($colonia!=''){
				$referencia_ok = "A ". round($mas_cercano,2)." Kms de ".$colonia." en ".$ciudad.", ".$estado;
			}else{
				$referencia_ok = "A ". round($mas_cercano,2)." Kms de ".$ciudad.", ".$estado;
			}
		}else{
			if ($colonia!=''){
				$referencia_ok = "En ".$colonia." en ".$ciudad.", ".$estado;
			}else{
				$referencia_ok = "En ".$ciudad.", ".$estado;
			}
		}
	}
	return $referencia_ok;
}




	//------------------------------------- con sf_pinteres ------------------------------------- 

function referencia_geo_pi($latitud,$longitud,$conexion,$bd){
	mysql_select_db($bd,$conexion);
	$referencia_ok = "";
	$lat_rango1 = $latitud;
	$lat_rango2 = $latitud;
	$lon_rango1 = $longitud;
	$lon_rango2 = $longitud;
	while ($referencia_ok == ""){
		$lat_rango1 = $lat_rango1 + 0.3;
		$lat_rango2 = $lat_rango2 - 0.3;
		$lon_rango1 = $lon_rango1 + 0.3;
		$lon_rango2 = $lon_rango2 - 0.3;
		
		$bandera = 0;
		$encontro = 0;
		$mas_cercano_pi = 999999;
		$distancia = 1000000;
		$consulta5 = "select nombre, latitud, longitud from ctg_pseguros where longitud>=".$lon_rango2." and longitud <= ".$lon_rango1." 
								and latitud>=".$lat_rango2." and latitud <= ".$lat_rango1." order by longitud ASC";
		$result5 = mysql_query($consulta5,$conexion);
		while ($row5=mysql_fetch_row($result5)){
			$distancia = distancia($row5[1], $row5[2], $latitud, $longitud);
			if ($bandera == 0){
				$mas_cercano_pi = $distancia;
				$bandera = 1;
			}
			if ($distancia<=$mas_cercano_pi){
				$p_interes= $row5[0];
				$mas_cercano_pi = $distancia;
				$encontro =1;
			}
					
		}
		
		
		
		if($encontro == 1){
			if($mas_cercano_pi>0){
				
				$referencia_ok = "A ". round($mas_cercano_pi,2)." Kms de ".$p_interes;
			}else{
				$referencia_ok = "En ".$p_interes;
			}
		}
	}
	
	return $referencia_ok;
}

function referencia_geo_pi_nombre($latitud,$longitud,$conexion,$bd){
	mysql_select_db($bd,$conexion);
	$referencia_ok = "";
	$lat_rango1 = $latitud;
	$lat_rango2 = $latitud;
	$lon_rango1 = $longitud;
	$lon_rango2 = $longitud;
	$inicial = 0;
	$incremento = 0.05;
	while ($referencia_ok == ""){
		$inicial = $inicial + $incremento;
		$lat_rango1 = $lat_rango1 + $inicial;
		$lat_rango2 = $lat_rango2 - $inicial;
		$lon_rango1 = $lon_rango1 + $inicial;
		$lon_rango2 = $lon_rango2 - $inicial;
		/*$lat_rango1 = $lat_rango1 + 0.25;
		$lat_rango2 = $lat_rango2 - 0.25;
		$lon_rango1 = $lon_rango1 + 0.25;
		$lon_rango2 = $lon_rango2 - 0.25;*/
		//echo "suma";
		
		$bandera = 0;
		$encontro = 0;
		$mas_cercano_pi = 999999;
		$distancia = 1000000;
		$consulta5 = "select nombre, latitud, longitud from ctg_pseguros where longitud>=".$lon_rango2." and longitud <= ".$lon_rango1." 
								and latitud>=".$lat_rango2." and latitud <= ".$lat_rango1." order by longitud ASC";
		//echo $consulta5."\n<br>";
		$result5 = mysql_query($consulta5,$conexion);
		while ($row5=mysql_fetch_row($result5)){
			$distancia = distancia($row5[1], $row5[2], $latitud, $longitud);
			//echo $distancia."\n<br>";
			if ($bandera == 0){
				$mas_cercano_pi = $distancia;
				$bandera = 1;
			}
			if ($distancia<=$mas_cercano_pi){
				$p_interes= $row5[0];
				$mas_cercano_pi = $distancia;
				$encontro =1;
				//echo "por lon ".$row[1]." ".$row[2]." ".$mas_cercano_pi." ".$colonia." ".$ciudad." ".$estado."\n<br>"; 
			}
					
		}
		
		
		if($encontro == 1){
			if($mas_cercano_pi>0){
				
				$referencia_ok = $p_interes;
			}else{
				$referencia_ok = $p_interes;
			}
		}
	}
	
	return $referencia_ok;
}

?>