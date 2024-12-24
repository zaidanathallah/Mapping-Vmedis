<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Menerima data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

// Logging data untuk debugging
file_put_contents('debug.log', json_encode($data) . PHP_EOL, FILE_APPEND);

if (isset($data['mapped_province']) && !empty($data['mapped_province'])) {
    $mappedProvinces = $data['mapped_province'];

    // Menyiapkan query
    $stmt = $conn->prepare("UPDATE default_mas_provinsi SET idprovinsi_satusehat = ? WHERE provinsi = ?");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
        exit();
    }

    // Melakukan update untuk setiap provinsi yang dipetakan
    foreach ($mappedProvinces as $mappedProv) {
        // Tentukan data yang akan digunakan
        if (isset($mappedProv['mapped_kemenkes_provinsi'])) {
            $medDataProv = $mappedProv['mapped_kemenkes_provinsi'];
        } elseif (isset($mappedProv['mapped_additional_provinsi'])) {
            $medDataProv = $mappedProv['mapped_additional_provinsi'];
        } else {
            // Lewati entri ini jika tidak ada data mapping yang tersedia
            continue;
        }

        // Ambil field dari data yang tersedia
        $IDPROVINSISATUSEHAT = $medDataProv['IDPROVINSISATUSEHAT'] ?? null;
        $PROVINSI = $mappedProv['selected_provinsi']['PROVINSI'] ?? null;

        // Validasi data
        if ($IDPROVINSISATUSEHAT === null || $PROVINSI === null) {
            continue; // Lewati jika data tidak lengkap
        }

        // Bind parameters dan eksekusi
        $stmt->bind_param("ss", $IDPROVINSISATUSEHAT, $PROVINSI);
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
