<?php 

  $vehiculo=$_POST['vehiculo'];
  $from=$_POST['from']; 
  $to=$_POST['to'];


  $consulta1  = " SELECT * FROM tb_vehiculos
                 WHERE txt_economico_veh=?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $vehiculo);  
  $query1->execute();
  $serie=0;  
  while($registro1 = $query1->fetch())
  	 $serie=$registro1["num_serie_veh"];
  if($serie==0)
  {
  	echo "<p>No se encontró el vehículo.</p>";
  	exit();
  }

  $consulta0  = "SELECT * FROM tb_parametros WHERE txt_nombre_par ='ajustegps' ";  
  $query0 = $conn->prepare($consulta0);
  $query0->execute();
  $registro0 = $query0->fetch();
  $ajustegps=$registro0["num_valor_par"];
  $query0->closeCursor();

  $fechainicial=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($from)));
  $fechafinal=date('Y-m-d H:i:s',strtotime($ajustegps.' hour',strtotime($to)));


  if($fechainicial < '2018-11-05 18:26:00'){
    $strSQL  = " SELECT * FROM tb_posiciones_historico WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  }elseif ($fechainicial < '2019-01-17 11:00:00' ){
    $strSQL  = " SELECT * FROM tb_posiciones_historico2 WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";

  }elseif ($fechainicial < '2019-02-01 12:40:00'){
    $strSQL  = " SELECT * FROM tb_posiciones_historico3 WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  } elseif ($fechainicial < '2019-11-05 14:45:00'){
    $strSQL  = " SELECT * FROM tb_posiciones_historico4 WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  } elseif($fechainicial < '2020-02-07 12:00:00'){
    $strSQL  = " SELECT * FROM tb_posiciones_historico5 WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  } else {
    $strSQL  = " SELECT * FROM tb_posiciones WHERE num_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  }



?>
