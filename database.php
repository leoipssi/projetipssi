?php
$servername = "localhost";
$username = "emotion_user";
$password = "IPSSI2024";
$database = "e_motion";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $pass>
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
