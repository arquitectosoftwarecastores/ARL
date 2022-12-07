<?PHP
 		include("Connections/conexiones_bd.php");
 		include('Connections/conexiones_bd_prov.php');
 		include('Includes/formato_mssql.php');
 		include("includes/calcula_distancia.php");

        $conecta_mysql = mysql_connect($hostname_smartfleet,$username_smartfleet,$password_smartfleet)
            or die ("error de conexion mysql");
        mysql_select_db($database_smartfleet,$conecta_mysql);

//		$conecta_mssql = mssql_connect($hostname_proveedor1,$username_proveedor1,$password_proveedor1) or die ("<pre><a href='javascript:history.go(-1)'>Error de conexión a Base de Datos, Revisar con Administrador.</a></pre>");
//		    mssql_select_db($database_proveedor1,$conecta_mssql);
	


function ajuste_gmt($fecha,$tipo){

            $gmt = (date('O')/100)* -1; //* Get Current Timezone //
            
            $campos_1 = explode (' ',$fecha);
			if ($tipo == 1){
            	$campos_11 = explode ('-',$campos_1[0]);
            }elseif($tipo == -1) {
				$campos_11 = explode ('/',$campos_1[0]);
			} 
            
			$campos_2 = explode (':',$campos_1[1]);

            $year   = $campos_11[0];
            $month  = $campos_11[1];
            $day    = $campos_11[2];

            $hora = $campos_2[0];
            $min = $campos_2[1];
            $sec = $campos_2[2];

            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
            $ajuste = ($gmt*60*60);
            
            if ($tipo == 1){
            		$timestamp = $timestamp - $ajuste;
            }elseif($tipo == -1) {
					$timestamp = $timestamp + $ajuste;
			} 
		

			
            $msg_timestamp = date("Y/m/d H:i:s",$timestamp);
		 	return $msg_timestamp;
	
}
	


	


		
		/*	if ($_GET['ini'] != '') {
				$ini_gmt = ajuste_gmt($_GET['ini']." 00:00:00",-1);
			}else{
				$ini_gmt = '';
			}
			
			if ($_GET['fin'] != '') {
				$fin_gmt = ajuste_gmt($_GET['fin']." 23:59:59",-1);
			}else{
				$fin_gmt = '';	
			}	*/
			
			
		
		if ($_GET['filtro']=="posiciones" or $_GET['filtro']=="trayectoria" ){
		
			// determina el proveedor de la unidad
			$consulta_proveedor = "select veh_proveedor from ctg_vehiculos where veh_eco ='".$_GET['id']."'";
			$result_proveedor = mysql_query($consulta_proveedor,$conecta_mysql);
			$row_proveedor = mysql_fetch_array($result_proveedor);
			$proveedor = $row_proveedor['veh_proveedor'];
			//echo $proveedor;
		   // ============================== para consulta con informacion local SM ============================
		   /*$consulta_posiciones = "select * 
		   							from sf_posiciones 
									where pos_eco = '".$_GET['id']."'";
			if ($ini_gmt != '' and $fin_gmt != ''){
			 	$consulta_posiciones .= " and  pos_uposicion >= '".$ini_gmt."' and  pos_uposicion <= '".
				                                           $fin_gmt."' order by pos_uposicion asc";
			}
			if ($ini_gmt == '' and $fin_gmt != ''){
				$consulta_posiciones .= " and pos_uposicion <= '".$fin_gmt."' order by pos_uposicion asc";
			}
			if ($ini_gmt != '' and $fin_gmt == ''){
				$consulta_posiciones .= " and pos_uposicion >= '".$ini_gmt."' order by pos_uposicion asc";
			}
		   
		  
			$row_array= array ();
			$result_posiciones = mysql_query  ($consulta_posiciones,$conecta_mysql);
			$r_totales = 0;
			
			
			
			while ($row = mysql_fetch_array($result_posiciones)){
			 		$icono = "images/posicion.png";
	                $posicion_tmp = $row['pos_posicion'];
					if ($posicion_tmp == ""){
						$posicion_tmp = "ND";
					}             
					$fila = array ( 'latitud'=>$row['pos_latitud'],
	                                'longitud'=>$row['pos_longitud'],
	                                'unidad'=>$_GET['id'],
	                                'posicion'=>"<![CDATA[".$posicion_tmp."]]>",
	                                'uposicion'=>$row['pos_uposicion'],
	                                'ignicion'=>$row['pos_ignicion'],
	                                'icono'=>$icono,
									'tipo'=>'Posicion');
	                $row_array[]= $fila;
	            $r_totales ++;
			 
			 
		    }*/
		// ================================================================================================   
		// ===================================== para QTRACS ==============================================
			
			 if ($proveedor == "OT"){
				
			
				$consulta_posiciones = "select a.id as id, CONVERT(varchar(20),a.time,120) as fecha_hora,a.latdecdeg as latitud,
												a.londecdeg as longitud,a.ignition as ignicion,a.city_name, a.city_state,
		                                        a.city_dist, a.city_direct, a.lm_name, a.lm_state, a.lm_dist, 
												a.lm_direct,b.unitno as eco,b.mctnumber as tmc
											from qtposition a
											inner join qtvehicles b on vehicle_id = b.id and b.unitno = '".$_GET['id']."'";
				
				$gmt = (date('O')/100)* -1;
		    	$ajuste = ($gmt*60*60);
		    	
		    	
		             	
		    	
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	$ini_gmt = $timestamp + $ajuste;
				    //$ini_gmt = date("d/m/Y H:i:s",$ini_gmt); // ojo con este formato, puede dar problemas en algunos SERVERS
				    $ini_gmt = date($formato_qt,$ini_gmt); 
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				    $fin_gmt = $timestamp + $ajuste;
				    $fin_gmt = date($formato_qt,$fin_gmt);
				    //$fin_gmt = date("d/m/Y H:i:s",$fin_gmt);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  a.time >= '".$ini_gmt."' and  a.time <= '".
					                                           $fin_gmt."' order by a.time asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and a.time <= '".$fin_gmt."' order by a.time asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and a.time >= '".$ini_gmt."' order by a.time asc";
				}
			    //echo $consulta_posiciones;
			    mssql_select_db($database_proveedor1,$conecta_mssql);
				$row_array= array ();
				$result_posiciones = mssql_query  ($consulta_posiciones,$conecta_mssql);
				$r_totales = 0;
				
				
				
				while ($row = mssql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha_ok = ajuste_gmt($row['fecha_hora'],1);
		                 switch ($tipo_referencia):
				        case 0:
				            $unidad_ubicacion = round ($row['city_dist']/0.48888888,2). " Km ". $row['city_name'].
				                         " , " . $row['city_state'];
				            break;
				        case 1:
				            $unidad_ubicacion = round($row['lm_dist']/0.48888888,2). " Km ". $row['lm_name'].
				                         " , " . $row['lm_state'];
				            break;
				        
				        endswitch;             
						$fila = array ( 'latitud'=>$row['latitud'],
		                                'longitud'=>$row['longitud'],
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$row['ignicion'],
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		// ===================================== para Copiloto ==============================================
			
			 if ($proveedor == "COP"){
				$consulta_nserie = "select veh_nserie from ctg_vehiculos where veh_eco like '%".$_GET['id']."%'";
				$result_nserie = mysql_query($consulta_nserie,$conecta_mysql);
				$row_nserie = mysql_fetch_row($result_nserie);
				$nserie = $row_nserie[0];
			 	
			 	
			 	
			 	 $consulta_posiciones = "select a.id_online as id, CONVERT(varchar(20),a.fechahora,120) as fecha_hora,a.lat as latitud,
												a.lon as longitud,1 as ignicion,a.colonia as col,a.municipio as mun,a.ciudad as cd, 
												a.estado as edo , 
												b.nombre as eco,b.nuid as nserie
											from online a
											inner join moviles_online b on a.movil = b.nuid and (a.tipo = 225 or a.tipo = 1300) and 
											b.nuid = '".$nserie."'";
			//echo $consulta_posiciones;								
				
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	$ini_gmt = $timestamp;
				    $ini_gmt = date($formato_cop,$ini_gmt);
				    //$ini_gmt = date("Y-m-d H:i:s",$ini_gmt);
				    
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				    $fin_gmt = $timestamp;
				    //$fin_gmt = date("Y-m-d H:i:s",$fin_gmt);
				    $fin_gmt = date($formato_cop,$fin_gmt);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  a.fechahora >= '".$ini_gmt."' and a.fechahora <= '".
					                                           $fin_gmt."' order by a.fechahora asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and a.fechahora <= '".$fin_gmt."' order by a.fechahora asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and a.fechahora >= '".$ini_gmt."' order by a.fechahora asc";
				}
			    //echo $consulta_posiciones;
			    mssql_select_db($database_online,$conecta_mssql_cop);
				$result = mssql_query($consulta_posiciones,$conecta_mssql_cop);
				$row_array= array ();
				$result_posiciones = mssql_query  ($consulta_posiciones,$conecta_mssql_cop);
				$r_totales = 0;
				
				
				
				while ($row = mssql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha = $row['fecha_hora'];
				 		//echo $fecha;
				 		$campos_1 = explode (' ',$fecha);
			            $campos_11 = explode ('-',$campos_1[0]);
			            $campos_2 = explode (':',$campos_1[1]);
			
			            $year   = $campos_11[0];
			            $month  = $campos_11[1];
			            $day    = $campos_11[2];
			
			            $hora = $campos_2[0];
			            $min = $campos_2[1];
			            $sec = $campos_2[2];
			
			            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
			            
			            
			            
			            $fecha_ok = date("Y/m/d H:i:s",$timestamp);
		                $unidad_ubicacion = $row['col'].",".$row['mun'].",".$row['ciu'].",".$row['edo'];
				        
		                $latitud = (substr($row['latitud'],0,2).".".substr($row['latitud'],2,strlen($row['latitud'])))*1;
		
						if (substr($row['longitud'],0,1)=='1'){
							$longitud = (substr($row['longitud'],0,3).".".substr($row['longitud'],3,strlen($row['longitud'])))*-1;
						}else{
							$longitud = (substr($row['longitud'],0,2).".".substr($row['longitud'],2,strlen($row['longitud'])))*-1;
						}
		                
		                
		                //echo $unidad_ubicacion."\n<br>";         
						$fila = array ( 'latitud'=>$latitud,
		                                'longitud'=>$longitud,
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$row['ignicion'],
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		
		
		// ===================================== para SEPROMEX ==============================================
			
			 if ($proveedor == "SPX"){
				$consulta_nserie = "select veh_nserie from ctg_vehiculos where veh_eco like '%".$_GET['id']."%'";
				$result_nserie = mysql_query($consulta_nserie,$conecta_mysql);
				$row_nserie = mysql_fetch_row($result_nserie);
				$nserie = $row_nserie[0];
			
			 	
			 	 $consulta_posiciones = "select  b.id_pos as id, fecha ,
			 	 								 b.latitud as latitud,
												 b.longitud as longitud, 1 as ignicion,a.economico as eco,a.id_veh as nserie
											from vehiculos a inner join  posiciones b on a.id_veh = b.id_veh 
											and a.id_veh = '".$nserie."' and b.tipo = 1 ";
											
											
											
			//echo $consulta_posiciones;								
				
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		           
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	$ini_gmt = $timestamp;
				    //$ini_gmt = date($formato_cop,$ini_gmt);
				    $ini_gmt = date("Y-m-d H:i:s",$ini_gmt);
				    
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				    $fin_gmt = $timestamp;
				    $fin_gmt = date("Y-m-d H:i:s",$fin_gmt);
				    //$fin_gmt = date($formato_cop,$fin_gmt);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  fecha >= '".$ini_gmt."' and fecha <= '".
					                                           $fin_gmt."' order by fecha asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and fecha <= '".$fin_gmt."' order by fecha asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and fecha >= '".$ini_gmt."' order by fecha asc";
				}
			    //echo $consulta_posiciones;
			    mysql_select_db($database_rcastores,$conecta_mysql_r);
				$result = mysql_query($consulta_posiciones,$conecta_mysql_r);
				$row_array= array ();
				$result_posiciones = mysql_query($consulta_posiciones,$conecta_mysql_r);
				$r_totales = 0;
				
				
				
				while ($row = mysql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha = $row['fecha'];
				 		//echo $fecha."\n <br>";
				 		$campos_1 = explode (' ',$fecha);
			            $campos_11 = explode ('-',$campos_1[0]);
			            $campos_2 = explode (':',$campos_1[1]);
			
			            $year   = $campos_11[0];
			            $month  = $campos_11[1];
			            $day    = $campos_11[2];
			
			            $hora = $campos_2[0];
			            $min = $campos_2[1];
			            $sec = $campos_2[2];
			
			            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
			            
			            
			            
			            $fecha_ok = date("Y/m/d H:i:s",$timestamp);
		                //$unidad_ubicacion = $row['col'].",".$row['mun'].",".$row['ciu'].",".$row['edo'];
				        
		                $latitud = $row['latitud'];
						$longitud = $row['longitud'];
		                
		                
		                //echo $unidad_ubicacion."\n<br>";         
						$fila = array ( 'latitud'=>$latitud,
		                                'longitud'=>$longitud,
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$row['ignicion'],
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		
		// ===================================== para UDA ==============================================
			
			 if ($proveedor == "UDA"){
				
				$consulta_posiciones = "select HS_COD_LAST_PACKAGE as id, CONVERT(varchar(20),HS_GPS_DATETIME,120) as fecha_hora,
	    							   HS_LATITUDE as latitud,HS_LONGITUDE as longitud,1 as ignicion, HS_CLIENTE_CERCANO as ubicacion,
	    							   HS_ITEM_NUMBER_UNITY as eco,HS_IP as nserie
											from SAVL_HISTORICO
											where  HS_EVENTO like '%(30MIN)%' and HS_ITEM_NUMBER_UNITY = '".$_GET['id']."'";
										
		
				
				 	
		    	
		             	
		    	
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	
				    //$ini_gmt = date("j/m/Y h:i:s A",$timestamp);
				    $ini_gmt = date($formato_uda,$timestamp);
				   
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				   
				    //$fin_gmt = date("j/m/Y h:i:s A",$timestamp);
				    $fin_gmt = date($formato_uda,$timestamp);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  HS_GPS_DATETIME >= '".$ini_gmt."' and  HS_GPS_DATETIME <= '".
					                                           $fin_gmt."' order by HS_GPS_DATETIME asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and HS_GPS_DATETIME <= '".$fin_gmt."' order by HS_GPS_DATETIME asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and HS_GPS_DATETIME >= '".$ini_gmt."' order by HS_GPS_DATETIME asc";
				}
			    //echo $consulta_posiciones;
			   mssql_select_db($database_uda,$conecta_mssql_uda);
				$row_array= array ();
				$result_posiciones = mssql_query  ($consulta_posiciones,$conecta_mssql_uda);
				$r_totales = 0;
				
				
				
				while ($row = mssql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha = $row['fecha_hora'];
				 		$campos_1 = explode (' ',$fecha);
			            $campos_11 = explode ('-',$campos_1[0]);
			            $campos_2 = explode (':',$campos_1[1]);
			
			            $year   = $campos_11[0];
			            $month  = $campos_11[1];
			            $day    = $campos_11[2];
			
			            $hora = $campos_2[0];
			            $min = $campos_2[1];
			            $sec = $campos_2[2];
			
			            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
			            
			            $fecha_ok = date("Y/m/d H:i:s",$timestamp);
				 		
				 		
				 		$unidad_ubicacion = $row['ubicacion'];   
						$fila = array ( 'latitud'=>$row['latitud'],
		                                'longitud'=>$row['longitud'],
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$row['ignicion'],
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		
		
		// ===================================== para TEcholider ==============================================
			
			 if ($proveedor == "TLID"){
				
				$consulta_posiciones = "select Indice as id, CONVERT(varchar(20),Fecha,120) as fecha_hora,
	    							   Latitud as latitud,Longitud as longitud,Ignicion as ignicion, Localidad as ubicacion,
	    							   Unidad as eco,Idf as nserie
											from J2_Posiciones_Hist
											where  Unidad = '".$_GET['id']."'";
										
		
				
				 	
		    	
		             	
		    	
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	
				     //$ini_gmt = date("j/m/Y h:i:s A",$timestamp);
				    $ini_gmt = date($formato_tlid,$timestamp);
				   
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				   
				    //$fin_gmt = date("j/m/Y h:i:s A",$timestamp);
				    $fin_gmt = date($formato_tlid,$timestamp);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  Fecha >= '".$ini_gmt."' and  Fecha <= '".
					                                           $fin_gmt."' order by Fecha asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and Fecha <= '".$fin_gmt."' order by Fecha asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and Fecha >= '".$ini_gmt."' order by Fecha asc";
				}
			    //echo $consulta_posiciones;
			   mssql_select_db($database_tlid,$conecta_mssql_tlid);
				$row_array= array ();
				$result_posiciones = mssql_query  ($consulta_posiciones,$conecta_mssql_tlid);
				$r_totales = 0;
				
				
				
				while ($row = mssql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha = $row['fecha_hora'];
				 		$campos_1 = explode (' ',$fecha);
			            $campos_11 = explode ('-',$campos_1[0]);
			            $campos_2 = explode (':',$campos_1[1]);
			
			            $year   = $campos_11[0];
			            $month  = $campos_11[1];
			            $day    = $campos_11[2];
			
			            $hora = $campos_2[0];
			            $min = $campos_2[1];
			            $sec = $campos_2[2];
			
			            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
			            
			            $fecha_ok = date("Y/m/d H:i:s",$timestamp);
				 		if ($row['Ignicion']){
				                $ignicion = "Encendido";
				     	}else{
				     		    $ignicion = "Apagado";
				     	}
				 		
				 		$unidad_ubicacion = $row['ubicacion'];   
						$fila = array ( 'latitud'=>$row['latitud'],
		                                'longitud'=>$row['longitud'],
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>$unidad_ubicacion,
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$ignicion,
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		
		
		
		// ===================================== para TMW ==============================================
			
			 if ($proveedor == "OTMW"){
				
			 	 $consulta_posiciones = "select ckc_number as id, CONVERT(varchar(20),ckc_updatedon,120) as fecha,
	    							   ckc_latseconds,ckc_longseconds,
	    							   ckc_vehicleignition as ignicion, 
	    							   ckc_comment as ubicacion,
	    							   ckc_tractor as eco, trc_mctid as nserie
									from chekall  inner join tractorprofile on ckc_tractor = trc_number
											where  ckc_tractor = '".$_GET['id']."'";
			//echo $consulta_posiciones;								
				
			
				
		    	
		    	
		             	
		    	
		    	if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
					$campos_1 = explode (' ',$ini_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
		            		    	
			    	$ini_gmt = $timestamp;
			    	//$ini_gmt = date("Y-m-d H:i:s",$ini_gmt);
				    $ini_gmt = date($formato_tmw,$ini_gmt);
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
					$campos_1 = explode (' ',$fin_gmt);
					$campos_11 = explode ('/',$campos_1[0]);
		            
		            
					$campos_2 = explode (':',$campos_1[1]);
		
		            $year   = $campos_11[0];
		            $month  = $campos_11[1];
		            $day    = $campos_11[2];
		
		            $hora = $campos_2[0];
		            $min = $campos_2[1];
		            $sec = $campos_2[2];
		
		            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
				    
				    $fin_gmt = $timestamp;
				    //$fin_gmt = date("Y-m-d H:i:s",$fin_gmt);
				    $fin_gmt = date($formato_tmw,$fin_gmt);
				}else{
					$fin_gmt = '';	
				}
		    	
	           
			   
			    
			    
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_posiciones .= " and  ckc_updatedon >= '".$ini_gmt."' and ckc_updatedon <= '".
					                                           $fin_gmt."' order by ckc_updatedon asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_posiciones .= " and ckc_updatedon <= '".$fin_gmt."' order by ckc_updatedon asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_posiciones .= " and ckc_updatedon >= '".$ini_gmt."' order by ckc_updatedon asc";
				}
			    //echo $consulta_posiciones;
			    mssql_select_db($database_tmw,$conecta_mssql_tmw);
				$result = mssql_query($consulta_posiciones,$conecta_mssql_tmw);
				$row_array= array ();
				$result_posiciones = mssql_query  ($consulta_posiciones,$conecta_mssql_tmw);
				$r_totales = 0;
				
			
				
				while ($row = mssql_fetch_array($result_posiciones)){
				 		$icono = "images/posicion.png";
				 		$fecha = $row['fecha'];
				 		$campos_1 = explode (' ',$fecha);
			            $campos_11 = explode ('-',$campos_1[0]);
			            $campos_2 = explode (':',$campos_1[1]);
			
			            $year   = $campos_11[0];
			            $month  = $campos_11[1];
			            $day    = $campos_11[2];
			
			            $hora = $campos_2[0];
			            $min = $campos_2[1];
			            $sec = $campos_2[2];
			
			            $timestamp = mktime($hora,$min,$sec,$month,$day,$year);
			            
			            $fecha_ok = date("Y/m/d H:i:s",$timestamp);
		                $unidad_ubicacion =$row['ubicacion'];
				                    
						$fila = array ( 'latitud'=>$row['latitud'],
		                                'longitud'=>$row['longitud'],
		                                'unidad'=>$_GET['id'],
		                                'posicion'=>"<![CDATA[".$unidad_ubicacion."]]>",
		                                'uposicion'=>$fecha_ok,
		                                'ignicion'=>$row['ignicion'],
		                                'icono'=>$icono,
										'tipo'=>'Posicion');
		                $row_array[]= $fila;
		            $r_totales ++;
				 
				 
			    }
			 }
		// ================================================================================================
		
		
		
		
		
		
       }
       //-------------------------  extraccion de mensajes en el periodo dado ------------------------


echo $consulta_posiciones ;

       if ($_GET['filtro']=='eventos'){
				if ($_GET['ini'] != '') {
					//$ini_gmt = $_GET['ini']." 00:00:00";
					$ini_gmt = $_GET['ini'];
				}else{
					$ini_gmt = '';
				}
				
				if ($_GET['fin'] != '') {
					//$fin_gmt = $_GET['fin']." 23:59:59";
					$fin_gmt = $_GET['fin'];
				}else{
					$fin_gmt = '';	
				}
				$consulta_mensajes ="select alerta_tipo, alerta_timestamp, alerta_ubicacion, alerta_tractor, alerta_latitud, alerta_longitud
									from sf_alertas
									where alerta_tractor = '".$_GET['id']."'";
				
				if ($ini_gmt != '' and $fin_gmt != ''){
				 	$consulta_mensajes .= " and alerta_timestamp >= '".$ini_gmt."' and alerta_timestamp <= '".
					                                           $fin_gmt."' order by alerta_timestamp asc";
				}
				if ($ini_gmt == '' and $fin_gmt != ''){
					$consulta_mensajes .= " and alerta_timestamp <= '".$fin_gmt."' order by alerta_timestamp asc";
				}
				if ($ini_gmt != '' and $fin_gmt == ''){
					$consulta_mensajes .= " and alerta_timestamp >= '".$ini_gmt."' order by alerta_timestamp asc";
				}
				
				//echo $consulta_mensajes;
				$result_mensajes = mysql_query($consulta_mensajes,$conecta_mysql);
				while ($row = mysql_fetch_array($result_mensajes)){
					switch ($row['ignicion']):
		                    case 1:
		                        $ignicion = 'Encendido';
		                        break;
		                    case 2:
		                        $ignicion = 'Apagado';
		                        break;
		                    default:
		                    	$ignicion = 'Desconocido';
		                    	 break;
		                endswitch;
		             switch ($row['alerta_tipo']):
		                    case 'Entrada Punto':
		                        	$icono = "images/entrada_punto.png";
		                        break;
		                    case 'Deteccion Parada':
		                        	$icono = "images/parada_na.png";
		                        break;
		                    default:
		                    		$icono = "images/evento.png";
		                    	 break;
		                endswitch;   
					$posicion_tmp = $row['alerta_ubicacion'];
					if ($posicion_tmp == ""){
						$posicion_tmp = "ND";
					}
					
		            $fila = array ( 'latitud'=>$row['alerta_latitud'],
		                            'longitud'=>$row['alerta_longitud'],
		                            'unidad'=>$_GET['id'],
		                            'posicion'=>"<![CDATA[".$posicion_tmp."]]>",
		                            'uposicion'=>$row['alerta_timestamp'],
		                            'ignicion'=>$ignicion,
		                            'icono'=>$icono,
									'tipo'=>$row['alerta_tipo']);
		            $row_array[]= $fila;
		            $r_totales ++;
				}
		}	
		
		
		
		
			foreach ($row_array as $llave => $fila) {
			    $uposicion[$llave]  = $fila['uposicion'];
			   
			}
			
			array_multisort($uposicion, SORT_ASC,$row_array);
			
			
			

	
			$ii = 0; 
	       foreach ($row_array as $filas) {
   				$latitud =$filas['latitud'];
				$longitud=$filas['longitud'];
				
			}
			
			
		$consulta_puntos = "select * from ctg_pseguros";
		$result_p = mysql_query($consulta_puntos,$conecta_mysql);
		
		$row_distancias=array ();
		while ($row_p=mysql_fetch_array($result_p)){
			
			$distancia = distancia($latitud,$longitud,$row_p['latitud'],$row_p['longitud']);
			$id = $row_p['nombre'];
			$lat = $row_p['latitud'];
			$lon = $row_p['longitud'];
			$filas_dist = array ( 'latitud'=>$lat,
		                          'longitud'=>$lon,
		                          'distancia'=>$distancia,
		                          'id'=>$id);
		                          
			$row_distancias[]=$filas_dist;
			
		}
		
		
			foreach ($row_distancias as $llave_d => $filas_dist) {
			    $dist_llave[$llave_d]  = $filas_dist['distancia'];
			   
			}
			
			array_multisort($dist_llave, SORT_ASC,$row_distancias);
		$i = 0;	
		$xml="<?PHPxml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
		$xml.="<Elemento>";
		 foreach ($row_distancias as $filas) {
		 		
		 		if ($i<=1){
		 			$xml .= "<Punto>";
			 	    $xml .= "<Nombre><![CDATA[".$filas['id']."]]></Nombre>";
	   				$xml .= "<Latitud>".$filas['latitud']."</Latitud>";
					$xml .= "<Longitud>".$filas['longitud']."</Longitud>";
					$xml .= "<Distancia>".round($filas['distancia'],3)."</Distancia>";
					$xml .= "</Punto>";
					//echo "puntos ".$filas['id']." ".$distancia."\n<br>";
					$i++;
		 		}else{
		 			break;
		 		}
				
			}
			
	$xml.="</Elemento>\n";
	header('Content-Type: text/xml');
	echo $xml;		

		
?>
