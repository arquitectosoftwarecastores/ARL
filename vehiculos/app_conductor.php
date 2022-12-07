﻿<div id="loader" class="centrado" style="width:100%; margin:0 auto; text-align: center;">
    <img src="imagenes/loader.svg" />
    <p class="centrado">Cargando información del conductor ...<p>
</div>
<script>
    $(document).ready(function(){
        $("#loader").hide();
        $("#datosconductor").fadeIn();
    });
</script>

<?php

$cadena = "http://www.castores.com.mx:8080/WSPortal/app/services/consultar_operador?noeconomico=" . $_GET["economico"];
$json = file_get_contents($cadena);
$obj = json_decode(utf8_encode($json));
// Verifica que el usuario exista en la BD de Castores
$servidor = "192.168.0.13";
$usuar = "usuarioWin";
$passw = "windows";
$bd = "camiones";

$nombreoperador = "";
$fechanacimiento = "";
$telefono = "";
$nombreciudad = "";
$estadonombre = "";
$colonia = "";
$calle = "";
$numero = "";
$cp = "";
$fechafoto = "";
$seriech = "";

try {
    // Conexion y consulta en mysql
    $var = 0;
    $con = new PDO("mysql:host=" . $servidor . ";dbname=" . $bd . "", $usuar, $passw);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $consult = "SELECT 
        o.nombre, p.fechanacimiento, p.tel, ci.nombre, e.nombre, p.colonia, p.calle, p.noexterior, p.cp, f.fechafotofre, c.seriechasis 
        FROM camiones.operadores o 
        JOIN camiones.camiones c ON c.unidad = o.unidad 
        JOIN personal.fotopersonal f ON f.idpersonal = o.idpersonal
        JOIN personal.personal p ON p.idpersonal = o.idpersonal
        JOIN camiones.ciudades ci ON ci.idciudad = p.idciudad
        JOIN camiones.estados e ON e.idestado = ci.idestado
        WHERE c.noeconomico = '" . $_GET["economico"] . "'";
    //$datos = $con->query($consult);
     $datos = $con->query($consult);
    foreach ($datos as $row) {
        $nombreoperador = $row[0];
        $fechanacimiento = $row[1];
        $telefono = $row[2];
        $nombreciudad = $row[3];
        $estadonombre = $row[4];
        $colonia = $row[5];
        $calle = $row[6];
        $numero = $row[7];
        $cp = $row[8];
        $fechafoto = $row[9];
        $seriech = $row[10];
    }
 //   $query->closeCursor();
} catch (PDOException $e) {
    // report error message
    echo "Error de conexion: " . $e->getMessage();
}
/*
  $cadena = "http//www.castores.com.mx:8080/WSPortal/app/services/consultar_operador?noeconomico=" . $_GET["economico"];
  $json = file_get_contents($cadena);
  $obj = json_decode(utf8_encode($json)); */
