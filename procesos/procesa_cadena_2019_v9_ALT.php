	/* Versiones
1.0 12/02/2019 Primera Versi�n del procesa cadena*/
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
//ini_set('max_execution_time', 300);
include ('../conexion/conexion.php');
date_default_timezone_set("America/Mexico_City");

/* 0 SIN REPORTAR
 * 1 BUFFER
 * 2 SALIDA FORMATEADA
 * 4 REGISTRO DEL GPS
 * 8 EL INSERT
 * 16 A UN ARCHIVO
 * 32 LOGS DE LA BASE DE DATOS
 * 100 MODO PRUEBA
 */

$strSerie = '';
$strIPGPS = '';
$strId = '';
$intTipo = '';
$CONTADOR = 0;
$strcadena = '';
$OLDid = 0;
class gps_suntech {

    public $data;           //1
    public $id_vehiculo;    //2
    public $tipo;           //3
    public $fecha;          //4
    public $hora;           //5
    public $lat;            //6       
    public $lon;            //7
    public $velocidad;      //8    
    public $orientacion;    //9 
    public $ignicion;       //10
    public $modo;           //11
    public $id_msg;         //12
    public $fix;            //13  
    public $dist;           //14
    public $desc;           //15     
    // Datos del motor
    public $odometro;       //16
    public $comb_total;     //17
    public $speed;          //18
    //Datos solicitados a motor
    public $com_ocioso;     //19  
    public $presion_aceite; //20
    public $rpm;            //21    
    public $tiempo_crucero; //22
    public $dtc;            //23     
    public $rendimiento;    //24
    public $modelo_gps;     //Guarda si es 600 0 300
    public $señal_intensidad;// Guarda la intesidad de la seña (campo RX_LVL en el manual)
    public $señal_estatus;// 1 si reporta en tiempo real , 0 si la cadena viene del buffer (campo MSG_TYPE en al manual)
    
}

/* Procesamos las cadenas */

echo "Iniciando Procesa Cadena... \n";

do {
    $consulta = "SELECT * 
                FROM avl_cadenas_g 
                WHERE 
                    cad_estatus = 1 AND 
                    cad_tipo NOT LIKE 'ST600STT'
                LIMIT 1";
    $query = $conn->prepare($consulta);
    $query->execute();
    while ($registro = $query->fetch()) {
        echo " Cadena: ".$registro["cad_string"]." \n";
         $strcadena = $registro["cad_string"];
        if ($registro["cad_string"] != NULL) {
            $strId = $registro["cad_id"];
            $strSerie = $registro["cad_nserie"];
            $strIPGPS = $registro["cad_ip"];
            $intTipo = $registro["cad_tipo"];
            //echo $strcadena;
        }
    }


    $evt = strpos($intTipo,"EVT");

  
    // si la cadena llega correctamente, entra a la condición
    if (strlen($strId) > 0 && $evt !== TRUE && $OLDid !== $strId) {    

        try {
            $datosGPS = new gps_suntech();    
        $dividida = separa($strcadena);
    
       
        //echo $strcadena;
        if(strpos($dividida[0], '300')){
            $datosGPS = objetoSt300($dividida);
        }elseif(strpos($dividida[0],'600' )){
            $datosGPS = ObjetoSt600($dividida);
        }

            UpdateCadEstatus($strId,$conn);

            $mensaje = "PROCESANDO CADENAS";
           
            //Inserción de las emergencias y alertas
            //verificamos que el tipo de cadena sea EMG,EVT o ALT
            if(strpos($datosGPS->tipo,'EMG') ||strpos($datosGPS->tipo,'ALT') ){
                Alerta($datosGPS,$strcadena,$conn);
               
            }

            //Insercion de posiciones
            //verificamos que el tipo de cadena sea STT
            if(strpos($datosGPS->tipo,'STT') && strlen($datosGPS->id_vehiculo)>0){
                Posicion($datosGPS,$strcadena,$conn);
                
            }

            //Verificación en la tabla ctg_vehiculos
            //solo actualizamos cuando la cadena es posicion
            if(strpos($datosGPS->tipo,'STT') && strlen($datosGPS->id_vehiculo)>0){
                UpdateCtg($datosGPS,$conn);
              
                
            }                                                                   

            //Actualizamos la tabla de secundarios
            if(strpos($datosGPS->tipo,'600STT') && strlen($datosGPS->id_vehiculo)>0){   
                UpdateSecundario($datosGPS,$conn);  
                

            }

            //print_r($datosGPS);
            //$CONTADOR ++;

            $query->closeCursor();
            unset($datosGPS);

            $OLDid = $strId;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), ' /\n';
            // sleep(0.00001);
        }
        

      
 
    }else{

   
        if( !isset($registro["cad_string"])){
                echo " No hay datos que actuaizar \n";
            sleep(2);
        }
    
    }

} while (TRUE);

