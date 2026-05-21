<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db  = 'manajemen_iklan';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo 'gagal terkoneksi';
}