/** Variables */
// Mapas
var mapPol, mapUni, mapStr;
// Datos de barra Poleos
var fechr, dis, p1, p2, tiempo1, tiempo2, totalTime, tiempo, disAcu, tim, velProm, averageSpeed, mkrTime;
var loadingMessage;
// Utilizados en los mapas
var infowindow, imagen, marker, mkPoleo, poleo, ruta1, ruta2, infoPoleo;
var mksPoleo = [];
var mksRuta1 = [];
var mksRuta2 = [];
var poleoPos = [];
var t;
var tablaDetalle = "";
var detalleEconomico = "";
// Barra de Fecha
var fecNow, fec30, fecMes;
var timer_is_on = 0;
var valor_boton;
var c = 0;
var t;
var poleoArr, poleoLen;
var timer_is_on = 0;

function drawMap() {
  // Configuracion MAPS
  var mapPolOp = {
    zoom: 5,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: true,
    fullscreenControl: false,
    controlSize: 30
  }

  var mapUniOp = {
    zoom: 5,
    mapTypeId: google.maps.MapTypeId.HYBRID,
    mapTypeControl: false,
    fullscreenControl: false,
    controlSize: 30
  }

  var x = { lat: 21.117453, lng: -101.6261554 };
  // Mapa Poleo
  mapPolOp.center = new google.maps.LatLng(23.8525998, -101.6652451);
  mapPol = new google.maps.Map(document.getElementById("mapPoleo"), mapPolOp);
  // Mapa Unidad
  mapUniOp.center = new google.maps.LatLng(23.8525998, -101.6652451);
  mapUni = new google.maps.Map(document.getElementById("mapUnidad"), mapUniOp);
  // Crea Markador Oculto en Mapa Unidad
  marker = new google.maps.Marker({
    map: mapUni,
    visible: false
  });
  infowindow = new google.maps.InfoWindow({
    content: "info"
  });
  marker.addListener('click', function () {
    infowindow.open(mapUni, marker);
  });
  // To disable f5
  function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
  /* OR jQuery >= 1.7 */
  $(document).on("keydown", disableF5);
  var input = document.getElementById("noeconomico");
  input.addEventListener("keyup", function (event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      buscarUnidadAhora();
    }
  });
  loadingMessage = document.getElementById('progress');
}

function buscarUnidad() {
  var economico = document.getElementById("noeconomico").value;
  var idUn, latUn, lonUn, fecUn, spcUn, latLng, imagen;
  // Realiza busqueda de Unidad en BD
  if (economico) {
    $.ajax({
      url: "mapasmonitoreo/con_unidad.php?economico=" + economico,
      cache: false
    }).done(function (unidad) {
      unidad = JSON.parse(unidad);
      if (unidad.id) {
        document.getElementById('noeconomico').disabled = true;
        document.getElementById('btnBuscar').disabled = true;
        document.getElementById('btnReloj').disabled = true;
        // Almacena los datos de la unidad 
        idUn = unidad.id;
        latUn = Number(unidad.lat);
        lonUn = Number(unidad.lon);
        fecUn = new Date(unidad.fec);
        spcUn = unidad.spc;
        latLng = {
          lat: latUn,
          lng: lonUn
        };
        // Actualiza Los Mapas
        obtenerUnidad(idUn, latLng, fecUn, spcUn);
        obtenerPoleo(economico);
      } else {
        alert("No se encontro la unidad.");
        document.getElementById('noeconomico').disabled = false;
        document.getElementById('btnReloj').disabled = false;
        document.getElementById('btnBuscar').disabled = false;
      }
    });
  }
}

/** Mapa Unidad 
 * Muestra unidad con detalles en el mapa
*/
function obtenerUnidad(id, latLng, fecUn, spcUn) {
  // Ajusta marcador
  marker.setPosition(latLng);
  marker.setVisible(true);
  // Selecciona el color corespondiende del Icono
  if (fecUn < (fec30)) {
    // Amarillo
    imagen = 'http://maps.google.com/mapfiles/ms/icons/yellow.png';
  } else {
    if (spcUn == 0) {
      // Verde
      imagen = 'http://maps.google.com/mapfiles/ms/icons/green.png';
    } else {
      // Naranja
      imagen = 'http://maps.google.com/mapfiles/ms/icons/orange.png';
    }
  }
  marker.setIcon(imagen);
  // Consulta detalles de la unidad
  $.ajax({
    url: "vehiculos/app_detalle.php?id=" + id,
    cache: false
  }).done(function (html) {
    $('#infovehiculo').val(html);
    infowindow.setContent(" ");
    html = '<div style="font-size: smaller">' + html + '</div>';
    infowindow.setContent(html);
    infowindow.open(mapUni, marker);
  });
  mapUni.setZoom(16);
}

