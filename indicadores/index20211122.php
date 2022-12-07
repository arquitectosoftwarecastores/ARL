<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Mexico_City");

include_once('./libs/crypt/password.php');


$selPass = 'SELECT txt_nombre_par
            FROM tb_parametros
            WHERE pk_clave_par = 3';
$qryPass = $conn->prepare($selPass);
$qryPass->execute();
$resPass = $qryPass->fetch();
$password = $resPass['txt_nombre_par'];
$qryPass->closeCursor();

$hash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
$url = 'https://indicadores.castores.com.mx/api/indicadores/powerbi/static/17/?hash=' . $hash;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_SSL_VERIFYPEER => false,
));

$response = json_decode(curl_exec($curl));

if (curl_errno($curl)) {
  print curl_error($curl);
}

curl_close($curl);

$accessToken = $response->respuesta->accessToken;
$embedUrl = $response->respuesta->embedUrl[0]->embedUrl;
?>

<div id="div-pbi" style="width: 100%; height: 84%;"></div>



<script src="./libs/power-bi/powerbi.min.js"></script>

<script>
  const pbi = window['powerbi-client'];
  const reportLoadConfig = {
    type: "report",
    tokenType: pbi.models.TokenType.Embed,
    accessToken: '<?php echo $accessToken ?>',
    embedUrl: '<?php echo $embedUrl ?>'
  };



  //Obtenemos la referencia del elemento Html (div id="div-reporte") en el cual pondremos el reporte
  let reportDiv = document.getElementById('div-pbi');

  powerbi = new pbi.service.Service(pbi.factories.hpmFactory, pbi.factories.wpmpFactory, pbi.factories.routerFactory);


  // Adjuntamos el reporte al div del html
  report = this.powerbi.embed(reportDiv, reportLoadConfig);

  report.off("loaded");

  report.on("loaded", () => {
    console.log("Reporte cargado correctamente.");
  });
  report.on("error", () => {
    alert("Hubo un error al conectarse con el servicio de PowerBI. Intente de nuevo mas tarde.");
    return;
  });
</script>
