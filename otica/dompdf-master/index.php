<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Meu PDF</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Olá, Mundo!</h1>
    <p>Este é meu primeiro PDF gerado com DomPDF.</p>
</body>
</html>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("meu-documento.pdf", ["Attachment" => 0]);