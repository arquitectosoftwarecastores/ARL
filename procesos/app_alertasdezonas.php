<?php 
	session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', true);
	include ('../conexion/conexion.php');
	include("../posiciones/app_referencia.php");
	include("../funciones/distancia.php");
	include("../funciones/checazona.php");
	date_default_timezone_set("America/Mexico_City");


    //Consulta parametro de ajuste de horas con respecto al GPS

	$consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";  
	$query0 = $conn->prepare($consulta0);
	$query0->execute();
	$registro0 = $query0->fetch();
	$ajustegps=$registro0["num_valor_par"];
	$query0->closeCursor();
	$consulta  = " SELECT * FROM ctg_vehiculos ";  
	$query = $conn->prepare($consulta);
	$query->execute();
	$contador=1;
	while($registro = $query->fetch())
	{
		$serie=$registro["veh_nserie"];
		$zriesgo = checazona($latitud,$longitud,3,$conn);
                
                            /* 02/07/2018 verificar zona de riesgo de la ciudad*/
           $zona2 = checazona($latitud, $longitud, 3, $conn);
            $inserta_geocerca  = "INSERT INTO geocercasporunidad (economico,geo1) VALUES (90000,200)";
                        $query2 = $conn->prepare($inserta_geocerca);
                        $query2 -> bindParam(1, $economico);
                        $query2 -> bindParam(2, $zona2);
                        $query2 -> execute();  
                        $query2 -> closeCursor();
            /*   */
                
		$pseguro = checazona($latitud,$longitud,2,$conn);
		$zpinteres = checazona($latitud,$longitud,1,$conn);
		$consulta1 = "select txt_nombre_zon, pk_clave_tipz from tb_zonas, tb_tiposdezona WHERE fk_clave_tipz=pk_clave_tipz AND txt_nombre_zon = '".$zpinteres_arr[1]."'";
		$tipo_zona = $registro1['pk_clave_tipz'];
		$consulta1  = "SELECT * FROM tb_vehiculos
		               WHERE num_serie_veh=?";  
		$query1 = $conn->prepare($consulta1);
		$query1->bindParam(1, $serie);
		$query1->execute();
		$encuentra=0;
		while($registro1 = $query1->fetch())
			{
			 $encuentra=1;
			 $latitud_ant=$registro1["num_latitud_veh"];
			 $longitud_ant=$registro1["num_longitud_veh"];
			 $fecha_ant=$registro1["fec_posicion_veh"];
			}

        echo "<p>".$contador.") ";

        if($encuentra)
        {

			$orienta = $rumbo[16];
			$latitud = $registro['veh_latitud'];
			$longitud = $registro['veh_longitud'];			
			$fecha=date('Y-m-d H:i:s',strtotime('-'.$ajustegps.' hour',strtotime($registro["veh_uposicion"])));

			//checa si cambio la posición


	        if ($longitud!=round($longitud_ant,6) or $latitud!=round($latitud_ant,6) )
            {

		        $indice = orientacion ($longitud_ant,$latitud_ant,round($longitud,6),round($latitud,6));
		        if ($indice >= 0)
		        	$orienta = $rumbo[$indice];

		       	/* $ignicion = 2;
            switch ($registro['veh_ignicion']):
                case 0:
                    $ignicion = 2;
                    break;
                case 1:
                    $ignicion = 1;
                    break;
            endswitch;*/
//	    echo " --- ".$registro['veh_ignicion']. " --- ";
	    if($registro['veh_ignicion']!=0){
             $ignicion = 1;                   
            }else{
             $ignicion = 2;
            }

				$ubicacion=georeferencia($latitud,$longitud,$conn);
				$ubicacionpi=georeferencia_pi($latitud,$longitud,$conn);

		        $combtot=$registro['veh_combtot'];
		        $odometro=$registro['veh_odometro'];		        



	      }
	     
        }
        else
			echo "No se encontró, serie:".$serie."</p>";

		$contador++;

	}

 ?>