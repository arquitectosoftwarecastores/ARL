<?php // include ('general/estilo.php') ?>
<?php// include("funciones/autofiltro.php") ?>
<form action="?seccion=<?php echo $seccion ?>&amp;accion=lista" id="form1" method="post">
    <div>  
        <div class="row renglon">
            <div class="col-md-1">
                <input type="checkbox" name="todos" id="todos"/>
            </div>
            <?php // $variable = "usuario" ?>             
            <div class="col-md-2 negritas">
               <h2> Usuario </h2>
                <?php// include ("general/for_orden.php"); ?>
            </div>  
            <div class="col-md-2 negritas">
               <h2> Zona de Riesgo </h2>
                <?php //include ("general/for_orden.php"); ?>
            </div> 
            <div class="col-md-1 negritas"> 
                <h2>Fecha </h2>
                <?php // include ("general/for_orden.php"); ?>
          <!--      <div class="col-md-3"> 
                    <?php /*
                    if (isset($_GET["from"]))
                        $from = $_GET["from"];
                    else
                        $from = "";
                    if (isset($_GET["to"]))
                        $to = $_GET["to"];
                    else
                        $to = "";
                   */ ?>
         <!--           <table>
                        <tr>
                            <td>De:&nbsp;</td>
                            <td><input type="text" class="filtro validate[required]" id="from" name="from" size="8" value="<?php echo $from ?>" /></td>
                            <td>&nbsp;A:&nbsp;</td>
                            <td><input type="text" class="filtro validate[required]" id="to" name="to" size="8" value="<?php echo $to ?>" /></td>
                        </tr>
                    </table>  
                </div> -->
            </div>
            <div class="col-md-1 negritas">
               <h2> Nombre Corto </h2>
                <?php// include ("general/for_orden.php"); ?>
            </div>
        </div>  
        <div class="row renglon">
            <div class="col-md-1"></div>
            <div class="col-md-1">
                <? //php autofiltro("txt_economico_veh","tb_vehiculos","economico",$conn) ?>
            </div>    
            <div class="col-md-2 centrado">
                <? //php autofiltro("num_serie_veh","tb_vehiculos","serie",$conn) ?>
            </div> 
            <div class="col-md-1 centrado">
                <?php
                $consulta2 = "select up.fecha as fecha, up.nombrecorto as nombrecorto, z.txt_nombre_zon as nombrezona, u.txt_nombre_usu nombreusuario 
                from monitoreo.usuariospermisos up 
                join monitoreo.tb_zonas z on z.pk_clave_zon::varchar = up.comandoejecutado 
                join monitoreo.tb_usuarios u on up.txt_usuario_usu = u.txt_usuario_usu 
                where up.nombrecorto = 'Modificar Zona' and z.fk_clave_tipz= 3 
                order by up.fecha desc 
                limit 30";
                $query = $conn->prepare($consulta2);
                $query->execute();
                while ($registro = $query->fetch()) {
                    ?>
                </div>
            </div>
            <div class="row renglon">
                <div class="col-md-1"><input type="checkbox" name="registros[]" value="<?php echo $registro[$campoId] ?>"/></div>
                <div class="col-md-2"><?php echo $registro["nombreusuario"]; ?></div>
                <div class="col-md-1 centrado"><?php echo $registro["nombrezona"] ?></div>      
                <div class="col-md-2 centrado"><?php echo $registro["fecha"] ?></div>
                <div class="col-md-1 centrado"><?php echo $registro["nombrecorto"] ?></div>
            </div>
        <?php } ?>
    </div>
</form>
<br><center>
 <?php
                $consulta3 = "select count(*) as total
                from monitoreo.usuariospermisos up 
                join monitoreo.tb_zonas z on z.pk_clave_zon::varchar = up.comandoejecutado 
                join monitoreo.tb_usuarios u on up.txt_usuario_usu = u.txt_usuario_usu 
                where up.nombrecorto = 'Modificar Zona' and z.fk_clave_tipz= 3";
                $query3 = $conn->prepare($consulta3);
                $query3->execute();
                while ($registro3 = $query3->fetch()) {
                    ?>        
<b>Registros: 1-<?php echo $registro3["total"] ?> de <?php echo $registro3["total"] ?> Página: 1 de 1</b>
</center>
<?php 
                }
$query->closeCursor(); 
$query3->closeCursor(); 
?>


<script>

    $('.adjuntarimagen').click(function () {
        $('#identrada').val($(this).data('id'));

    });

</script>