$seriemotor = "";
$tipo = "";
$marca = "";
$modelo = "";
$origen = "";
$destino = "";
$circuito_ruta = "";
$arriboprogramado = "";
$estatusunidad = "";
$estatusviaje = "";
$idtipounidad = "";
$idviaje = "";
$tipoviaje = "";
$placas = "";
$idtipounidad = "";
$consulta1 = "SELECT * FROM monitoreo.informacion_unidades WHERE noeconomico= '" . $_GET["economico"] . "'";
$query1 = $conn->prepare($consulta1);
$query1->execute();
while ($registro1 = $query1->fetch()) {
    $seriemotor = $registro1['seriemotor'];
    $tipo = $registro1['tipo'];
    $marca = $registro1['marca'];
    $modelo = $registro1['modelo'];
    $origen = $registro1['plaza_origen'];
    $destino = $registro1['plaza_destino'];
    $circuito_ruta = $registro1['circuito_ruta'];
    $estatusunidad = $registro1['estatusunidad'];
    $estatusviaje = $registro1['estatusviaje'];
    $idtipounidad = $registro1['idtipounidad'];
    $arriboprogramado = $registro1['arribo_programado'];
    $idviaje = $registro1['idviaje'];
    $placas = $registro1['placas'];
    $idtipounidad = $registro1['idtipounidad'];
    $tipoviaje = $registro1['idcatalogo_viajes'];
}
?>
<div align="center">
    <br>        
    <table id="datosconductor2" style="width:80%" class="table table-bordered">
        <tr>
            <td colspan="7"><h2 style="text-align:center">Numero Económico <?php echo $_GET["economico"] ?></h2> </td>
        </tr>
        <tr>
            <td rowspan="17" width="30%" class="img-center"><img class="foto" id="foto" src="<?php echo "http://www.castores.com.mx:8080" . $obj->urlfoto ?>"/> </td>
            <td colspan="4" style="text-align: center"> <strong style="font-size: medium">Datos Operador</strong></td>
        </tr>
        <tr>
            <td width="10%"><Strong>Nombre: </Strong></td>
            <td colspan="3"><?php echo $nombreoperador ?></td>         
        </tr>
        <tr>
            <td width="10%"><Strong style="font-size: small">Fec. Nac.:</strong></td>
            <td width="25%"> <?php echo $fechanacimiento ?> </td>
            <td width="10%"><Strong style="font-size: small">Telefono:</strong></td>
            <td width="25%"><?php echo $telefono ?></td>         
        </tr>
        <tr>
            <td width="10%"><Strong style="font-size: small">Ciudad:</strong></td>
            <td width="25%"><?php echo $nombreciudad ?></td>
            <td width="10%"><Strong style="font-size: small">Estado:</strong></td>
            <td width="25%"><?php echo $estadonombre ?></td>         
        </tr>
        <tr>
            <td width="10%"><Strong style="font-size: small">Colonia:</strong></td>
            <td colspan="3"><?php echo $colonia ?></td>        
        </tr>              
        <tr>
            <td width="10%"><Strong style="font-size: small">Calle:</strong></td>
            <td colspan="3"><?php echo $calle ?></td>       
        </tr>
        <tr>
            <td width="10%"><Strong style="font-size: small">Numero:</strong></td>
            <td width="25%"><?php echo $numero ?></td>
            <td width="10%"><Strong style="font-size: small">Cod. Postal:</strong></td>
            <td width="25%"><?php echo $cp ?></td>         
        </tr>
        <tr>
            <td colspan="4" style="text-align: center"> <strong style="font-size: medium">Datos Seguimiento</strong></td>       
        </tr>
        <tr>
            <td width="10%"><Strong style="font-size: small">Ruta:</strong></td>
            <td colspan="3"><?php echo $circuito_ruta ?></td>          
        </tr>
        <tr>
            <td><Strong style="font-size: small">Viaje:</strong></td>
            <td><?php echo $idviaje ?></td>
            <td><Strong style="font-size: small">Estatus:</strong></td>
            <td><?php
