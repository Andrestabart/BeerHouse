<?php
require_once 'db.php';

$orders = getTodayOrders();
$filename = 'orders_' . date('Ymd') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$out = fopen('php://output', 'w');
fputcsv($out, ['Hora','Cliente','Teléfono','Item','Cantidad','Precio']);
foreach ($orders as $o) {
    fputcsv($out, [
        date('H:i', strtotime($o['created_at'])),
        $o['name'],
        $o['phone'],
        $o['item'],
        $o['qty'],
        number_format($o['price'], 2)
    ]);
}
 fclose($out);
