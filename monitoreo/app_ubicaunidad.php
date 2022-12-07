<script>

$(document).ready(function(){

  $("#ubicar").click(function(){
  var encontrado=0;
  for (var i = 0; i < vehiculos.length; i++) {      
    if(vehiculos[i].get("economico")==$("#unidadabuscar").val())
    {
      encontrado=1;
      var latLng = new google.maps.LatLng(vehiculos[i].get("latitud"), vehiculos[i].get("longitud"));

      infowindow.close();
      map.setZoom(18);

      map.setCenter(latLng);
      vehiculos[i].setAnimation(4);    
      vehiculos[i].setAnimation(google.maps.Animation.BOUNCE);
      stopAnimation(vehiculos[i]);  
/*
      unidad=vehiculos[i].get("id");

                      $.ajax({
                        url: "vehiculos/app_detalle.php?id="+unidad,
                        cache: false
                      })
                        .done(function( html ) {                       
                          $('#infovehiculo').val( html );
                          infowindow.setContent(" ");
                          infowindow.setContent($('#infovehiculo').val());
                          infowindow.open(map,vehiculos[i]);
                        });
            */

    }
  }
  if(!encontrado)
    alert("No se encontró la unidad.");

  });
});

</script>