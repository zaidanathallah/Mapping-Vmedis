<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Menerima data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

// Logging data untuk debugging
file_put_contents('debug.log', json_encode($data) . PHP_EOL, FILE_APPEND);

if (isset($data['mapped_kelurahan']) && !empty($data['mapped_kelurahan'])) {
    $mappedKelurahan = $data['mapped_kelurahan'];

    // Menyiapkan query
    $stmt = $conn->prepare("UPDATE default_mas_kelurahan SET idkelurahan_satusehat = ? WHERE idkelurahan = ?");

    if (!$stmt) {
        file_put_contents('debug.log', "Prepare Statement Error: " . $conn->error . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
        exit();
    }

    $totalUpdated = 0;

    foreach ($mappedKelurahan as $mappedKel) {
        $IDKELURAHANSATUSEHAT = $mappedKel['mapped_kemenkes_kelurahan']['IDKELURAHANSATUSEHAT'] ?? null;
        $IDKELURAHAN = $mappedKel['selected_kelurahan']['IDKELURAHAN'] ?? null;

        // Logging nilai yang akan di-update
        file_put_contents('debug.log', "Attempting Update: IDKELURAHANSATUSEHAT = $IDKELURAHANSATUSEHAT, IDKELURAHAN = $IDKELURAHAN" . PHP_EOL, FILE_APPEND);

        if (empty($IDKELURAHANSATUSEHAT) || empty($IDKELURAHAN)) {
            file_put_contents('debug.log', "Skipped Update: Missing Data." . PHP_EOL, FILE_APPEND);
            continue;
        }

        $stmt->bind_param("ss", $IDKELURAHANSATUSEHAT, $IDKELURAHAN);

        if ($stmt->execute()) {
            $affectedRows = $stmt->affected_rows;
            $totalUpdated += $affectedRows;
            file_put_contents('debug.log', "Query Succeeded: $affectedRows rows affected." . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents('debug.log', "Query Failed: " . $stmt->error . PHP_EOL, FILE_APPEND);
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'updated' => $totalUpdated]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau kosong.']);
}
?>


