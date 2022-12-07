<?php 

  $consulta1  = "SELECT * FROM tb_circuitos ORDER BY txt_nombre_cir ASC";  
  $query1 = $conn->prepare($consulta1);
  $query1->execute();

?>
<!-- Modal -->
<div id="nuevo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agrega <?php echo $elemento;?></h4>
      </div>
      <div class="modal-body">


      <form action="?seccion=<?php echo $seccion;?>&amp;accion=agrega" id="form1" method="post">
        <fieldset>   
          <div class="row">   
            <div class="col-md-12">
                Número ecónomico:<input type="text" name="numero" id="numero" class="validate[required] text-input text form-control" size="120" maxlength="120"   />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                No de Serie:<input type="text" name="serie" id="serie" class="validate[required] text-input text form-control" size="20" maxlength="20"   />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Circuito:
                <select name="circuito" id="circuito" class="form-control">
                <?php while($registro1 = $query1->fetch())
                      {  ?>
                        <option value="<?php   echo $registro1["pk_clave_cir"]; ?>" ><?php echo $registro1["txt_nombre_cir"]; ?></option>
                <?php  } ?>                 
                </select>           
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                <!--Autor: Marco Sánchez   Fecha //07/Septiembre/2017
                Se agregan la linea de abajo para que muestre los campos de tipo camion -->  
                Tipo de vehículo:  
                <Select name="tipocamion" id="tipocamion" class="form-control"> 
                    <option VALUE="1">Trailer</option>
                    <option VALUE="2">Torton</option>
                    <option VALUE="3">Otro</option>
                </Select> 
             </div>
          </div>
          </div>    
          <div class="row">
            <div class="col-md-12">
              <input type="checkbox"  name="especial" id="especial" value="1" class="text-input text"/> Con seguimiento especial.
            </div>
          </div>          
          <div class="row">   
            <div class="col-md-12">
                <p>&nbsp;</p>
                <button type="submit" id="agrega"  class="btn btn-primary">AGREGAR</button>
                <?php   include ("general/for_filtros.php"); ?>               
            </div>
          </div>   
          </fieldset>
        </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

<?php $query1->closeCursor(); ?>