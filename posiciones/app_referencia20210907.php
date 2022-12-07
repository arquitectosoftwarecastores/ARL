<?php
function distancia2($dLatDegrees1, $dLonDegrees1,$dLatDegrees2, $dLonDegrees2){
	    $EARTH_RADIUS_MI    = 3963.2263272;
	    $PI                 = 3.14159265358979323846;
	    $dDistMiles         = 0;
	    $ddistkm            = 0;
	    $dDistMiles = sin ($dLatDegrees1* ($PI/180)) * sin ($dLatDegrees2* ($PI/180)) +
	                     cos ($dLatDegrees1 * ($PI/180)) * cos ($dLatDegrees2*($PI/180)) *
	                     cos (($dLonDegrees1 - $dLonDegrees2)*($PI/180));
	    $dDistMiles = $EARTH_RADIUS_MI * acos ($dDistMiles);
	    $ddistkm = $dDistMiles * 1.609344;
	  return $ddistkm;
	}

function georeferencia($latitud,$longitud,$conn){ 
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
    $consulta = "SELECT txt_colonia_geo, num_latitud_geo, num_longitud_geo, txt_cp_geo, txt_ciudad_geo, txt_estadoabreviado_geo, txt_estado_geo 
            FROM tb_georeferencias  
            WHERE num_longitud_geo>=".$lon_rango2." and num_longitud_geo <= ".$lon_rango1." and 
                  num_latitud_geo>=".$lat_rango2." and num_latitud_geo <= ".$lat_rango1." order by num_longitud_geo DESC limit 200";
    $query = $conn->prepare($consulta);
    $query->execute(); 
    while ($row=$query->fetch()){
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
function georeferencia_pi($latitud,$longitud,$conn){
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
    $consulta5  = "select * from tb_puntosseguros where num_tipo_pun = 2 and num_longitud_pun>=".$lon_rango2." and num_longitud_pun <= ".$lon_rango1." 
                and num_latitud_pun>=".$lat_rango2." and num_latitud_pun <= ".$lat_rango1." order by num_longitud_pun ASC";  
    $query5 = $conn->prepare($consulta5);
    $query5->execute();    
    while ($row5 = $query5->fetch()){
      $distancia = distancia($row5["num_latitud_pun"], $row5["num_longitud_pun"], $latitud, $longitud);
      if ($bandera == 0){
        $mas_cercano_pi = $distancia;
        $bandera = 1;
      }
      if ($distancia<=$mas_cercano_pi){
        $p_interes= $row5["txt_nombre_pun"];
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

function georeferencia2($latitud,$longitud,$conn){
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
    $consulta = "SELECT txt_colonia_geo, num_latitud_geo, num_longitud_geo, txt_cp_geo, txt_ciudad_geo, txt_estadoabreviado_geo, txt_estado_geo 
            FROM tb_georeferencias  
            WHERE num_longitud_geo>=".$lon_rango2." and num_longitud_geo <= ".$lon_rango1." and 
                  num_latitud_geo>=".$lat_rango2." and num_latitud_geo <= ".$lat_rango1." order by num_longitud_geo DESC limit 200";
    $query = $conn->prepare($consulta);
    $query->execute(); 
    while ($row=$query->fetch()){
      $distancia = distancia2($row[1], $row[2], $latitud, $longitud);
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
function georeferencia_pi2($latitud,$longitud,$conn){
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
    $consulta5  = "select * from tb_puntosseguros where num_tipo_pun = 2 and num_longitud_pun>=".$lon_rango2." and num_longitud_pun <= ".$lon_rango1." 
                and num_latitud_pun>=".$lat_rango2." and num_latitud_pun <= ".$lat_rango1." order by num_longitud_pun ASC";  
    $query5 = $conn->prepare($consulta5);
    $query5->execute();    
    while ($row5 = $query5->fetch()){
      $distancia = distancia2($row5["num_latitud_pun"], $row5["num_longitud_pun"], $latitud, $longitud);
      if ($bandera == 0){
        $mas_cercano_pi = $distancia;
        $bandera = 1;
      }
      if ($distancia<=$mas_cercano_pi){
        $p_interes= $row5["txt_nombre_pun"];
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
?>