 <!-- Fortaleza de contraseñas -->
 <script type="text/javascript" src="scripts/pwstrength.js"></script>
 <?php

  $id = $_GET["id"];
  $consulta1  = " SELECT * FROM tb_usuarios
                 WHERE pk_clave_usu=?";

  $query1 = $conn->prepare($consulta1);
  $query1->bindParam(1, $id);
  $query1->execute();
  $registro1 = $query1->fetch();


  $consulta2  = "SELECT * FROM tb_roles ORDER BY txt_nombre_rol ASC";
  $query2 = $conn->prepare($consulta2);
  $query2->execute();


  $consulta3  = "SELECT * FROM tb_empresas ORDER BY txt_nombre_emp ASC";
  $query3 = $conn->prepare($consulta3);
  $query3->execute();

  ?>

 <div class="container">
   <div class="row">
     <div class="col-md-12">
       <h1>Modifica Usuario</h1>
     </div>
   </div>

   <div class="row">
     <div class="col-md-12">
       <form action="?seccion=usuarios&amp;accion=actualiza&amp;id=<?php echo $id; ?>" id="form1" method="post">
         <fieldset>
           <div class="row">
             <div class="col-md-12">
               Nombre:<input type="text" name="nombre" id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120" value="<?php echo $registro1["txt_nombre_usu"] ?>" />
             </div>
           </div>
           <div class="row">
             <div class="col">
               Usuario
               <input type="text" name="usuario" id="usuario" class="form-control" size="20" maxlength="20" value="<?php echo $registro1["txt_usuario_usu"] ?>" required />
             </div>

             <div class="col">
               Contraseña
               <input type="password" name="contrasena" id="contrasena" class="form-control" size="20" maxlength="20" value="<?php echo $registro1["txt_contrasena_usu"] ?>" required />
             </div>

           </div>
           <div class="row">
             <div class="col-md-6">
               Correo
               <input type="text" name="correo" id="correo" class="form-control" size="120" maxlength="120" value="<?php echo $registro1["txt_email_usu"] ?>" required />
             </div>
             <div class="col-md-6">
               <p>&nbsp;</p>
               Usuario activo:&nbsp;<input type="checkbox" name="activo" id="activo" <?php if ($registro1['num_activo_usu'] == 1) echo " checked "; ?>value="1" class="text-input text" />&nbsp;&nbsp;

               <!--
               Usuario maestro:&nbsp;<input type="checkbox" name="maestro" id="maestro" <?php if ($registro1['maestro'] == 1) echo " checked "; ?> value="1" class="text-input text" />&nbsp;&nbsp;
               Acceso externo:&nbsp;<input type="checkbox" name="acceso_externo" id="acceso_externo" <?php if ($registro1['acceso_externo'] == 1) echo " checked "; ?> value="1" class="text-input text" />
              -->
             </div>
           </div>
           <div class="row">
             <div class="col-md-12">
               Rol:
               <select name="rol" id="rol" class="form-control">
                 <?php while ($registro2 = $query2->fetch()) {
                    if ($registro1['fk_clave_rol'] == $registro2["pk_clave_rol"])
                      $seleccionado = "selected";
                    else
                      $seleccionado = "";
                  ?>
                   <option value="<?php echo $registro2["pk_clave_rol"]; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["txt_nombre_rol"]; ?></option>
                 <?php  } ?>
               </select>
             </div>
           </div>
           <div class="row mt-3">
             <div class="col">
               <h5>Circuitos</h5>
               <hr />
             </div>
           </div>
           <div class="row">
             <div class="col">
               <?php
                $consulta4  = " SELECT * FROM  tb_circuitos ORDER BY txt_nombre_cir ASC";
                $query4 = $conn->prepare($consulta4);
                $query4->execute();
                while ($registro4 = $query4->fetch()) {

                  $consulta5  = " SELECT * FROM tb_circuitosxusuario WHERE fk_clave_cir=? and fk_clave_usu=?";
                  $query5 = $conn->prepare($consulta5);
                  $query5->bindParam(1, $registro4["pk_clave_cir"]);
                  $query5->bindParam(2, $registro1["pk_clave_usu"]);
                  $query5->execute();
                  $cuenta = 0;
                  while ($registro5 = $query5->fetch())
                    $cuenta++;

                ?>
                 <div class="col-md-3">
                   <input type="checkbox" name="circuitos[]" value="<?php echo $registro4["pk_clave_cir"] ?>" <?php if ($cuenta) echo "checked" ?> /> &nbsp;<?php echo $registro4["txt_nombre_cir"] ?>
                 </div>
               <?php
                }
                ?>
               <hr>
             </div>
           </div>
           <div class="row">
             <div class="col-md-12 centrado renglon">
               <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
             </div>
           </div>
           <div class="row">
             <div class="col-md-12 centrado">
               <a href="#" onclick="history.go(-1); return false;"><button type="button" class="btn btn-warning">REGRESAR</button></a>
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