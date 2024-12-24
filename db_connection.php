<?php
$host = 'vmeddb-develop.cmef0kxy4b3u.ap-southeast-1.rds.amazonaws.com';
$user = 'zaidan';
$password = 'Zaidan20241002';
$database = 'db_vmedis_mr';
$port = 2222;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
?>
