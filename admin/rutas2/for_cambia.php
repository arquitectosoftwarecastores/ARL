<?php 
  $id=$_GET["id"];
  $consulta  = " SELECT * FROM tb_rutas WHERE pk_clave_rut=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();   
  $consulta3  = " SELECT * FROM tb_zonas WHERE fk_clave_tipz=2 ORDER BY txt_nombre_zon ASC ";  
  $query3 = $conn->prepare($consulta3);
  $query3->execute();   
  $consulta4  = " SELECT * FROM tb_zonas WHERE fk_clave_tipz=2 ORDER BY txt_nombre_zon ASC ";  
  $query4 = $conn->prepare($consulta4);
  $query4->execute();  
  
  function ejemplo(){
      echo "Prueba";
  }
  
?>    
<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Cambiar Parámetros de la Ruta</h1>      
    </div> 
  </div>
  <div class="row"> 
    <div class="col-md-12">   
      <form action="?seccion=rutas&amp;accion=actualiza" id="form1" method="post">
          <div class="row">   
            <div class="col-md-12">
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120" value="<?php echo $registro["txt_nombre_rut"]?>"  />
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">                  
                Origen:  
                <select name="origen" id="origen" class="text-input text form-control">
                  <?php
                    while ($registro3 = $query3->fetch()) {
                      if($registro['fk_clave_zon1']==$registro3["pk_clave_zon"])
                        $seleccionado="selected";
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro3['pk_clave_zon']; ?>" <?php echo $seleccionado; ?>><?php echo $registro3['txt_nombre_zon']; ?></option>
                  <?php } ?>
                </select>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                Destino:<br>
                <select name="destino" id="destino" class="text-input text form-control" >
                  <?php
                    while ($registro4 = $query4->fetch()) {
                      if($registro['fk_clave_zon2']==$registro4['pk_clave_zon'])
                        $seleccionado="selected";
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro4['pk_clave_zon']; ?>" <?php echo $seleccionado; ?>><?php echo $registro4['txt_nombre_zon']; ?></option>
                  <?php } 
                  
                  if ($seleccionado=="selected") 
                  { 
                    echo '<script language="JavaScript"> alert(" Debe introducir una fecha "); </script>'; 
                    exit(); 
                   } 
                  
                  ?>
                </select>
              </div>                
          </div>  
          </form>
          </div>
          <form action id="form1" onsubmit="ejemplo();">
          <div class="row">   
            <div class="col-md-12 centrado">
                <input type="hidden" value="<?php echo $id?>" name="id">
                <button type="submit"  class="btn btn-primary">MODIFICAR EL TRAZADO DE LA RUTA</button>
            </div>
          </div>
          <div class="row">   
            <div class="col-md-12 centrado renglon">
                <input type="hidden" value="<?php echo $id?>" name="id">
                <button type="submit"  class="btn btn-primary">GUARDAR CAMBIOS</button>
            </div>
          </div>  
          <div class="row">   
              <div class="col-md-12 centrado">
          <!--      <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">CANCELAR</button></a> -->
              </div>
          </div> 
        </form>
      </div>   
    </div>
  </div>
</div>