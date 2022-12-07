    <?php
      if($seccion=="acceso")
      	$titulo="";
      else      	
      	$titulo=$seccion;
    ?>
    <?php if($seccion!="monitoreo") { ?>
      <div class="container-fluid fondocolor">
      	<div class="row">
      		<div class="col-md-3 izquierda"><h1 class="blanco"><?php echo strtoupper($titulo)?></h1></div>
        </div>      
      </div>
    <?php } ?>