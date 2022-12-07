<?php
	//--------------------
	function distancia($dLatDegrees1, $dLonDegrees1,$dLatDegrees2, $dLonDegrees2){

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


?>