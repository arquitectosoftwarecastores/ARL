<?php 

  $vehiculo=$_POST['vehiculo'];
  $from=$_POST['from']; 
  $to=$_POST['to'];

  $consulta1  = " SELECT * FROM tb_remolques WHERE txt_economico_rem=?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $vehiculo);  
  $query1->execute();
  $serie=0;  
  while($registro1 = $query1->fetch())
  	 $serie=$registro1["txt_nserie_rem"];
  if($serie==0)
  {
  	echo "<p>No se encontró el remolque.</p>";
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


    $strSQL  = " SELECT * FROM tb_posiciones WHERE txt_nserie_pos='".$serie."' 
    AND fec_ultimaposicion_pos >= '".$fechainicial."'
    AND fec_ultimaposicion_pos <= '".$fechafinal."' 
    ORDER BY fec_ultimaposicion_pos ASC";
  



?>
