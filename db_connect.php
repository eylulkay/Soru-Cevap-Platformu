<?php
session_start();

try {
    $db = new PDO(
        "mysql:host=localhost;dbname=sherlock;charset=utf8",
        "root",
        ""
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB HATA: " . $e->getMessage());
}
