<?php 

  $id=$_GET["id"];
  $consulta1  = " SELECT * FROM tb_vehiculos WHERE pk_clave_veh=?";  
  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $id);
  $query1->execute();
  $registro1 = $query1->fetch();

  $consulta2  = "SELECT * FROM tb_circuitos ORDER BY txt_nombre_cir ASC";  
  $query2 = $conn->prepare($consulta2);
  $query2->execute();
  
  $consulta3  = "SELECT * FROM informacion_veh WHERE txt_numero_veh = ?";
  $query3 = $conn->prepare($consulta3);
  $query3->bindParam(1,$registro1["txt_economico_veh"]);
  $query3->execute();
  $registro3 = $query3->fetch(); 
 // echo "Tipo de Unidad: ".$registro3["idtipounidad"];
 // echo "Estatus: ".$registro3["status"];
?>

<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Cambia vehículo</h1>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   
      <form action="?seccion=vehiculos&amp;accion=actualiza&amp;id=<?php echo $id;?>" id="form1" method="post">
        <fieldset>  
          <div class="row">   
            <div class="col-md-12">
                Número ecónomico:<input type="text" name="numero" id="numero" class="validate[required] text-input text form-control" size="120" maxlength="120"  value="<?php echo $registro1["txt_economico_veh"]?>" <?php   if(!isset($_SESSION["altaybajadevehiculos"])) echo "readonly"; ?> readonly />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Serie:<input type="text" name="serie" id="serie" class="validate[required] text-input text form-control" size="20" maxlength="20"  value="<?php echo $registro1["num_serie_veh"]?>"
                <?php   if(!isset($_SESSION["altaybajadevehiculos"])) echo "readonly"; ?> />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Circuito:
                <select name="circuito" id="circuito" class="form-control">
                <?php while($registro2 = $query2->fetch())
                      {  
                          if($registro1['fk_clave_cir']==$registro2["pk_clave_cir"])
                            $seleccionado="selected";
                          else
                            $seleccionado="";
                        ?>
                        <option value="<?php   echo $registro2["pk_clave_cir"]; ?>" <?php echo $seleccionado; ?> ><?php echo $registro2["txt_nombre_cir"]; ?></option>
                <?php  } ?>                 
                </select>           
            </div>
          </div>
          <!--Autor: Marco Sánchez   Fecha //07/Septiembre/2017
          Se agregan la linea de abajo para que muestre los campos de tipo camion y el estatus del mismo -->  
          <div class="row">
            <div class="col-md-12">
                Tipo de vehículo:  
                <Select name="tipocamion" id="tipocamion" class="form-control" > 
                    <option VALUE="1" <?php if ($registro3["idtipounidad"]==1){ echo "selected";}?> > Trailer</option>
                    <option VALUE="2" <?php if ($registro3["idtipounidad"]==2){ echo "selected";}?> > Torton</option>
                    <option VALUE="3" <?php if ($registro3["idtipounidad"]>=3){ echo "selected";}?> > Otro</option>
                </Select> 
             </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Estatus del vehículo:  
                <Select name="estatuscamion" id="estatuscamion" class="form-control">
                    <option VALUE="1" <?php if ($registro3["status"]==1){ echo "selected";}?> > Activo </option>                    
                    <option VALUE="0" <?php if ($registro3["status"]==0){ echo "selected";}?> >Inactivo</option>
                </Select> 
             </div>
          </div> 
            
          <div class="row">
            <div class="col-md-12">
              <input type="checkbox"  name="especial" id="especial" <?php if($registro1["num_seguimientoespecial_veh"]) echo "checked" ?> value="1" class="text-input text"/> Con seguimiento especial.
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
<?php $query2->closeCursor(); ?>