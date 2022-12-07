<?php
/* Versiones
    1.0 2020/07/22 Primera Versión del Nuevo Procesa Cadena
*/
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
//ini_set('max_execution_time', 300);

# Incluye Conexion y Clase Cadena
include_once ('../conexion/conexion.php');
include_once ('./class_Cadena.php');

/* 0 SIN REPORTAR
 * 1 BUFFER
 * 2 SALIDA FORMATEADA
 * 4 REGISTRO DEL GPS
 * 8 EL INSERT
 * 16 A UN ARCHIVO
 * 32 LOGS DE LA BASE DE DATOS
 * 100 MODO PRUEBA
 */



/*
$strSerie = '';
$strIPGPS = '';
$intTipo = '';
*/

echo "\n\n";

# Valida Argumento
if (isset($argv[1])) {
    echo "Cadenas a Procesar " . $argv[1] . "\n";
    
    # Valida el Tipo de Cadena que será procesada
    switch ($argv[1]) {
        case 'STT_G':
            // Posiciones
            $consulta = "SELECT * 
                        FROM avl_cadenas_g 
                        WHERE 
                            cad_estatus = 1 AND 
                            cad_tipo LIKE 'STT'
                        LIMIT 1";
            procesaCadena($consulta, $conn);
            break;


        case 'ALT_G':
            // Alertas
            $consulta = "SELECT * 
                        FROM avl_cadenas_g 
                        WHERE 
                            cad_estatus = 1 AND 
                            cad_tipo NOT LIKE 'STT'
                        LIMIT 1";
            procesaCadena($consulta, $conn);
        break;


        case 'STT_G2':
            // Posiciones
            $consulta = "SELECT * 
                        FROM avl_cadenas_g2 
                        WHERE 
                            cad_estatus = 1 AND 
                            cad_tipo LIKE 'STT'
                        LIMIT 1";
            procesaCadena($consulta, $conn);
            break;

        case 'ALT_G2':
            // Alertas
            $consulta = "SELECT * 
                        FROM avl_cadenas_g2 
                        WHERE 
                            cad_estatus = 1 AND 
                            cad_tipo NOT LIKE 'STT'
                        LIMIT 1";
            procesaCadena($consulta, $conn);
            break;
        
        default:
            echo "Tipo de Cadena no Valida\n";
            break;
    }

} else {
    echo "Por favor indique el tipo de cadena a procesar. \n";
}




/**  Funciones  */

/* Procesamos las cadenas */

# Procesa Cadenas Para Nuevo Modelo

function procesaCadena ($consulta, $conn) {

    echo "\nIniciando Procesa CadenasG2... \n";

    $strId = '';
    $strcadena = '';
    $OLDid = 0;

    do {
        # Consulta Cadena
        $qryCad = $conn->prepare($consulta);
        $qryCad->execute();
        $regCad = $qryCad->fetch();

        // si la cadena llega correctamente, entra a la condición
        if (strlen($regCad["cad_id"]) > 0 && $OLDid !== $regCad["cad_id"]) {
            try {

                echo "\n\t\t" . $regCad["cad_string"];
                $strcadena = $regCad["cad_string"];
                $strId = $regCad["cad_id"];


                /*
                    $strSerie = $regCad["cad_nserie"];
                    $strIPGPS = $regCad["cad_ip"];
                    $intTipo = $regCad["cad_tipo"];
                */
                
                # Actualiza el Estatus del registro en la tabla
                UpdateCadEstatus($strId, $conn);
                
                # Crea Objeto con Cadena del GPS
                $datosGPS = new Cadena($strcadena);


                # Valida si la existencia del Registro
                if (strcmp($datosGPS->tipo,'STT') == 0) {
                    // Inserta Posicion
                    $datosGPS->Posicion($conn);
                    
                    // Verificación en la tabla ctg_vehiculos
                    $datosGPS->UpdateCtg($conn);
                    
                    //Actualizamos la tabla de secundarios
                    $datosGPS->UpdateSecundario($conn);

                } elseif (strcmp($datosGPS->tipo, 'ALT') == 0) {
                    $datosGPS->Alerta($conn);
                }
                
                unset($datosGPS);

                $OLDid = $strId;
            } catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                usleep(1000);
            }

            $qryCad->closeCursor();
        } else {
            $qryCad->closeCursor();

            if (!isset($registro["cad_string"])) {
                echo "\n No hay datos que actuaizar. \n";
                sleep(20);
            }
        }
    } while (TRUE);
}

/** 
 * Actualiza el estatus de la cadena
 */

function UpdateCadEstatus($cad_id,$conn){
    
    $string_estatus = "UPDATE avl_cadenas_g2
                        SET 
                            cad_estatus = 2 ,
                            cad_fecharegistrobd = NOW() 
                        WHERE 
                            cad_id = ? ";
    $query_update_estatus = $conn->prepare($string_estatus);         
    $query_update_estatus->bindParam(1,$cad_id);
    $query_update_estatus->execute();    
    $query_update_estatus->closeCursor();

}

?>
