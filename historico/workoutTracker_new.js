// *********************************************************************************
// Conjunto de funciones para mostrar la informaci�n del hist�rico de posiciones
//
//  Modificado en Nov 2013 para API v3
//
// *********************************************************************************

// for the map
 var map;
 var markers;
 var marker;
 var timeOut = 1500;  // length to wait till next point is plotted
 timeOut=200;
 var i = 0;

 // for display
 var loadingMessage;
 var distanceMessage;
 var speedMessage;
 var timeMessage;
 var bRowColor = false;
 var checkPointCount = 1;
 var Eve = '';
 var posicion = '';
 var dateMessage;
 var distanceacumMessage;
  // for distance calculations
 var distance = 0;
 var distanceacum=0;
// var checkPoint;
 var checkPointDistance = 0;
 var my_dtotal = 0;

 // for time calculations
 var currentTime = 0;
 var lastTime = 0;
 var totalTime = 0;
 var my_ttotal = 0;
 var fecha_hora = '';

 // for speed calculations
 var averageSpeed;


 var vfiltro ='';
 var vini = '';
 var vfin = '';
 var vidi = '';
 var valida_xml = '';

var distancia_total_odometro = 0;
var combustible_total = 0;
var rendimiento_total = 0;

var t;
var timer_is_on=0;



function doTimer(){
	if (!timer_is_on){
	  timer_is_on=1;
	  plotPoint();

	}
}

function stopCount()
{
	valor_boton = document.getElementById('pausar').value;
	if (valor_boton == 'Pausar'){
		document.getElementById('pausar').value="Continuar";
		clearTimeout(t);
		timer_is_on=0;
	}else{
		if(valor_boton == 'Continuar'){
			document.getElementById('pausar').value="Pausar";
			doTimer();
		}
	}
}


function openNewWindow(theURL,winName,features) {
window.open(theURL,winName,features);
}

function crea_punto(latitud,longitud){
	openNewWindow('./modules/puntos/captura_nombre.php?new=1&lat='+latitud+'&lon='+longitud+''+'','','status=no ,location = 0, scrollbars=no ,resizable=no ,width=400,height=250');
}

// CREATE MAP & DISPLAY CONTROLS AND STARTING LOCATION //
function onLoad() {
	loadingMessage = document.getElementById('progress');
	distanceMessage = document.getElementById('distanceMessage');
        distanceacumMessage = document.getElementById('distanceacumMessage');
	speedMessage = document.getElementById("speedMessage");
	timeMessage = document.getElementById('timeMessage');
	dateMessage = document.getElementById('dateMessage');
	var mapOptions = {
		zoom:15,
		panControl: false,
		zoomControl: true, //map.addControl(new GLargeMapControl());
		mapTypeControl: true, //map.addControl(new GMapTypeControl());
		scaleControl: true,  //map.addControl(new GScaleControl());
		streetViewControl: false,
		overviewMapControl: false
 		}

    map = new google.maps.Map(document.getElementById("map"),mapOptions);


	//map.addControl(new GMapTypeControl());
	//map.enableDoubleClickZoom();
    //map.addControl(new GScaleControl());
	//map.addControl(new GLargeMapControl());
	//map.enableScrollWheelZoom();

	loadInfo2();

}

function initButtons(){
	document.getElementById("showStats").onclick=function(){
		document.getElementById("stats").style.display = "block";
		document.getElementById("showStats").style.display = "none";
		document.getElementById("hideStats").style.display = "block";
		}
	document.getElementById("hideStats").onclick=function(){
		document.getElementById("stats").style.display = "none";
		document.getElementById("showStats").style.display = "block";
		document.getElementById("hideStats").style.display = "none";
		}
}

