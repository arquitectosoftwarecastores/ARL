<script>

   $("#rxp").change(function() {        
        url= "?seccion=<?php echo $seccion ?>&accion=lista";   
        url+="&rxp="+$(this).val(); 
        ir(url);        
    });

   $("#buscar").click(function() {        
        url= "?seccion=<?php echo $seccion ?>&accion=lista";   
        url+="&busca="+$("#busca").val(); 
        <?php if(isset($_GET["rxp"])) { ?> url+="&rxp=<?php echo $_GET['rxp']; ?>";  <?php } ?>        
        <?php if(isset($_GET["orden"])) { ?> url+="&orden=<?php echo $_GET['orden']; ?>";  <?php } ?>        
        <?php if(isset($_GET["inicia"])) { ?> url+="&inicia=<?php echo $_GET['inicia']; ?>";  <?php } ?>
        window.location = url;        
    });  

    $('.borra').click(function(){

        var r=confirm("Esta seguro que desea borrar este registro?");
        if (r==true)
            {
            url= "?seccion=<?php echo $seccion;?>&accion=borra&id="+$(this).data('id');   
            ir(url);
            }
        else
            return false;
    });


    $(function() {
      $('.renglon').hover(function(){
        $(this).addClass('gris');
      },
      function(){
          $(this).removeClass('gris');
      });
    });


    $( ".filtro" ).change(function() {
        
        url= "?seccion=<?php echo $seccion;?>&accion=lista"; 

        if($(this).attr("name")=="nombre" && $(this).val()!=0)
        {
            url+="&nombre="+$(this).val();
            <?php if(isset($_GET["usuario"])) { ?> url+="&usuario=<?php echo $_GET['usuario']; ?>";  <?php } ?>
            <?php if(isset($_GET["rol"])) { ?> url+="&rol=<?php echo $_GET['rol']; ?>";  <?php } ?>
            <?php if(isset($_GET["empresa"])) { ?> url+="&empresa=<?php echo $_GET['empresa']; ?>";  <?php } ?>
            <?php if(isset($_GET["ciudad"])) { ?> url+="&ciudad=<?php echo $_GET['ciudad']; ?>";  <?php } ?>
        }
        
        if($(this).attr("name")=="usuario" && $(this).val()!=0)
        {
            url+="&usuario="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["rol"])) { ?> url+="&rol=<?php echo $_GET['rol']; ?>";  <?php } ?>
            <?php if(isset($_GET["empresa"])) { ?> url+="&empresa=<?php echo $_GET['empresa']; ?>";  <?php } ?>
 
        }
        
        if($(this).attr("name")=="rol"&& $(this).val()!=0)
        {
            url+="&rol="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["usuario"])) { ?> url+="&usuario=<?php echo $_GET['usuario']; ?>";  <?php } ?>
            <?php if(isset($_GET["empresa"])) { ?> url+="&empresa=<?php echo $_GET['empresa']; ?>";  <?php } ?>
        }
        
        if($(this).attr("name")=="empresa"&& $(this).val()!=0)
        {
            url+="&empresa="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["usuario"])) { ?> url+="&usuario=<?php echo $_GET['usuario']; ?>";  <?php } ?>
            <?php if(isset($_GET["rol"])) { ?> url+="&rol=<?php echo $_GET['rol']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="tipo"&& $(this).val()!=0)
        {
            url+="&tipo="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["ciudad"])) { ?> url+="&ciudad=<?php echo $_GET['ciudad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estado"])) { ?> url+="&estado=<?php echo $_GET['estado']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="ciudad"&& $(this).val()!=0)
        {
            url+="&ciudad="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["estado"])) { ?> url+="&estado=<?php echo $_GET['estado']; ?>";  <?php } ?>
            <?php if(isset($_GET["tipo"])) { ?> url+="&tipo=<?php echo $_GET['tipo']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="estado"&& $(this).val()!=0)
        {
            url+="&estado="+$(this).val();
            <?php if(isset($_GET["nombre"])) { ?> url+="&nombre=<?php echo $_GET['nombre']; ?>";  <?php } ?>
            <?php if(isset($_GET["ciudad"])) { ?> url+="&ciudad=<?php echo $_GET['ciudad']; ?>";  <?php } ?>
            <?php if(isset($_GET["tipo"])) { ?> url+="&tipo=<?php echo $_GET['tipo']; ?>";  <?php } ?>
        }


        if($(this).attr("name")=="unidad"&& $(this).val()!=0)
        {
            url+="&unidad="+$(this).val();
            <?php if(isset($_GET["alerta"])) { ?> url+="&nombre=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&nombre=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["circuito"])) { ?> url+="&circuito=<?php echo $_GET['circuito']; ?>";  <?php } ?>
        }      

        if($(this).attr("name")=="alertaAlta")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&alerta="+$(this).val();
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }

         if($(this).attr("name")=="alertaBaja" )
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&alertaBaja="+$(this).val();
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }

        if($(this).attr("name")=="prioridad")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&prioridad="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }


        if($(this).attr("name")=="from")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&from="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>   
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>         
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }

         if($(this).attr("name")=="from2")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&from2="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>   
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>         
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }


        if($(this).attr("name")=="to")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&to="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }

        if($(this).attr("name")=="to2")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&to2="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>

        }

        if($(this).attr("name")=="economico")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&economico="+$(this).val();
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>            
            <?php if(isset($_GET["zona"])) { ?> url+="&zona=<?php echo $_GET['zona']; ?>";  <?php } ?>
            <?php if(isset($_GET["perdida"])) { ?> url+="&perdida=<?php echo $_GET['perdida']; ?>";  <?php } ?>
            <?php if(isset($_GET["especial"])) { ?> url+="&especial=<?php echo $_GET['especial']; ?>";  <?php } ?>
            <?php if(isset($_GET["circuito"])) { ?> url+="&circuito=<?php echo $_GET['circuito']; ?>";  <?php } ?>
            <?php if(isset($_GET["serie"])) { ?> url+="&serie=<?php echo $_GET['serie']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="estatus")
        {
            if($(this).val()!="")
                url+="&estatus="+$(this).val();
            <?php if(isset($_GET["estatus2"])) { ?> url+="&estatus2=<?php echo $_GET['estatus2']; ?>";  <?php } ?>
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="estatus2")
        {
            if($(this).val()!="")
                url+="&estatus2="+$(this).val();
            <?php if(isset($_GET["estatus"])) { ?> url+="&estatus=<?php echo $_GET['estatus']; ?>";  <?php } ?>
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["alerta"])) { ?> url+="&alerta=<?php echo $_GET['alerta']; ?>";  <?php } ?>
            <?php if(isset($_GET["alertaBaja"])) { ?> url+="&alertaBaja=<?php echo $_GET['alertaBaja']; ?>";  <?php } ?>
            <?php if(isset($_GET["from"])) { ?> url+="&from=<?php echo $_GET['from']; ?>";  <?php } ?>
            <?php if(isset($_GET["from2"])) { ?> url+="&from2=<?php echo $_GET['from2']; ?>";  <?php } ?>
            <?php if(isset($_GET["to"])) { ?> url+="&to=<?php echo $_GET['to']; ?>";  <?php } ?>
            <?php if(isset($_GET["to2"])) { ?> url+="&to2=<?php echo $_GET['to2']; ?>";  <?php } ?>
            <?php if(isset($_GET["prioridad"])) { ?> url+="&prioridad=<?php echo $_GET['prioridad']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="zona")
        {
            if($(this).val()!="")
                url+="&zona="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="especial")
        {
            if($(this).val()!="-1" )
                url+="&especial="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["serie"])) { ?> url+="&serie=<?php echo $_GET['serie']; ?>";  <?php } ?>
            <?php if(isset($_GET["perdida"])) { ?> url+="&perdida=<?php echo $_GET['perdida']; ?>";  <?php } ?>
        }


        if($(this).attr("name")=="perdida")
        {
            if($(this).val()!="")
                url+="&perdida="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["serie"])) { ?> url+="&serie=<?php echo $_GET['serie']; ?>";  <?php } ?>
            <?php if(isset($_GET["especial"])) { ?> url+="&especial=<?php echo $_GET['especial']; ?>";  <?php } ?>
            <?php if(isset($_GET["circuito"])) { ?> url+="&circuito=<?php echo $_GET['circuito']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="serie")
        {
            if($(this).val()!=0 && $(this).val()!="")
                url+="&serie="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["perdida"])) { ?> url+="&perdida=<?php echo $_GET['perdida']; ?>";  <?php } ?>
            <?php if(isset($_GET["especial"])) { ?> url+="&especial=<?php echo $_GET['especial']; ?>";  <?php } ?>
            <?php if(isset($_GET["circuito"])) { ?> url+="&circuito=<?php echo $_GET['circuito']; ?>";  <?php } ?>
        }

        if($(this).attr("name")=="circuito")
        {
            if($(this).val()!="")
                url+="&circuito="+$(this).val();
            <?php if(isset($_GET["economico"])) { ?> url+="&economico=<?php echo $_GET['economico']; ?>";  <?php } ?>
            <?php if(isset($_GET["perdida"])) { ?> url+="&perdida=<?php echo $_GET['perdida']; ?>";  <?php } ?>
            <?php if(isset($_GET["especial"])) { ?> url+="&especial=<?php echo $_GET['especial']; ?>";  <?php } ?>
            <?php if(isset($_GET["serie"])) { ?> url+="&serie=<?php echo $_GET['serie']; ?>";  <?php } ?>
            <?php if(isset($_GET["unidad"])) { ?> url+="&unidad=<?php echo $_GET['unidad']; ?>";  <?php } ?>
        }

        
        <?php if(isset($_GET["rxp"])) { ?> url+="&rxp=<?php echo $_GET['rxp']; ?>";  <?php } ?>        
        <?php if(isset($_GET["orden"])) { ?> url+="&orden=<?php echo $_GET['orden']; ?>";  <?php } ?>        
        <?php if(isset($_GET["inicia"])) { ?> url+="&inicia=<?php echo $_GET['inicia']; ?>";  <?php } ?>

        window.location = url;
    });    

    function ir(url) {       
        <?php if(isset($_GET["orden"])) { ?> url+="&orden=<?php echo $_GET['orden']; ?>";  <?php } ?>
        <?php if(isset($_GET["busca"])) { ?> url+="&busca=<?php echo $_GET['busca']; ?>";  <?php } ?>
        <?php if(isset($_GET["inicia"])) { ?> url+="&inicia=<?php echo $_GET['inicia']; ?>";  <?php } ?>
        window.location = url;
    }


    $('#todos').change(function() {
        var checkboxes = $(this).closest('form').find(':checkbox');

        if($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });



    $('#borratodos').click(function(){

        var r=confirm("Esta seguro que desea borrar los registros seleccionados?");
        if (r==true)
             return true;
        else
            return false;
    });



</script>