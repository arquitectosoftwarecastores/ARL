

<?php

function autofiltroAlertas($campo,$tabla,$variable,$conn) {

                        $consulta2  = " SELECT distinct(".$campo.") as campo FROM ".$tabla." where pk_clave_tipa in (4,203,103,150) ORDER BY ".$campo." ASC ";  
                   

                  
                  $query2 = $conn->prepare($consulta2);
                  $query2->execute();  
                  $seleccionado="";
                ?>
                <select id="#<?php echo $variable ?>" name="<?php echo $variable ?>" class="filtro">
                  <option value="0">Ver todos</option>                    
                  <?php
                    while ($registro2 = $query2->fetch()) {
                        if(isset($_GET["alerta"]))
                            if($_GET["alerta"]==trim($registro2["campo"]))
                                $seleccionado="selected";
                            else
                                $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['campo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["campo"] ?></option>
                  <?php } ?>
                </select>  
<?php 

}
?>

<?php

function autofiltroAlertasBaja($campo,$tabla,$variable,$conn) {

                   
                        $consulta2  = " SELECT distinct(".$campo.") as campo FROM ".$tabla." where pk_clave_tipa in (201,9,3,205,2,16) ORDER BY ".$campo." ASC ";  
                    

                  
                  $query2 = $conn->prepare($consulta2);
                  $query2->execute();  
                  $seleccionado="";
                ?>
                <select id="#<?php echo $variable ?>" name="<?php echo $variable ?>" class="filtro">
                  <option value="0">Ver todos</option>                    
                  <?php
                    while ($registro2 = $query2->fetch()) {
                        if(isset($_GET["alertaBaja"]))
                            if($_GET["alertaBaja"]==trim($registro2["campo"]))
                                $seleccionado="selected";
                            else
                                $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['campo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["campo"] ?></option>
                  <?php } ?>
                </select>  
<?php 

}
?>

