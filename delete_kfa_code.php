<?php
include 'db_connection.php';

if (isset($_GET['obatkode'])) {
    $obatkode = $conn->real_escape_string($_GET['obatkode']);

    // Menghapus isi kfa_code dan obatindekskeamananwanitahamil menjadi null
    $sql = "UPDATE default_obat SET kfa_code = NULL, obatindekskeamananwanitahamil = NULL, obatkodebpom = NULL WHERE obatkode = '$obatkode'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
