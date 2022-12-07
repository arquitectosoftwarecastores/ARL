<?php
// Verifica que el usuario exista en la BD de Castores
$servidor="192.168.0.13";
$usuar="usuarioWin";
$passw = "windows";
$bd = "personal";

$usuario = $_POST["usuario"];

try{
      // Conexion y consulta en mysql
      $var=0;
      $con = new PDO("mysql:host=".$servidor.";dbname=".$bd."",$usuar,$passw);
      $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $consult = 'SELECT COUNT(*) FROM personal WHERE idusuario = '. $usuario.' AND status = 1';
      $datos = $con->query($consult);
      foreach($datos as $row){
          //echo $row[0] . '<br/>';
          $var =  $row[0];
      }

      echo $var;

}catch(PDOException $e) {
    // report error message
    echo "Error de conexion: ".$e->getMessage();
}
?>
