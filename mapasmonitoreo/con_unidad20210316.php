<?php
if (isset($_GET["economico"])) {
    include ('../conexion/conexion.php');
    session_start();
    $id = $_GET["economico"];
    $myArr = array();
    $valUni = true;
    if ($valUni) {
        $consulta  = " SELECT * FROM tb_remolques WHERE txt_economico_rem = ? AND estatus = 1";
        $query = $conn->prepare($consulta);
        $query->bindParam(1, $id);
        $query->execute();    
        while ($registro = $query->fetch()) {
            $myArr = array(
                'id'  => $registro["pk_clave_rem"],
                'lat' => $registro["num_latitud_rem"] , 
                'lon' => $registro["num_longitud_rem"],
                'fec' => $registro["fec_posicion_rem"],
                'spc' => $registro["num_seguimiento_rem"]
            );            
        }
        $query->closeCursor();
    }
    $myJSON = json_encode($myArr);
    echo $myJSON;
}
?>