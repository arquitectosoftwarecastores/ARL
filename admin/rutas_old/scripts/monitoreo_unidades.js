var contador_puntos=0;
var ArrayPuntos=[];
$(document).ready(function(){
  $("#agregapuntointermedioButton").click(function(e){
       e.preventDefault();
       contador_puntos++;
      var contador_arreglo=0;
      var idzona= $("#puntointermedioSelect").val();
      var punto_intermiedio= $("#puntointermedioSelect option[value='"+idzona+"']").text();
      var returnArray=jQuery.inArray( idzona, ArrayPuntos);      
             if(contador_puntos==1){
              $("#ListaPuntosIntermedios").html(punto_intermiedio);
                ArrayPuntos.push({"id_zona":idzona});
              }
             else{
                  $.each(ArrayPuntos, function(i, item){
                            if(item.id_zona==idzona){
                                alert("Ya existe");
                            }
                            else{
                                contador_arreglo++;
                                
                                if(contador_arreglo==1){
                                    ArrayPuntos.push({"id_zona":idzona});
                                    $("#ListaPuntosIntermedios").append("<br>"+punto_intermiedio);
                                }
                                
                            }
                    });
             }
          $("#JsonPuntosIntermedios").val(JSON.stringify(ArrayPuntos));
     });
});