function initCheckpoint(){



	ctrl = document.getElementById("submit");
	form_consulta = document.getElementById("criterios");
    fecharango=$('#daterange').val();
    fechas = fecharango.split("-");
	vini=fechas[0].trim();
	vfin=fechas[1].trim();

	if (form_consulta.filtro[0].checked){
		vfiltro = form_consulta.filtro[0].value;
	}
	if (form_consulta.filtro[1].checked){
		vfiltro = form_consulta.filtro[1].value;
	}
	if (form_consulta.filtro[2].checked){
		vfiltro = form_consulta.filtro[2].value;
	}
	// para determinar la velocidad de muestra en el mapa
	if (form_consulta.velocidad[0].checked){
		timeOut = form_consulta.velocidad[0].value* 1000;
	}
	if (form_consulta.velocidad[1].checked){
		timeOut = form_consulta.velocidad[1].value* 1000;
	}
	if (form_consulta.velocidad[2].checked){
		timeOut = form_consulta.velocidad[2].value* 1000;
	}

	vid = document.getElementById("id").value;

	ctrl.click=function(){document.getElementById("criterios").submit();}


}


function loadInfo2(){


		var url = "http://avl.castores.com.mx/historico/historico_genera.php?id="+vid+"&ini="+vini+"&fin="+vfin+"&filtro="+vfiltro;

		$.ajax({
		    type: 'GET',
		    url: url,
		    dataType: 'xml',
		    success: function(xmlDoc)
		    {
				markers = xmlDoc.getElementsByTagName("Trackpoint");
				valida_xml = markers[0].getElementsByTagName("Evento");
				valida_xml = valida_xml[0].firstChild.nodeValue;
				if (valida_xml == 1){
				 	alert ("No hay registros que cumplan ");
					return true;
				}
				plotPoint();
		    }

		});

/*
	var url = "historico_genera.php?id="+vid+"&ini="+vini+"&fin="+vfin+"&filtro="+vfiltro;
	$.get( url, function( data ) {
	 	var xmlDoc = data;

		markers = xmlDoc.documentElement.getElementsByTagName("Trackpoint");
		valida_xml = markers[0].getElementsByTagName("Evento");
		valida_xml = valida_xml[0].firstChild.nodeValue;
		if (valida_xml == 1){
		 	alert ("No hay registros que cumplan ");
			return true;
		}

		plotPoint();
	});

	*/
}


// LOAD THE XML FILE AND PLOT THE FIRST POINT //
function loadInfo(){
	var request = GXmlHttp.create();
	var url = "http://avl.castores.com.mx/historico/historico_genera.php?id="+vid+"&ini="+vini+"&fin="+vfin+"&filtro="+vfiltro;
	request.open("GET", url , true);
	request.onreadystatechange = function() {
	if (request.readyState == 4) {
		var xmlDoc = request.responseXML;
		markers = xmlDoc.documentElement.getElementsByTagName("Trackpoint");
		valida_xml = markers[0].getElementsByTagName("Evento");
		valida_xml = valida_xml[0].firstChild.nodeValue;
		if (valida_xml == 1){
		 	alert ("No hay registros que cumplan ");
			return true;
		}
		plotPoint();
	}
  }
  request.send(null);
}

