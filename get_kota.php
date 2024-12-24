<?php
include 'db_connection.php';

// Tangkap parameter pencarian
$provinsi = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
$kota = isset($_GET['kota']) ? $_GET['kota'] : '';
$idkota = isset($_GET['idkota']) ? $_GET['idkota'] : ''; // Tangkap idkota
$idkotaStatus = isset($_GET['idkotaStatus']) ? $_GET['idkotaStatus'] : '';
$provinsiAktif = isset($_GET['ProvinsiAktif']) ? $_GET['ProvinsiAktif'] : '';
$kotaAktif = isset($_GET['KotaAktif']) ? $_GET['KotaAktif'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : '';

// Mulai membangun query
$sql = "SELECT DMP.*, DMK.kota, DMK.kotaaktif, DMK.idkota_satusehat, DMK.idkota
        FROM default_mas_provinsi DMP
        LEFT JOIN default_mas_kota DMK ON DMP.idprovinsi = DMK.idprovinsi
        WHERE 1=1";

// Tambahkan kondisi pencarian untuk provinsi
if (!empty($provinsi)) {
    $provinsi = $conn->real_escape_string($provinsi);
    $sql .= " AND provinsi LIKE '%$provinsi%'";
}

// Tambahkan kondisi pencarian untuk kota
if (!empty($kota)) {
    $kota = $conn->real_escape_string($kota);
    $sql .= " AND kota LIKE '%$kota%'";
}

// Tambahkan kondisi pencarian untuk ID kota
if (!empty($idkota)) {
    $idkota = $conn->real_escape_string($idkota);
    $sql .= " AND DMK.idkota = '$idkota'";
}

// Tambahkan kondisi untuk status provinsi
if ($provinsiAktif === '1') {
    $sql .= " AND DMP.provinsiaktif = 1";
} elseif ($provinsiAktif === '0') {
    $sql .= " AND (DMP.provinsiaktif = 0 OR DMP.provinsiaktif IS NULL)";
}

// Tambahkan kondisi untuk status kota
if ($kotaAktif === '1') {
    $sql .= " AND DMK.kotaaktif = 1";
} elseif ($kotaAktif === '0') {
    $sql .= " AND (DMK.kotaaktif = 0 OR DMK.kotaaktif IS NULL)";
}

// Tambahkan kondisi untuk status ID kota
if ($idkotaStatus === 'sudah') {
    $sql .= " AND DMK.idkota_satusehat IS NOT NULL AND DMK.idkota_satusehat != ''";
} elseif ($idkotaStatus === 'belum') {
    $sql .= " AND (DMK.idkota_satusehat IS NULL OR DMK.idkota_satusehat = '')";
}

// Tambahkan logika pengurutan untuk provinsi dan kota secara bersamaan
if ($sortBy === 'provinsi_kota_asc') {
    $sql .= " ORDER BY DMP.provinsi ASC, DMK.kota ASC";
}

// Eksekusi query
$result = $conn->query($sql);
$dataList = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataList[] = $row;
    }
}
$conn->close();

// Kembalikan data dalam format JSON

echo json_encode($dataList);
?>
