<?php
$servername = "localhost";
$username = "emotion_user";
$password = "IPSSI2024";
$database = "e_motion";
global $db;

try {
    $db = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

function is_db_connected() {
    global $db;
    return isset($db) && $db instanceof PDO;
}
?>
