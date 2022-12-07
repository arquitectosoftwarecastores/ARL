<?php 
	$url = "index.php?seccion=".$seccion."&amp;accion=lista";
	if (isset($_GET["rxp"])) {
		$url .= "&rxp=".$_GET["rxp"];
	} 
	else {
		$url .= "&rxp=500";
	}	
	
	if (isset($_GET["busca"])) {
		$url .= "&busca=".$_GET["busca"];
	}

	if (isset($_GET["inicia"])) {
		$url .= "&inicia=".$_GET["inicia"];
	}	

	if (isset($_GET["from"])) {
		$url .= "&from=".$_GET["from"];
	}	

	if (isset($_GET["from2"])) {
		$url .= "&from2=".$_GET["from2"];
	}

	if (isset($_GET["to"])) {
		$url .= "&to=".$_GET["to"];
	}	

	if (isset($_GET["to2"])) {
		$url .= "&to2=".$_GET["to2"];
	}	
	if (isset($_GET["economico"])) {
		$url .= "&economico=".$_GET["economico"];
	}	

	if (isset($_GET["alerta"])) {
		$url .= "&alerta=".$_GET["alerta"];
	}

	if (isset($_GET["estatus"])) {
		$url .= "&estatus=".$_GET["estatus"];
	}

	if (isset($_GET["acumuladas"])) {
		$url .= "&acumuladas=".$_GET["acumuladas"];
	}

	if (isset($_GET["tiempo"])) {
		$url .= "&tiempo=".$_GET["tiempo"];
	}

	if (isset($_GET["circuito"])) {
		$url .= "&circuito=".$_GET["circuito"];
	}

	if (isset($_GET["especial"])) {
		$url .= "&especial=".$_GET["especial"];
	}

	if (isset($_GET["perdida"])) {
		$url .= "&perdida=".$_GET["perdida"];
	}
?>