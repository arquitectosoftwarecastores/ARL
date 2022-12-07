<?php
if (isset($_GET["economico"])) {
    include ('../conexion/conexion.php');
    session_name('ARL');
    session_start();
    $id = $_GET["economico"];
    $myArr = array();
    $valUni = true;
    if ($valUni) {
        $consulta  = " SELECT *
						FROM tb_usuarios AS tu
                        INNER JOIN tb_circuitosxusuario AS tcxu
						    ON tu.pk_clave_usu = tcxu.fk_clave_usu
						INNER JOIN tb_remolques AS tv
						    ON tcxu.fk_clave_cir = tv.fk_clave_cir
						WHERE 
							pk_clave_usu = ? AND
							txt_economico_rem = ?";
        $query = $conn->prepare($consulta);
        $query->bindParam(1, $_SESSION['id']);
        $query->bindParam(2, $id);
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
