<?php
require_once 'db.php';

function simplePDF($orders) {
    $objects = [];
    $objNum = 1;

    $fontObj = $objNum++;
    $objects[$fontObj] = "$fontObj 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

    $content = "BT\n/F1 12 Tf\n1 0 0 1 50 750 Tm (Tabla de pedidos) Tj\n";
    $y = 730;
    foreach ($orders as $o) {
        $y -= 20;
        $line = sprintf('%s %s %s %s x%s $%0.2f',
            date('H:i', strtotime($o['created_at'])),
            $o['name'], $o['phone'], $o['item'], $o['qty'], $o['price']);
        $safe = str_replace(['(',')','\\'], ['\\(','\\)','\\\\'], $line);
        $content .= "1 0 0 1 50 $y Tm ($safe) Tj\n";
    }
    $content .= "ET";

    $contentObj = $objNum++;
    $objects[$contentObj] = "$contentObj 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n$content\nendstream\nendobj\n";

    $pageObj = $objNum++;
    $pagesObj = $objNum; // next object number
    $objects[$pageObj] = "$pageObj 0 obj\n<< /Type /Page /Parent $pagesObj 0 R /MediaBox [0 0 595 842] /Contents $contentObj 0 R /Resources << /Font << /F1 $fontObj 0 R >> >> >>\nendobj\n";

    $objects[$pagesObj] = "$pagesObj 0 obj\n<< /Type /Pages /Kids [ $pageObj 0 R ] /Count 1 >>\nendobj\n";
    $objNum++;

    $catalogObj = $objNum++;
    $objects[$catalogObj] = "$catalogObj 0 obj\n<< /Type /Catalog /Pages $pagesObj 0 R >>\nendobj\n";

    $pdf = "%PDF-1.4\n";
    $xref = "xref\n0 $objNum\n0000000000 65535 f \n";
    $offsets = []; $pos = strlen($pdf);
    for ($i = 1; $i < $objNum; $i++) { $offsets[] = $pos; $pdf .= $objects[$i]; $pos += strlen($objects[$i]); }
    foreach ($offsets as $off) { $xref .= sprintf("%010d 00000 n \n", $off); }
    $pdf .= $xref;
    $pdf .= "trailer\n<< /Size $objNum /Root $catalogObj 0 R >>\nstartxref\n" . $pos . "\n%%EOF";

    return $pdf;
}

$orders = getTodayOrders();
$pdf = simplePDF($orders);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="orders_' . date('Ymd') . '.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