function plotPoint(){
	if (i < markers.length ) {
		document.getElementById('contador').value=i;
		var Lat = markers[i].getElementsByTagName("Latitude");
		var Lng = markers[i].getElementsByTagName("Longitude");
		var CombTot = markers[i].getElementsByTagName("CombConsumido");
		var Rendimiento = markers[i].getElementsByTagName("RendimientoCalc");
		var Distancia_Odo = markers[i].getElementsByTagName("Distancia_Odo");
		var Datos_motor = markers[i].getElementsByTagName("DatosMotor");
		Lat = Lat[0].firstChild.nodeValue;
		Lng = Lng[0].firstChild.nodeValue;
		CombTot = CombTot[0].firstChild.nodeValue * 1;
		Rendimiento = Rendimiento[0].firstChild.nodeValue ;
		Distancia_Odo = Distancia_Odo[0].firstChild.nodeValue * 1;
		Datos_motor = Datos_motor[0].firstChild.nodeValue * 1;
		// ========================================
		distancia_total_odometro = Distancia_Odo + distancia_total_odometro;
		combustible_total = combustible_total + CombTot;
		// ========================================
		var point = new google.maps.LatLng( Lat,Lng);
		if ( i < markers.length  && i != 0){
			var Lat1 = markers[i-1].getElementsByTagName("Latitude");
			var Lng1 = markers[i-1].getElementsByTagName("Longitude");
			Lat1 = Lat1[0].firstChild.nodeValue;
			Lng1 = Lng1[0].firstChild.nodeValue;
			var point1 = new google.maps.LatLng( Lat1  ,  Lng1 );
			var points=[point, point1];
			//RECENTER MAP EVERY TWO POINTS
			if (i%2==0){ map.panTo(point); }
			if (i < markers.length/2){
				// LINEA AZUL 1a. Mitad
				var lineaDeRuta = new google.maps.Polyline({
					path: points,
					geodesic: true,
					strokeColor: '#0000ff',
					strokeOpacity: 0.75,
					strokeWeight: 3
				  	});
				lineaDeRuta.setMap(map);
				}
			else{
				// LINEA ROJA 2a. Mitad
				var lineaDeRuta1 = new google.maps.Polyline({
					path: points,
					geodesic: true,
					strokeColor: '#ff0000',
					strokeOpacity: 0.75,
					strokeWeight: 3
				  	});
				lineaDeRuta1.setMap(map);
				}
			calculateDistance(Lng, Lat, Lng1, Lat1);
			}
		calculateTime(i);
	        loadingPercentage(i);
		distanceMessage.innerHTML=Math.round(distance*100)/100 + " Km.";
		dateMessage.innerHTML=fecha_hora;
		distanceMessage.innerHTML=Math.round(distance*100)/100 + " Km.";
                distanceacumMessage.innerHTML=Math.round(distanceacum*100)/100 + " Kmts.";
		if (i < markers.length - 1){
                    t=window.setTimeout(plotPoint,timeOut);
                    averageSpeed = Math.round((distance / (totalTime/3600))*100)/100;
                    speedMessage.innerHTML = averageSpeed + " Km/h ";
                    }
                else {
			distanceMessage.innerHTML=Math.round(my_dtotal*100)/100 + " Km.";
                         distanceacumMessage.innerHTML=Math.round(my_dtotal*100)/100 + " Kms.";

                       averageSpeed = Math.round((my_dtotal / (my_ttotal/3600))*100)/100;
			speedMessage.innerHTML = averageSpeed + " Km/h ";
			if(combustible_total > 0){
				temporal = distancia_total_odometro / combustible_total;
				rendimiento_total = Math.round(temporal*100)/100;
				}
			if(Datos_motor == 1){
				createStat(fecha_hora,posicion, Eve+"-Fin", distancia_total_odometro, my_ttotal, speedMessage.innerHTML.replace(" Km/h ", ""),combustible_total,rendimiento_total,distancia_total_odometro,Datos_motor);
				}
			else{
				createStat(fecha_hora,posicion, Eve+"-Fin", my_dtotal, my_ttotal, speedMessage.innerHTML.replace(" Km/h ", ""),combustible_total,rendimiento_total,distancia_total_odometro,Datos_motor);
				}
        	}
		marker = createMarker(point,i,markers.length,CombTot,Rendimiento,Distancia_Odo,Datos_motor,fecha_hora,posicion);
		i++;
		}
}

