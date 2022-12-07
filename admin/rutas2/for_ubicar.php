<?php 

  $id=$_GET["id"];
  $consulta  = " SELECT * FROM tb_rutas
                 WHERE pk_clave_zon=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();

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

  $consulta4  = " SELECT * FROM tb_estados ORDER BY txt_nombre_edo ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();  

  $consulta5  = " SELECT * FROM tb_municipios, tb_estados WHERE pk_clave_edo=fk_clave_edo AND fk_clave_edo=? ORDER BY txt_nombre_mun ASC ";  
  $query5 = $conn->prepare($consulta5);
  $query5->bindParam(1, $estado);
  $estado=$registro6["fk_clave_edo"];
  $query5->execute();   

?>

<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Cambia punto seguro</h1>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   
   
          <div class="row">   
            <div class="col-md-12">
                Nombre:<?php echo $registro["txt_nombre_zon"]?>
            </div>
          </div>
          <div class="row">   
            <div class="col-md-6">
                Latitud:<?php echo $registro["txt_latitud_zon"]?>
            </div>
            <div class="col-md-6">
                Longitud:<?php echo $registro["txt_longitud_zon"]?>
            </div>
          </div>
          <div class="row">
              <div class="col-md-3">
                Estado:  
                <select name="estado" id="estado" class="text-input text form-control" disabled>
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
                <select name="ciudad" id="ciudad" class="text-input text form-control" disabled>
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
          </div>
          <div class="row">   
              <div class="col-md-12 centrado">
                MAPA
              </div>
          </div>  
          <div class="row">   
              <div class="col-md-12 centrado">
                <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">REGRESAR</button></a>
              </div>
          </div>  
          </div>
 
      </div>   
    </div>
  </div>
</div>