/* Funcion para separar las cadenas 
*Recibe: String de la cadena completa
*Retorno: arreglo con la cadena dividida
*/
function separa ($strcadena){
   $result = explode(";",$strcadena); 
   return $result; 
}


/*funcion para  Inicializar objeto de GPS ST600
*Recibe: arreglo con la cadena dividida
*Retorno: objeto tipo gps_suntech con modelo ST600
*/
function ObjetoSt600($CadenaDividida){

    
    $datosGPS = new gps_suntech();
    $datosGPS->tipo = $CadenaDividida[0];//
    $datosGPS->id_vehiculo = $CadenaDividida[1];//
    $datosGPS->modelo_gps = $CadenaDividida[2];//
    $datosGPS->fecha = $CadenaDividida[4];//
    $datosGPS->hora = $CadenaDividida[5];//
    $datosGPS->señal_intensidad = $CadenaDividida[10];//
    $datosGPS->lat = round($CadenaDividida[11],6);//
    $datosGPS->lon = round($CadenaDividida[12],6);//
    $datosGPS->orientacion = $CadenaDividida[14];//
    $datosGPS->fix = $CadenaDividida[16];//
    $datosGPS->dist = $CadenaDividida[17];//
    $datosGPS->ignicion = $CadenaDividida[19];//
    $datosGPS->modo = $CadenaDividida[20];//
    $datosGPS->id_msg = $CadenaDividida[21];//
    $datosGPS->señal_estatus = $CadenaDividida[24]; // 
    $datosGPS->rpm = 0;  //
    $datosGPS->velocidad = 0;//
    $datosGPS->com_ocioso = 0;//
    $datosGPS->presion_aceite = 0;//
    $datosGPS->rendimiento = 0;//
    $datosGPS->tiempo_crucero = "1|";//
    $datosGPS->speed = 0;//
    $datosGPS->comb_total = 0;// 
    $datosGPS->odometro = 0;//
    $datosGPS->data =  " ";//
    $datosGPS->desc = 0;//
    $datosGPS->dtc = 0;

    //si es primario entra, si es secundario  asigna valor 0 
    if($datosGPS->modelo_gps == '34' || $datosGPS->modelo_gps == '35'){
        (isset($CadenaDividida[26])) ? $datosGPS->odometro = $CadenaDividida[26]:$datosGPS->odometro = 0;
        (isset($CadenaDividida[27])) ? $datosGPS->comb_total = $CadenaDividida[27]:$datosGPS->comb_total = 0;
    }else{
        $datosGPS->odometro = 0;
        $datosGPS->comb_total = 0;
    }
    return $datosGPS;
}

