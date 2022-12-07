<?php
//Módulo 2 = Monitoreo
$consulta = "select count(*) as total from monitoreo.tb_usuarios u join monitoreo.tb_modulosxrol r on u.fk_clave_rol = r.fk_clave_rol where r.fk_clave_mod = 2 and pk_clave_usu = " . $_SESSION['id'];
$query = $conn->prepare($consulta);
$query->execute();
$registro = $query->fetch();
$permiso = $registro['total'];

if ($permiso > 0) {
?>
  <style>
    #zonasderiesgo {
      display: none;
    }

    #btnvermapa {
      display: none;
    }

    #map * {
      max-width: none !important;
    }

    #btnverTODO {
      display: none;
    }
  </style>

  <!-- script para actulizar semaforos de procesos
  <script src="scripts/crones.js"></script>
 -->

  <?php
  $latitudcentro = 23;
  $longitudcentro = -102;

  $codigoblanco = "#67DDDD";
  $codigoazul = "#6991FD";
  $codigoverde = "#00E64D";
  $codigoamarillo = "#FDF569";
  $codigomorado = "#8A2BE2";
  $codigorojo = "#FD7466";


  //Consultas
  $consulta = "SELECT COUNT(num_icono_rem) as total 
              FROM tb_remolques 
              WHERE num_icono_rem < 1 AND estatus = 1";
  $query = $conn->prepare($consulta);
  $query->execute();
  $registro = $query->fetch();
  $totalremolquesconperdida = $registro['total'];

  $consulta = "SELECT COUNT(num_icono_rem) as total 
                FROM tb_remolques 
                WHERE num_icono_rem > 0 AND estatus = 1";
  $query = $conn->prepare($consulta);
  $query->execute();
  $registro = $query->fetch();
  $totalremolquessinperdida = $registro['total'];

  $consulta = " SELECT 
                  (SELECT COUNT(*) FROM tb_remolques WHERE num_icono_rem = 1 AND estatus = 1) AS total_rep, 
                  (SELECT COUNT(*) FROM tb_remolques WHERE num_icono_rem = 0 AND estatus = 1) AS total_sin_rep,
                  (SELECT COUNT(*) FROM tb_remolques WHERE num_icono_rem = 2 AND estatus = 1) AS total_hib,
                  (SELECT COUNT(*) FROM tb_remolques WHERE num_icono_rem = 3 AND estatus = 1) AS total_baj,
                  (select count(*) from monitoreo.geocercasporunidad where zonaroja <> 0) as total_eu";
  $query = $conn->prepare($consulta);
  $query->execute();
  $registro = $query->fetch();
  $totalvehiculosconperdida = $registro['total_sin_rep'];
  $totalvehiculossinperdida = $registro['total_rep'];
  $totalvehiculoshibernando = $registro['total_hib'];
  $totalvehiculosbateriabaja = $registro['total_baj'];
  $totalvehiculoseu = $registro['total_eu'];

  $consulta5 = " SELECT 
                    pk_clave_zon, num_latitud_zon, 
                    num_longitud_zon,txt_nombre_zon
                  FROM tb_zonas, tb_detallezonas
                  WHERE fk_clave_tipz=3 AND pk_clave_zon=fk_clave_zon
                  GROUP BY pk_clave_zon, num_latitud_zon,num_longitud_zon,txt_nombre_zon
                  ORDER BY txt_nombre_zon ASC ";
  $query5 = $conn->prepare($consulta5);
  $query5->execute();
  ?>
  <div class="container-fluid fondocolor mt-n3">
    <div class="row">
      <div class="col-md-1  izquierda">
        <h4 class="blanco">MONITOREO</h4>
      </div>
      <div class="col-md-1  centrado blanco" hidden>

        <input type="hidden" value="0" id="valorriesgo" />
      </div>

      <div class="col-md-2  centrado blanco">
        <img src="http://maps.google.com/mapfiles/ms/icons/green.png" width="16px" />
        Remolques (<?php echo $totalvehiculossinperdida ?>)
        <input type="checkbox" value="1" checked id="verde">
        <input type="hidden" value="1" id="valorverde" />
      </div>

      <div class="col-md-2 centrado blanco">
        <img src="http://maps.google.com/mapfiles/ms/icons/yellow.png" width="16px" />
        Remolques (<?php echo $totalvehiculosconperdida ?>)

        <input type="checkbox" value="1" checked id="amarillo">
        <input type="hidden" value="1" id="valoramarillo" />
      </div>

      <div class="col-md-2 centrado blanco">
        <img src="http://maps.google.com/mapfiles/ms/icons/blue.png" width="16px" />
        Remolques (<?php echo $totalvehiculoshibernando ?>)

        <input type="checkbox" value="1" checked id="azul">
        <input type="hidden" value="1" id="valorazul" />
      </div>

      <div class="col-md-2 centrado blanco">
        <img src="http://maps.google.com/mapfiles/ms/icons/red.png" width="16px" />
        Remolques (<?php echo $totalvehiculosbateriabaja ?>)

        <input type="checkbox" value="1" checked id="rojo">
        <input type="hidden" value="1" id="valorrojo" />
      </div>

      <div class="col-md-2 centrado blanco">
        <b>E</b>
        Remolques (<?php echo $totalvehiculoseu ?>)

        <input type="checkbox" value="0" id="frontera">
        <input type="hidden" value="0" id="valorfrontera" />
      </div>

      <div class="col-md-2  centrado blanco" hidden>
        <img src="http://maps.google.com/mapfiles/ms/icons/orange.png" width="16px" />
        Especial (<?php echo $totalvehiculosespecial ?>)
        <input type="checkbox" value="1" checked id="naranja">
        <input type="hidden" value="1" id="valornaranja" />
      </div>
    </div>
  </div>

  <div id="map"></div>
  <input type="hidden" id="infovehiculo" value="" />
  <input type="hidden" id="infoautoridad" value="" />
  <input type="hidden" id="infolatitud" value="" />
  <input type="hidden" id="infolongitud" value="" />
  <input type="hidden" id="infocambio" value="" />

  <script>
    var vehiculos = [];
    var autoridades = [];
    var zonas = [];
    var map;
    var arrLabel = [];
    var label;

    // Nueva funcion para localizar los vehiculos que estan en oficinas
    function verificaroficina(x, y) {
      // console.log("latitud: " + x + " longitud: " + y);
    }

    function initMap() {
      var myLatLng = {
        lat: <?php echo $latitudcentro ?>,
        lng: <?php echo $longitudcentro ?>
      };
      map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.HYBRID
      });
      cargaMarcadores();
      // cargaZonas();
      panorama = map.getStreetView();
    }

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

    function cargaZonas() {
      var coordenadas = [];
      $.getJSON("zonas/json.php", function(json) {
        $.each(json.zonas, function(index, zona) {
          if (zona.tipo == 3)
            color = '#FF0000'; //color para Zona de Riesgo
          else
            color = '#67DDDD'; //color para Zona Segura
          $.each(zona.puntos, function(index, punto) {
            coordenadas.push(new google.maps.LatLng(punto.lat, punto.long));
          });
          var poligonozona = new google.maps.Polygon({
            paths: coordenadas,
            strokeColor: '#000000',
            strokeOpacity: 0.5,
            strokeWeight: 1,
            fillColor: color,
            fillOpacity: 0.5
          });
          zonas.push(poligonozona);
          coordenadas = [];
        }); // fin del each
      }); // fin del getJSON
      toogleZonas(null);
    } // fin del la función cargaZonas
    var myVar = setInterval(actualizaMarcadores, 1800000);

    function actualizaMarcadores(map) {
      $.getJSON("vehiculos/app_actualizaposicion.php?idusuario=<?php echo $_SESSION['id'] ?>" + "&rnd=" + Math.random() * 10000, function(json1) {
        $.each(json1.vehiculos, function(key, data) {
          for (var i = 0; i < vehiculos.length; i++) {
            if (vehiculos[i].get("id") == data.id) {
              $('#infocambio').val(data.id);
              $('#infolatitud').val(data.lat);
              $('#infolongitud').val(data.long);
              var latLng = new google.maps.LatLng($('#infolatitud').val(), $('#infolongitud').val());
              vehiculos[i].setPosition(latLng);
              vehiculos[i].set("latitud", $('#infolatitud').val());
              vehiculos[i].set("longitud", $('#infolongitud').val());
              //**********************
              verificaroficina(data.lat, data.long);
              //**********************
            }
          };
        }); // fin del each
      }); // fin del getJSON
    } // fin de la función actualizaMarcadores




    function actualizaMarcadores2(map) {
      for (var i = 0; i < vehiculos.length; i++) {
        id = vehiculos[i].get("id");
        lat = vehiculos[i].get("latitud");
        long = vehiculos[i].get("longitud");
        var cambio = 0;
        var nuevalat = 0;
        var nuevalong = 0;
        setTimeout(function() {
          $.getJSON("vehiculos/app_actualizaposicion.php?id=" + id + "&latitud=" + lat + "&longitud=" + long + "&" + parseInt(Math.random() * 100), function(json1) {
            $.each(json1.vehiculos, function(key, data) {
              if (data.id != 0) {
                $('#infocambio').val(data.id);
                $('#infolatitud').val(data.lat);
                $('#infolongitud').val(data.long);
              }
            }); // fin del each
          }); // fin del getJSON
        }, 10);
        if ($('#infocambio').val() == 1) {
          var latLng = new google.maps.LatLng($('#infolatitud').val(), $('#infolongitud').val());
          vehiculos[i].setPosition(latLng);
          vehiculos[i].set("latitud", $('#infolatitud').val());
          vehiculos[i].set("longitud", $('#infolongitud').val());
          $('#infocambio').val(0);
        }
      } // fin del for
    } // fin de la función actualizaMarcadores

    function cargaMarcadores() {
      var espscialnp = "";
      var codigocolor = "";
      $.getJSON("vehiculos/json_circuito.php?idusuario=<?php echo $_SESSION['id'] ?>", function(json1) {
        $.each(json1.vehiculos, function(key, data) {
          var latLng = new google.maps.LatLng(data.lat, data.long);
          var color = "";
          switch (Number(data.color)) {
            case 1:
              color = "green";
              codigocolor = "<?php echo $codigoverde ?>";
              espscialnp = "";
              break;
            case 0:
              color = "yellow";
              codigocolor = "<?php echo $codigoamarillo ?>";
              espscialnp = "";
              break;

            case 2:
              color = "blue";
              codigocolor = "<?php echo $codigoazul ?>";
              espscialnp = "";
              break;

            case 3:
              color = "red";
              codigocolor = "<?php echo $codigorojo ?>";
              espscialnp = "";
              break;
          }

          let inUSA = Number(data.frontera)
          let mkLabel = null

          if (inUSA > 0) {
            mkLabel = {
              text: 'E',
              color: 'white'
            }
            inUSA = 1
          }

          // console.log(data.color)
          icon = "http://maps.google.com/mapfiles/ms/icons/" + color + ".png";
          let marker = new google.maps.Marker({
            position: latLng,
            label: mkLabel,
            icon: new google.maps.MarkerImage(icon),
            title: data.economico
          });

          /* Cambio para saber unidades con inmovilizador
          var marker = new google.maps.Marker({
              position: latLng,
              icon: new google.maps.MarkerImage(icon),
              title: data.economico
          });*/

          // console.log(data.color)
          marker.setMap(map);
          marker.set("id", data.id);
          marker.set("economico", data.economico);
          marker.set("latitud", data.lat);
          marker.set("longitud", data.long);
          marker.set("color", data.color);
          marker.set("zonaderiesgo", data.zonaderiesgo);
          marker.set("especial", data.especial);
          marker.set("usa", inUSA);
          //Agregamos la zona de riesgo a la que pertenece
          marker.set("zonaderiesgoreal", data.zonaderiesgoreal);

          let label = new Label({
            map: map,
            color: color,
            codigocolor: codigocolor,
            text: data.economico
          });
          arrLabel.push(label);
          label.bindTo('position', marker, 'position');
          //  label.bindTo('text', marker, 'I');
          vehiculos.push(marker);
          infowindow = new google.maps.InfoWindow({
            content: 'Info '
          });
          google.maps.event.addListener(marker, 'click', (function(marker) {
            return function() {
              map.setCenter(marker.getPosition());
              var content = data.economico;
              $.ajax({
                  url: "vehiculos/app_detalle.php?id=" + data.id,
                  cache: false
                })
                .done(function(html) {
                  $('#infovehiculo').val(html);
                  $('#ecoMant').val(data.economico);
                  $('#titleman').text("Unidad " + data.economico);
                  $('#descripcion').val('');
                  infowindow.setContent(" ");
                  infowindow.setContent($('#infovehiculo').val());
                  infowindow.open(map, marker);
                });
            };
          })(marker));
        });
        // toogleVehiculos();
      });
      // cargaAutoridades();
    }
    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
      for (var i = 0; i < vehiculos.length; i++) {
        vehiculos[i].setMap(map);
      }
    }
    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
      setMapOnAll(null);
    }
    // Shows any markers currently in the array.
    function showMarkers() {
      setMapOnAll(map);
    }

    // Muestra y oculta los marcadores de los vehiculos
    function toogleVehiculos() {
      const frontera = Number($("#valorfrontera").val())
      let v = 0
      let h = 0

      for (var i = 0; i < vehiculos.length; i++) {
        const colorPoint = Number(vehiculos[i].get("color"))
        const inUsa = Number(vehiculos[i].get("usa"))

        let visible

        switch (colorPoint) {
          case 1:
            visible = Number($("#valorverde").val())
            break;

          case 0:
            visible = Number($("#valoramarillo").val())
            break;

          case 2:
            visible = Number($("#valorazul").val())
            break;

          case 3:
            visible = Number($("#valorrojo").val())
            break;
        }

        // Valida Frontera
        if (frontera > 0) {
          if (inUsa <= 0) {
            visible = 0
          }
        }

        // Muestra u Oculta Iconos
        if (visible == 1) {
          vehiculos[i].setVisible(true)

          const idv = "#v" + vehiculos[i].economico
          document.getElementsByClassName(idv).hidden = false
          $(idv).show();
        } else {
          vehiculos[i].setVisible(false)
          const idv = "#v" + vehiculos[i].economico
          $(idv).hide();
        }
      }
    }

    function toogleZonas(map) {
      for (var i = 0; i < zonas.length; i++) {
        zonas[i].setMap(map);
      }
    }

    function toggleStreetView(latitud1, longitud1) {
      var toggle = panorama.getVisible();
      if (toggle == false) {
        var myLatLng2 = {
          lat: latitud1,
          lng: longitud1
        };
        panorama.setPosition(myLatLng2);
        panorama.setPov( /** @type {google.maps.StreetViewPov} */ ({
          heading: 265,
          pitch: 0
        }));
        panorama.setVisible(true);
      } else {
        panorama.setVisible(false);
      }
    }
  </script>

  <script>
    //Oculta o muestra los vehiculos en color amarillo
    $(document).ready(function() {
      $("#amarillo").click(function() {
        if ($("#valoramarillo").val() == 1) {
          $("#valoramarillo").val(0);
          toogleVehiculos();
        } else {
          $("#valoramarillo").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los vehiculos en color morado
    //añadido
    $(document).ready(function() {
      $("#morado").click(function() {
        if (parseInt($("#valormorado").val()) == 1) {
          $("#valormorado").val(0);
          toogleVehiculos();
        } else {
          $("#valormorado").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los vehiculos en color verde
    $(document).ready(function() {
      $("#verde").click(function() {
        if (parseInt($("#valorverde").val()) == 1) {
          $("#valorverde").val(0);
          toogleVehiculos();
        } else {
          $("#valorverde").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los vehiculos en color naranja
    $(document).ready(function() {
      $("#naranja").click(function() {
        if (parseInt($("#valornaranja").val()) == 1) {
          $("#valornaranja").val(0);
          toogleVehiculos();
        } else {
          $("#valornaranja").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los remol
    $(document).ready(function() {
      $("#azul").click(function() {
        if (parseInt($("#valorazul").val()) == 1) {
          $("#valorazul").val(0);
          toogleVehiculos();
        } else {
          $("#valorazul").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra las autoridades
    $(document).ready(function() {
      $("#rojo").click(function() {
        if (parseInt($("#valorrojo").val()) == 1) {
          $("#valorrojo").val(0);
          toogleVehiculos();
        } else {
          $("#valorrojo").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra las autoridades
    $(document).ready(function() {
      $("#frontera").click(function() {
        if (parseInt($("#valorfrontera").val()) == 1) {
          $("#valorfrontera").val(0);
          toogleVehiculos();
        } else {
          $("#valorfrontera").val(1);
          toogleVehiculos();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los vehiculos en color verde
    $(document).ready(function() {
      $("#blanco").click(function() {
        if (parseInt($("#valorblanco").val()) == 1) {
          $("#valorblanco").val(0);
          clearMarkersps();
        } else {
          $("#valorblanco").val(1);
          showMarkersps();
        }
      });
    });
  </script>

  <script>
    //Oculta o muestra los vehiculos en color verde
    $(document).ready(function() {
      $("#riesgo").click(function() {
        if (parseInt($("#valorriesgo").val()) == 1) {
          $("#valorriesgo").val(0);
          toogleZonas(null);
          toogleVehiculos(null);
        } else {
          $("#valorriesgo").val(1);
          toogleZonas(map);
          toogleVehiculos(map);
        }
      });
    });
  </script>

  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmk; ?>&language=es-MX&region=MX"></script>

  <div class="container-fluid fondocolor">
    <div class="row">
      <div class="col-md-1 mt-2 mb-2">
        <select name="economico" id="economico" class="custom-select" style="font-size:12px;min-width: 95px;">

          <option value="0">-</option>
          <?php
          $consulta7 = "  SELECT tr.txt_economico_rem  
                          FROM tb_usuarios tu 
                          INNER JOIN tb_circuitosxusuario tc 
                            ON tu.pk_clave_usu = tc.fk_clave_usu 
                          INNER JOIN tb_remolques tr 
                            ON tc.fk_clave_cir = tr.fk_clave_cir 
                          WHERE 
                            tu.pk_clave_usu = ? AND
                            tr.estatus = 1 
                          ORDER BY tr.txt_economico_rem ASC";
          $query7 = $conn->prepare($consulta7);
          $query7->bindParam(1, $_SESSION["id"]);
          $query7->execute();
          $optionEco = '';

          while ($registro7 = $query7->fetch()) {
            $optionEco .= '<option value="' . $registro7["txt_economico_rem"] . '">' .
              $registro7["txt_economico_rem"] .
              '</option>';
          }
          echo $optionEco;

          ?>
        </select>
      </div>

      <div class="col-md mt-1">
        <div id="muestraubicacion" class="blanco">&nbsp;</div>
      </div>

      <div class="col-md-1 mt-2 dercha">
        <button type="button" class="btn btn-sm btn-default" id="btnverTODO" onclick="verTodo();">VER TODOS</button>
      </div>

      <!--

      <div class="col-md-1" <?php
                            if ($_SESSION["nombrerol"] == "Usuario externo")
                              echo "style='display:none;'";
                            ?>>
        <a href="?seccion=cercanas" target="_blank" hidden>
          <button type="button" class="btn btn-sm btn-default">
            UNIDADES CERCANAS
          </button>
        </a>
      </div>
      <div class="col-md-1 col-xs-08 " <?php
                                        if ($_SESSION["nombrerol"] == "Usuario externo")
                                          echo "style='display:none;'";
                                        ?> hidden>
        <button type="button" class="btn btn-sm btn-default" data-toggle="modal" id="btnmensajesenviados" data-target="#mensajesenviados">MENSAJES</button>
      </div>
      
    </div>
    <div class="row">
      <div class="col-md-4 col-xs-08 ">
        <select name="zonasderiesgo" id="zonasderiesgo" class="text-input text form-control">
          <option value="0">Seleccione una zona de riesgo (<?php echo $totalzonasderiesgo ?>)</option>
          <?php
          $actual = 0;
          while ($registro5 = $query5->fetch()) {
            if ($actual != $registro5["pk_clave_zon"]) {
          ?>
              <option value="<?php echo $registro5['num_latitud_zon'] . "," . $registro5['num_longitud_zon']; ?>"><?php echo $registro5['txt_nombre_zon']; ?></option>
              <?php
              $actual = $registro5["pk_clave_zon"];
            }
          }
              ?>
        </select>
      </div>
      
    -->
    </div>





  </div>



  <div id="procesos" class="row">

  </div>



  <?php
  include('autoridades/app_lista.php');
  include('monitoreo/app_ubicaunidad.php');
  include('monitoreo/app_panelmensajes.php');
  ?>

  <script>
    $("#excel").click(function() {

      var url = "monitoreo/for_excel.php?excel=1";
      window.open(url);

    });
  </script>

  <script>
    $('#zonasderiesgo').on('change', function() {
      var coordenadas = $(this).find(":selected").val().split(",");
      var coordenadaszonaderiesgo = {
        lat: parseFloat(coordenadas[0]),
        lng: parseFloat(coordenadas[1])
      };
      map.setZoom(10);
      map.setCenter(coordenadaszonaderiesgo);
    });
  </script>

  <script>
    $('#btnvermapa').on('click', function() {
      window.open("index.php?seccion=mapa&accion=muestra&economico=" + $("#unidadvermapa").val(), '_blank');
    });

    $('#economico').on('change', function() {
      selectEco(0);
    });

    $('#economicoPrimario').on('change', function() {
      selectEco(1);
    });

    $('#economicoSecundario').on('change', function() {
      selectEco(2);
    });


    function selectEco(numsel) {
      var idunidad;
      var encontrado;
      var selectVal;

      if (numsel == 0) {
        selectVal = $("#economico option:selected").val();
      } else if (numsel == 1) {
        selectVal = $("#economicoPrimario option:selected").val();
      } else if (numsel == 2) {
        selectVal = $("#economicoSecundario option:selected").val();
      }


      $.ajax({
          url: "vehiculos/app_ubicacion.php?id=" + selectVal,
          cache: false
        })
        .done(function(html) {
          $('#muestraubicacion').html(html);
          for (var i = 0; i < vehiculos.length; i++) {
            if (vehiculos[i].get("economico") == selectVal) {

              // Oculta Todos los puntos
              for (let j = 0; j < vehiculos.length; j++) {
                marker = vehiculos[j];
                marker.setVisible(false);
                label = arrLabel[j];
              }

              // Oculta Labels de Marcadores
              $(".mkLabel").hide();

              // Muestra la unidad Seleccionada
              vehiculos[i].setVisible(true);
              map.panTo(vehiculos[i].getPosition());
              map.setZoom(16);
              var btnT = document.getElementById("btnverTODO");
              btnT.style.display = "block";

              const idv = "#v" + vehiculos[i].economico
              $(idv).show();

              // Muestra detalles de la unidad
              google.maps.event.trigger(vehiculos[i], 'click');

              panorama = map.getStreetView();
              //panorama.setPosition(myLatLng);
              panorama.setPov( /** @type {google.maps.StreetViewPov} */ ({
                heading: 265,
                pitch: 0
              }));

              stopAnimation(vehiculos[i]);
              $('#btnvertodo').show();
              //$('#btnvermapa').show();
              //$('#unidadvermapa').val(vehiculos[i].get("economico"));
            }
          } // fin del for
        });
    }



    function stopAnimation(marcador) {
      setTimeout(function() {
        marcador.setAnimation(null);
      }, 3000);
    }
  </script>

  <script>
    // Define the overlay, derived from google.maps.OverlayView
    function Label(opt_options) {
      // Initialization
      this.setValues(opt_options);
      // Label specific
      var span = this.span_ = document.createElement('span');
      span.className = "mkLabel";
      span.id = "v" + this.get('text')
      span.style.cssText = 'position: relative; left: -50%; top: 0px; ' +
        'white-space: nowrap; ' +
        'padding: 2px;  font-weight: bold; font-size:10px; color: ' + this.get('codigocolor') +
        '; text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;';
      var div = this.div_ = document.createElement('div');
      div.appendChild(span);
      div.style.cssText = 'position: absolute; display: none';
    };

    Label.prototype = new google.maps.OverlayView;
    // Implement onAdd
    Label.prototype.onAdd = function() {
      var pane = this.getPanes().overlayLayer;
      pane.appendChild(this.div_);
      // Ensures the label is redrawn if the text or position is changed.
      var me = this;
      this.listeners_ = [
        google.maps.event.addListener(this, 'position_changed',
          function() {
            me.draw();
          }),
        google.maps.event.addListener(this, 'text_changed',
          function() {
            me.draw();
          })
      ];
    };

    // Implement onRemove
    Label.prototype.onRemove = function() {
      this.div_.parentNode.removeChild(this.div_);
      // Label is removed from the map, stop updating its position/text.
      for (var i = 0, I = this.listeners_.length; i < I; ++i) {
        google.maps.event.removeListener(this.listeners_[i]);
      }
    };

    // Implement draw
    Label.prototype.draw = function() {
      var projection = this.getProjection();
      var position = projection.fromLatLngToDivPixel(this.get('position'));
      var div = this.div_;
      div.style.left = position.x + 'px';
      div.style.top = position.y + 'px';
      div.style.display = 'block';
      this.span_.innerHTML = this.get('text').toString();
    };
  </script>

  <script>
    $(document).ready(function() {
      initMap();
    });
  </script>

  <script>
    //funcion para actualizar el numero de seguimiento
    //añadido

    function actualizar(id, valor) {
      var button = document.getElementById("btnUpdate");

      var r = confirm("¿Estas seguro/a?");
      if (r == true) {

        $.ajax({
          type: 'POST', //aqui puede ser igual get
          url: 'vehiculos/app_actualiza.php', //aqui va tu direccion donde esta tu funcion php
          data: {
            id,
            valor
          }, //aqui tus datos
          success: function(data) {
            //lo que devuelve
            alert(data);
            cargaMarcadores();
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



  <script type="text/javascript">
    // Realiza Alta o Baja de Mantenimiento

    function mantenimiento() {

      var economico = document.form_mant.ecoMant.value;
      var descripcion = document.form_mant.descripcion.value;
      var tipo = document.form_mant.tipo_mantenimiento.value;

      if (descripcion == '' || tipo == "") {
        return false;
      }
      $.ajax({
        type: 'POST',
        url: 'mantenimiento/con_mantenimiento.php',
        data: {
          economico,
          descripcion,
          tipo
        }, //aqui tus datos
        success: function(data) {

          $('#btnmant').attr('disabled', true);
          $('#myModal').modal('hide');

        },
        error: function(data) {
          //lo que devuelve si falla
          alert('La actualizacion fallo ' + ' ' + data);

        }
      });

    }

    function verTodo() {
      toogleVehiculos()

      var latLangMx = {
        lat: 23,
        lng: -102
      }
      map.panTo(latLangMx);

      var muUb = document.getElementById("muestraubicacion");
      var btnT = document.getElementById("btnverTODO");

      document.getElementById('economico').selectedIndex = 0

      btnT.style.display = "none";
      $('#muestraubicacion').html('');
      map.setZoom(5);
    }
  </script>
<?php
} else {
?>
  <div class="container">
    <div class="alert alert-warning">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <strong>Su usuario no tiene acceso a este módulo</strong>.
    </div>
  </div>
<?php
}
?>