 <!-- Fortaleza de contraseñas -->
<script type="text/javascript" src="scripts/pwstrength.js"></script>
<?php 
  $id=$_GET["id"];

  $consulta  = " SELECT * FROM  tb_roles WHERE pk_clave_rol=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();
?>

<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Cambia asignación de modulos al Rol</h1>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   
      <form action="?seccion=roles&amp;accion=actualiza&amp;id=<?php echo $id;?>" id="form1" method="post">
        <fieldset>  
          <div class="row">   
            <div class="col-md-12">
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120"  value="<?php echo $registro["txt_nombre_rol"]?>" />
            </div>
          </div>
          <div class="row">   
          <?php 

            $consulta1  = " SELECT * FROM  tb_modulos ORDER BY txt_nombre_mod ASC";  
            $query1 = $conn->prepare($consulta1);
            $query1->execute();
            while ($registro1 = $query1->fetch()) {

              $consulta2  = " SELECT * FROM  tb_modulosxrol WHERE fk_clave_mod=? AND fk_clave_rol=?";  
              $query2 = $conn->prepare($consulta2);
              $query2->bindParam(1, $registro1["pk_clave_mod"]); 
              $query2->bindParam(2, $id); 
              $query2->execute();
              $cuenta=0;
              while ($registro2 = $query2->fetch()) 
                   $cuenta++;

              ?>
            <div class="col-md-4">
               <p><input type="checkbox" name="modulos[]" value="<?php echo $registro1["pk_clave_mod"] ?>" <?php if($cuenta) echo "checked"?>/> &nbsp;<?php echo $registro1["txt_nombre_mod"]?></p>
            </div>
          <?php
            }
          ?>          
          </div>
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