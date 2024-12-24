<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Menerima data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

// Logging data untuk debugging
file_put_contents('debug.log', json_encode($data) . PHP_EOL, FILE_APPEND);

if (isset($data['mapped_kecamatan']) && !empty($data['mapped_kecamatan'])) {
    $mappedKecamatan = $data['mapped_kecamatan'];

    // Menyiapkan query
    $stmt = $conn->prepare("UPDATE default_mas_kecamatan SET idkecamatan_satusehat = ? WHERE idkecamatan = ?");

    if (!$stmt) {
        file_put_contents('debug.log', "Prepare Statement Error: " . $conn->error . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
        exit();
    }

    $totalUpdated = 0;

    foreach ($mappedKecamatan as $mappedKec) {
        $IDKECAMATANSATUSEHAT = $mappedKec['mapped_kemenkes_kecematan']['IDKECAMATANSATUSEHAT'] ?? null;
        $IDKECAMATAN = $mappedKec['selected_kecamatan']['IDKECAMATAN'] ?? null;

        // Logging nilai yang akan di-update
        file_put_contents('debug.log', "Attempting Update: IDKECAMATANSATUSEHAT = $IDKECAMATANSATUSEHAT, IDKECAMATAN = $IDKECAMATAN" . PHP_EOL, FILE_APPEND);

        if (empty($IDKECAMATANSATUSEHAT) || empty($IDKECAMATAN)) {
            file_put_contents('debug.log', "Skipped Update: Missing Data." . PHP_EOL, FILE_APPEND);
            continue;
        }

        $stmt->bind_param("ss", $IDKECAMATANSATUSEHAT, $IDKECAMATAN);

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