//  Poleos
function obtenerPoleo(economico) {
  console.log(economico)
  if (!economico) {
    var economico = document.getElementById("noeconomico").value;
  }
  if (economico) {
    // Limpia los Poleos si contienen algo 
    limpiarPoleos();
    //  Obtiene fechas de DateRange y las almacena en un arreglo
    var drp = document.getElementById("daterange").value;
    var date = drp.split(" - ");
    var ini = date[0];
    var fin = date[1];
    // La fecha no debe de ser mayor a 3 meses de antiguedad
    if (limitaFec(ini) === true) {
      // Mayor a 30
      return false;
    }
    // Consulta Poleos
    $.ajax({
      url: "mapasmonitoreo/con_poleo.php?id=" + economico + "&ini=" + ini + "&fin=" + fin + "&filtro=trayectoria",
      cache: false
    }).done(function (poleo) {
      poleo = JSON.parse(poleo);
      if (poleo.length > 0) {
        // Habilita Botones de Archivos
        document.getElementById('btnTimer').disabled = false;
        document.getElementById('daterange').disabled = true;
        document.getElementById('btnBuscar').disabled = true;
        document.getElementById('noeconomico').disabled = true;
        document.getElementById('btnReloj').disabled = true;
        detalleEconomico = document.getElementById('noeconomico').value;
        poleoLen = poleo.length;
        // Reinida variables
        p1 = 0;
        dis = 0;
        disAcu = 0;
        tiempo = 0;
        totalTime = 0;
        averageSpeed = 0;
        tablaDetalle = "";
        // Coloca  Marcador de la Unidad en el Ultimo Punto
        latUn = Number(poleo[poleo.length - 1].latitud);
        lonUn = Number(poleo[poleo.length - 1].longitud);
        fecUn = new Date(poleo.fec);
        latLng = {
          lat: latUn,
          lng: lonUn
        };
        // Coloca marcador en Mapa Poleo
        marker.setPosition(latLng);
        marker.setVisible(true);
        poleoArr = poleo;
        c = 0;
        document.getElementById('btnTimer').innerHTML = '<img src="/assets/icons/pause.png" height="15px" width="15px" />';
        document.getElementById('btnTimer').value = 1;
        startCount();
      } else {
        alert("No se encontraron Poleos.");
        document.getElementById('noeconomico').disabled = false;
        document.getElementById('btnBuscar').disabled = false;
        document.getElementById('btnReloj').disabled = false;
      }
    });
  }
}

/** Limpia Mapa de Poleo */
function limpiarPoleos() {
  for (var i = 0; i < mksPoleo.length; i++) {
    mksPoleo[i].setMap(null);
  }
  for (var i = 0; i < mksRuta1.length; i++) {
    mksRuta1[i].setMap(null);
  }
  for (var i = 0; i < mksRuta2.length; i++) {
    mksRuta2[i].setMap(null);
  }
  mksPoleo = [];
  mksRuta1 = [];
  mksRuta2 = [];
}

// Formatea la fecha
function formatoFec(today) {
  var dd = today.getDate();
  var mm = today.getMonth() + 1;
  var yyyy = today.getFullYear();
  var hr = today.getHours();
  var mi = today.getMinutes();
  if (dd < 10) {
    dd = '0' + dd;
  }
  if (mm < 10) {
    mm = '0' + mm;
  }
  if (hr < 10) {
    hr = '0' + hr;
  }
  if (mi < 10) {
    mi = '0' + mi;
  }
  today = mm + '/' + dd + '/' + yyyy + ' ' + hr + ':' + mi;
  return today;
}

/** Compara Fecha Limite
 * 4 meses en General
 * 1 mes para Inhouse
 */
