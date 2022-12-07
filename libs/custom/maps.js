const codigoblanco = "#67DDDD"
const codigoazul = "#6991FD"
const codigoverde = "#00E64D"
const codigoamarillo = "#FDF569"
const codigomorado = "#8A2BE2"

function drawMarker (map, lat, lon, color) {
  const coords = results.features[i].geometry.coordinates;
  const latLng = new google.maps.LatLng(lat, lon);
  new google.maps.Marker({
    position: latLng,
    map: map,
  });
}
