<?php
// $host = '127.0.0.1';
// $user = 'root';
// $pass = '';
// $db  = 'manajemen_iklan';
// $conn = mysqli_connect($host, $user, $pass, $db);

// if (!$conn) {
//     echo 'gagal terkoneksi';
$dsn = "mysql:host=localhost;dbname=manajemen_iklan;charset=utf8mb4";
$dbuser = "root";
$dbpass = "";

try {
    $conn = new PDO($dsn, $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>