function limitaFec(fec) {
  // Fecha - Hora Actual
  var today = new Date();

  // Fecha - Hora actual menos 4 meses
  var today30 = new Date();
  today30.setMonth(today.getMonth() - 4);

  // Fecha - Hora actual menos 1 meses
  var todayMonth = new Date();
  todayMonth.setMonth(today.getMonth() - 1);
  console.log(todayMonth)

  // Fecha Introducida por Usuario
  fec = new Date(fec);
  const idrol = document.getElementById('idrol').value;
  console.log(idrol);

  if ((fec < today30) || (idrol == 64 & fec < todayMonth)) {

    if (idrol == 64) {
      alert('No se encontraron Poleos.');
    } else {

      alert('Favor de Levantar Ticket.\nPoleo con Mayor a 4 meses de antiguedad.');
    }
    document.getElementById('btnTimer').disabled = false;
    document.getElementById('daterange').disabled = false;
    document.getElementById('btnBuscar').disabled = false;
    document.getElementById('noeconomico').disabled = false;
    document.getElementById('btnReloj').disabled = false;
    return true;
  } else {
    return false;
  }
}

function addMarkerPoleo() {
  if (c < poleoLen) {
    i = c;
    var poleo = poleoArr[c];
    // Almacena los datos del poleo 
    idUn = poleo.unidad;
    latUn = Number(poleo.latitud);
    lonUn = Number(poleo.longitud);
    fecUn = new Date(poleo.fec);
    latLng = {
      lat: latUn,
      lng: lonUn
    };
    var tipoDetalle = "";
    var rendimiento = "";
    var velocidad;

    // Selecciona el icon correspondiente
    if (i == 0) {
      // Verde
      imagen = 'historico/images//mm_20_green.png';
    } else if (i == (poleoLen - 1)) {
      // Rojo
      imagen = 'historico/images//mm_20_red.png';
    } else {
      imagen = 'historico/images/posicion.png';
    }
    // Añade Markers al mapa Poleos
    mkPoleo = new google.maps.Marker({
      map: mapPol,
      visible: true,
      position: latLng,
      title: idUn,
      icon: imagen
    });
    // Configura InfoWindow de Poleos
    // Añade evento informacion del marcador
    infoPoleo = new google.maps.InfoWindow();
    var varPoleo = poleo;

    if (i == 0) {
      // Inicio
      (function (mkPoleo, varPoleo) {
        google.maps.event.addListener(mkPoleo, "click", function (e) {
          infoPoleo.setContent("<strong class='text-success'>INICIO</strong>");
          infoPoleo.open(mapPol, mkPoleo);
        });
      })(mkPoleo, varPoleo);
      tipoDetalle = "Posicion - Inicio";
    } else if (i == (poleoLen - 1)) {
      // Fin
      (function (mkPoleo, varPoleo) {
        google.maps.event.addListener(mkPoleo, "click", function (e) {
          infoPoleo.setContent("<strong class='text-danger'>FIN</strong>");
          infoPoleo.open(mapPol, mkPoleo);
        });
      })(mkPoleo, varPoleo);
      document.getElementById('noeconomico').disabled = false;
      document.getElementById('btnReloj').disabled = false;
      document.getElementById('btnBuscar').disabled = false;
      tipoDetalle = "Posicion - Fin";
      document.getElementById('daterange').disabled = false;
      document.getElementById('btnPDF').disabled = false;
      document.getElementById('btnExcel').disabled = false;
      document.getElementById('btnImprimir').disabled = false;
    } else {
      // Checkpoints
      (function (mkPoleo, varPoleo) {
        google.maps.event.addListener(mkPoleo, "click", function (e) {
          infoPoleo.setContent("<p class='text-warning' style='font-size:smaller;' ><strong>Posición: </strong> " + varPoleo.posicion + " (" + varPoleo.uposicion + "), " + mkPoleo.position + " </p>");
          infoPoleo.open(mapPol, mkPoleo);
        });
      })(mkPoleo, varPoleo);
      tipoDetalle = "Posicion";
    }

    // Añade lineas de ruta al mapa Poleos
    if (i < poleoLen && i != 0) {
      var point = new google.maps.LatLng(latUn, lonUn);
      var point1 = mksPoleo[i - 1].getPosition();
      var points = [point, point1];
      if (i < poleoLen / 2) {
        // LINEA AZUL 1a. Mitad
        ruta1 = new google.maps.Polyline({
          path: points,
          geodesic: true,
          strokeColor: '#0000ff',
          strokeOpacity: 0.75,
          strokeWeight: 3
        });
        ruta1.setMap(mapPol);
        mksRuta1.push(ruta1);
      } else {
        // LINEA ROJA 2a. Mitad
        ruta2 = new google.maps.Polyline({
          path: points,
          geodesic: true,
          strokeColor: '#ff0000',
          strokeOpacity: 0.75,
          strokeWeight: 3
        });
        ruta2.setMap(mapPol);
        mksRuta1.push(ruta2);
      }
    }
    mksPoleo.push(mkPoleo); // Almacena el marcador Poleo
    // Ajusta Mapas
    mapPol.panTo(latLng);
    // Realiza Calculos
    p1 = latLng;
    tiempo1 = new Date(varPoleo.uposicion);
    var speed = 0
    if (i > 0) {
      // Calcula Distancia
      dis = calculaDistancia(p1, p2);
      disAcu += dis;
      // Calcula Tiempo
      tiempo = Math.ceil((tiempo1.getTime() - tiempo2.getTime()) / (1000))
      totalTime += tiempo;
      // Calcula Velocidad Promedio
      speed = Math.round((dis / (tiempo / 3600)) * 100) / 100;
      averageSpeed = Math.round((disAcu / (totalTime / 3600)) * 100) / 100;
    }
    tiempo2 = tiempo1;
    p2 = p1;
    // Barra de Progreso
    loadingPercentage(i, poleoLen);
    rendimiento = (Math.round(dis * 100) / 100) / poleo.comb_consumido;
    tablaDetalle = tablaDetalle + "<tr>" +
      "<td>" + poleo.uposicion + "</td>" +
      "<td>" + poleo.posicion + "</td>" +
      "<td>" + tipoDetalle + "</td>" +
      "<td>" + Math.round(dis * 100) / 100 + " Km </td>" +
      "<td>" + poleo.comb_consumido + " Lts. </td>" +
      "<td>" + rendimiento + " Km/Lts. </td>" +
      "<td>" + convertSeconds(tiempo) + "</td>" +
      "<td>" + speed + " Km/Hr </td>" +
      "</tr>";
    tDetalle.innerHTML = tablaDetalle;
    dateMessage.innerHTML = varPoleo.uposicion;
    distanceMessage.innerHTML = Math.round(dis * 100) / 100 + " Km.";
    distanceacumMessage.innerHTML = Math.round(disAcu * 100) / 100 + " Km.";
    dateMessage.innerHTML = varPoleo.uposicion;
    timeMessage.innerHTML = convertSeconds(totalTime);
    speedMessage.innerHTML = averageSpeed + " Km/h ";
    c = c + 1;
    t = window.setTimeout(addMarkerPoleo, 90);
  } else {
    clearTimeout(t);
    timer_is_on = 0;
    document.getElementById('btnTimer').disabled = true;
  }
}

