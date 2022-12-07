<?php
include("../../conexion/conexion.php");

// Verifica que el usuario exista en la BD de Castores
$servidor="192.168.0.13";
$usuar="usuarioWin";
$passw = "windows";
$bd = "camiones";

$noeconomico = $_POST["numero"];
$myArr = array('noeconomico' => '0','tipounidad' => '','status' => 0);

try{

    // Verifica si ya esta registrado en el AVL
    $obten = "SELECT txt_economico_veh,tipo,status FROM tb_vehiculos WHERE txt_economico_veh = ?";
    $query = $conn -> prepare($obten);
    $query->bindParam(1, $noeconomico);
    $query->execute();
    $registro = $query->fetch();

    if ($registro){
        unset ($myArr);
        $myArr = array('noeconomico' => $registro['txt_economico_veh'],'tipounidad' => $registro['tipo'], 'status' => $registro['status'] );
    }
    else {

      // Conexion y consulta en mysql
      $var=0;
      $con = new PDO("mysql:host=".$servidor.";dbname=".$bd."",$usuar,$passw);
      $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $consult = 'SELECT noeconomico, tipounidad FROM unidades WHERE noeconomico = '. $noeconomico.' AND estatus = 1 AND (tipounidad = 1 OR tipounidad = 2 OR tipounidad = 5)';
      $datos = $con->query($consult);
      foreach($datos as $row){
        //echo $row[0] . '<br/>';
        $myArr = array('noeconomico' => $row[0],'tipounidad' => $row[1] , 'status' => 0);
      }


    }

    $query->closeCursor();

    $myJSON = json_encode($myArr);
    echo $myJSON;

}catch(PDOException $e) {
    // report error message
    echo "Error de conexion: ".$e->getMessage();
}
?>