// GET THE APPROPRIATE MARKER FOR START, FINISH, CHECKPOINT, AND LINE
function createMarker(point, i, markerLength,CombTot,Rendimiento,Distancia_Odo,Datos_motor,fecha_hora,posicion) {
	if (i== 1){ map.setZoom(map.getZoom() - 2); }



	Eve = markers[i].getElementsByTagName("Evento");
	Eve = Eve[0].firstChild.nodeValue;

	posicion = markers[i].getElementsByTagName("Posicion");
	posicion = posicion[0].firstChild.nodeValue;

	/**	var xmlStr = $( markers[i]) ;
	posicion_tmp = xmlStr.find("Posicion");
	posicion = posicion_tmp.text();**/

	//map.setCenter(point) ;
	//map.setZoom(10) ;

	var imagen = "";
	var informacion = "";
	var infowindow = new google.maps.InfoWindow();
	var marcar = false;

	switch (i){
		case 0:
				// MARKER DE INICIO

				imagen = 'images/mm_20_green.png';
				informacion = "<div class='alert alert-success'><p><b>INICIO</b></p></div>";

				//creado para agregar el primer evento con fecha y hora
				fecha_actual = markers[i].getElementsByTagName("Time");

				fecha_actual = fecha_actual[0].firstChild.nodeValue;
				var time_stamp = fecha_actual.split("T");
				fecha_hora = time_stamp[0]+" "+time_stamp[1];
				fecha_hora = fecha_hora.replace("Z","");

				fecha_hora = fecha_actual;
				createStat(fecha_hora,posicion, Eve+"-Inicio", 0, 0, 0,0,0,0,0);
				marcar = true;
				break;

		case markers.length -1:
				// MARKER FINAL

				imagen = 'images/mm_20_red.png';
				informacion = "<div class='alert alert-success'><p><b>FIN</b></p></div>";
				marcar = true;
				break;

		default :
				if (vfiltro!='trayectoria'){
					imagen = 'images/posicion.png';

					var coordenadas = point.toString();
					coordenadas = coordenadas.replace("(","");
					coordenadas = coordenadas.replace(")","");
					var cad_coordenadas = coordenadas.split(",");

					lat_lm = cad_coordenadas[1];
					long_lm = cad_coordenadas[0];

					var texto = "<b>Posici&oacute;n: "+posicion+" ("+fecha_hora+")</b>";
					var boton_crear = "<button type=\"submit\" name=\"cmd_landmark\" id=\"cmd_landmark\" value=\"Punto Interes\" onclick=\"crea_punto("+lat_lm+","+long_lm+")\" /><img src=\"images/flag-green.png\" width=\"22\" height=\"22\" alt=\"Crear Punto\"/></button>";
					boton_crear ="";
					informacion = "<div class='alert alert-warning'><p>"
									+ "<font size=1px face=arial class=plain_no_bold_text>"
									+ texto
									+ "</font><br>"
									+ boton_crear
									+ "</p></div>";
									;
					marcar = true;
					}

				//creado para agregar el primer evento con fecha y hora
				fecha_actual = markers[i].getElementsByTagName("Time");
				fecha_actual = fecha_actual[0].firstChild.nodeValue;
				var time_stamp = fecha_actual.split("T");
				fecha_hora = time_stamp[0]+" "+time_stamp[1];
				fecha_hora = fecha_hora.replace("Z","");

				fecha_hora = fecha_actual;

				//------------------------------------------------------

				createStat(fecha_hora,posicion,  Eve, distance, totalTime, averageSpeed,CombTot,Rendimiento,Distancia_Odo,Datos_motor);
				checkPointDistance = 0;
				checkPointCount += 1;

				break;
		}

	if (marcar == true){
		var marker = new google.maps.Marker({
			position: point,
			map: map,
			icon: imagen
			});

		google.maps.event.addListener(marker,'click',function(){
			infowindow.setContent(informacion);
			infowindow.open(map,marker);
			});

		map.panTo(point);
		return marker;
		}
	else{
		imagen = 'images/mm_20_shadow.png';
		var marker = new google.maps.Marker({
			position: point,
			map: map,
			icon: imagen
			});

		map.panTo(point);
		return marker;
		}

}

