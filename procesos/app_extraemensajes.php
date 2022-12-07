<?php 
	session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set("America/Mexico_City");

	include ('../conexion/conexion.php');
	include("../funciones/distancia.php");
	include("../posiciones/app_referencia.php");



    //Consulta parametro de ajuste de horas con respecto al GPS

	$consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";  
	$query0 = $conn->prepare($consulta0);
	$query0->execute();
	$registro0 = $query0->fetch();
	$ajustegps=$registro0["num_valor_par"];
	$query0->closeCursor();


	$consulta  = " SELECT num_ultimaalerta_con FROM tb_configuracion";  
	$query = $conn->prepare($consulta);
	$query->execute();
	$registro = $query->fetch();
	$ultimaalerta=$registro["num_ultimaalerta_con"];
	echo "Ultima alerta:".$ultimaalerta."<br>";
	$ultimo=$ultimaalerta;  

	$consulta1 = "SELECT COUNT(pk_clave_tipa) as totalregistros FROM tb_tiposdealertas ";
	$query1 = $conn->prepare($consulta1);
	$query1->execute();	
	$registro1 = $query1->fetch();
	$totalregistros=$registro1["totalregistros"];
	
	$consulta2 = "SELECT * FROM tb_tiposdealertas ";
	$query2 = $conn->prepare($consulta2);
	$query2->execute();	
	$listaalertas="";
	$cuenta=1;
	while ($registro2 = $query2->fetch()) {
	    $listaalertas .= "alerta_dat1 = '".$registro2['txt_nombre_tipa']."'";
	    if($cuenta<$totalregistros)
	    	$listaalertas .= " or ";
	    $cuenta++;
	}

	$consulta3  = "SELECT alerta_id, alerta_nserie, alerta_timestamp,
		           alerta_ignicion, alerta_dat1, alerta_latitud, alerta_longitud, alerta_dat0
		           FROM avl_alertas
		           WHERE (" . $listaalertas . ") and alerta_id >" . $ultimaalerta . " ORDER BY alerta_id ASC LIMIT 10";   
 
	$query3 = $conn->prepare($consulta3);
	$query3->execute();
	$contador=0;
	while($registro3 = $query3->fetch())
	{
        
        $consulta4  = "SELECT * FROM tb_vehiculos WHERE num_serie_veh = ?";  
        $query4 = $conn->prepare($consulta4);
        $query4->bindParam(1, $registro3["alerta_nserie"]);
		$query4->execute();
		$registro4 = $query4->fetch();
   
        $unidad=$registro4["txt_economico_veh"];
        $serie=$registro3["alerta_nserie"];

        switch ($registro3['alerta_ignicion']):
            case 0:
                $ignicion = 'Apagado';
                break;
            case 1:
                $ignicion = 'Encendido';
                break;
        endswitch;		

		$fecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro3["alerta_timestamp"])));

        $latitud = $registro3['alerta_latitud'];
        $longitud = $registro3['alerta_longitud'];		
        $ubicacion=georeferencia($latitud,$longitud,$conn);

        $consulta5  = "SELECT pk_clave_tipa, num_prioridad_tipa, num_tipo_tipa FROM tb_tiposdealertas WHERE  txt_nombre_tipa = ?";  
        $query5 = $conn->prepare($consulta5);
        $query5->bindParam(1, $registro3["alerta_dat1"]);
		$query5->execute();
		$registro5 = $query5->fetch();    
        $prioridad=$registro5["num_prioridad_tipa"];  /* 1=Seguridad, 0=Administrativa*/
        $tipodealerta=$registro5["num_tipo_tipa"];    /* 1=Baja, 2=Media, 3=Alta*/
        $idalerta=$registro5["pk_clave_tipa"]; 
        $estatus=0;

		if ($unidad != "" and $tipodealerta != "") 
		{
	        $consulta6  = "INSERT INTO tb_alertas 
	        			   (fk_clave_tipa,fec_fecha_ale,txt_ubicacion_ale,txt_economico_veh,txt_ignicion_ale,num_latitud_ale,num_longitud_ale,num_estatus_ale,num_prioridad_ale,num_tipo_ale)
	        			   VALUES(?,?,?,?,?,?,?,?,?,?)";  
	        $query6 = $conn->prepare($consulta6);
	        $query6->bindParam(1, $idalerta);
	        $query6->bindParam(2, $fecha);
	        $query6->bindParam(3, $ubicacion);
	        $query6->bindParam(4, $unidad);
	        $query6->bindParam(5, $ignicion);
	        $query6->bindParam(6, $latitud);
	        $query6->bindParam(7, $longitud);
	        $query6->bindParam(8, $estatus);
	        $query6->bindParam(9, $prioridad);
	        $query6->bindParam(10, $tipodealerta);
			$query6->execute();
			$ultimo = $registro3['alerta_id'];
			echo "<strong>Se insertó alerta con éxito</strong>";
			echo "Ignición:".$ignicion.", fecha:".$fecha.", latitud:".$latitud.", longitud:".$longitud.", ubicacion:".$ubicacion.", serie:".$serie.", vehiculo:".$unidad.",alerta:".$registro3["alerta_dat1"].", prioridad:".$prioridad.", tipo:".$tipodealerta." <br>";   	
			$contador++;		
		}
		else
			echo "<p>No se encontró unidad: ".$registro3["alerta_nserie"].", tipo de alerta: ".$registro3["alerta_dat1"]."</p>";     

    }  // fin del while

    $consulta7  = "UPDATE tb_configuracion SET
                   num_ultimaalerta_con=? ";  
    $query7 = $conn->prepare($consulta7);
    $query7->bindParam(1, $ultimo);
	$query7->execute();	
	echo "<p>Ultima alerta:".$ultimo."</p>";
	echo "<p>Se insertaron:".$contador." registros </p>";

	$query->closeCursor();
	$query1->closeCursor();
	$query2->closeCursor();
	$query3->closeCursor();
	$query4->closeCursor();
	$query5->closeCursor();
	$query7->closeCursor();
 ?>