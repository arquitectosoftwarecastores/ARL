<?php
	// ob_start guardará en un búfer lo que esté
	// en la ruta del include.
ob_start();
    include(dirname(__FILE__).'/vistas/for_pdf2.php');
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
        $html2pdf = new HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', 3); //Configura la hoja
        $html2pdf->pdf->SetDisplayMode('fullpage'); //Ver otros parámetros para SetDisplaMode
        $html2pdf->writeHTML($content); //Se escribe el contenido 
    // Para evitar el error: Some data has already been output, can't send PDF
	ob_end_clean();
        //ob_get_clean();
        $html2pdf->Output('reporte de alertas.pdf'); //Nombre default del PDF
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
