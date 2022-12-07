<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set("America/Mexico_City");
include_once('../conexion/conexion.php');


// Consulta Sucursales
$conSu = "SELECT z.txt_nombre_zon, z.num_latitudcen_zon, z.num_longitudcen_zon, cs.radio ,cs.correos 
          FROM tb_correosucursal AS cs INNER JOIN tb_zonas AS z ON cs.pk_clave_zon = z.pk_clave_zon";
$querySu = $conn->prepare($conSu);
$querySu->execute();

while ($sucursal = $querySu->fetch()) {
  $suc = $sucursal["txt_nombre_zon"];
  $lon = $sucursal["num_longitudcen_zon"];
  $lat = $sucursal["num_latitudcen_zon"];
  $rad = $sucursal["radio"];
  $cor = $sucursal["correos"];
  echo "Sucursal: " . $suc . " long:" . $lon . " lat:" . $lat . " rad:" . $rad . " cor:" . $cor . "\n";
  $correo = explode(";", $cor);


  // Consulta las unidades que se encuentran en el radio indicado
  $R = 6371;  // Radio de la Tierra en KM
  $maxLat = $lat + rad2deg($rad / $R);
  $minLat = $lat - rad2deg($rad / $R);
  $maxLon = $lon + rad2deg(asin($rad / $R) / cos(deg2rad($lat)));
  $minLon = $lon - rad2deg(asin($rad / $R) / cos(deg2rad($lat)));
  $latdeg = deg2rad($lat);
  $londeg = deg2rad($lon);

  // Consulta Unidades Cercanas a la Sucursal
  $conUni = "SELECT *,
                 acos(sin(?)*sin(radians(num_latitud_rem)) + cos(?)*cos(radians(num_latitud_rem))*cos(radians(num_longitud_rem)-?)) * ? AS distancia
                FROM (
                  SELECT *
                  FROM tb_remolques
                  WHERE num_latitud_rem BETWEEN ? AND ?
                  AND num_longitud_rem BETWEEN ? AND ?
                  ) AS FirstCut
                WHERE acos(sin(?)*sin(radians(num_latitud_rem)) + cos(?)*cos(radians(num_latitud_rem))*cos(radians(num_longitud_rem)-?)) * ? < ?
                ORDER BY distancia";


  $queryUni = $conn->prepare($conUni);
  $queryUni->bindParam(1, $latdeg);
  $queryUni->bindParam(2, $latdeg);
  $queryUni->bindParam(3, $londeg);
  $queryUni->bindParam(4, $R);
  $queryUni->bindParam(5, $minLat);
  $queryUni->bindParam(6, $maxLat);
  $queryUni->bindParam(7, $minLon);
  $queryUni->bindParam(8, $maxLon);
  $queryUni->bindParam(9, $latdeg);
  $queryUni->bindParam(10, $latdeg);
  $queryUni->bindParam(11, $londeg);
  $queryUni->bindParam(12, $R);
  $queryUni->bindParam(13, $rad);
  $queryUni->execute();

  $head = "";
  $body = '<div align="center">
    <img src="http://avl.castores.com.mx/imagenes/logocorreo.png" alt=""></div>
    <div align="center">
    <table cellspacing="0" border="0">
    <colgroup width="85" span="3"></colgroup> <colgroup width="420"></colgroup> <colgroup width="147"></colgroup>
    <colgroup width="206"></colgroup>
    <tbody>
    <tr>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" height="39" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">No</font></b></td>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">Unidad</font></b></td>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">Distancia</font></b></td>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">Ubicacion</font></b></td>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">Fecha-hora</font></b></td>
    <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font size="3" face="Liberation Serif" color="#FFFFFF">Estatus</font></b></td>
    </tr>';

  $num = 0;
  while ($unidad = $queryUni->fetch()) {
    $uniEco = $unidad["txt_economico_rem"];
    $uniDis = round($unidad["distancia"], 2) . " Kms";
    $uniUbi = $unidad["txt_georeferencia_mun"] . ", " . $unidad["txt_georeferencia_cas"];
    $uniFec = date("d/m/Y H:i:s", strtotime($unidad["fec_posicion_rem"]));

    // Crea tabla con correo
    $body = $body . '<tr>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" height="33" align="center">
          <font face="Liberation Serif" color="#000000">'
      . ++$num .
      '</font>
        </td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" align="center">
          <font face="Liberation Serif" color="#000000">'
      . $uniEco .
      '</font>
        </td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" align="center">
          <font face="Liberation Serif" color="#000000">'
      . $uniDis .
      '</font>
        </td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" align="center">
          <font face="Liberation Serif" color="#000000">' .
      $uniUbi
      . '</font>
        </td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" align="center">
          <font face="Liberation Serif" color="#000000">'
      . $uniFec .
      '</font>
        </td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" align="center">
          <font face="Liberation Serif" color="#000000">
            <br>
          </font>
        </td>
    </tr>
    ';
  }

  $body = $body . "</tbody></table>";

  // Envia correo
  /*
  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail = new PHPMailer();
  $mail->Host = "10.3.1.181"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  // $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  // $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de Correo
  $mail->Username = "gerentemseg_lem@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "JR505nCOvf"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 500; // Puerto de conexión al servidor de envio.


  // Se Añaden los Correos
  for ($i = 0; $i < count($correo); $i++) {
    //echo "Correo".$i." = ".$correo[$i] ."<br />";
    //    $mail->AddAddress($correo[$i]);
  }

  // $mail->AddAddress("gerentelogistica_lem@castores.com.mx");
  // $mail->AddAddress("jefeadministrativoml_lem@castores.com.mx");
  // $mail->AddAddress("jefelogisticatraficonal_lem@castores.com.mx");
  // $mail->AddAddress("coordlogisticatraficonal_lem@castores.com.mx");
  $mail->AddAddress("gerenteinnovacionti_lem@castores.com.mx");
  $mail->isHTML(true);
  $mail->Subject = "POLEO " . $suc . " " . $rad . " KM"; // Este es el titulo del email.
  // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
  $mail->Body = $body; // Mensaje a enviar.
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
    //echo $body;
    echo "El correo fue enviado correctamente.\n";
  } else {
    echo "Hubo un problema. Contacta a un administrador.\n";
  }
  */

  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail = new PHPMailer();
  $mail->IsHTML(true);
  $mail->Host = "10.3.1.181"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  $mail->Username = "gerentemseg_lem@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "JR505nCOvf"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 500; // Puerto de conexión al servidor de envio.
  $mail->AddAddress("gerenteinnovacionti_lem@castores.com.mx");
  $mail->Subject = "POLEO " . $suc . " " . $rad . " KM"; // Este es el titulo del email.
  $mail->Body = $body; // Mensaje a enviar.
  
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
    echo "El correo fue enviado correctamente.";
  } else {
    echo "Hubo un problema. Contacta a un administrador.";
  }

  $correo = "";
  $body = "";
}
