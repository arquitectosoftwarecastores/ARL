<?php

function calcula_diferencia($f_fin,$f_ini){
	$arr_ffin = explode(" ",$f_fin);
	$arr_fefin = explode("/",$arr_ffin[0]);
	$arr_hofin = explode(":",$arr_ffin[1]);
	$seg_ffin = mktime($arr_hofin[0],$arr_hofin[1],$arr_hofin[2],$arr_fefin[1],$arr_fefin[2],$arr_fefin[0]);
	
	$arr_fini = explode(" ",$f_ini);
	$arr_feini = explode("/",$arr_fini[0]);
	$arr_hoini = explode(":",$arr_fini[1]);
	$seg_fini = mktime($arr_hoini[0],$arr_hoini[1],$arr_hoini[2],$arr_feini[1],$arr_feini[2],$arr_feini[0]);
	
	$diferencia = $seg_ffin - $seg_fini;
	$minutes = floor($diferencia / 60) ;
    
    $hours = floor($minutes / 60) ;
    $minutes_left = $minutes % 60 ;
    if ($hours < 10){
        $hours = "0".$hours;
    }
    if ($minutes_left < 10){
        $minutes_left = "0".$minutes_left;
    }
    $horas_diferencia = $hours.":".$minutes_left; 
	
	return $horas_diferencia;
	
}


?>