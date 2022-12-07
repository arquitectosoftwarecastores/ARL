<?php
//Módulo 3 = Remolques
/*		$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 3 and pk_clave_usu = ".$_SESSION['id'];
		$query = $conn->prepare($consulta);
		$query->execute();
		$registro = $query->fetch();
		$permiso = $registro['total'];*/

$permiso = 1;
if ($permiso > 0) {
	$accion = (isset($_GET['accion']) && $_GET['accion'] != '') ? $_GET['accion'] : 'lista';
	switch ($accion) {
		case "lista":
			include_once("remolquesporsucursal/for_elementos_new.php");
			break;

		default:
			include_once("remolquesporsucursal/for_elementos_new.php");
			break;
	}
} else {
?>
	<div class="container">
		<div class="alert alert-warning">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<strong>Su usuario no tiene acceso a este módulo</strong>.
		</div>
	</div>
<?php
}
?>