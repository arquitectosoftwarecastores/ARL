<?php
  $consulta  = "SELECT * FROM tb_empresas WHERE pk_clave_emp=? ";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $id=$_GET["id"];
  $query->execute();
  $registro = $query->fetch();
?>

<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <p class="titulo">Consulta <?php echo $elemento;?></p>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   

          <div class="row">   
              <div class="col-md-2">
                <br><strong>DATOS GENERALES:</strong>
              </div>
              <div class="col-md-10">
                <br><hr />
              </div>
          </div>
          <div class="row">   
              <div class="col-md-6">
                Nombre:<input type="text" readonly name="nombre" id="nombre" class="validate[required] text-input text form-control"  size="40" maxlength="40"  value="<?php echo $registro['txt_nombre_emp'];?>" /> 
              </div>
              <div class="col-md-6">
                Dirección:<input type="text" readonly name="direccion" id="direccion" class="validate[required] text-input text form-control"  size="40" maxlength="40"  value="<?php echo $registro['txt_direccion_emp'];?>" /> 
              </div>              
          </div>  
          <div class="row">   
              <div class="col-md-2">
                Colonia:<input type="text" readonly name="colonia" id="colonia" class="validate[required] text-input text form-control"  size="30" maxlength="30"  value="<?php echo $registro['txt_colonia_emp'];?>" /> 
              </div>
              <div class="col-md-2">
                Código Postal:<input type="text" readonly name="cp" id="cp" class="validate[required] text-input text form-control numericOnly"  size="6" maxlength="6"  value="<?php echo $registro['txt_cp_emp'];?>" /> 
              </div>
              <div class="col-md-2">
                Teléfono:<input type="text" readonly name="telefono" id="telefono" class="validate[required] text-input text form-control numericOnly"  size="10" maxlength="10" value="<?php echo $registro['txt_telefono_emp'];?>"  /> 
              </div>              
              <?php include ("municipios/for_listaconsulta.php"); ?>
          </div> 
          <div class="row">   
              <div class="col-md-6">
                Email:<input type="text" readonly name="email" id="email" class="validate[required,custom[email]] text-input text form-control"  size="30" maxlength="30" value="<?php echo $registro['txt_email_emp'];?>"  /> 
              </div>
              <div class="col-md-6">
                RFC:<input type="text" readonly name="rfc" id="rfc" class="text-input text form-control"  size="13" maxlength="13"  value="<?php echo $registro['txt_rfc_emp'];?>" /> 
              </div>
          </div> 
          <div class="row">   
              <div class="col-md-2">
                <br><strong>DATOS BANCARIOS:</strong>
              </div>
              <div class="col-md-10">
                <br><hr />
              </div>
          </div> 
          <div class="row">   
              <div class="col-md-4">
                Nombre del Banco:<input type="text" readonly name="banco" id="banco" class="text-input text form-control"  size="50" maxlength="50"  value="<?php echo $registro['txt_banco_emp'];?>"  /> 
              </div>
              <div class="col-md-2">
                No. de sucursal:<input type="text" readonly name="sucursal" id="sucursal" class="text-input text form-control"  size="10" maxlength="10"  value="<?php echo $registro['txt_sucursal_emp'];?>" /> 
              </div>
              <div class="col-md-6">
                Titular:<input type="text" readonly name="titular" id="titular" class="text-input text form-control"  size="70" maxlength="70"  value="<?php echo $registro['txt_titular_emp'];?>" /> 
              </div>
          </div> 
          <div class="row">   
              <div class="col-md-6">
                No de Cuenta:<input type="text" readonly name="cuenta" id="cuenta" class="text-input text form-control"  size="20" maxlength="20" value="<?php echo $registro['txt_cuenta_emp'];?>"  /> 
              </div>
              <div class="col-md-6">
                CLABE:<input type="text" readonly name="clabe" id="clabe" class="text-input text form-control"  size="20" maxlength="20"  value="<?php echo $registro['txt_clabe_emp'];?>" /> 
              </div>
          </div> 
          <div class="row">   
          <div class="col-md-4"></div>
            <div class="col-md-4 centrado">
                <input name="id" type="hidden" id="id" value="<?php echo $_GET["id"];?>" >
            </div>
            <div class="col-md-4"></div>
          </div>  
          <div class="row">   
            <div class="col-md-4"></div>
              <div class="col-md-4 centrado">
                <a href="javascript:window.history.back();"><button type="button"  class="btn btn-warning">REGRESAR</button></a>
              </div>
            <div class="col-md-4"></div>
          </div>  

      </div>   
    </div>
  </div>
</div>
<p>&nbsp;</p>