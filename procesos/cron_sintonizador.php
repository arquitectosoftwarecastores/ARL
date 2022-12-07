<?php

include_once('../conexion/conexion.php');
include_once('../funciones/checazona.php');
include_once('../funciones/distancia.php');
date_default_timezone_set('America/Mexico_City');

echo "Iniciando...\n";
do {

    echo "Consultando...\n";
    # Consulta de Unidades
    $conPos = "SELECT COUNT(*) AS num_cad, num_nserie_pos,
                num_latitud_pos, num_longitud_pos
            FROM tb_posiciones AS tp
            WHERE txt_descolgada_pos = '0'
                AND fec_ultimaposicion_pos > (NOW() + INTERVAL '350 minutes')
            GROUP BY num_nserie_pos, num_latitud_pos, num_longitud_pos
            HAVING COUNT(*) > 3
            ORDER BY num_cad DESC;";
    
    $qryPos = $conn->prepare($conPos);
    $qryPos->execute();

    while ($regPos = $qryPos->fetch()) {
        # Extrae Datos
        
        $nserie = $regPos['num_nserie_pos'];
        $num_cad = $regPos['num_cad'];
        $lat = $regPos['num_latitud_pos'];
        $lon = $regPos['num_longitud_pos'];

        echo "\n * * * * * * * * * * * * * * * * * * * * *\n\n";
        echo "No. Serie: ".$nserie." - ".$num_cad."\n";
        echo $lat.", ".$lon."\n";

        # -------------- Valida Posicion -------------- 
        // Valida no encontrarce en posicon de reset

        // Calcula Distancia y Convierte a Metros
        $dist = (float) distancia($lat, $lon, 19.445874, -99.126892) * 1000;
        echo "Dist Res:".$dist."\n";

        if ($dist > 100) {
            # -------------- Consulta Vehiculo -------------- 
    
            $conVeh = "SELECT * FROM avl_secundario AS tsec
                    INNER JOIN tb_vehiculos AS tv
                        ON tv.num_serie_veh = tsec.sec_primario
                    WHERE
                        status = 1 AND tv.num_serie_veh not like '05%' AND 
                        (CAST(sec_secundario AS BIGINT) = ? 
                        OR 
                        CAST(sec_primario AS BIGINT) = ?)";
            $qryVeh = $conn->prepare($conVeh);
            $qryVeh->bindParam(1, $nserie);
            $qryVeh->bindParam(2, $nserie);
            $qryVeh->execute();
            $regVeh = $qryVeh->fetch();
            
            $eco = $regVeh['txt_economico_veh'];
            $pos = $regVeh['txt_posicion_veh'];
            
            if (isset($eco) & $eco != '') {
                echo "Economico: ".$eco."\n";
        
                # -------------- Consulta ultima alerta -------------- 
                $zonaferry = checazona($lat, $lon, 1, $conn);
                
                $conAlt = "SELECT COUNT(*) AS num_ale
                        FROM tb_alertas 
                        WHERE fk_clave_tipa = 15 AND txt_economico_veh = ? 
                            AND fec_fecha_ale > (NOW() - INTERVAL '30 MINUTES')";
                $qryAlt = $conn->prepare($conAlt);
                $qryAlt->bindParam(1, $eco);
                $qryAlt->execute();
                $regAlt = $qryAlt->fetch();
                
                if ($regAlt['num_ale'] == 0 && $zonaferry != 4192) {
                    $inCas = "INSERT INTO tb_alertas (fk_clave_tipa, fec_fecha_ale, txt_ubicacion_ale, txt_economico_veh, num_latitud_ale, num_longitud_ale, num_tipo_ale, num_estatus_ale, num_prioridad_ale, alerta_fecha_registro)
                        VALUES (15, NOW(), ?, ?, ?, ?, 1, 0, 3, NOW())";
                    $qryCas = $conn->prepare($inCas);
                    $qryCas->bindParam(1, $pos);
                    $qryCas->bindParam(2, $eco);
                    $qryCas->bindParam(3, $lat);
                    $qryCas->bindParam(4, $lon);
                    $qryCas->execute();
                    $qryCas->closeCursor();
                    echo "\t* Alerta Sintonizador * \n";
                }
            $qryAlt->closeCursor();
            }
            $qryVeh->closeCursor();
        } else {
            echo "Unidad en Zona Default\n";
        }
    }

    /* Inserta Fecha de Ejecución */
    $actualiza_cron_historico = "INSERT INTO tb_estatus_crones_historico(id_cron,fecha_registro) VALUES (8,now())";
    $qryInCrHi = $conn->prepare($actualiza_cron_historico);
    $qryInCrHi->execute();
    $qryInCrHi->closeCursor();

    $actualiza_cron = "UPDATE tb_estatus_crones SET ultimo_registro= NOW() WHERE id_cron = 8";
    $qryUpCon = $conn->prepare($actualiza_cron);
    $qryUpCon->execute();
    $qryUpCon->closeCursor();

    $qryPos->closeCursor();
    sleep(600);
} while (TRUE);

?>
