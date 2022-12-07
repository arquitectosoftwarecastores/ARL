<?php  session_start(); ?>    
<!doctype html>
<html lang="es">
<?php 
    error_reporting(E_ALL);
    ini_set('display_errors', true);
 $cad="5161";
$id_geocerca = substr($cad, 1, strlen($cad));
                $id_geocerca = intval($id_geocerca * 1);

                echo  $id_geocerca;s

?>