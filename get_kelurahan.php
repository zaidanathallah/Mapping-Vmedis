<?php
include 'db_connection.php';

// Tangkap parameter pencarian
$provinsi = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
$provinsiAktif = isset($_GET['ProvinsiAktif']) ? $_GET['ProvinsiAktif'] : '';
$kota = isset($_GET['kota']) ? $_GET['kota'] : '';
$kotaAktif = isset($_GET['KotaAktif']) ? $_GET['KotaAktif'] : '';
$kecamatanAktif = isset($_GET['KecamatanAktif']) ? $_GET['KecamatanAktif'] : '';
$kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$idkelurahan = isset($_GET['idkelurahan']) ? $_GET['idkelurahan'] : '';
$idkelurahanStatus = isset($_GET['idkelurahanStatus']) ? $_GET['idkelurahanStatus'] : '';
$kelurahanAktif = isset($_GET['KelurahanAktif']) ? $_GET['KelurahanAktif'] : '';
$kelurahan = isset($_GET['kelurahan']) ? $_GET['kelurahan'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : '';

// Mulai membangun query
$sql = "SELECT 
            DMP.provinsi, DMP.provinsiaktif, DMP.idprovinsi_satusehat, DMP.idprovinsi,
            DMK.kota, DMK.kotaaktif, DMK.idkota_satusehat, DMK.idkota,
            DMKC.kecamatan, DMKC.kecamatanaktif, DMKC.idkecamatan_satusehat, DMKC.idkecamatan,
            DMKL.kelurahan, DMKL.kelurahanaktif, DMKL.idkelurahan_satusehat, DMKL.idkelurahan, DMKL.idkelurahan
        FROM default_mas_provinsi DMP
        LEFT JOIN default_mas_kota DMK ON DMP.idprovinsi = DMK.idprovinsi
        LEFT JOIN default_mas_kecamatan DMKC ON DMK.idkota = DMKC.idkota
        LEFT JOIN default_mas_kelurahan DMKL ON DMKC.idkecamatan = DMKL.idkecamatan
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

// Tambahkan kondisi pencarian untuk kota
if (!empty($kelurahan)) {
    $kelurahan = $conn->real_escape_string($kelurahan);
    $sql .= " AND kelurahan LIKE '%$kelurahan%'";
}


// Tambahkan kondisi pencarian untuk ID kelurahan
if (!empty($idkelurahan)) {
    $idkelurahan = $conn->real_escape_string($idkelurahan);
    $sql .= " AND DMKL.idkelurahan = '$idkelurahan'";
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

// Tambahkan kondisi untuk status kelurahan 
if ($kelurahanAktif === '1') {
    $sql .= " AND DMKL.kelurahanaktif = 1"; 
} elseif ($kelurahanAktif === '0') {
    $sql .= " AND (DMKL.kelurahanaktif = 0 OR DMKL.kelurahanaktif IS NULL)";    
}



// Tambahkan kondisi untuk status ID kota
if ($idkelurahanStatus === 'sudah') {
    $sql .= " AND DMKL.idkelurahan_satusehat IS NOT NULL AND DMKL.idkelurahan_satusehat != ''";
} elseif ($idkelurahanStatus === 'belum') {
    $sql .= " AND (DMKL.idkelurahan_satusehat IS NULL OR DMKL.idkelurahan_satusehat = '')";
}

// Tambahkan pengurutan berdasarkan provinsi, kota, dan kecamatan
if ($sortBy === 'provinsi_kota_kecamatan_kelurahan_asc') {
    $sql .= " ORDER BY DMP.provinsi ASC, DMK.kota ASC, DMKC.kecamatan ASC, DMKL.kelurahan ASC";
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
