<?php

include ('../conexion/conexion.php');

// Consulta Circuitos de Clientes
$conCir = "SELECT cir.pk_clave_cir,cir.txt_nombre_cir AS nombre, cor.correos  FROM tb_correoclientes AS cor INNER JOIN tb_circuitos AS cir ON cor.pk_clave_cir = cir.pk_clave_cir";
$queryCir = $conn->prepare($conCir);
$queryCir->execute();


while ($circuito = $queryCir->fetch()) {


  // Consulta Vehiculos
  $pkCir  = $circuito["pk_clave_cir"];
  $nomCir  = $circuito["nombre"];
  $corCir  = $circuito["correos"];
  $correo = explode(";", $corCir);
  $conVeh = "SELECT * FROM tb_vehiculos WHERE fk_clave_cir = ?";
  $queryVeh = $conn->prepare($conVeh);
  $queryVeh->bindParam(1, $pkCir);
  $queryVeh->execute();


  // Se Genera el cuerpo del PDF
  $body = '
    <div align="center">
      <hr>
      <img src="../imagenes/logo.jpg" style="text-align:center; width:150px;" >
      <br>
      <br>
      <strong>'.$circuito["nombre"].', Fecha: '.date("d/m/Y H:i:s").'</strong>
      <hr>
      <br>
    </div>
    <div style="align:center;">

    <table cellspacing="0" border="0">
      <tbody>
      <tr>
      <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" height="25" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">
        Económico
      </font></b></td>
      <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">
        Fecha
      </font></b></td>
      <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" width="575" bgcolor="#0066B3" align="center"><b><font face="Liberation Serif" color="#FFFFFF">
        Ubicación
      </font></b></td>
      </tr>';


  while ($vehiculo = $queryVeh->fetch()) {

    // Vehiculos
    $body = $body.'
      <tr>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" height="25" align="center">'
          .$vehiculo["txt_economico_veh"].
        '</td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" height="25" align="center">'
          .date('d/m/Y H:i:s',strtotime($vehiculo["fec_posicion_veh"])).
        '</td>
        <td style="border-top:1px solid #000000;border-bottom:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000" valign="middle" width="575" height="25" align="center">'
          .$vehiculo["txt_posicion_veh"].", ".$vehiculo["txt_upsmart_veh"].", ".$vehiculo["num_latitud_veh"].",".$vehiculo["num_longitud_veh"].
        '</td>
      </tr>
      ';

  }


  $body = $body.'</tbody>
      </table>
    </div>';


  // Crea PDF y Regresa su Nombre
  $file_name = crearPDF($body);

  //  Envia Correo
  enviaPDF($file_name,$correo,$nomCir);

}



function crearPDF($body){

  ob_start();
      include(dirname(__FILE__).'/../reportedevehiculos/vistas/for_pdf2.php');
      // En una variable llamada $content se obtiene lo que tenga la ruta especificada
      // NOTA: Se usa ob_get_clean porque trae SOLO el contenido
      // Evitará este error tan común en FPDF:
      // FPDF error: Some data has already been output, can't send PDF
      $content = ob_get_clean();

      // Se obtiene la librería
      // require_once(dirname(__FILE__).'/../../html2pdf/html2pdf.class.php');
      // En este caso no se usó la línea anterior porque fue instalado vía composer
      // Para ello se usa el archivo autoload.php que hace que funcione la librería
      require_once(dirname(__FILE__).'/../html2pdf/vendor/autoload.php');
      try
      {
          $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', 3); //Configura la hoja
          $html2pdf->pdf->SetDisplayMode('fullpage'); //Ver otros parámetros para SetDisplaMode
          $html2pdf->writeHTML($body); //Se escribe el contenido
      // Para evitar el error: Some data has already been output, can't send PDF
  	   //ob_end_clean();
          //ob_get_clean();
          $file_name = md5(uniqid(rand(), true));
          $html2pdf->output('/var/www/html/procesos/pdf/'.$file_name.'.pdf', 'F'); //  Crea PDF

          return $file_name.'.pdf';

      }
      catch(HTML2PDF_exception $e) {
          echo $e;
          exit;
      }
}



function enviaPDF($file_name,$correo,$nomCir){

  $asunto = strtoupper($nomCir);
  $asunto = $nomCir." UBICACIONES ".date("H")." HRS";

  // Envia correo
  include_once("/var/www/html/PHPMailer_v5.1-master/class.phpmailer.php");
  $mail = new PHPMailer();
  include_once("/var/www/html/PHPMailer_v5.1-master/class.smtp.php");
  $mail->Host = "64.34.107.89"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
  $mail->Username = "castores@castores.com.mx"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
  $mail->Password = "V8DbHj471D"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
  $mail->Port = 465; // Puerto de conexión al servidor de envio.

  // Se Añaden los Correos
  for($i = 0; $i < count($correo); $i++){
    //echo "Correo".$i." = ".$correo[$i] ."<br />";
    $mail->AddAddress($correo[$i]);
  }

  $mail->AddAddress("monitoreo_logistica_lem@castores.com.mx");
  $mail->AddAddress("jefeoperativoml_lem@castores.com.mx");
  $mail->AddAddress("jefeadministrativoml_lem@castores.com.mx");
  // $mail->AddAddress("liderinnovacionti_lem@castores.com.mx");
  $mail->Subject = $asunto; // Este es el titulo del email.
  // $link = "http://69.172.241.230/historico/for_mapa1.php?id=".$id;
  $mail->Body = "Archivo Adjunto."; // Mensaje a enviar.
  // AddAttachment(Direccion en donde se Encuentra el PDF , Nombre de archivo con el que se envia)
  $mail->AddAttachment( "pdf/".$file_name , $asunto.'.pdf' );
  $exito = $mail->Send(); // Envía el correo.
  if ($exito) {
    echo "El correo fue enviado correctamente.";
    unlink("pdf/".$file_name); // Elimina el Fichero
  } else {
    echo "Hubo un problema. Contacta a un administrador.";
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    unlink("pdf/".$file_name);  // Elimina el Fichero
  }

}


?>
