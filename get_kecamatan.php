<?php
include 'db_connection.php';

// Tangkap parameter pencarian
$provinsi = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
$kota = isset($_GET['kota']) ? $_GET['kota'] : '';
$idkota = isset($_GET['idkota']) ? $_GET['idkota'] : ''; // Tangkap idkota
$provinsiAktif = isset($_GET['ProvinsiAktif']) ? $_GET['ProvinsiAktif'] : '';
$kotaAktif = isset($_GET['KotaAktif']) ? $_GET['KotaAktif'] : '';
$idkecamatan = isset($_GET['idkecamatan']) ? $_GET['idkecamatan'] : '';
$idkecamatanStatus = isset($_GET['idkecamatanStatus']) ? $_GET['idkecamatanStatus'] : '';
$kecamatanAktif = isset($_GET['KecamatanAktif']) ? $_GET['KecamatanAktif'] : '';
$kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : '';

// Mulai membangun query
$sql = "SELECT 
            DMP.provinsi, DMP.provinsiaktif, DMP.idprovinsi_satusehat, DMP.idprovinsi,
            DMK.kota, DMK.kotaaktif, DMK.idkota_satusehat, DMK.idkota,
            DMKC.kecamatan, DMKC.kecamatanaktif, DMKC.idkecamatan_satusehat, DMKC.idkecamatan
        FROM default_mas_provinsi DMP
        LEFT JOIN default_mas_kota DMK ON DMP.idprovinsi = DMK.idprovinsi
        LEFT JOIN default_mas_kecamatan DMKC ON DMK.idkota = DMKC.idkota
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

// Tambahkan kondisi pencarian untuk kota
if (!empty($kecamatan)) {
    $kecamatan = $conn->real_escape_string($kecamatan);
    $sql .= " AND kecamatan LIKE '%$kecamatan%'";
}


// Tambahkan kondisi pencarian untuk ID kota
if (!empty($idkota)) {
    $idkota = $conn->real_escape_string($idkota);
    $sql .= " AND DMK.idkota = '$idkota'";
}

// Tambahkan kondisi pencarian untuk ID kecamatan
if (!empty($idkecamatan)) {
    $idkecamatan = $conn->real_escape_string($idkecamatan);
    $sql .= " AND DMKC.idkecamatan = '$idkecamatan'";
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

// Tambahkan kondisi untuk status kecamatan
if ($kecamatanAktif === '1') {
    $sql .= " AND DMKC.kecamatanaktif = 1";
} elseif ($kecamatanAktif === '0') {
    $sql .= " AND (DMKC.kecamatanaktif = 0 OR DMKC.kecamatanaktif IS NULL)";
}



// Tambahkan kondisi untuk status ID kota
if ($idkecamatanStatus === 'sudah') {
    $sql .= " AND DMKC.idkecamatan_satusehat IS NOT NULL AND DMKC.idkecamatan_satusehat != ''";
} elseif ($idkecamatanStatus === 'belum') {
    $sql .= " AND (DMKC.idkecamatan_satusehat IS NULL OR DMKC.idkecamatan_satusehat = '')";
}

// Tambahkan pengurutan berdasarkan provinsi, kota, dan kecamatan
if ($sortBy === 'provinsi_kota_kecamatan_asc') {
    $sql .= " ORDER BY DMP.provinsi ASC, DMK.kota ASC, DMKC.kecamatan ASC";
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
