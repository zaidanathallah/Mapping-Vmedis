<?php
include 'db_connection.php';

if (isset($_GET['provinsi'])) {
    $provinsi = $conn->real_escape_string($_GET['provinsi']);

    // Menghapus isi kfa_code menjadi null
    $sql = "UPDATE default_mas_provinsi SET idprovinsi_satusehat = NULL WHERE provinsi = '$provinsi'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
