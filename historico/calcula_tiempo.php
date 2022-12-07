<?PHP
function tiempototal($dstart, $dend){

	$dteStart = new DateTime($dstart);
	$dteEnd = new DateTime($dend);

	$dteDiff  = $dteStart->diff($dteEnd);

	return $dteDiff->format("%H:%I:%S");

}

function datetohours($hourdate){
	//converts a date in the format hh:mm:ss to hours

	list($h, $m, $s) = explode(':', $hourdate);
	$secToHours = ($s/60)/60;
	$minToHours = $m/60;

	$hours = $h + $secToHours + $minToHours;

	return $hours;
}

function averagespeed($distance, $hours){
	$averspeed = $distance/$hours;

	return $averspeed;
}	
?>
