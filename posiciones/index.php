<?php 
    if(true) 
    {
    $accion = (isset($_GET['accion']) && $_GET['accion']!='') ? $_GET['accion'] : 'captura';
    switch ($accion)
    {
      case "captura":       
          include ("posiciones/for_captura.php");
        break;  
      case "lista":       
          include ("posiciones/for_captura.php");
          include("posiciones/con_elemento.php");   
          include("posiciones/for_elemento.php");
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