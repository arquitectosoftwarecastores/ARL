<?php 

  $id=$_GET["id"];
  $consulta  = " SELECT * FROM tb_autoridades
                 WHERE pk_clave_aut=?";  
  $query = $conn->prepare($consulta);
  $query->bindParam(1, $id);
  $query->execute();
  $registro = $query->fetch();

  $consulta6  = "SELECT * FROM tb_municipios WHERE pk_clave_mun=? ";  
  $query6 = $conn->prepare($consulta6);
  $query6->bindParam(1, $ciudad);
  $ciudad=$registro["fk_clave_mun"];
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


    <!-- Lista de los municipios -->
    <script src="scripts/listamunicipios.js"></script>

    
<div class="container">
  <div class="row">    
    <div class="col-md-12">
      <h1>Ver mapa de autoridad</h1>      
    </div> 
  </div>

  <div class="row"> 
    <div class="col-md-12">   
          <div class="row">   
            <div class="col-md-12">
                Nombre:<input type="text" name="nombre" readonly id="nombre" class="validate[required] text-input text form-control" size="120" maxlength="120" value="<?php echo $registro["txt_nombre_aut"]?>"  />
            </div>
          </div>
          <div class="row">   
            <div class="col-md-6">
                Teléfono 1:<input type="text" name="telefono1" readonly id="telefono1" class="validate[required] text-input text form-control" size="20" maxlength="20" value="<?php echo $registro["txt_telefono1_aut"]?>"   />
            </div>
            <div class="col-md-6">
                Teléfono 2:<input type="text" name="telefono2" readonly id="telefono2" class="text-input text form-control" size="20" maxlength="20"  value="<?php echo $registro["txt_telefono2_aut"]?>" />
            </div>
          </div>
          <div class="row">   
            <div class="col-md-6">
                Latitud:<input type="text" name="latitud" id="latitud" readonly class="validate[required] text-input text form-control" size="30" maxlength="30"  value="<?php echo $registro["num_latitud_aut"]?>" />
            </div>
            <div class="col-md-6">
                Longitud:<input type="text" name="longitud" id="longitud" readonly class="validate[required] text form-control" size="30" maxlength="30" value="<?php echo $registro["num_longitud_aut"]?>"  />
            </div>
          </div>
          <div class="row">
              <div class="col-md-6">
                Estado:  
                <select name="estado" id="estado" class="text-input text form-control" disabled>
                  <?php
                    while ($registro1 = $query1->fetch()) {
                      if($registro1['pk_clave_edo']==$estado)
                        {
                          $seleccionado="selected";
                          $estado=$registro1['txt_nombre_edo'];
                        }
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro1['pk_clave_edo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro1['txt_nombre_edo']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-6">
                Ciudad:<br>
                <select name="ciudad" id="ciudad" class="text-input text form-control" disabled>
                  <?php
                    while ($registro2 = $query2->fetch()) {
                      if($registro2['pk_clave_mun']==$registro['fk_clave_mun'])
                        {
                          $seleccionado="selected";
                          $ciudad=$registro2['txt_nombre_mun'];
                        }
                      else
                        $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['pk_clave_mun']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2['txt_nombre_mun']; ?></option>
                  <?php } ?>
                </select>
              </div>                
          </div>  
          </div>
          <div class="row">   
              <div class="col-md-6 centrado">
                <a href="#" onclick="history.go(-1); return false;"><button type="button"  class="btn btn-warning">REGRESAR</button></a>
              </div>
              <div class="col-md-6 centrado">
                <button type="button"  class="btn btn-primary" onclick="toggleStreetView();">VISTA DE CALLE</button>                
              </div>
          </div> 
      </div>   
    </div>
  </div>
</div>

<div id="map" ></div>

    <script>

    var panorama;

    function initMap() {
      var myLatLng = {lat: <?php echo $registro["num_latitud_aut"]?>, lng: <?php echo $registro["num_longitud_aut"]?>};

      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.HYBRID
      });

      var contentString = "<?php echo $registro["txt_nombre_aut"]?>, <?php echo $registro["txt_telefono1_aut"]?>, <?php echo $registro["txt_telefono2_aut"]?>, <?php echo $ciudad?>, <?php echo $estado?>";

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: 'Autoridad'
      });

      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });

      infowindow.open(map,marker);


      // We get the map's default panorama and set up some defaults.
      // Note that we don't yet set it visible.
      panorama = map.getStreetView();
      panorama.setPosition(myLatLng);
      panorama.setPov(/** @type {google.maps.StreetViewPov} */({
        heading: 265,
        pitch: 0
      }));



    }

    function toggleStreetView() {
      var toggle = panorama.getVisible();
      if (toggle == false) {
        panorama.setVisible(true);
      } else {
        panorama.setVisible(false);
      }
    }

    </script>


         <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&libraries=places&callback=initMap"></script>