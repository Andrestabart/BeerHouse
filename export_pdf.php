<?php
require_once 'db.php';

function simplePDF($orders) {
    $pdf = "%PDF-1.4\n";
    $objects = [];
    $pages = [];
    $currentObject = 1;

    // fonts object
    $objects[] = "$currentObject 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
    $fontObj = $currentObject;
    $currentObject++;

    // page content
    $content = "BT\n/F1 12 Tf\n50 750 Td\n(Tabla de pedidos) Tj\n";
    $y = 720;
    foreach ($orders as $o) {
        $line = sprintf('%s %s %s %s x%s $%0.2f',
            date('H:i', strtotime($o['created_at'])),
            $o['name'], $o['phone'], $o['item'], $o['qty'], $o['price']);
        $content .= sprintf("50 %d Td (%s) Tj\n", $y, str_replace(['(',')','\\'],['\\(','\\)','\\\\'],$line));
        $y -= 20;
    }
    $content .= "ET";

    $objects[] = "$currentObject 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n$content\nendstream\nendobj\n";
    $contentObj = $currentObject;
    $currentObject++;

    // page object
    $objects[] = "$currentObject 0 obj\n<< /Type /Page /Parent 0 0 R /MediaBox [0 0 595 842] /Contents $contentObj 0 R /Resources << /Font << /F1 $fontObj 0 R >> >> >>\nendobj\n";
    $pageObj = $currentObject;
    $currentObject++;
    $pages[] = $pageObj;

    // pages object
    $kids = implode(' 0 R ', $pages) . ' 0 R';
    $objects[] = "$currentObject 0 obj\n<< /Type /Pages /Kids [ $kids ] /Count " . count($pages) . " >>\nendobj\n";
    $pagesObj = $currentObject;
    $currentObject++;

    // catalog object
    $objects[] = "$currentObject 0 obj\n<< /Type /Catalog /Pages $pagesObj 0 R >>\nendobj\n";
    $catalogObj = $currentObject;
    $currentObject++;

    // xref
    $xref = "xref\n0 $currentObject\n0000000000 65535 f \n";
    $offsets = []; $pos = strlen($pdf);
    foreach ($objects as $obj) { $offsets[] = $pos; $pdf .= $obj; $pos += strlen($obj); }
    foreach ($offsets as $off) { $xref .= sprintf("%010d 00000 n \n", $off); }
    $pdf .= $xref;
    $pdf .= "trailer\n<< /Size $currentObject /Root $catalogObj 0 R >>\nstartxref\n" . $pos . "\n%%EOF";

    return $pdf;
}

$orders = getTodayOrders();
$pdf = simplePDF($orders);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="orders_' . date('Ymd') . '.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
