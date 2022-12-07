<?php

/** Clase de GPS */

class Cadena {

    public $strcadena;
    public $data;

    public $tipo;               // 0    - HDR
    public $id_vehiculo;        // 1    - DEV_ID
    public $modelo_gps;         // 3    - MODEL (Guarda si es 600 0 300)
    public $señal_estatus;      // 5    - MSG_TYPE (1 tiempo real / 0 buffer)
    public $fecha;              // 6    - DATE
    public $hora;               // 7    - TIME
    public $señal_intensidad;   // 12   - RX_LVL Guarda la intesidad de la seña
    public $lat;                // 13   - LAT
    public $lon;                // 14   - LON 
    public $velocidad;          // 15   - SPD
    public $orientacion;        // 16   - CRS
    public $fix;                // 18   - FIX 
    public $ignicion;           // 19   - IN_STATE
    
    // STT / ALT
    public $modo;               // 21   - ALERT_ID / MODE 
    public $id_msg;             // 23   - ALERT_MOD / STT_RPT_TYPE

    // ALT
    public $descripcion;

    public $dist;           //
    public $desc;           //

    // Datos del motor (en Cadenas GED)
    public $speed;          //
    public $odometro;       //
    public $comb_total;     //

    //Datos solicitados a motor
    public $com_ocioso;     //  
    public $presion_aceite; //
    public $rpm;            //    
    public $tiempo_crucero; //
    public $dtc;            //     
    public $rendimiento;    //


    /** Constructor para crear objeto con valores
     * Recibe: String de la cadena del GPS
     */
    function __construct($strcadena) {
        // Divide Cadena
        $this->CadenaDividida = explode(";", $strcadena);

        // Almacena Variables en Objeto
        $this->strcadena = $strcadena;
        $this->tipo = $this->CadenaDividida[0];               //
        $this->id_vehiculo = $this->CadenaDividida[1];        //
        $this->modelo_gps = $this->CadenaDividida[3];         //
        $this->señal_estatus = $this->CadenaDividida[5]; // 
        $this->fecha = $this->CadenaDividida[6];              //
        $this->hora = $this->CadenaDividida[7];               //
        $this->fechahora = $this->fecha . " " . $this->hora;
        $this->señal_intensidad = $this->CadenaDividida[12];  //
        $this->lat = round((float) $this->CadenaDividida[13], 6);     //
        $this->lon = round((float) $this->CadenaDividida[14], 6);     //
        $this->velocidad = (float) $this->CadenaDividida[15]; //
        $this->orientacion = (float) $this->CadenaDividida[16];       //
        $this->fix = $this->CadenaDividida[18]; //
        $this->ignicion = $this->CadenaDividida[19]; //
        
        // STT / ALT
        $this->modo = (int) $this->CadenaDividida[21]; //

        // STT 
        $this->id_msg = (int) $this->CadenaDividida[23]; //

        $this->comb_total = 0; // 
        $this->odometro = 0; //
        $this->dist = 0;

        $this->rpm = 0;  //
        $this->com_ocioso = 0; //
        $this->presion_aceite = 0; //
        $this->rendimiento = 0; //
        $this->tiempo_crucero = "1|"; //
        $this->speed = 0; //
        $this->data =  " "; //
        $this->desc = 0; //
        $this->dtc = 0;

        # Valida Existencia de Algunos Campos

        // Distancia Recorrida
        // $this->dist = $CadenaDividida[17]; Utilizado en el Modelo Anterior

        /*
        (isset($CadenaDividida[18])) ?
            $this->dist = $CadenaDividida[18] :
            $this->dist = 0;

        // Odometro
        (isset($CadenaDividida[22])) ?
            $this->odometro = $CadenaDividida[22] :
            $this->odometro = 0;

        // Combustible Total
        (isset($CadenaDividida[23])) ?
            $this->comb_total = $CadenaDividida[23] :
            $this->comb_total = 0;
        */
        // print_r($this);
    }

