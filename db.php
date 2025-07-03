<?php
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('sqlite:database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $db;
}

function createTables() {
    $db = getDB();
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        phone TEXT,
        item TEXT,
        qty INTEGER,
        price REAL,
        created_at TEXT
    )");
}

function addOrder($name, $phone, $item, $qty, $price) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO orders (name, phone, item, qty, price, created_at)
                          VALUES (?, ?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$name, $phone, $item, $qty, $price]);
}

function getTodayOrders() {
    $db = getDB();
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT * FROM orders WHERE date(created_at)=? ORDER BY created_at DESC");
    $stmt->execute([$today]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
