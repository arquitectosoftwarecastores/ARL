 <!-- Fortaleza de contraseñas -->
<script type="text/javascript" src="scripts/pwstrength.js"></script>
<?php 

  $consulta1  = "SELECT * FROM tb_roles ORDER BY txt_nombre_rol ASC";  
  $query1 = $conn->prepare($consulta1);
  $query1->execute();

  $consulta2  = "SELECT * FROM tb_empresas ORDER BY txt_nombre_emp ASC";  
  $query2 = $conn->prepare($consulta2);
  $query2->execute();

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
                Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120"   />
            </div>
          </div>
          <div class="row" id="pwd-container">
            <div class="col-md-6">
                Usuario:<input type="text" name="usuario" id="usuario" class="validate[required] text-input text form-control" size="20" maxlength="20"   />
            </div>
            <div class="col-md-6" id="pwd-container">
                Contraseña:<input type="text"  name="password" id="password" class="validate[required] text-input text form-control" size="25" maxlength="25"   />
                <br>
                <div class="pwstrength_viewport_progress"></div>
                <div id="messages" class="col-sm-12"></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                Correo:<input type="text"  name="correo" id="correo" class="validate[required,custom[email]] text-input text form-control" />
            </div>
            <div class="col-md-6">
                <p>&nbsp;</p>
                Usuario activo:&nbsp;<input type="checkbox"  name="activo" id="activo" checked value="1" class="text-input text"/>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Rol:
                <select name="rol" id="rol" class="form-control">
                <?php while($registro1 = $query1->fetch())
                      {  ?>
                        <option value="<?php   echo $registro1["pk_clave_rol"]; ?>" ><?php echo $registro1["txt_nombre_rol"]; ?></option>
                <?php  } ?>                 
                </select>           
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                Empresa:
                <select name="empresa" id="empresa" class="form-control">
                <?php while($registro2 = $query2->fetch())
                      {  ?>
                        <option value="<?php   echo $registro2["pk_clave_emp"]; ?>" ><?php echo $registro2["txt_nombre_emp"]; ?></option>
                <?php  } ?>                 
                </select>           
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