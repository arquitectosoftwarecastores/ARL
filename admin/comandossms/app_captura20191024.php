<?php
  $consulta1  = "SELECT txt_economico_veh FROM tb_vehiculos ORDER BY txt_economico_veh ASC";  
  $query1 = $conn->prepare($consulta1);
  $query1->execute();

  $consulta2 = "SELECT * FROM tb_tiposdemensajessms ORDER BY txt_nombre_tipm ASC";  
  $query2 = $conn->prepare($consulta2);
  $query2->execute();

?>
<form id="form1" action="?seccion=comandossms&amp;accion=envia" method="post">
<div class="container-fluid"> 
  <div class="container">
      <div class="row" id="mensajecodigo" style="display:none">  
       <div class="alert alert-danger">
          <strong>El código de confirmación es incorrecto.</strong> Intente nuevamente.
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Ecónomico:</strong>
        </div>
        <div class="col-md-6 izquierda">
          <select name="economico" id="economico" class="form-control">
          <option value="0">Seleccione un No Económico</option> 
          <?php   
            while ($registro1 = $query1->fetch()) 
            {
          ?>
            <option value="<?php echo $registro1["txt_economico_veh"]?>"><?php echo $registro1["txt_economico_veh"]?></option> 
          <?php 
            } 
          ?>    
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-6">
           <div id="lista"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Tipo de mensaje:</strong>
        </div>
        <div class="col-md-6 izquierda">
          <select name="mensaje" id="mensaje" class="form-control">
            <?php   
              while ($registro2 = $query2->fetch()) 
              {
            ?>
              <option value="<?php echo $registro2["pk_clave_tipm"]?>"><?php echo $registro2["txt_nombre_tipm"]?></option> 
            <?php 
              } 
	      $query1 -> closeCursor();
              $query2 -> closeCursor();
            ?>    
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Mensaje a enviar:</strong>
        </div>
        <div class="col-md-6 izquierda">
          <textarea class="validate[required] form-control" rows="6" name="comentario"></textarea>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 derecha">
          <strong>Código de confirmación:</strong>
        </div>
        <div class="col-md-6 izquierda">
            <input type="password" name="codigo" id="codigo" class="validate[required] form-control"/>
        </div>
      <div class="row">
        <div class="col-md-12 centrado">
          <button type="submit" class="btn btn-primary confirmaenviar" id="btnEnviar">ENVIAR MENSAJE</button>
        </div>
      </div>
  </div>
</div>
<input type="hidden" value="0" id="numerodevehiculos" name="numerodevehiculos">
<input type="hidden" value="0" id="estatus" name="estatus">
</form>