// Boton  Detalle
var sDetalle = 1;
function muestraDetalles() {
  if (sDetalle == 1) {
    // Muestra
    var boxEco = document.getElementById('noeconomico').value;
    if (!boxEco || boxEco != detalleEconomico) {
      document.getElementById("btnPDF").disabled = true;
      document.getElementById("btnExcel").disabled = true;
      document.getElementById("btnImprimir").disabled = true;
    } else {
      document.getElementById('btnPDF').disabled = false;
      document.getElementById('btnExcel').disabled = false;
      document.getElementById('btnImprimir').disabled = false;
    }
    document.getElementById('stats').style.display = 'block';
    sDetalle = 0;
  } else {
    // Oculta
    document.getElementById('stats').style.display = 'none';
    sDetalle = 1;
  }
}

// LOADING BAR //
function loadingPercentage(currentPoint, pointLen) {
  var percentage = Math.round((currentPoint / (pointLen - 1)) * 100);
  if (isNaN(percentage)) {
    percentage = 0;
  }
  loadingMessage.style.width = percentage + "%";
}

/** CALCULOS **/
function calculaDistancia(point1, point2) {
  var dLatDegrees1 = point1.lat;
  var dLonDegrees1 = point1.lng;
  var dLatDegrees2 = point2.lat;
  var dLonDegrees2 = point2.lng;
  var EARTH_RADIUS_MI = 3963.2263272;
  var PI = 3.14159265358979323846;
  var dDistMiles = 0;
  var ddistkm = 0;
  dDistMiles = Math.sin(dLatDegrees1 * (PI / 180)) * Math.sin(dLatDegrees2 * (PI / 180)) +
    Math.cos(dLatDegrees1 * (PI / 180)) * Math.cos(dLatDegrees2 * (PI / 180)) *
    Math.cos((dLonDegrees1 - dLonDegrees2) * (PI / 180));
  dDistMiles = EARTH_RADIUS_MI * Math.acos(dDistMiles);
  ddistkm = dDistMiles * 1.609344;
  return ddistkm;
}

