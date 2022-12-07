<?php 
	session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
	date_default_timezone_set("America/Mexico_City");
	include ('../conexion/conexion.php');

	$consulta  = "SELECT * FROM avl_secundario
				WHERE to_timestamp(sec_secundarioupos, 'YYYYMMDD HH24:MI:SS') > 
				(to_timestamp(sec_primarioupos, 'YYYYMMDD HH24:MI:SS')+ INTERVAL '16 MINUTES')
				AND sec_secundario<>'' ";
	$query = $conn->prepare($consulta);
	$query->execute();

            $consulta1  = "SELECT * FROM tb_tiposdealertas WHERE txt_nombre_tipa='Monitoreo Seguridad'";
	$query1 = $conn->prepare($consulta1);
	$query1->execute();
	$registro1 = $query1->fetch();
    $prioridad = $registro1["num_prioridad_tipa"];
	$tipoalerta = $registro1["pk_clave_tipa"];

	echo "<p>Revisando si hay secundarios reportando...</p>";
	$unidades=0;
	while($registro = $query->fetch())
	{
		$consulta2  = "SELECT * FROM  tb_vehiculos WHERE num_serie_veh = ?";  
		$query2 = $conn->prepare($consulta2);
		$query2->bindParam(1, $registro["sec_primario"]);
		$query2->execute();
		$encontrado=0;
		while($registro2 = $query2->fetch())
		{
			$fec_posicion_veh=$registro2["fec_posicion_veh"];
			$txt_economico_veh=$registro2["txt_economico_veh"];
			$num_ignicion_veh=$registro2["num_ignicion_veh"];
			$num_latitud_veh=$registro2["num_latitud_veh"];
			$num_longitud_veh=$registro2["num_longitud_veh"];
			$num_serie_veh=$registro2["num_serie_veh"];
			$encontrado=1;
		}

        if($encontrado)
        {
			$consulta3  = "UPDATE tb_vehiculos SET num_serie_veh = ? WHERE num_serie_veh = ?";  
			$query3 = $conn->prepare($consulta3);
			$query3->bindParam(1, $registro["sec_secundario"]);
			$query3->bindParam(2, $registro["sec_primario"]);
			$query3->execute();
			echo "<p>Actualizando tb_vehiculos No de serie antes: ".$registro["sec_primario"]." ahora la serie es: ".$registro["sec_secundario"]."</p>";
			echo "<p>Generando alerta de seguridad...</p>";

			$consulta4  = "INSERT INTO 
			tb_alertas (fk_clave_tipa,fec_fecha_ale,txt_economico_veh,txt_ignicion_ale,num_prioridad_ale,num_latitud_ale,num_longitud_ale,txt_upsmart_ale,num_tipo_ale) 
			VALUES (?,?,?,?,?,?,?,?,0)";  
			$query4 = $conn->prepare($consulta4);
			$query4->bindParam(1, $tipoalerta);
			$query4->bindParam(2, $fec_posicion_veh);
			$query4->bindParam(3, $txt_economico_veh);
			$query4->bindParam(4, $num_ignicion_veh);
			$query4->bindParam(5, $prioridad);
			$query4->bindParam(6, $num_latitud_veh);
			$query4->bindParam(7, $num_longitud_veh);
			$query4->bindParam(8, $num_serie_veh);
			$query4->execute();
			echo "<p>Se inserto alerta para vehículo económico :".$txt_economico_veh.", con serie: ".$registro["sec_secundario"]."</p>";

		}
		else
			echo "<p style='color:#FF0000'>No se encontró primario: ".$registro["sec_primario"]."</p>";
      	$unidades++;
	}
	echo "<p>Finalizó actualización a secundarios ...</p>";
	echo "<p>==============================================================================</p>";
	echo "<p>Revisando si regresa el primario ...</p>";

	$consulta5  = "SELECT * FROM avl_secundario
				WHERE to_timestamp(sec_secundarioupos, 'YYYYMMDD HH24:MI:SS') < 
				(to_timestamp(sec_primarioupos, 'YYYYMMDD HH24:MI:SS')+ INTERVAL '16 MINUTES') 
				AND sec_secundario<>'' ";
	$query5 = $conn->prepare($consulta5);
	$query5->execute();
	while($registro5 = $query5->fetch())
	{
		$consulta6  = "SELECT * FROM  tb_vehiculos WHERE num_serie_veh = ?";  
		$query6 = $conn->prepare($consulta6);
		$query6->bindParam(1, $registro5["sec_secundario"]);
		$query6->execute();
		$encontrado=0;
		while($registro6 = $query6->fetch())
		{
			$fec_posicion_veh=$registro6["fec_posicion_veh"];
			$txt_economico_veh=$registro6["txt_economico_veh"];
			$num_ignicion_veh=$registro6["num_ignicion_veh"];
			$num_latitud_veh=$registro6["num_latitud_veh"];
			$num_longitud_veh=$registro6["num_longitud_veh"];
			$num_serie_veh=$registro6["num_serie_veh"];
			$encontrado=1;
		}

        if($encontrado)
        {
			$consulta7  = "UPDATE tb_vehiculos SET num_serie_veh = ? WHERE num_serie_veh = ?";  
			$query7 = $conn->prepare($consulta7);
			$query7->bindParam(1, $registro5["sec_primario"]);
			$query7->bindParam(2, $registro5["sec_secundario"]);
			$query7->execute();

			echo "<p>Actualizando tb_vehiculos No de serie antes: ".$registro5["sec_secundario"]." ahora la serie es: ".$registro5["sec_primario"]."</p>";

		}
		else
			echo "<p style='color:#FF0000'>No se encontró secundario: ".$registro5["sec_secundario"]." desde primario: ".$registro5["sec_primario"]."</p>";

	}

	echo "<p>Finalizó el proceso de actualización.</p>";

    $query->closeCursor();
    $query1->closeCursor();
    $query2->closeCursor();
    $query3->closeCursor();
    $query4->closeCursor();
    $query5->closeCursor();
    $query6->closeCursor();
    $query7->closeCursor();
?>