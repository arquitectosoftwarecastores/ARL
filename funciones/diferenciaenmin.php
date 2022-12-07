<?PHP
function tiempototal($dstart, $dend){

	$dteStart = new DateTime($dstart);
	$dteEnd = new DateTime($dend);

	$dteDiff  = $dteStart->diff($dteEnd);

	return $dteDiff->format("%H:%I:%S");

}

function datetomin($hourdate){
    //converts a date in the format hh:mm:ss to minutes

	list($h, $m, $s) = explode(':', $hourdate);
	$secToMin = ($s/60)/60;
	$hoursToMin = $h*60;

	$minutes = $m + $secToMin + $hoursToMin;

	return $minutes;
}

?>
