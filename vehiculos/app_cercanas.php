         <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Unidad</th>
                <th>Fecha-Hora</th>
                <th>Posición</th>
                <th>Ignición</th>
              </tr>
            </thead>
          <?php 
            $consulta1  = " SELECT *, txt_economico_veh, num_latitud_veh, num_longitud_veh, SQRT(
                  POWER(69.1 * (num_latitud_veh - ?), 2) +
                  POWER(69.1 * (? - num_longitud_veh) * COS(num_latitud_veh / 57.3), 2)) AS distance
              FROM tb_vehiculos ORDER BY distance LIMIT 5;";  
            $query1 = $conn->prepare($consulta1);
            $query1->bindParam(1, $latitud);  
            $query1->bindParam(2, $longitud);      
            $query1->execute();
            while($registro1 = $query1->fetch()) { 
                if($registro1["distance"]>0)
                {
                ?>
                    <tr>
                      <td class="derecha">
                        <strong><?php echo $registro1["txt_economico_veh"]?> a <?php echo sprintf('%0.2f',$registro1["distance"])?> Km.</strong>
                      </td>
                      <td>
                        <?php echo date('d/m/Y H:i:s',strtotime($registro1["fec_posicion_veh"]))?>
                      </td>
                      <td>
                        <?php echo $registro1["txt_posicion_veh"]?>
                      </td>
                      <td>
                        <?php if ($registro['num_ignicion_veh']==1) echo "Encendido"; else echo "Apagado";?>
                      </td>
                    </tr>
                <?php
              }
            }
          ?>
          </table>
<?php $query1->closeCursor(); ?>