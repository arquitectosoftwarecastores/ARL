<div id="map"></div>
<input type="hidden" id="infovehiculo" value="" />
<?php


$codigoblanco = "#67DDDD";
$codigoazul = "#6991FD";
$codigoverde = "#00E64D";
$codigoamarillo = "#FDF569";
$codigomorado = "#8A2BE2";
$economico = $_POST["economico"];

$consulta = " SELECT * FROM tb_remolques WHERE txt_economico_rem = ?";
$query = $conn->prepare($consulta);
$query->bindParam(1, $economico);
$query->execute();
$registro = $query->fetch();
$latitud = $registro["num_latitud_rem"];
$longitud = $registro["num_longitud_rem"];

//$id = $registro['pk_clave_veh'];
//$zonaderiesgo = $registro['num_zonariesgo_veh'];
$id = $registro['pk_clave_rem'];
$zonaderiesgo = 1;
if (strlen($registro['txt_tperdida_veh'])) {
  $color = 2;
  $especial = $registro['num_seguimientoespecial_veh'];
} else {
  $color = 1;
  $especial = $registro['num_seguimientoespecial_veh'];
}
?>



<script>
  var map;
  var latitud = <?php echo $latitud ?>;
  var longitud = <?php echo $longitud ?>;
  var economico = <?php echo $economico ?>;
  var id = <?php echo $id ?>;
  var codigocolor = '';

  function initMap() {

    var myLatLng = {
      lat: latitud,
      lng: longitud
    };

    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 17,
      center: myLatLng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    });

    <?php
    if (strlen($registro['txt_tperdida_veh']))
      //if($registro['fec_posicion_veh'] < (time() - 25*60))
      //if((intval((strtotime(date('Y-m-d H:i:s')) -  strtotime($registro["fec_posicion_veh"]))/60)) > 30)
      echo "color=2;";
    else
      echo "color=1;";
    ?>

    especial = <?php echo $especial ?>;

    switch (color) {
      case 1:
        if (especial == "1") {
          color = "orange";
          codigocolor = "#FF9900";
        } else {
          color = "green";
          codigocolor = "<?php echo $codigoverde ?>";
        }
        break;
      case 2:
        if (especial == "2") {
          //a�adido
          color = "purple";
          codigocolor = "<?php echo $codigomorado ?>";
          break;
        } else {
          color = "yellow";
          codigocolor = "<?php echo $codigoamarillo ?>";
          break;
        }
    }



    icon = "http://maps.google.com/mapfiles/ms/icons/" + color + ".png";

    var marker = new google.maps.Marker({
      position: myLatLng,
      icon: new google.maps.MarkerImage(icon),
      title: '<?php echo $economico; ?>'
    });
    marker.setMap(map);

    infowindow = new google.maps.InfoWindow({
      content: 'Info '
    });

    google.maps.event.addListener(marker, 'click', (function(marker) {
      return function() {
        map.setCenter(marker.getPosition());
        var content = economico;

        $.ajax({
            url: "vehiculos/app_detalle.php?id=" + id,
            cache: false
          })
          .done(function(html) {
            $('#infovehiculo').val(html);
            infowindow.setContent(" ");
            infowindow.setContent($('#infovehiculo').val());
            infowindow.open(map, marker);
          });
      };
    })(marker));


    google.maps.event.trigger(marker, 'click');

    panorama = map.getStreetView();
    panorama.setPosition(myLatLng);
    panorama.setPov( /** @type {google.maps.StreetViewPov} */ ({
      heading: 265,
      pitch: 0
    }));



  } // fin de initmap


  function toggleStreetView(latitud, longitud) {
    var toggle = panorama.getVisible();
    var myLatLng = {
      lat: latitud,
      lng: longitud
    };
    if (toggle == false) {
      panorama.setPosition(myLatLng);
      panorama.setVisible(true);
    } else {
      panorama.setVisible(false);
    }
  }

  //funcion para actualizar el numero de seguimiento
  //a�adido

  function actualizar(id, valor) {
    var button = document.getElementById("btnUpdate");

    var r = confirm("�Estas seguro/a?");
    if (r == true) {
      console.log(valor);
      $.ajax({
        type: 'POST', //aqui puede ser igual get
        url: 'vehiculos/app_actualiza.php', //aqui va tu direccion donde esta tu funcion php
        data: {
          id,
          valor
        }, //aqui tus datos
        success: function(data) {
          //lo que devuelve

          location.reload();
        },
        error: function(data) {
          //lo que devuelve si falla
          alert('la actualizacion fallo' + ' ' + data);

        }
      });
    } else {

    }

  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk ?>&callback=initMap&language=es-MX&region=MX"></script>


<!-- The Modal -->
<div class="modal fade" id="myModal">
  <form class="" action="index.html" method="post" name="form_mant" id="form_mant">

    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="titleman">Mantenimiento</h4>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          Descripción:
          <textarea class="form-control" rows="5" id="descripcion" name="descripcion" maxlength="1000" required></textarea>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          Tipo de Mantenimiento:
          <select class="" name="tipo_mantenimiento" id="tipo_mantenimiento">
            <option value="">.</option>
            <?php
            $conTi = "SELECT * FROM  tb_tiposdefallas";
            $queryTi = $conn->prepare($conTi);
            $queryTi->execute();
            while ($tipo = $queryTi->fetch()) {
            ?>

              <option value="<?php echo $tipo["pk_clave_man"]; ?>"><?php echo $tipo["txt_nombre_man"]; ?></option>

            <?php } ?>
          </select>


          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" onclick="mantenimiento()" name="ecoMant" id="ecoMant" value="">Aceptar</button>


        </div>


      </div>
    </div>

  </form>
</div>