/*funcion para  Inicializar objeto de GPS ST300
*Recibe: arreglo con la cadena dividida
*Retorno: objeto tipo gps_suntech con modelo ST300
*/
function objetoSt300($CadenaDividida){
    $datosGPS = new gps_suntech();
    $datosGPS->tipo = $CadenaDividida[0];
    $datosGPS->id_vehiculo = $CadenaDividida[1];
    $datosGPS->fecha = $CadenaDividida[4];
    $datosGPS->hora = $CadenaDividida[5];
    $datosGPS->lat = $CadenaDividida[7];
    $datosGPS->lon = $CadenaDividida[8];
    ($CadenaDividida[9] == null) ? $CadenaDividida[9] = 0: TRUE;
    $datosGPS->velocidad = $CadenaDividida[9];
    $datosGPS->orientacion = $CadenaDividida[10];
    $datosGPS->fix = $CadenaDividida[12];
    $datosGPS->dist = $CadenaDividida[13];
    $datosGPS->ignicion = $CadenaDividida[15];
    $datosGPS->modo = $CadenaDividida[16];
    $datosGPS->id_msg = $CadenaDividida[17];
    $datosGPS->rpm = 0;  
    $datosGPS->com_ocioso = 0;
    $datosGPS->presion_aceite = 0;
    $datosGPS->rendimiento = 0;
    $datosGPS->tiempo_crucero = "1|";
    $datosGPS->speed = 0;
    $datosGPS->comb_total = 0;  
    $datosGPS->odometro = 0;    
    $datosGPS->data =  " ";//
    $datosGPS->desc = 0;//
    $datosGPS->dtc = 0;
    $datosGPS->señal_estatus = 0;
    $datosGPS->señal_intensidad = 0;//

    return $datosGPS;
}

/*funcion para maipular las alertas
*Recibe: Objeto de tipo gps_suntec  
*Recibe: Cadena completa
*/
function Alerta($datosGPS,$strcadena,$conn){
    if(strpos($datosGPS->tipo,'EMG')){
        InsertEMG($datosGPS,$strcadena,$conn);
    }else{
        InsertALT($datosGPS,$strcadena,$conn);
    }

}