// 
//          if ($estatusviaje != 3 || ($estatusunidad == 3 || $estatusunidad == 8)) 
//                echo "VACIO"; 
//              else if ($estatusunidad == 1 || $estatusunidad == 52 || $estatusunidad == 66)
//                    echo "CARGADO"; 
//                else if ($estatusunidad == 4)
//                    echo "REPARTIENDO"; 
//                else if ($estatusunidad == 15)
//                    echo "VIAJE LOCAL"; 
//                else if ($estatusunidad == 11)
//                    echo "RECOLECCION"; 
//                else if ($estatusunidad == 10 || $estatusunidad == 53 || $estatusunidad == 54)
//                    echo "CARGA / DESCARGA"; 
//                else if ($estatusunidad == 51 || $estatusunidad == 68 || $estatusunidad == 69)
//                    echo "EN OFICINA"; 
//                else
//                    echo "VACIO";
if ($estatusviaje == 0 || $estatusviaje == 1 || $estatusviaje == 3 || $estatusviaje == 6) {
    echo "VACIO";
} else {

    switch ($estatusunidad) {
        case 1:
            echo "CARGADO";
            break;
        case 3: // never reached because "a" is already matched with 0
            echo "VACIO";
            break;
        case 4:
            echo "REPARTIENDO";
            break;
        case 8: // never reached because "a" is already matched with 0
            echo "VACIO";
            break;
        case 10:
            echo "CARGANDO";
            break;
        case 11: // never reached because "a" is already matched with 0
            echo "RECOLECCION";
            break;
        case 15:
            echo "VIAJE LOCAL";
            break;
        case 51: // never reached because "a" is already matched with 0
            echo "EN OFICINA";
            break;
        case 52:
            echo "CONTINUA VIAJE";
            break;
        case 53: // never reached because "a" is already matched with 0
            echo "RECARGA/DESCARGA";
            break;
        case 54:
            echo "DESCARGA";
            break;
        case 66: // never reached because "a" is already matched with 0
            echo "EN TRAYECTO";
            break;
        case 68:
            echo "EN OFICINA";
            break;
        case 69: // never reached because "a" is already matched with 0
            echo "EN OFICINA";
            break;
        default:
            echo "VACIO";
            break;
    }
}
?></td>         
        </tr>
        <tr>
            <td><Strong style="font-size: small">Origen:</strong></td>
            <td><?php echo $origen ?></td>
            <td><Strong style="font-size: small">Destino:</strong></td>
            <td><?php echo $destino ?></td>         
        </tr>
        <tr>
            <td><Strong style="font-size: small">Arribo Programado:</strong></td>
            <td><?php echo $arriboprogramado ?></td>  
            <td><Strong style="font-size: small">Tipo de Viaje:</strong></td>
            <td><?php
                switch ($tipoviaje) {
                    case 1: echo "COMPLETO";
                        break;
                    case 2: echo "VACIO A";
                        break;
                    case 3: echo "SERVICIO SOCIAL";
                        break;
                    case 7: echo "SERVICIO SOCIAL";
                        break;
                    case 8: echo "CIRCUITO MULTIPLE";
                        break;
                    case 9: echo "COMPLETO CON PAQUETERIA";
                        break;
                    case 10: echo "SERVICIO SOCIAL";
                        break;
                    case 11: echo "VIAJE REDONDO";
                        break;
                    case 12: echo "SERVICIO SOCIAL";
                        break;
                    case 14: echo "CIRCUITO MULTIPLE";
                        break;
                    case 15: echo "SERVICIO SOCIAL";
                        break;
                    case 16: echo "SERVICIO SOCIAL";
                        break;
                    case 17: echo "CIRCUITO MULTIPLE";
                        break;
                    case 19: echo "SERVICIO SOCIAL";
                        break;
                    case 20: echo "CIRCUITO MULTIPLE";
                        break;
                    case 21: echo "SERVICIO SOCIAL";
                        break;
                    case 22: echo "SERVICIO SOCIAL";
                        break;
                    case 23: echo "SERVICIO SOCIAL";
                        break;
                    case 24: echo "SERVICIO SOCIAL";
                        break;
                    case 26: echo "SERVICIO SOCIAL";
                        break;
                    case 27: echo "SERVICIO SOCIAL";
                        break;
                }

//echo "TRAILER"; else echo "TORTON"; 
?></td>   
        </tr>
        <tr>
            <td colspan="4" style="text-align: center"> <strong style="font-size: medium">Datos Seguridad</strong></td>        
        </tr>
        <?php if (isset($obj->datosseguridad[0]->placas)) { ?>
            <tr>
                <td><Strong style="font-size: small">Placas:</strong></td>
                <td><?php echo $placas ?></td>
                <td><Strong style="font-size: small">Serie Chasis:</strong></td>
                <td><?php echo $seriech;// $obj->datosseguridad[0]->noserie ?></td>         
            </tr>            
            <tr>                
                <td><Strong style="font-size: small">Marca:</strong></td>
                <td><?php echo $marca ?></td>
                <td><Strong style="font-size: small">Modelo:</strong></td>
                <td><?php echo $modelo ?></td>         
            </tr>
            <tr >                
                <td><Strong style="font-size: small">No. Serie Motor:</strong></td>
                <td><?php echo $seriemotor ?></td>
                <td><Strong style="font-size: small">Tipo Unidad:</strong></td>
                <td><?php if ($idtipounidad == 1) echo "TRAILER"; else echo "TORTON"; ?></td>         
            </tr>
<?php
}
else {
    echo "'<tr><td colspan='4' style='text-align: center'>No hay información.</td></tr>";
}
$query1->closeCursor();
?>
    </table>
</div>