    /** Procesamos la posiscion e insertamos en tb_posiciones */
    function Posicion ($conn) {

        $s = substr($this->ignicion, 0, 1);

        $insSTT = "INSERT INTO monitoreo.tb_posiciones 
                    VALUES 
                        (default,?,?,?,?,?,?,?,?,?,?,?,?,?,'','',0,'',?,?,?,?,?,1,?,?,current_timestamp)";
        $querySTT = $conn->prepare($insSTT);

        $querySTT->bindParam(1, $this->tipo);
        $querySTT->bindParam(2, $this->id_vehiculo);
        $querySTT->bindParam(3, $this->fechahora);
        $querySTT->bindParam(4, $this->fechahora);
        $querySTT->bindParam(5, $this->lat);
        $querySTT->bindParam(6, $this->lon);
        $querySTT->bindParam(7, $s);
        $querySTT->bindParam(8, $this->comb_total);
        $querySTT->bindParam(9, $this->com_ocioso);
        $querySTT->bindParam(10, $this->odometro);
        $querySTT->bindParam(11, $this->señal_estatus);
        $querySTT->bindParam(12, $this->presion_aceite);
        $querySTT->bindParam(13, $this->rpm);
        $querySTT->bindParam(14, $this->velocidad);
        $querySTT->bindParam(15, $this->orientacion);
        $querySTT->bindParam(16, $this->presion_aceite);
        $querySTT->bindParam(17, $this->señal_intensidad);
        $querySTT->bindParam(18, $this->rendimiento);
        $querySTT->bindParam(19, $this->id_msg);
        $querySTT->bindParam(20, $this->strcadena);

        $querySTT->execute();
        $querySTT->closeCursor();

        unset($fechahora);
    }


    /** Funcion para maipular las alertas */
    function Alerta ($conn) {
        switch ($this->modo) {
            case 11:
                $this->descripcion = "Boton Panico";
                break;

            case 50:
                $this->descripcion = "Jammer";
                break;

            case 41:
                $this->descripcion = "Desconexion de Energia";
                break;

            case 16:
                $this->descripcion = "Collission";
                break;
        }


        echo $this->descripcion . " - ";

        if (strlen($this->descripcion) > 0) {
            $inALT = "INSERT INTO monitoreo.avl_alertas 
                    VALUES 
                        (default, ?, ? , '',
                        ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, '', '', ?, 
                        ?, ?, 0, NOW(), ?)";
            $queryALT = $conn->prepare($inALT);

            $queryALT->bindParam(1, $this->tipo);
            $queryALT->bindParam(2, $this->fechahora);
            $queryALT->bindParam(3, $this->id_vehiculo);
            $queryALT->bindParam(4, $this->ignicion);
            $queryALT->bindParam(5, $this->orientacion);
            $queryALT->bindParam(6, $this->señal_intensidad);
            $queryALT->bindParam(7, $this->lat);
            $queryALT->bindParam(8, $this->lon);
            $queryALT->bindParam(9, $this->modo);
            $queryALT->bindParam(10, $this->descripcion);
            $queryALT->bindParam(11, $this->señal_estatus);
            $queryALT->bindParam(12, $this->velocidad);
            $queryALT->bindParam(13, $this->odometro);
            $queryALT->bindParam(14, $this->comb_total);
            $queryALT->bindParam(15, $this->strcadena);
            $queryALT->execute();
            $queryALT->closeCursor();
        }
    }

    /** Actualiza ctg_vehiculos, si el numero de serie no existe , lo inserta */
    function UpdateCtg($conn) {

        $consulta_ctg = "SELECT veh_uposicion 
                        FROM ctg_vehiculos 
                        WHERE veh_nserie = ?";
        $query_ctg = $conn->prepare($consulta_ctg);
        $fechahora = $this->fecha . " " . $this->hora;
        $query_ctg->bindParam(1, $this->id_vehiculo);
        $query_ctg->execute();
        $registro_ctg = $query_ctg->fetch();
        $presion = 0;

        if (isset($registro_ctg['veh_uposicion'])) {
            // Actualiza unidad si existe en ctg_vehiculos
            $consulta_update = "UPDATE ctg_vehiculos 
                            SET 
                                veh_uposicion = ?,
                                veh_latitud = ?,
                                veh_longitud = ?,
                                veh_ignicion = ?,
                                veh_combtot = ?, 
                                veh_comboci = ?,
                                veh_odometro = ?, 
                                veh_ralenti = ?,
                                veh_taceite = ?,
                                veh_rpm = ?,
                                veh_velcruc = ?, 
                                veh_descolgada = ?,
                                veh_velocidad = ?,
                                veh_orientacion = ?
                            WHERE 
                                veh_nserie = ?";
            $query_update = $conn->prepare($consulta_update);
            $query_update->bindParam(1, $fechahora);
            $query_update->bindParam(2, $this->lat);
            $query_update->bindParam(3, $this->lon);
            $query_update->bindParam(4, $this->ignicion);
            $query_update->bindParam(5, $this->comb_total);
            $query_update->bindParam(6, $this->com_ocioso);
            $query_update->bindParam(7, $this->odometro);
            $query_update->bindParam(8, $this->señal_estatus);
            $query_update->bindParam(9, $presion);
            $query_update->bindParam(10, $this->rpm);
            $query_update->bindParam(11, $this->tiempo_crucero);
            $query_update->bindParam(12, $this->speed);
            $query_update->bindParam(13, $this->velocidad);
            $query_update->bindParam(14, $this->orientacion);
            $query_update->bindParam(15, $this->id_vehiculo);
            $query_update->execute();

            $query_update->closeCursor();
        } else {
            // Inserta la unidad, si no existe en ctg_vehiculos
            $this->InsertCtg($conn);
        }
        $query_ctg->closeCursor();
    }