function convertSeconds(seconds) {
  //find hours
  if (seconds > 3600) {
    hours = Math.round(((seconds / 3600) * 10) / 10) - Math.round((((seconds % 3600) * 10) / 3600) / 10);
    seconds = seconds - (hours * 3600);
    if (hours < 10) {
      hours = "0" + hours + ":";
    } else {
      //hours += " hrs ";
      hours += ":";
    }
  }
  else {
    hours = "00:";
  }
  //find minutes
  if (seconds > 60) {
    minutes = Math.round(((seconds / 60) * 10) / 10) - Math.round((((seconds % 60) * 10) / 60) / 10);
    seconds = seconds - (minutes * 60);
    if (minutes < 10) {
      minutes = "0" + minutes + ":";
    } else {
      minutes += ":";
    }
  }
  else {
    minutes = "00:";
  }
  if (seconds < 10) {
    seconds = "0" + seconds;
  } else {
    seconds += "";
  }
  return hours + minutes + seconds;
  //return hours + minutes + seconds + " secs ";
}

/** Funciones para Exportar */
function generaPDF() {
  // Calcula Fechas
  var fecharango = $('#daterange').val();
  var fechas = fecharango.split("-");
  var vini = fechas[0].trim();
  var vfin = fechas[1].trim();
  var economico = $('#noeconomico').val();
  var vtxr = document.getElementById("txr").value;
  var url = "historico/for_pdf.php?pdf=1";
  // Genera Enlace
  url = url + "&id=" + economico;
  url = url + "&filtro=posiciones";
  url = url + "&txr=" + vtxr;
  url = url + "&ini=" + vini;
  url = url + "&fin=" + vfin;
  window.open(url);
}

function generaExcel() {
  fecharango = $('#daterange').val();
  fechas = fecharango.split("-");
  vini = fechas[0].trim();
  vfin = fechas[1].trim();
  var economico = $('#noeconomico').val();
  var url = "historico/for_excel.php?excel=1";
  url = url + "&id=" + economico;
  url = url + "&filtro=posiciones";
  url = url + "&ini=" + vini;
  url = url + "&fin=" + vfin;
  window.open(url);
}

function generaImpresion() {
  var divToPrint = document.getElementById("statsTable");
  newWin = window.open("");
  newWin.document.write('<html><head><title>Histórico de posiciones</title><link rel="stylesheet" href="librerias/bootstrap/css/bootstrap.min.css"></head><body>');
  newWin.document.write('<p><strong>Reporte Histórico de posiciones, Fecha: 16/08/2019 13:20:20</strong></p>');
  newWin.document.write(divToPrint.outerHTML);
  newWin.print();
  newWin.close();
}

/** Buscar unidad fecha actual - 30 minutos */
function buscarUnidadAhora() {
  // Actualiza Fecha Actual
  fecNow = new Date();
  // Hora actual con 6 horas
  fec30 = new Date();
  fec30.setHours(fecNow.getHours() - 6);
  // Coloca fecha
  var strfec = formatoFec(fec30) + " - " + formatoFec(fecNow);
  document.getElementById("daterange").value = strfec;
  buscarUnidad();
}

/** Conteo
 * Contador utilizado para la animacion de poleos
 */
function startCount() {
  if (timer_is_on == 0) {
    timer_is_on = 1;
    mapPol.setZoom(12);
    addMarkerPoleo();
  }
}

function stopCount() {
  clearTimeout(t);
  timer_is_on = 0;
}

function conTimer() {
  var press = document.getElementById('btnTimer').value;
  if (press == 0) {
    // Presiono para Continuar 
    document.getElementById('btnTimer').innerHTML = '<img src="/assets/icons/pause.png" height="15px" width="15px" />';
    document.getElementById('btnTimer').value = 1;
    startCount();
  } else {
    // Para pausar
    document.getElementById('btnTimer').innerHTML = '<img src="/assets/icons/play.png" height="15px" width="15px" />';
    document.getElementById('btnTimer').value = 0;
    stopCount();
  }
}