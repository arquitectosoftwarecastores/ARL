<?PHP

// ********************************************************************************************
// Función que determina si una unidad se encuentra en una zona de riesgo, interes o segura
// ********************************************************************************************
//Creamos una funcion para saber si una unidad se encuentra en esa zona

/* determinamos si la unidad se ha detenido en zona de riesgo */
function detenidaenzona($lat, $lon, $zr) {

    if ($zr == '3210' && ($lat + $lon) < .01) {
        generaalertapordetencion();
    }

    $principal = 0;
    $contador = 0;
    $zonasarr = '';
    $oddNodes = 0;

    $consulta_zonas = "SELECT * from tb_zonas ORDER BY fk_clave_tipz DESC";
    //  select * from tb_zonas z left join monitoreo.definirtipodezona tz on z.pk_clave_zon = clave_zon where tz.tipo is null order by pk_clave_zon asc 
    $query = $conn->prepare($consulta_zonas);
    $query->execute();
    $oddNodes = false;

    while ($row_zonas = $query->fetch()) {
        $consulta_segmentos = "SELECT * from tb_detallezonas where fk_clave_zon = " . $row_zonas['pk_clave_zon'] . " ORDER BY pk_clave_det ASC";
        $query1 = $conn->prepare($consulta_segmentos);
        $query1->execute();
        $arr_x = array();
        $arr_y = array();
        $total_vertices = 0;
        $oddNodes = false;
        while ($row_segmentos = $query1->fetch()) {
            $arr_x[] = $row_segmentos['num_longitud_zon'];
            $arr_y[] = $row_segmentos['num_latitud_zon'];
            $total_vertices++;
        }
        # $total_vertices--;
        $j = 0;

        for ($i = 0; $i < $total_vertices; $i++) {
            $j++;
            if ($j == $total_vertices) {
                $j = 0;
            }
            if ((($arr_y[$i] < $y_lat) and ($arr_y[$j] >= $y_lat)) or (($arr_y[$j] < $y_lat) and ($arr_y[$i] >= $y_lat))) {

                if ($arr_x[$i] + ($y_lat - $arr_y[$i]) / ($arr_y[$j] - $arr_y[$i]) * ($arr_x[$j] - $arr_x[$i]) < $x_lng) {

                    $oddNodes = !$oddNodes;
                }
            }
        }
        if ($oddNodes) {
            return $row_zonas["pk_clave_zon"];
        }
    }

    if ($oddNodes == "") {
        $oddNodes = 0;
    }
    return $oddNodes;
}

function checazonaprioridad($y_lat, $x_lng, $conn) {
    $principal = 0;
    $contador = 0;
    $zonasarr = '';
    $oddNodes = 0;

    $consulta_zonas = "SELECT * from tb_zonas ORDER BY fk_clave_tipz DESC";
    //  select * from tb_zonas z left join monitoreo.definirtipodezona tz on z.pk_clave_zon = clave_zon where tz.tipo is null order by pk_clave_zon asc 
    $query = $conn->prepare($consulta_zonas);
    $query->execute();
    $oddNodes = false;

    while ($row_zonas = $query->fetch()) {
        $consulta_segmentos = "SELECT * from tb_detallezonas where fk_clave_zon = " . $row_zonas['pk_clave_zon'] . " ORDER BY pk_clave_det ASC";
        $query1 = $conn->prepare($consulta_segmentos);
        $query1->execute();
        $arr_x = array();
        $arr_y = array();
        $total_vertices = 0;
        $oddNodes = false;
        while ($row_segmentos = $query1->fetch()) {
            $arr_x[] = $row_segmentos['num_longitud_zon'];
            $arr_y[] = $row_segmentos['num_latitud_zon'];
            $total_vertices++;
        }
        $j = 0;

        for ($i = 0; $i < $total_vertices; $i++) {
            $j++;
            if ($j == $total_vertices) {
                $j = 0;
            }
            if ((($arr_y[$i] < $y_lat) and ($arr_y[$j] >= $y_lat)) or (($arr_y[$j] < $y_lat) and ($arr_y[$i] >= $y_lat))) {

                if ($arr_x[$i] + ($y_lat - $arr_y[$i]) / ($arr_y[$j] - $arr_y[$i]) * ($arr_x[$j] - $arr_x[$i]) < $x_lng) {

                    $oddNodes = !$oddNodes;
                }
            }
        }
        if ($oddNodes) {
            return $row_zonas["pk_clave_zon"];
        }
    }

    if ($oddNodes == "") {
        $oddNodes = 0;
    }
    return $oddNodes;
}