    /* Actualiza avl_secundario, dependiendo si la cadena es primario o secundario */
    function UpdateSecundario($conn) {
        $query_sec = "";
        $retorno = $this->VerificaSecundario($conn);

        if ($this->señal_estatus == 1 && $retorno == 1) {
            # Valida Modelo del GPS
            switch ($this->modelo_gps) {
                case 54:
                    $query_sec = "UPDATE avl_secundario 
                                    SET sec_primarioupos = ? 
                                    WHERE sec_primario = ? ";
                    break;
                case 58:
                    $query_sec = "UPDATE avl_secundario 
                                    SET sec_secundarioupos = ? 
                                    WHERE sec_secundario = ?";
                    break;
            }

            $query_update_sec = $conn->prepare($query_sec);
            $query_update_sec->bindParam(1, $this->fechahora);
            $query_update_sec->bindParam(2, $this->id_vehiculo);
            $query_update_sec->execute();
            $query_update_sec->closeCursor();
        }
    }

    /** Verifca si la fecha-hora de la cadena es mas actual, que la almacenada en avl_secundario
    * Si es mas actual, actualiza el campo, devuelve 1
    * No, no actualiza el campo, devuelve 2 
    */
    function VerificaSecundario ($conn) {
        $retorno = 2;

        if ($this->señal_estatus == 1) {

            switch ($this->modelo_gps) {
                case 54:
                    $query_sec = "SELECT sec_primarioupos AS fecha 
                                FROM avl_secundario 
                                WHERE sec_primario = ?";
                    break;
                case 58:
                    $query_sec = "SELECT sec_secundarioupos AS fecha 
                                FROM avl_secundario 
                                WHERE sec_secundario = ?";
                    break;
            }

            $query_update_sec = $conn->prepare($query_sec);
            $query_update_sec->bindParam(1, $this->id_vehiculo);
            $query_update_sec->execute();
            $resultado = $query_update_sec->fetch();
            
            $fechaBD = $resultado['fecha'];
            $query_update_sec->closeCursor();

            // Crea Fechas
            $fechaBD = date_create($fechaBD);
            $fechaCadena = date_create($this->fechahora);
            // Da Formatos de fecha
            $fechaBD = $fechaBD->format('Y-m-d H:i:s');
            $fechaCadena = $fechaCadena->format('Y-m-d H:i:s');

            // Compara Fechas
            if ($fechaCadena > $fechaBD) {
                $retorno = 1;
            } else {
                $retorno = 2;
            }

            unset($fechaBD);
            unset($fechaCadena);
        }

        return $retorno;
    }


    /** Inserta en ctg_vehiculos, si el numero serie no existe */
    function InsertCtg ($conn) {
        $fechahora = $this->fecha . " " . $this->hora;
        $presion = 0;
        $consulta_insert = " INSERT INTO ctg_vehiculos 
                        VALUES (default,?,?,?,?,?,?,?,?,?,?,?,'',?,?,'',?,'0')";
        $query_insert = $conn->prepare($consulta_insert);
        $fechahora = $this->fecha . " " . $this->hora;
        $query_insert->bindParam(1, $this->id_vehiculo);
        $query_insert->bindParam(2, $fechahora);
        $query_insert->bindParam(3, $this->lat);
        $query_insert->bindParam(4, $this->lon);
        $query_insert->bindParam(5, $this->ignicion);
        $query_insert->bindParam(6, $this->comb_total);
        $query_insert->bindParam(7, $this->com_ocioso);
        $query_insert->bindParam(8, $this->odometro);
        $query_insert->bindParam(9, $this->señal_estatus);
        $query_insert->bindParam(10, $presion);
        $query_insert->bindParam(11, $this->rpm);
        $query_insert->bindParam(12, $this->tiempo_crucero);
        $query_insert->bindParam(13, $this->speed);
        $query_insert->bindParam(14, $this->orientacion);
        $query_insert->execute();
        $query_insert->closeCursor();

        echo "----------NUEVA UNIDAD INSERTADA-------------------";

        unset($fechahora);
    }
}

?>