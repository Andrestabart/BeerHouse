<?php
require_once 'db.php';

createTables();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $item = trim($_POST['item'] ?? '');
    $qty = (int)($_POST['qty'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);

    if (!$name || !$item) {
        $errors[] = 'Nombre e item son requeridos';
    } else {
        addOrder($name, $phone, $item, $qty, $price);
        header('Location: index.php');
        exit;
    }
}

$orders = getTodayOrders();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gastro Bar</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Pedidos Gastro Bar</h1>
<form method="post" class="order-form">
    <h2>Nuevo Pedido</h2>
    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>
    <label>Nombre Cliente
        <input type="text" name="name" required>
    </label>
    <label>Teléfono
        <input type="text" name="phone">
    </label>
    <label>Item
        <input type="text" name="item" required>
    </label>
    <label>Cantidad
        <input type="number" name="qty" value="1" min="1">
    </label>
    <label>Precio
        <input type="number" name="price" value="0" step="0.01" min="0">
    </label>
    <button type="submit">Agregar</button>
</form>

<h2>Pedidos de hoy</h2>
<table>
    <thead>
        <tr><th>Hora</th><th>Cliente</th><th>Teléfono</th><th>Item</th><th>Cant</th><th>Precio</th></tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?=htmlspecialchars(date('H:i', strtotime($o['created_at'])))?></td>
                <td><?=htmlspecialchars($o['name'])?></td>
                <td><?=htmlspecialchars($o['phone'])?></td>
                <td><?=htmlspecialchars($o['item'])?></td>
                <td><?=htmlspecialchars($o['qty'])?></td>
                <td><?=htmlspecialchars(number_format($o['price'], 2))?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="actions">
    <a href="export_csv.php" class="button">Exportar CSV</a>
    <a href="export_pdf.php" class="button">Reporte PDF</a>
</div>
</body>
</html>
