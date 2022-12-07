<?php 

  $id=$_GET["id"];
  $consulta1  = " SELECT * FROM tb_tiposdealertas 
                 WHERE pk_clave_tipa=?";  

  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $id);
  $query1->execute();
  $registro1 = $query1->fetch();

?>

<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Cambia tipo de alerta</h1>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   
      <form action="?seccion=tiposdealertas&amp;accion=actualiza&amp;id=<?php echo $id;?>" id="form1" method="post">
        <fieldset>  
          <div class="row">   
            <div class="col-md-12">
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120"  value="<?php echo $registro1["txt_nombre_tipa"]?>"   />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                Prioridad:
                <select name="prioridad" id="prioridad" class="form-control">
                  <option value="3" <?php if($registro1["num_prioridad_tipa"]==3) echo "selected";?>>Alta</option>
                  <option value="2" <?php if($registro1["num_prioridad_tipa"]==2) echo "selected";?>>Media</option>
                  <option value="1" <?php if($registro1["num_prioridad_tipa"]==1) echo "selected";?>>Baja</option>
                </select>   
            </div>
            <div class="col-md-6">
                Tipo:
                <select name="tipo" id="tipo" class="form-control">
                  <option value="0" <?php if($registro1["num_tipo_tipa"]==0) echo "selected";?>>Monitoreo</option>
                  <option value="1" <?php if($registro1["num_tipo_tipa"]==1) echo "selected";?>>Seguridad</option>
                </select>  
            </div>            
          </div>
          <div class="row">   
            <div class="col-md-12 centrado renglon">
                <button type="submit"  class="btn btn-primary">GUARDAR CAMBIOS</button>
            </div>
          </div>  
          <div class="row">   
              <div class="col-md-12 centrado">
                <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">REGRESAR</button></a>
              </div>
          </div>  
          </fieldset>
        </form>
      </div>   
    </div>
  </div>
</div>
<?php $query1->closeCursor(); ?>