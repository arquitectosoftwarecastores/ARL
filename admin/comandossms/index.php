<?php 
    if(isset($_SESSION["comandossms"])) 
    {
	 	$accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'captura';
		switch ($accion)
		{
			case "captura":				
					include("admin/comandossms/app_captura.php");
				break;
			case "envia":				
					include("admin/comandossms/app_envia.php");
				break;											
	 	}
	}
	else
	{
     ?>
		<div class="container">
		   <div class="alert alert-warning">
		        <a href="#" class="close" data-dismiss="alert">&times;</a>
		        <strong>Su usuario no tiene acceso a este módulo</strong>.
		    </div>
		</div>
    <?php
	}
?>
