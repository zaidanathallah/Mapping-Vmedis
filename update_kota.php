<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Menerima data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

// Logging data untuk debugging
file_put_contents('debug.log', json_encode($data) . PHP_EOL, FILE_APPEND);

if (isset($data['mapped_kota']) && !empty($data['mapped_kota'])) {
    $mappedKota = $data['mapped_kota'];

    // Menyiapkan query
    $stmt = $conn->prepare("UPDATE default_mas_kota SET idkota_satusehat = ? WHERE idkota = ?");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
        exit();
    }

    // Melakukan update untuk setiap kota yang dipetakan
    foreach ($mappedKota as $mappedKot) {
        // Ambil data yang diperlukan
        $IDKOTASATUSEHAT = $mappedKot['mapped_kemenkes_kota']['IDKOTASATUSEHAT'] ?? null;
        $IDKOTA = $mappedKot['selected_kota']['IDKOTA'] ?? null;

        // Lewati jika salah satu data tidak valid
        if (empty($IDKOTASATUSEHAT) || empty($IDKOTA)) {
            continue;
        }

        // Bind parameters dan eksekusi
        $stmt->bind_param("ss", $IDKOTASATUSEHAT, $IDKOTA);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Execute statement gagal: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit();
        }
    }

    $stmt->close();
    $conn->close();

    // Kembalikan respons sukses
    echo json_encode(['success' => true]);
} else {
    // Kembalikan respons gagal jika data tidak lengkap
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau kosong.']);
}
?>
