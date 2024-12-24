<?php
include 'db_connection.php';

if (isset($_GET['kecamatan'])) {
    $kecamatan = $conn->real_escape_string($_GET['kecamatan']);

    // Menghapus isi kfa_code menjadi null
    $sql = "UPDATE default_mas_kecamatan SET idkecamatan_satusehat = NULL WHERE kecamatan = '$kecamatan'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
