<script>
    // Variables de Mapas
    var mapPol, mapUni, mapStr;
    // Variables datos de barra Poleos
    var fechr, dis, p1, p2, tiempo1, tiempo2, totalTime, tiempo, disAcu, tim, velProm, averageSpeed, mkrTime;
    var loadingMessage;
    // Variables utilizados en los mapas
    var infowindow, imagen, marker, mkPoleo, poleo, ruta1, ruta2, infoPoleo;
    var mksPoleo = [];
    var mksRuta1 = [];
    var mksRuta2 = [];
    var poleoPos = [];
    var t;
    var tablaDetalle = "";
    var detalleEconomico = "";
    var timer_is_on=0;
    var valor_boton;
    var fecNow, fec30;

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
    mapStr = new google.maps.StreetViewPanorama(
        document.getElementById('mapStreet'), {
            position: x,
            pov: {
                heading: 34,
                pitch: 10
            },
            controlSize: 30
        });    
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
    if(economico){
        $.ajax({
            url: "mapasmonitoreo/con_unidad.php?economico=" + economico,
            cache: false
        })
        .done(function (unidad) {           
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
                obtenerStreet(latLng);
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

function obtenerUnidad(id, latLng, fecUn, spcUn) {
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
    if (!economico) {
        var economico = document.getElementById("noeconomico").value;
    }
    if(economico){
        //  Obtiene fechas y las procesa
        var drp = document.getElementById("daterange").value;
        var date = drp.split(" - ");
        var ini = date[0];
        var fin = date[1];
        console.log("inicio: "+ini);
        console.log("fin: "+fin);
        console.log("economico: "+economico);
        // Limpia los Poleos si contienen algo 
        limpiarPoleos();
        $.ajax({
            url: "mapasmonitoreo/con_poleo.php?id=" + economico +"&ini="+ini+"&fin="+fin+"&filtro=trayectoria",
            cache: false
        }).done(function (poleo) {     
            poleo = JSON.parse(poleo);
            console.log(poleo.length);
            if (poleo.length > 0) {
                // Habilita Botones de Archivos
                document.getElementById('btnPDF').disabled = false;
                document.getElementById('btnExcel').disabled = false;
                document.getElementById('btnImprimir').disabled = false;
                document.getElementById('daterange').disabled = true;
                document.getElementById('btnBuscar').disabled = true;
                document.getElementById('noeconomico').disabled = true;
                document.getElementById('btnReloj').disabled = true;
                detalleEconomico = document.getElementById('noeconomico').value;
                var poleoLen = poleo.length;
                p1 = 0;
                dis = 0;
                disAcu = 0;
                tiempo = 0;
                totalTime = 0;
                averageSpeed = 0;
                tablaDetalle = "";
                // Coloca  Marcador de la Unidad en el Ultimo Punto
                latUn = Number(poleo[poleo.length-1].latitud);
                lonUn = Number(poleo[poleo.length-1].longitud);
                fecUn = new Date(poleo.fec);
                latLng = {
                    lat: latUn,
                    lng: lonUn
                };
                marker.setPosition(latLng);
                mapStr.setPosition(latLng);
                marker.setVisible(true);
                for (var i = 0; i < poleoLen; i++) {
                    // Añade Marcadores al Mapa Poleo
                    // Todos Los Poleos en 3 segundos mkrTime = i * (3000/poleoLen); // Tiempo en colocar los poleos
                    mkrTime = i * 200; // Tiempo en colocar los poleos
                    addMarkerPoleo(poleo[i], poleoLen, i, mkrTime);
                }               
            } else {
                alert("No se encontraron Poleos.");
                document.getElementById('noeconomico').disabled = false;
                document.getElementById('btnBuscar').disabled = false;
                document.getElementById('btnReloj').disabled = false;
            }
        });
    }
}

// Set Posicion
function obtenerStreet(latLng) {
    mapStr.setPosition(latLng);
}

function limpiarPoleos(){
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

function formatoFec(today) {
    var dd = today.getDate();
    var mm = today.getMonth()+1; 
    var yyyy = today.getFullYear();
    var hr = today.getHours();
    var mi = today.getMinutes();
    if(dd<10) 
    {
        dd='0'+dd;
    } 
    if(mm<10) 
    {
        mm='0'+mm;
    } 
    if(hr<10) 
    {
        hr='0'+hr;
    }
    if(mi<10) 
    {
        mi='0'+mi;
    }
    today = mm+'/'+dd+'/'+yyyy+' '+hr+':'+mi;
    console.log(today);
    return today;
}

function addMarkerPoleo(poleo, poleoLen, i,timeout) {
    window.setTimeout(function() {
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
        if(i == 0){
            // Verde
            imagen = 'historico/images//mm_20_green.png';
        }else if(i == (poleoLen-1)){
            // Rojo
            imagen = 'historico/images//mm_20_red.png';          
        }else{
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
            (function(mkPoleo, varPoleo) {
                google.maps.event.addListener(mkPoleo, "click", function(e) {
                    infoPoleo.setContent("<strong class='text-success'>INICIO</strong>");
                    infoPoleo.open(mapPol, mkPoleo);
                });
            })(mkPoleo, varPoleo);
            tipoDetalle =  "Posicion - Inicio";
        }else if( i == (poleoLen-1) ){
            // Fin
            (function(mkPoleo, varPoleo) {
                google.maps.event.addListener(mkPoleo, "click", function(e) {
                    infoPoleo.setContent("<strong class='text-danger'>FIN</strong>");
                    infoPoleo.open(mapPol, mkPoleo);
                });
            })(mkPoleo, varPoleo);
            document.getElementById('noeconomico').disabled = false;
            document.getElementById('btnReloj').disabled = false;
            document.getElementById('btnBuscar').disabled = false;
            tipoDetalle =  "Posicion - Fin";
            document.getElementById('daterange').disabled = false;
            }else{
                // Checkpoints
                (function(mkPoleo, varPoleo) {
                    google.maps.event.addListener(mkPoleo, "click", function(e) {
                        infoPoleo.setContent("<p class='text-warning' style='font-size:smaller;' ><strong>Posición: </strong> "+ varPoleo.posicion +" ("+varPoleo.uposicion+") </p>");
                        infoPoleo.open(mapPol, mkPoleo);
                    });
                })(mkPoleo, varPoleo);
                tipoDetalle =  "Posicion";
            }
            // Añade lineas de ruta al mapa Poleos
            if ( i < poleoLen  && i != 0){
                var point = new google.maps.LatLng(latUn,lonUn);
                var point1 = mksPoleo[i-1].getPosition();
                var points=[point, point1];	  
                if (i < poleoLen/2){
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
            }else{                
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
        mapPol.setZoom(12);
        // Realiza Calculos
        p1 = latLng;
        tiempo1 = new Date(varPoleo.uposicion);
        var speed = 0
        if(i > 0){
            // Calcula Distancia
            dis = calculaDistancia(p1,p2);
            disAcu += dis;
            // Calcula Tiempo
            tiempo = Math.ceil((tiempo1.getTime() - tiempo2.getTime())/(1000))
            totalTime += tiempo;
            console.log(tiempo);
            // Calcula Velocidad Promedio
            speed = Math.round((dis / (tiempo/3600))*100)/100;
            averageSpeed = Math.round((disAcu / (totalTime/3600))*100)/100;           
        }
        tiempo2 = tiempo1;
        p2 = p1;        
        // Barra de Progreso
        loadingPercentage(i,poleoLen);
        rendimiento = (Math.round(dis*100)/100) / poleo.comb_consumido;
        tablaDetalle = tablaDetalle + "<tr>" +
                    "<td>"+ poleo.uposicion +"</td>" +
                    "<td>"+ poleo.posicion + "</td>" +
                    "<td>"+ tipoDetalle +"</td>" +
                    "<td>"+ Math.round(dis*100)/100 +" Km </td>" +
                    "<td>"+ poleo.comb_consumido +" Lts. </td>" +
                    "<td>"+ rendimiento +" Km/Lts. </td>" +
                    "<td>"+ convertSeconds(tiempo) +"</td>" +
                    "<td>"+ speed +" Km/Hr </td>" +
                "</tr>";
        tDetalle.innerHTML = tablaDetalle;
        dateMessage.innerHTML=varPoleo.uposicion;
        distanceMessage.innerHTML=Math.round(dis*100)/100 + " Km.";
        distanceacumMessage.innerHTML=Math.round(disAcu*100)/100 + " Km.";
        dateMessage.innerHTML=varPoleo.uposicion;
        timeMessage.innerHTML=convertSeconds(totalTime);
        speedMessage.innerHTML = averageSpeed + " Km/h ";   
    }, timeout);
}

// Boton  Detalle
var sDetalle = 1;
function muestraDetalles(){
    if (sDetalle == 1) {
        // Muestra
        var boxEco = document.getElementById('noeconomico').value;
        if (!boxEco || boxEco != detalleEconomico) {
            document.getElementById("btnPDF").disabled = true;
            document.getElementById("btnExcel").disabled = true;
            document.getElementById("btnImprimir").disabled = true;
        }else{
            document.getElementById('btnPDF').disabled = false;
            document.getElementById('btnExcel').disabled = false;
            document.getElementById('btnImprimir').disabled = false;
        }  
        document.getElementById('stats').style.display = 'block';
        sDetalle = 0;
    }else{
        // Oculta
        document.getElementById('stats').style.display = 'none';
        sDetalle = 1;
    }
}

// LOADING BAR //
function loadingPercentage(currentPoint,pointLen){
	var percentage = Math.round((currentPoint/(pointLen - 1)) * 100);
	if (isNaN(percentage)){
		percentage = 0;
	}
	loadingMessage.style.width = percentage +"%"; 
}

/** CALCULOS **/
function calculaDistancia(point1, point2){
    var dLatDegrees1 = point1.lat;
    var dLonDegrees1 = point1.lng;
    var dLatDegrees2 = point2.lat;
    var dLonDegrees2 = point2.lng;
    var EARTH_RADIUS_MI    = 3963.2263272;
    var PI                 = 3.14159265358979323846;
    var dDistMiles         = 0;
    var ddistkm            = 0;
       dDistMiles = Math.sin (dLatDegrees1* (PI/180)) * Math.sin (dLatDegrees2* (PI/180)) +
                     Math.cos (dLatDegrees1 * (PI/180)) * Math.cos (dLatDegrees2*(PI/180)) *
                     Math.cos ((dLonDegrees1 - dLonDegrees2)*(PI/180));
       dDistMiles = EARTH_RADIUS_MI * Math.acos (dDistMiles);
       ddistkm = dDistMiles * 1.609344;
     return ddistkm;
}

function convertSeconds(seconds) {
	 //find hours
	 if(seconds > 3600) {
		hours = Math.round(((seconds / 3600)*10)/10) - Math.round((((seconds % 3600)*10)/3600)/10);
		seconds = seconds - (hours * 3600);
		if (hours < 10){
			hours = "0"+hours+":";
		}else{ 
		//hours += " hrs ";
		hours += ":";
		}
	 }
	 else {
		hours = "00:";
	 }
	 //find minutes
	 if(seconds > 60) {
		minutes = Math.round(((seconds / 60)*10)/10) - Math.round((((seconds % 60)*10)/60)/10);
		seconds = seconds - (minutes * 60);
		if (minutes < 10){
			minutes = "0"+minutes+":";
		}else{ 
		minutes += ":";
		}
	 }
	 else {
		minutes = "00:";
	 }
	if (seconds < 10){
			seconds = "0"+seconds;
		}else{ 
		seconds += "";
		}
	return hours + minutes + seconds;
	//return hours + minutes + seconds + " secs ";
}

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
    url = url + "&id="+ economico;
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
    url = url + "&id="+economico;
    url = url + "&filtro=posiciones";
    url = url + "&ini=" + vini;
    url = url + "&fin=" + vfin;
    window.open(url);
}

function generaImpresion(){
    var divToPrint=document.getElementById("statsTable");
    newWin= window.open("");
    newWin.document.write('<html><head><title>Histórico de posiciones</title><link rel="stylesheet" href="librerias/bootstrap/css/bootstrap.min.css"></head><body>');
    newWin.document.write('<p><strong>Reporte Histórico de posiciones, Fecha: 16/08/2019 13:20:20</strong></p>');
    newWin.document.write(divToPrint.outerHTML);
    newWin.print();
    newWin.close();
}

function buscarUnidadAhora() {
    // Actualiza Fecha Actual
    fecNow = new Date();
    // Hora actual con 30 Min menos
    fec30 = new Date();
    fec30.setMinutes(fecNow.getMinutes() - 300);
    // Coloca fecha
    var strfec = formatoFec(fec30)+" - "+formatoFec(fecNow);
    document.getElementById("daterange").value = strfec;
    buscarUnidad();
}
</script>