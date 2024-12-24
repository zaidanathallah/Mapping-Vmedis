<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Menerima data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['mapped_meds']) && !empty($data['mapped_meds'])) {
    $mappedMeds = $data['mapped_meds'];

    // Menyiapkan query dengan tambahan kolom `obatindekskeamananwanitahamil`
    $stmt = $conn->prepare("UPDATE default_obat SET kfa_code = ?, obatkodebpom = ?, pabnama = ?, obatindekskeamananwanitahamil = ? WHERE obatnama = ?");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement gagal: ' . $conn->error]);
        exit();
    }

    // Melakukan update untuk setiap obat yang dipetakan
    foreach ($mappedMeds as $mappedMed) {
        // Tentukan data yang akan digunakan
        if (isset($mappedMed['mapped_kemenkes_med'])) {
            $medData = $mappedMed['mapped_kemenkes_med'];
        } elseif (isset($mappedMed['mapped_additional_med'])) {
            $medData = $mappedMed['mapped_additional_med'];
        } else {
            // Lewati entri ini jika tidak ada data mapping yang tersedia
            continue;
        }

        // Ambil field dari data yang tersedia
        $kfa_code = $medData['kfa_code'] ?? null;
        $obatkodebpom = $medData['obatkodebpom'] ?? null;
        $pabnama = $medData['pabnama'] ?? null;
        $obatindekskeamananwanitahamil = $medData['name'] ?? null; // Menambahkan nama untuk kolom `obatindekskeamananwanitahamil`
        $obatnama = $mappedMed['selected_med']['nama_obat'];

        // Bind parameters dan eksekusi
        $stmt->bind_param("sssss", $kfa_code, $obatkodebpom, $pabnama, $obatindekskeamananwanitahamil, $obatnama);
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
