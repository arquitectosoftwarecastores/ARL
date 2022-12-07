                <?php
                  $consulta2  = " SELECT distinct(".$campoMostrar.") as campo FROM ".$Tabla." ORDER BY ".$campoMostrar." ASC ";  
                  $query2 = $conn->prepare($consulta2);
                  $query2->execute();  
                  $seleccionado="";
                ?>
                <select id="#<?php echo $variable ?>" name="<?php echo $variable ?>" class="filtro form-control">
                  <option value="">Ver todos</option>                    
                  <?php
                    while ($registro2 = $query2->fetch()) {
                        if(isset($_GET["busca"]))
                            if($_GET["busca"]==$registro2["campo"])
                                $seleccionado="selected";
                            else
                                $seleccionado="";
                  ?>
                  <option value="<?php echo $registro2['campo']; ?>" <?php echo $seleccionado; ?>><?php echo $registro2["campo"]; ?></option>
                  <?php } ?>
                </select>  
                <?php $query2->closeCursor(); ?> 
