<?php

        include ('../conexion/conexion.php');
        $consulta1  = "SELECT * FROM tb_vehiculos";
                $query1 = $conn->prepare($consulta1);
                $query1->execute();
                $encuentra=0;

                while($registro1 = $query1->fetch())
                        {
                         $encuentra=1;
                         $latitud_ant=$registro1["num_latitud_veh"];
                         $longitud_ant=$registro1["num_longitud_veh"];
                         $fecha_ant=$registro1["fec_posicion_veh"];
                         $zriesgo_ant = $registro1['num_zonariesgo_veh'];
                         $economico= $registro1['txt_economico_veh'];
                         $veh_zpinteres=$registro1['txt_zonapinteres_veh'];
                         $veh_sespecial=$registro1['num_seguimientoespecial_veh'];

                                $tiemp_max_sin_reportar = date('Y-m-d H:i:s', strtotime('-10 minutes'));
                                $dif_minutos =  (intval
                                                   (
                                                      (strtotime($registro1['fec_posicion_veh']) -  
                                                       strtotime($tiemp_max_sin_reportar)
                                                       )/60
                                                    )
                                                  );
                                $dif_minutos_txt = ' ';
                                if ($dif_minutos < 0)
                                       {$dif_minutos_txt = abs($dif_minutos);}
                                else  {$dif_minutos_txt = '';}

                         echo $veh_fec=$registro1['fec_posicion_veh']; echo '********';
                         $timeIn30Minutes = date('Y-m-d H:i:s', strtotime('-10 minutes'));
                         echo $timeIn30Minutes;
                         echo '<>'; $segundos=  (strtotime($veh_fec) -  strtotime($timeIn30Minutes));
                         //echo abs(intval($segundos/60));
			 echo $dif_minutos_txt;
                         if ($registro1['fec_posicion_veh'] < $timeIn30Minutes)
                                       {echo 'no reporta';}
                         else  {echo 'ok';}
                         echo '____________<br>';
                        }

echo 'aqui';

 ?>