/*inserta EMG
*Recibe: objeto tipo gps_suntech
*/
// las actuales alertas, se procesan en base al acuerdo de adecuaciones autorizado el 18 enero 2019
function InsertEMG($datosGPS,$strcadena,$conn){
    $tipo = $datosGPS->modo;
    $descripcion = "";
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    switch($tipo){
        case 1: $descripcion = "Boton Panico"; break;
        case 3: $descripcion = "Desconexion de Energia"; break;    
    }
    $datosGPS->id_msg = "0";
    $date_actual = date('Y-m-d H:i:s');
    if($datosGPS->fix >= 0){
        $query_insert_EMG = "insert into monitoreo.avl_alertas values(default,?,?,'',?,?,?,?,?,?,?,?,?,'','',?,?,?,0,?,?)";
        $queryEMG = $conn->prepare($query_insert_EMG);
        
        $queryEMG->bindParam(1,$datosGPS->tipo);
        $queryEMG->bindParam(2,$fechahora);
        $queryEMG->bindParam(3,$datosGPS->id_vehiculo);
        $queryEMG->bindParam(4,$datosGPS->ignicion);
        $queryEMG->bindParam(5,$datosGPS->orientacion);
        $queryEMG->bindParam(6,$datosGPS->señal_intensidad);
        $queryEMG->bindParam(7,$datosGPS->lat);
        $queryEMG->bindParam(8,$datosGPS->lon);
        $queryEMG->bindParam(9,$datosGPS->modo);
        $queryEMG->bindParam(10,$descripcion);
        $queryEMG->bindParam(11,$datosGPS->señal_estatus);
        $queryEMG->bindParam(12,$datosGPS->velocidad);
        $queryEMG->bindParam(13,$datosGPS->odometro);
        $queryEMG->bindParam(14,$datosGPS->comb_total);
        $queryEMG->bindParam(15,$date_actual);
        $queryEMG->bindParam(16,$strcadena);
        $queryEMG->execute();
        $queryEMG->closeCursor();

        
    }

    unset($tipo);
    unset($descripcion);
    unset($fechahora);

}

 
/*inserta ALT
*Recibe: objeto tipo gps_suntech
*/
function InsertALT($datosGPS,$strcadena,$conn){
    $tipo = $datosGPS->modo;
    $descripcion = "";
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    switch($tipo){
        case 3: $descripcion = "Desconexión de antena."; break;
        case 4: $descripcion = "Reconexión de antena."; break;        
        case 16: $descripcion = "Collission"; break;  
        case 50: $descripcion = "Jammer"; break;
        case 68: $descripcion = "Parada en viaje"; break;  
          
    }
    $datosGPS->id_msg = "0";
    $date_actual = date('Y-m-d H:i:s');
    if($datosGPS->fix >= 0){
        $query_insert_ALT = "insert into monitoreo.avl_alertas values(default,?,?,'',?,?,?,?,?,?,?,?,?,'','',?,?,?,0,?,?)";
        $queryALT = $conn->prepare($query_insert_ALT);
        
        $queryALT->bindParam(1,$datosGPS->tipo);
        $queryALT->bindParam(2,$fechahora);
        $queryALT->bindParam(3,$datosGPS->id_vehiculo);
        $queryALT->bindParam(4,$datosGPS->ignicion);
        $queryALT->bindParam(5,$datosGPS->orientacion);
        $queryALT->bindParam(6,$datosGPS->señal_intensidad);
        $queryALT->bindParam(7,$datosGPS->lat);
        $queryALT->bindParam(8,$datosGPS->lon);
        $queryALT->bindParam(9,$datosGPS->modo);
        $queryALT->bindParam(10,$descripcion);
        $queryALT->bindParam(11,$datosGPS->señal_estatus);
        ($datosGPS->velocidad == null) ? $datosGPS->velocidad = 0: TRUE;
        $queryALT->bindParam(12,$datosGPS->velocidad);
        $queryALT->bindParam(13,$datosGPS->odometro);
        $queryALT->bindParam(14,$datosGPS->comb_total);
        $queryALT->bindParam(15,$date_actual);
        $queryALT->bindParam(16,$strcadena);
        $queryALT->execute();
        $queryALT->closeCursor();
    }

    unset($tipo);
    unset($descripcion);
    unset($fechahora);

}


/*Procesamos la posiscion e insertamos en tb_posiciones
*Recibe: objeto tipo gps_suntech
*Recibe: String con la cadena completa
*/
function Posicion($datosGPS,$strcadena,$conn){
    $tipo = $datosGPS->modo;
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $query_insert_STT = "insert into monitoreo.tb_posiciones values(default,?,?,?,?,?,?,?,?,?,?,?,?,?,'','',0,'',?,?,?,?,?,1,?,?,current_timestamp)";
    $querySTT = $conn->prepare($query_insert_STT);
    
    $querySTT->bindParam(1,$datosGPS->tipo);
    $querySTT->bindParam(2,$datosGPS->id_vehiculo);
    $querySTT->bindParam(3,$fechahora);
    $querySTT->bindParam(4,$fechahora);
    $querySTT->bindParam(5,$datosGPS->lat);
    $querySTT->bindParam(6,$datosGPS->lon);
     $s = substr($datosGPS->ignicion, 0,1);
    $querySTT->bindParam(7,$s);
    $querySTT->bindParam(8,$datosGPS->comb_total);  
    $querySTT->bindParam(9,$datosGPS->com_ocioso);
    $querySTT->bindParam(10,$datosGPS->odometro);
    $querySTT->bindParam(11,$datosGPS->señal_estatus);
    $querySTT->bindParam(12,$datosGPS->presion_aceite);
    $querySTT->bindParam(13,$datosGPS->rpm);
    $querySTT->bindParam(14,$datosGPS->velocidad);
    $querySTT->bindParam(15,$datosGPS->orientacion);
    $querySTT->bindParam(16,$datosGPS->presion_aceite);
    $querySTT->bindParam(17,$datosGPS->señal_intensidad);
    $querySTT->bindParam(18,$datosGPS->rendimiento);
    $querySTT->bindParam(19,$datosGPS->id_msg);
    $querySTT->bindParam(20,$strcadena);
    
    $querySTT->execute();
    $querySTT->closeCursor();

    unset($tipo);
    unset($fechahora);

}

