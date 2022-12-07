window.addEventListener('load',semaforo, false);


function semaforo( )
    {
        $("#procesos").empty();

            $.ajax({
                type:'POST', //aqui puede ser igual get
                url: 'monitoreo/app_crones.php',//aqui va tu direccion donde esta tu funcion php
                dataType : 'json',
                success:function(data){
                    //lo que devuelve 
                    $.each(data, function(i,item){
                if(item.estatus == 2){
                    $("#procesos").append('<div class="col-md-1 "><button type="button" class="btn btn-default btn-lg ">'+item.id_cron+' <div class="circulo fondorojo blanco"> STOP </div></button></div>');

                } else if (item.estatus == 3) {
                            $("#procesos").append('<div class="col-md-1 "><button type="button" class="btn btn-default btn-lg ">' + item.id_cron + ' <div class="circulo fondoamarillo blanco"> Activo </div></button></div>');

                } else{
                    $("#procesos").append('<div class="col-md-1 "><button type="button" class="btn btn-default btn-lg ">'+item.id_cron+' <div class="circulo fondoverde blanco"> Activo </div></button></div>');

                }
            })
            },
            error:function(data){
                //lo que devuelve si falla 
                alert('la actualizacion de semaforos fallo'+ ' '+data);
                
            }
            });
       
            setTimeout('semaforo()',120000);

    }
