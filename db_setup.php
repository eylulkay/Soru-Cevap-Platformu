<?php
require_once "db_connect.php";

$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
$stmt->execute(['admin']);

if ($stmt->fetchColumn() == 0) {
    $db->prepare("
        INSERT INTO users (username, password, role)
        VALUES (?, ?, 'admin')
    ")->execute(['admin', 'admin123']);
}