<script>
    
    var contador=0;
    var cuenta=0;
    $('#economico').change(function() {
      economico=$( "#economico option:selected" ).val();
      if(economico!="0")
      {
        var agregado=0;
   
        $(".vehiculos").each(function() {
            if(this.value==economico) {
             alert( "Ya se agregó el No Económico: "+economico);
             agregado=1;
            }
        });

        if(!agregado & cuenta == 0)
        {

          $("#lista").append('<input type="hidden" class="vehiculos" name="vehiculo'+contador+'" id="vehiculo'+contador+'" value="'+economico+'"/>');
          $("#lista").append('<button style="margin:5px;" type="button" class="btn btn-xs btn-danger" onclick="quita('+contador+')" id="btn'+contador+'" ><span class="glyphicon glyphicon-remove"></span> '+economico+'</button>');
          contador++;
          $('#numerodevehiculos').val(contador);
          cuenta++;
        }
      }

    });

    function quita(valor) {
      $("#vehiculo"+valor).remove();
      $("#btn"+valor).remove();
      cuenta=0;
      contador=0;
    };
 

     $('.confirmaenviar').click(function(){

        if(cuenta>0)
        {
          
          idmensaje=$( "#mensaje option:selected" ).val();
          codigo=$( "#codigo" ).val();
          $.ajax({
            async: false,
            url:"admin/comandossms/app_revisacodigo.php?idmensaje="+idmensaje+"&codigo="+codigo,
            success:function(html)
            {
              if(html=="1")
                $("#estatus").val(1); 
            }
          });

          var coment = $('#form-validation-field-0').val();
          if (coment.lenght <= 1) {
            return false;
          }
        

          if($("#estatus").val() == 1)
          {
            var r=confirm("¿Esta seguro que desea enviar este mensaje?");
            if (r)
            {
                if($( "#mensaje option:selected" ).text() == "Reinicializar Dispositivo" || $( "#mensaje option:selected" ).text() == "Resetear Dispositivo" ){
                  if(!confirm("El envio de este comando puede afectar el funcionamiento del Dispositivo de manera Temporal. ¿Desea Continuar?")){
                    return;
                  }
                  
                }
                
                if($( "#mensaje option:selected" ).text() == "Activar Paro de Motor" || $( "#mensaje option:selected" ).text() == "Activar alarma"){
                  if(!confirm("ATENCION!! Este comando debera enviarse bajo condiciones controladas de la Unidad . ¿Desea Continuar?")){
                    return;
                  }
                  
                }

                /** Crea Array Mensaje y almacena datos de formulario */
                var arrMsj = {};
                arrMsj.eco = $('#vehiculo0').val();
                arrMsj.tip = $('#mensaje option:selected').val();
                arrMsj.com = $('#form-validation-field-0').val();
                arrMsj.usr = '<?php echo $_SESSION['id'] ?>';
                
                
                /** Indica por que metodo será enviado y preparará el comando 
                  * Se almacena los datos a enviar en un Array
                  */
                 $.ajax({
                      type: 'GET',
                      data: { array: arrMsj },
                      url: 'admin/comandossms/app_preparacmd.php',
                      async: false,
                      success: function(data){
                        cadJson  = JSON.parse(data);
                        
                        arrMsj.protocolo = cadJson.protocolo;
                        arrMsj.puerto = cadJson.puerto;
                        arrMsj.imei = cadJson.imei;
                        arrMsj.lat = cadJson.lat;
                        arrMsj.lon = cadJson.lon;
                        arrMsj.pos = cadJson.pos;
                        arrMsj.cmd = cadJson.cmd;
                        
                      },error:function(data){
                        alert('Error de conexion. '+ data);
                      }
                    });

                    /** Verifica el tipo de protocolo y realiza el proceso de envio
                     * U (UDP): Realiza el envio por el metodo antiguo mediante (con el formulario) PHP
                     * T (TCP): Envia el comando preparado
                     */ 
                    if (arrMsj.protocolo === 'U') {

                      // Procede a enviar comando
                      return true;

                    } else if (arrMsj.protocolo === 'T') {
                      const dir = 'http://69.172.241.230:' + arrMsj.puerto + '/comando';
                      
                      // Envia comando 
                      $.ajax({
                        type: 'GET',
                        url: dir,
                        data: { msj: arrMsj },
                        async: false,
                        success: function(data){
                          cadJson  = JSON.parse(data);
                          // Verifica la respuesta del servidor 
                          console.log(cadJson);
                          if (cadJson.res === '0OK') {
                            
                            // Se envio el comando y redirecciona a pagina de comandos
                            location.href = "?seccion=mensajes&accion=lista";
                            
                            document.getElementById("btnEnviar").disabled = true;
                            
                          }
                          
                        },error:function(data){
                          json = JSON.parse(data);
                          alert('Error de conexion. '+ json);
                        }
                      });
                    }
            } 
            else
            {
              return false;
            }
          } 
          else
          {
            $("#mensajecodigo").show(); 
            return false;
          }

          
        
        }
        else
        {
          alert("Agregue al menos un No económico");
          return false;
        }

    });

</script>