/*Actualiza ctg_vehiculos, si el numero de serie no existe , lo inserta
*Recibe: objeto tipo gps_suntech
*/
function UpdateCtg($datosGPS,$conn){
    
    $consulta_ctg = " SELECT  veh_uposicion from ctg_vehiculos where veh_nserie = ?";
    $query_ctg = $conn->prepare($consulta_ctg);
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $query_ctg->bindParam(1,$datosGPS->id_vehiculo);
    $query_ctg->execute();
    $registro_ctg = $query_ctg->fetch();
    $presion = 0;

    if(isset($registro_ctg['veh_uposicion'])){
        //actualiza unidad si existe en ctg_vehiculos
        $consulta_update = "UPDATE ctg_vehiculos SET veh_uposicion = ?,veh_latitud = ?,veh_longitud = ?,
                                                        veh_ignicion = ?,veh_combtot = ?, veh_comboci = ?,
                                                        veh_odometro = ?, veh_ralenti = ?,veh_taceite = ?,
                                                        veh_rpm = ?,veh_velcruc = ?, veh_descolgada = ?,
                                                        veh_velocidad = ?,veh_orientacion = ?
                                                        where veh_nserie = ?";
        $query_update = $conn->prepare($consulta_update);         
        $query_update->bindParam(1,$fechahora);
        $query_update->bindParam(2,$datosGPS->lat);
        $query_update->bindParam(3,$datosGPS->lon);
        $query_update->bindParam(4,$datosGPS->ignicion);
        $query_update->bindParam(5,$datosGPS->comb_total);
        $query_update->bindParam(6,$datosGPS->com_ocioso);
        $query_update->bindParam(7,$datosGPS->odometro);
        $query_update->bindParam(8,$datosGPS->señal_estatus);
        $query_update->bindParam(9,$presion);
        $query_update->bindParam(10,$datosGPS->rpm);
        $query_update->bindParam(11,$datosGPS->tiempo_crucero);
        $query_update->bindParam(12,$datosGPS->speed);
        $query_update->bindParam(13,$datosGPS->velocidad);
        $query_update->bindParam(14,$datosGPS->orientacion);
        $query_update->bindParam(15,$datosGPS->id_vehiculo);
        $query_update->execute();
        
        $query_update->closeCursor();
        $query_ctg->closeCursor();
        //echo 'Actualizo ctg vehiculos';
    }else{
        //inserta la unidad, si no existe en ctg_vehiculos
        InsertCtg($datosGPS,$conn);
        $query_ctg->closeCursor();

    }             
}


/*Actualiza avl_secundario, dependiendo si la cadena es primariooo o secundario
*Recibe: objeto tipo gps_suntech
*/
function UpdateSecundario($datosGPS,$conn){
    $query_sec = "";
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $bandera = $datosGPS->modelo_gps;
    $retorno = VerificaSecundario($datosGPS,$conn);
    if($datosGPS->señal_estatus == 1 && $retorno == 1){
            switch($bandera){
            case 34: $query_sec = "update avl_secundario set sec_primarioupos = ? where sec_primario = ? ";
            break;
            case 20: $query_sec = "update avl_secundario set sec_secundarioupos = ? where sec_secundario = ?";
            break;
        }

        $query_update_sec = $conn->prepare($query_sec);         
        $query_update_sec->bindParam(1,$fechahora);
        $query_update_sec->bindParam(2,$datosGPS->id_vehiculo);
        $query_update_sec->execute();
        $query_update_sec->closeCursor(); 
    }else{
        
         //ECHO 'BUFFER';
    }

   
    unset($bandera);
    unset($fechahora);
  
} 

