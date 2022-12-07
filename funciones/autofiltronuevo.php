<?php

function autofiltronuevo($campo,$tabla,$variable,$conn) {
                  $consulta2  = " SELECT distinct(".$campo.") as campo FROM ".$tabla." WHERE status = 1 ORDER BY ".$campo." ASC ";
                  $query2 = $conn->prepare($consulta2);
                  $query2->execute();
                  $seleccionado="";
                ?>
                <select id="#<?php echo $variable ?>" name="<?php echo $variable ?>" class="filtro">
                  <option value="0">Ver todos</option>
                  <?php
                    while ($registro2 = $query2->fetch()) {
                        if(isset($_GET["$variable"]))
                            if($_GET["$variable"]==trim($registro2["campo"]))
                                $seleccionado="selected";
                            else
                                $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['campo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["campo"] ?></option>
                  <?php } ?>
                </select>
<?php

}

function autofiltroalerta($campo,$tabla,$variable,$conn) {
                  $consulta2  = " SELECT distinct(".$campo.") as campo FROM ".$tabla." AS a INNER JOIN tb_alertas_rol as b ON b.alerta = a.pk_clave_tipa  WHERE rol = ".$_SESSION['rol']." ORDER BY ".$campo." ASC ";  
                  $query2 = $conn->prepare($consulta2);
                  $query2->execute();
                  $seleccionado="";
                ?>
                <select id="#<?php echo $variable ?>" name="<?php echo $variable ?>" class="filtro">
                  <option value="0">Ver todos</option>
                  <?php
                    while ($registro2 = $query2->fetch()) {
                        if(isset($_GET["$variable"]))
                            if($_GET["$variable"]==trim($registro2["campo"]))
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
