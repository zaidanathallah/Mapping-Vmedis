<?php
include 'db_connection.php';

if (isset($_GET['kota'])) {
    $kota = $conn->real_escape_string($_GET['kota']);

    // Menghapus isi kfa_code menjadi null
    $sql = "UPDATE default_mas_kota SET idkota_satusehat = NULL WHERE kota = '$kota'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