function createStat(fecha,posicion, pointName, distance, time, speed,CombTot,Rendimiento,Distancia_Odo,Datos_motor){
	var tbody;
	if(pointName != "End") {
		tbody = document.getElementById("statsTable").getElementsByTagName("tbody")[0];
		}
	else {
		tbody = document.getElementById("statsTable").getElementsByTagName("tfoot")[0];
		}

	row = document.createElement("tr");
	if(bRowColor == true && pointName != "End") {
		row.className = "alt";
		bRowColor = false;
	}
	else {
		bRowColor = true;
	}
	fName = document.createElement("td");
	fName.innerHTML = fecha;
	poName = document.createElement("td");
	poName.innerHTML = posicion;
	pName = document.createElement("td");
	pName.innerHTML = pointName;
	dName = document.createElement("td");
	if (Datos_motor == 0){
		dName.innerHTML = Math.round(distance*100)/100 +' Kms';
	}else{
		dName.innerHTML = Distancia_Odo +' Kms';
	}
	ComName = document.createElement("td");
	ComName.innerHTML = CombTot+' Lts.';
	RendName = document.createElement("td");
	RendName.innerHTML = Rendimiento+' Kms/Lt.';
	tName = document.createElement("td");
	tName.innerHTML = convertSeconds(time);
	sName = document.createElement("td");
	sName.innerHTML = speed +' Km/h';
	row.appendChild(fName);
	row.appendChild(poName);
	row.appendChild(pName);
	row.appendChild(dName);
	row.appendChild(ComName);
	row.appendChild(RendName);
	row.appendChild(tName);
	row.appendChild(sName);
	tbody.appendChild(row);
}

// LOADING BAR //
function loadingPercentage(currentPoint){
	var percentage = Math.round((currentPoint/(markers.length - 1)) * 100);
	if (isNaN(percentage)){
		percentage = 0;
	}

	loadingMessage.style.width = percentage +"%";
}

// Function based on Vincenty formula

function calculateDistance(point1y, point1x, point2y, point2x) {  // Vincenty formula
  traveled = LatLong.distVincenty(new LatLong(point2x, point2y), new LatLong(point1x, point1y));
  traveled = traveled /1000;

  distance = traveled; // solo muestra la distancia entre los ultimos 2 puntos
  distanceacum = distanceacum + distance;
  my_dtotal = my_dtotal + traveled;
  checkPointDistance = checkPointDistance + traveled;
}

/*
 * LatLong constructor:
 *
 *   arguments are in degrees, either numeric or formatted as per LatLong.degToRad
 *   returned lat -pi/2 ... +pi/2, E = +ve
 *   returned lon -pi ... +pi, N = +ve
 */
function LatLong(degLat, degLong) {
  if (typeof degLat == 'number' && typeof degLong == 'number') {  // numerics
    this.lat = degLat * Math.PI / 180;
    this.lon = degLong * Math.PI / 180;
  } else if (!isNaN(Number(degLat)) && !isNaN(Number(degLong))) { // numerics-as-strings
    this.lat = degLat * Math.PI / 180;
    this.lon = degLong * Math.PI / 180;
  } else {                                                        // deg-min-sec with dir'n
    this.lat = LatLong.degToRad(degLat);
    this.lon = LatLong.degToRad(degLong);
  }
}

/*
 * Calculate geodesic distance (in m) between two points specified by latitude/longitude.
 *
 */
LatLong.distVincenty = function(p1, p2) {
  var a = 6378137, b = 6356752.3142,  f = 1/298.257223563;
  var L = p2.lon - p1.lon;
  var U1 = Math.atan((1-f) * Math.tan(p1.lat));
  var U2 = Math.atan((1-f) * Math.tan(p2.lat));
  var sinU1 = Math.sin(U1), cosU1 = Math.cos(U1);
  var sinU2 = Math.sin(U2), cosU2 = Math.cos(U2);
  var lambda = L, lambdaP = 2*Math.PI;
  var iterLimit = 20;
  while (Math.abs(lambda-lambdaP) > 1e-12 && --iterLimit>0) {
    var sinLambda = Math.sin(lambda), cosLambda = Math.cos(lambda);
    var sinSigma = Math.sqrt((cosU2*sinLambda) * (cosU2*sinLambda) +
      (cosU1*sinU2-sinU1*cosU2*cosLambda) * (cosU1*sinU2-sinU1*cosU2*cosLambda));
    if (sinSigma==0) return 0;  // co-incident points
    var cosSigma = sinU1*sinU2 + cosU1*cosU2*cosLambda;
    var sigma = Math.atan2(sinSigma, cosSigma);
    var alpha = Math.asin(cosU1 * cosU2 * sinLambda / sinSigma);
    var cosSqAlpha = Math.cos(alpha) * Math.cos(alpha);
    var cos2SigmaM = cosSigma - 2*sinU1*sinU2/cosSqAlpha;
    var C = f/16*cosSqAlpha*(4+f*(4-3*cosSqAlpha));
    lambdaP = lambda;
    lambda = L + (1-C) * f * Math.sin(alpha) *
      (sigma + C*sinSigma*(cos2SigmaM+C*cosSigma*(-1+2*cos2SigmaM*cos2SigmaM)));
  }
  if (iterLimit==0) return NaN  // formula failed to converge
  var uSq = cosSqAlpha * (a*a - b*b) / (b*b);
  var A = 1 + uSq/16384*(4096+uSq*(-768+uSq*(320-175*uSq)));
  var B = uSq/1024 * (256+uSq*(-128+uSq*(74-47*uSq)));
  deltaSigma = B*sinSigma*(cos2SigmaM+B/4*(cosSigma*(-1+2*cos2SigmaM*cos2SigmaM)-
    B/6*cos2SigmaM*(-3+4*sinSigma*sinSigma)*(-3+4*cos2SigmaM*cos2SigmaM)));
  s = b*A*(sigma-deltaSigma);
  s = s.toFixed(2); // round to 1mm precision
  return s;
}

