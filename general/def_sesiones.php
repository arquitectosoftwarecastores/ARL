<?php 
/*	
*	Mediante sesiones se configura que flecha de ordenamiento no se mostrará
*	@sesion_none: sesion que oculta su icono 
*	no regresa valores
*/	
function set_sesionesdesplegar($sesion_none) {
		$sesiones_img = array("ciudad_up", "ciudad_do");
		foreach ($sesiones_img as $sesion => $nombre_sesion) {
			if ($sesion_none == $nombre_sesion) {
				$_SESSION[$nombre_sesion] = " style='display:none'; ";
			}
			else {
				$_SESSION[$nombre_sesion] = "";
			}
		}
}


	if (isset($_GET["rxp"])) 
		$rxp = $_GET["rxp"];
	else 
		$rxp = 500;	

	if (isset($_GET["inicia="])) {
		$_SESSION["inicia="] = $_GET["inicia="];
	}
	else {
		if (!isset($_SESSION["inicia="])) {
			$_SESSION["inicia="] = 1;
		}
	}	

	
?>