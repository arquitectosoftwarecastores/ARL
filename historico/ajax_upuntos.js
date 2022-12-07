
function crearXMLHttpRequest() 
{
  var xmlHttp=null;
  if (window.ActiveXObject) 
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  else 
    if (window.XMLHttpRequest) 
      xmlHttp = new XMLHttpRequest();
  return xmlHttp;
}

function muestra_puntosI(latp,longp,textop){
		
		var imagen = "images/mira1.png";
		var p1 = new google.maps.LatLng(latp,longp);
		var marker1 = new google.maps.Marker({
					position: p1,
					map: map,
					icon : imagen
			  		});
		
	   	var infowindow = new google.maps.InfoWindow();
		var markerP_html = "<div align = left><font size=1px face=arial class=plain_no_bold_text><b>" 
							+ textop 
							+ "</b></font></div>"; 
		google.maps.event.addListener(marker1, 'click', function() {
                   infowindow.setContent(markerP_html);
	           	   infowindow.open(map, marker1);	
                });
		
		
		
	    /*  
		  var iconP = new GIcon();
	      iconP.image = 'images/mira1.png';
		  iconP.iconSize = new GSize(30, 30);
		  iconP.iconAnchor = new GPoint(7, 7);
	 	  iconP.infoWindowAnchor = new GPoint(5, 2);
		
		
		
        var latlngp = new GLatLng(latp,longp);
        var markerP = new GMarker(latlngp, iconP);
       
       	var markerP_html = "<div align = left><font size=1px face=arial class=plain_no_bold_text><b>" + textop + "</b></font></div>"; 
        
       
      
		
		GEvent.addListener(markerP, 'click', function() {
    	   markerP.openInfoWindowHtml(markerP_html);
    	});

        map.addOverlay(markerP);
		*/
        
}

function buscaUPuntos(){			 

	var vini = document.getElementById("fecha_ini").value;
	var vfin = document.getElementById("fecha_fin").value;

	if (form_consulta.filtro[0].checked){
		vfiltro = form_consulta.filtro[0].value;
		//alert (vfiltro);
	}
	if (form_consulta.filtro[1].checked){
		vfiltro = form_consulta.filtro[1].value;
		//alert (vfiltro);
	}	
	if (form_consulta.filtro[2].checked){
		vfiltro = form_consulta.filtro[2].value;
		//alert (vfiltro);
	}

	var vid = document.getElementById("id").value;
	var url1 = "historico_upuntos.php?id="+vid+"&ini="+vini+"&fin="+vfin+"&filtro="+vfiltro+"&rnd="+ Math.random()*10000;
	//alert(url1);
    conexionUpun=crearXMLHttpRequest();
	conexionUpun.open("GET", url1);
	conexionUpun.onreadystatechange = function(){
		if (conexionUpun.readyState ==4){
			var xml 		= conexionUpun.responseXML;
			var item 		= xml.getElementsByTagName('Punto')[0];
			for(f=0;f<xml.getElementsByTagName("Punto").length;f++)
		    {
				var item 		= xml.getElementsByTagName('Punto')[f];
				var nombre 		= item.getElementsByTagName('Nombre')[0].firstChild.data;
				var latitud 	= item.getElementsByTagName('Latitud')[0].firstChild.data;
				var longitud 	= item.getElementsByTagName('Longitud')[0].firstChild.data;
				var distancia 	= item.getElementsByTagName('Distancia')[0].firstChild.data;
				
				var texto = "Nombre Punto: "+nombre+"<br><b>Latitud:</b>"+latitud+"</b><br><b>Longitud:"+longitud+"</b><br><b>Distancia:"+distancia+" Kms.";
				muestra_puntosI(latitud,longitud,texto);
				
		    } 
			
		}else{
			
		}
	}
	
	conexionUpun.send(null);
	
}