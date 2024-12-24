<?php
include 'db_connection.php';

if (isset($_GET['kelurahan'])) {
    $kelurahan = $conn->real_escape_string($_GET['kelurahan']);

    // Menghapus isi kfa_code menjadi null
    $sql = "UPDATE default_mas_kelurahan SET idkelurahan_satusehat = NULL WHERE kelurahan = '$kelurahan'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
