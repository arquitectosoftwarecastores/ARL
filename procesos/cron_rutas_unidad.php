<?php

include('../conexion/conexion.php');
include('../funciones/checazona.php');
date_default_timezone_set('America/Mexico_City');


do {
    echo "Inicia Rutas Unidades...\n";

    # Consulta Rutas y Unidades
    $conRuta = "SELECT * 
                FROM tb_registro_rutas_unidades AS trru
                INNER JOIN tb_vehiculos AS tv 
                    ON trru.unidad = tv.txt_economico_veh
                LEFT JOIN avl_secundario AS asec
                    ON (tv.num_serie_veh = asec.sec_primario 
                        OR tv.num_serie_veh = asec.sec_secundario)
                WHERE destino IS NULL AND status = 1";
    $qryRuta = $conn->prepare($conRuta);
    $qryRuta->execute();

    while ($regRuta = $qryRuta->fetch()) {

        # Extrae Datos de la Unidad
        $uni = $regRuta["txt_economico_veh"];
        $ser = $regRuta["num_serie_veh"];
        $ser1 = $regRuta["sec_primario"];
        $ser2 = $regRuta["sec_secundario"];
        $pos = $regRuta["txt_posicion_veh"];
        $ign = $regRuta["num_ignicion_veh"];
        
        echo "Unidad: ". $uni. "\n";
        echo "Ser1: ".$ser1."\n";
        echo "Ser2: ".$ser2."\n";

        # Consulta Ultima Posicion
        $conPos = "SELECT * 
                    FROM tb_posiciones 
                    WHERE num_nserie_pos = ? OR num_nserie_pos = ? 
                    ORDER BY fec_ultimaposicion_pos DESC
                    LIMIT 1";
        $qryPos = $conn->prepare($conPos);
        $qryPos->bindParam(1, $ser1);
        $qryPos->bindParam(2, $ser2);
        $qryPos->execute();
        $regPos = $qryPos->fetch();

        $lat = $regPos["num_latitud_pos"];
        $lon = $regPos["num_longitud_pos"];

        echo "Coord: ".$lat.", ".$lon."\n";

        $qryPos->closeCursor();


        # ----------- ChecaZonas ----------------------
        # Checa Zona en Caseta
        $zonCaseta = checazona($lat, $lon, -10, $conn);

        # Checa Zona en Destino
        $zonDestino = checazona($lat, $lon, 2, $conn);


        echo " - Caseta: ". $zonCaseta ."\n";
        echo " - Destino: ". $zonDestino ."\n";

        /**
         * 0 - ID Zonas
         * 1 - Hora Llegada Unidad
         * 2 - Hora Estimada
         */

        # ----------- ID de Zonas ----------------------

        # Casetas
        $caseta[0][0] = 171;
        $caseta[1][0] = 126;
        $caseta[2][0] = 104;
        $caseta[3][0] = 3829;
        $caseta[4][0] = 546;
        $caseta[5][0] = 90;
        # Destino
        $destino[0] = 645;


        # ----------- Tiempos ----------------------
        
        # Hora Salida
        $salida = date('2020-04-03 19:00:00');
        # Hora Actual
        $now = date('Y-m-d H:i:s');

        # Obtiene las Horas de LLegada
        $caseta[0][1] = $regRuta["caseta1"];
        $caseta[1][1] = $regRuta["caseta2"];
        $caseta[2][1] = $regRuta["caseta3"];
        $caseta[3][1] = $regRuta["caseta4"];
        $caseta[4][1] = $regRuta["caseta5"];
        $caseta[5][1] = $regRuta["caseta6"];
        $destino[1] = $regRuta["destino"];
    
        # Hora de llegada a casetas estimada
        $caseta[0][2] = dateCaseta($salida, 0, 20);
        $caseta[1][2] = dateCaseta($caseta[0][2], 0, 30);
        $caseta[2][2] = dateCaseta($caseta[1][2], 1, 30);
        $caseta[3][2] = dateCaseta($caseta[2][2], 1, 20);
        $caseta[4][2] = dateCaseta($caseta[3][2], 5, 0);
        $caseta[5][2] = dateCaseta($caseta[4][2], 1, 0);
        # Hora Destino
        $destino[2] = dateCaseta($caseta[5][2], 1, 0);


        $alerta = 0;


        echo " - Salida: ".$salida."\n";
        echo " - Hora: ".$now."\n";


        echo "\tEn Ruta\n";
        
        
        # ----------- Validacion de Casetas ----------------------
        # Realiza Recorrido Casetas
        for ($i=0; $i < 6; $i++) {
            $z = $i + 1; 
            echo "\t - Caseta ".$z.": ".$caseta[$i][2]." - ";
            
            # Valida si ya fue registrada
            if ($caseta[$i][1] == NULL) {
                
                # Valida si se encuentra en Caseta
                if ($caseta[$i][0] == $zonCaseta) {
                    echo "Unidad en Caseta\n";
                    
                    $upCas = "UPDATE tb_registro_rutas_unidades
                    SET caseta".$z." = NOW()
                    WHERE unidad = ?";
                    $qryCas = $conn->prepare($upCas);
                    $qryCas->bindParam(1, $uni);
                    $qryCas->execute();
                    $qryCas->closeCursor();
                    $alerta = 0;
                
                    # Valida tiempo de llegada
                    if (strtotime($now) > strtotime($caseta[$i][2])) {
                        # Valida si la Unidad esta en tiempo
                        echo "Unidad fuera de tiempo\n";
                        $alerta = 1;
                    } else {
                        echo "Sin Registro\n";
                    }
                }

            } else {
                echo $caseta[$i][1];
                $alerta = 0;
            }

            echo "\n";

        }
        
        # ----------- Validacion de Destinos ----------------------
        echo "\t - Destino: ".$destino[2]." - ";

        # Valida si la Unidad ya se encuentra en destino
        if ($destino[0] == $zonDestino) {
            echo "Unidad en Destino\n";
    
            $inCas = "UPDATE tb_registro_rutas_unidades
                    SET destino = NOW()
                    WHERE unidad = ?";
            $qryCas = $conn->prepare($inCas);
            $qryCas->bindParam(1, $uni);
            $qryCas->execute();
            $qryCas->closeCursor();

        
            # Valida el tiempo
            if (strtotime($now) > strtotime($destino[2])) {
                # Valida Tiempo de llegada a destino
                echo "Unidad fuera de tiempo\n";
                $alerta = 1;
            } else {
                echo "Sin Registro";
            }
        }
            
            
        # ----------- Validacion de Alertas ----------------------    
        # Valida si debe Insertar Alerta
        if ($alerta == 1) {
            # Consulta ultima alerta
            $conCad = "SELECT COUNT(*) AS num_ale
                    FROM tb_alertas 
                    WHERE fk_clave_tipa = 10 AND txt_economico_veh = ? AND fec_fecha_ale > (NOW() - INTERVAL '30 MINUTES')";
            $qryCad = $conn->prepare($conCad);
            $qryCad->bindParam(1, $uni);
            $qryCad->execute();
            $regCad = $qryCad->fetch();

            if ($regCad["num_ale"] == 0) {
                $inCas = "INSERT INTO tb_alertas (fk_clave_tipa, fec_fecha_ale, txt_ubicacion_ale, txt_economico_veh, num_latitud_ale, num_longitud_ale, num_tipo_ale, num_estatus_ale, num_prioridad_ale, alerta_fecha_registro)
                VALUES (10, NOW(), ?, ?, ?, ?, 1, 0, 3, NOW())";
                $qryCas = $conn->prepare($inCas);
                $qryCas->bindParam(1, $pos);
                $qryCas->bindParam(2, $uni);
                $qryCas->bindParam(3, $lat);
                $qryCas->bindParam(4, $lon);
                $qryCas->execute();
                $qryCas->closeCursor();

                echo "\t* Alerta Desvio Circuito * \n";
            }
            $qryCad->closeCursor();
        }

        echo "\n";
    }

    $qryRuta->closeCursor();
    sleep(10);

} while (TRUE);


function dateCaseta ($date, $hours, $minutes) {
    $hours = '+ '. $hours . ' Hours';
    $minutes = '+ '. $minutes . ' Minutes';
    
    $date = date('Y-m-d H:i:s', strtotime($date. $hours. $minutes));

    return $date;
}


?>