function checazona($y_lat, $x_lng, $tipo, $conn) {
    $principal = 0;
    $contador = 0;
    $zonasarr = '';
    $oddNodes = 0;
    if ($tipo == 4) {
        $consulta_zonas = "SELECT * from tb_zonas ORDER BY fk_clave_tipz DESC";
        //  select * from tb_zonas z left join monitoreo.definirtipodezona tz on z.pk_clave_zon = clave_zon where tz.tipo is null order by pk_clave_zon asc 
        $query = $conn->prepare($consulta_zonas);
    } else {
        $consulta_zonas = "SELECT * from tb_zonas WHERE fk_clave_tipz=?";
        $query = $conn->prepare($consulta_zonas);
        $query->bindParam(1, $tipo);
    }

    $query->execute();
    $oddNodes = false;

    while ($row_zonas = $query->fetch()) {
        $consulta_segmentos = "SELECT * from tb_detallezonas where fk_clave_zon = " . $row_zonas['pk_clave_zon'] . " ORDER BY pk_clave_det ASC";
        $query1 = $conn->prepare($consulta_segmentos);
        $query1->execute();
        $arr_x = array();
        $arr_y = array();
        $total_vertices = 0;
        $oddNodes = false;
        while ($row_segmentos = $query1->fetch()) {
            $arr_x[] = $row_segmentos['num_longitud_zon'];
            $arr_y[] = $row_segmentos['num_latitud_zon'];
            $total_vertices++;
        }
        $j = 0;
        for ($i = 0; $i < $total_vertices; $i++) {
            $j++;
            if ($j == $total_vertices) {
                $j = 0;
            }
            if ((($arr_y[$i] < $y_lat) and ($arr_y[$j] >= $y_lat)) or (($arr_y[$j] < $y_lat) and ($arr_y[$i] >= $y_lat))) {

                if ($arr_x[$i] + ($y_lat - $arr_y[$i]) / ($arr_y[$j] - $arr_y[$i]) * ($arr_x[$j] - $arr_x[$i]) < $x_lng) {

                    $oddNodes = !$oddNodes;
                }
            }
        }
        if ($oddNodes) {
            return $row_zonas["pk_clave_zon"];
        }
    }
    if ($oddNodes == "") {
        $oddNodes = 0;
    }
    // return $principal;
    return $oddNodes;
}

function checazona_riesgo($y_lat, $x_lng, $tipo, $conn) {
    $principal = 0;
    $contador = 0;
    $zonasarr = '';
    $oddNodes = 0;
    if ($tipo == 0) {
        $consulta_zonas = "SELECT * from tb_zonas ORDER BY fk_clave_tipz DESC";
        //  select * from tb_zonas z left join monitoreo.definirtipodezona tz on z.pk_clave_zon = clave_zon where tz.tipo is null order by pk_clave_zon asc 
        $query = $conn->prepare($consulta_zonas);
    } else {
        $consulta_zonas = "SELECT * from tb_zonas WHERE fk_clave_tipz=?";
        $query = $conn->prepare($consulta_zonas);
        $query->bindParam(1, $tipo);
    }
    $query->execute();
    $oddNodes = false;
    while ($row_zonas = $query->fetch()) {
        $consulta_segmentos = "SELECT * from tb_detallezonas where fk_clave_zon = " . $row_zonas['pk_clave_zon'] . " ORDER BY pk_clave_det ASC";
        $query1 = $conn->prepare($consulta_segmentos);
        $query1->execute();
        $arr_x = array();
        $arr_y = array();
        $total_vertices = 0;
        $oddNodes = false;
        while ($row_segmentos = $query1->fetch()) {
            $arr_x[] = $row_segmentos['num_longitud_zon'];
            $arr_y[] = $row_segmentos['num_latitud_zon'];
            $total_vertices++;
        }
        $j = 0;
        for ($i = 0; $i < $total_vertices; $i++) {
            $j++;
            if ($j == $total_vertices) {
                $j = 0;
            }
            if ((($arr_y[$i] < $y_lat) and ($arr_y[$j] >= $y_lat)) or (($arr_y[$j] < $y_lat) and ($arr_y[$i] >= $y_lat))) {
                if ($arr_x[$i] + ($y_lat - $arr_y[$i]) / ($arr_y[$j] - $arr_y[$i]) * ($arr_x[$j] - $arr_x[$i]) < $x_lng) {
                    $oddNodes = !$oddNodes;
                }
            }
        }
        if ($oddNodes) {
//                    if($principal==0){           
//                        $principal=$row_zonas["pk_clave_zon"];
//                                            //inserta las zona por unidades 
//                        $inserta_geocerca  = "INSERT INTO geocercasporunidad (economico,geo1) VALUES (10000,1)";
//                        $query = $conn->prepare($inserta_geocerca);
////                        $query->bindParam(1, 10000);
//                        $query->execute();  
//                        $query->closeCursor();
//                    }
            return $row_zonas["pk_clave_zon"];
        }
    }

    if ($oddNodes == "") {
        $oddNodes = 0;
    }

    // return $principal;
    return $oddNodes;
}

?>