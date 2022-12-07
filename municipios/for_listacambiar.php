<?php

  $consulta6  = "SELECT * FROM tb_municipios WHERE pk_clave_mun=? ";  
  $query6 = $conn->prepare($consulta6);
  $query6->bindParam(1, $ciudad);
  $ciudad=$registro["fk_clave_ciu"];
  $query6->execute();
  $registro6= $query6->fetch();  

  $consulta1  = " SELECT * FROM tb_estados ORDER BY txt_nombre_edo ASC ";  
  $query1 = $conn->prepare($consulta1);
  $query1->execute();   

  $consulta2  = " SELECT * FROM tb_municipios, tb_estados WHERE pk_clave_edo=fk_clave_edo AND fk_clave_edo=? ORDER BY txt_nombre_mun ASC ";  
  $query2 = $conn->prepare($consulta2);
  $query2->bindParam(1, $estado);
  $estado=$registro6["fk_clave_edo"];
  $query2->execute();   

?>

              <div class="col-md-3">
                Estado:  
                <select name="estado" id="estado" class="text-input text form-control" >
                  <?php
                    while ($registro1 = $query1->fetch()) {
                      if($registro1['pk_clave_edo']==$estado)
                        $seleccionado="selected";
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro1['pk_clave_edo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro1['txt_nombre_edo']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-3">
                Ciudad:<br>
                <select name="ciudad" id="ciudad" class="text-input text form-control" >
                  <?php
                    while ($registro2 = $query2->fetch()) {
                      if($registro2['pk_clave_mun']==$registro['fk_clave_ciu'])
                        $seleccionado="selected";
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['pk_clave_mun']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2['txt_nombre_mun']; ?></option>
                  <?php } ?>
                </select>
              </div> 