// PARSE TIME FROM XML AND THEN FIND THE DIFFERENCE BETWEEN LAST MEASUREMENT WITH RUNNING TOTAL //
function calculateTime(i)
{
	currentTime = markers[i].getElementsByTagName("Time");
	currentTime = currentTime[0].firstChild.nodeValue;

	if (i > 0 ){
		lastTime = markers[i-1].getElementsByTagName("Time");
		lastTime = lastTime[0].firstChild.nodeValue;

		//begin year/month/day


		var largeDate = currentTime.split(" ");
		var date = largeDate[0];

		date = date.split("-");
		var beforeYear = date[0];
		var beforeDay = date[2];
		var beforeMonth = date[1];
	    fecha_hora = largeDate[0]+" "+largeDate[1];



		fecha_hora = fecha_hora;
		var largeDate1 = lastTime;

	//	fecha_hora = fecha_hora.replace("Z","");
		var largeDate1 = lastTime.split(" ");

		var date1 = largeDate1[0];
		date1 = date1.split("-");
		var afterYear = date1[0];
		var afterDay = date1[2];
		var afterMonth = date1[1];

		//begin hour/min/seconds

		//var after = largeDate[1].replace("T","");
		//after = largeDate[1].replace("Z","");

		var after = largeDate[1];
		after = largeDate[1];

		afterArray = after.split(":");
		var hour = afterArray[0];
		var minute = afterArray[1];
		var second = afterArray[2];

		//end hour/min/seconds
		var after1 = largeDate1[1];
		after1 = largeDate1[1];


		//var after1 = largeDate1[1].replace("T","");
		//after1 = largeDate1[1].replace("Z","");


		afterArray1 = after1.split(":");
		var hour1 = afterArray1[0];
		var minute1 = afterArray1[1];
		var second1 = afterArray1[2];

		console.log(afterYear+" "+afterMonth+" "+afterDay+" "+hour1+" "+minute1+" "+second1);

		var before =new Date(beforeYear, beforeMonth, beforeDay, hour, minute, second);
		var after =new Date(afterYear, afterMonth, afterDay, hour1, minute1, second1);
		var seconds = 1000;
		var secondsBetween = Math.ceil((before.getTime()-after.getTime())/(seconds));
		totalTime = secondsBetween; // solo muestra el tiempo entre ultimos 2 puntos.
		my_ttotal = my_ttotal + secondsBetween;
		// agregado para mostrar el total del tiempo al final
		if (i < markers.length - 1){
			timeMessage.innerHTML=convertSeconds(totalTime);
		}else{
		 	timeMessage.innerHTML=convertSeconds(my_ttotal);

		}
	}
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




/*
window.onload = function(){
initCheckpoint();
initButtons();
onLoad();
}

*/