/*Verifca si la fecha-hora de la cadena es mas actual, que la almacenada en avl_secundario
* Si es mas actual, actualiza el campo, devuelve 1
* No, no actualiza el campo, devuelve 2 
*/
function VerificaSecundario($datosGPS,$conn){
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $bandera = $datosGPS->modelo_gps;
    $retorno = 2;
    if($datosGPS->señal_estatus == 1){
        
        switch($bandera){
        case 34: $query_sec = "select sec_primarioupos as fecha from avl_secundario where sec_primario = ?";
        break;
        case 20: $query_sec = "select sec_secundarioupos as fecha from avl_secundario where sec_secundario = ?";
        break;
        }

        $query_update_sec = $conn->prepare($query_sec);         
        $query_update_sec->bindParam(1,$datosGPS->id_vehiculo);        
        $query_update_sec->execute();
        $resultado = $query_update_sec->fetch();
        $fechaBD = $resultado['fecha'];
        $query_update_sec->closeCursor();
        
        //creamos las fechas
        $fechaBD = date_create($fechaBD);
        $fechaCadena = date_create($fechahora);
        //formato de fecha
        $fechaBD = $fechaBD->format('Y-m-d H:i:s');
        $fechaCadena = $fechaCadena->format('Y-m-d H:i:s');

        //comparamos fechas
        if($fechaCadena > $fechaBD ){
            $retorno = 1;
        }else{
            $retorno = 2;
        }

        unset($fechaBD);
        unset($fechaCadena);
    }

    unset($bandera);
    unset($fechahora);

return $retorno;
}

/*Inserta en ctg_vehiculos, si el numero serie no existe
*Recibe: objeto tipo gps_suntech
*/
function InsertCtg($datosGPS,$conn){
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $presion = 0;
    $consulta_insert = " INSERT INTO ctg_vehiculos values(default,?,?,?,?,?,?,?,?,?,?,?,'',?,?,'',?,'0')";
    $query_insert = $conn->prepare($consulta_insert);
    $fechahora = $datosGPS->fecha." ".$datosGPS->hora;
    $query_insert->bindParam(1,$datosGPS->id_vehiculo);
    $query_insert->bindParam(2,$fechahora);
    $query_insert->bindParam(3,$datosGPS->lat);
    $query_insert->bindParam(4,$datosGPS->lon);
    $query_insert->bindParam(5,$datosGPS->ignicion);
    $query_insert->bindParam(6,$datosGPS->comb_total);
    $query_insert->bindParam(7,$datosGPS->com_ocioso);
    $query_insert->bindParam(8,$datosGPS->odometro);
    $query_insert->bindParam(9,$datosGPS->señal_estatus);
    $query_insert->bindParam(10,$presion);
    $query_insert->bindParam(11,$datosGPS->rpm);
    $query_insert->bindParam(12,$datosGPS->tiempo_crucero);
    $query_insert->bindParam(13,$datosGPS->speed);
    $query_insert->bindParam(14,$datosGPS->orientacion);
    $query_insert->execute();
    $query_insert->closeCursor();

 echo "----------NUEVA UNIDAD INSERTADA-------------------";
    
 unset($fechahora);
}


/*
*
*/

function UpdateCadEstatus($cad_id,$conn){
    
    $string_estatus = "update avl_cadenas_g set cad_estatus = 2 ,cad_fecharegistrobd = now() where cad_id = ? ";
    $query_update_estatus = $conn->prepare($string_estatus);         
    $query_update_estatus->bindParam(1,$cad_id);
    $query_update_estatus->execute();    
    $query_update_estatus->closeCursor();


}
?>

