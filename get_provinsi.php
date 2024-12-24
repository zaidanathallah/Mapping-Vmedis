<?php
include 'db_connection.php';

// Menerima parameter pencarian
$provinsi = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
$idprovinsiStatus = isset($_GET['idprovinsiStatus']) ? $_GET['idprovinsiStatus'] : ''; // Parameter untuk status KFA
$provinsiAktif = isset($_GET['ProvinsiAktif']) ? $_GET['ProvinsiAktif'] : ''; // Parameter untuk status aktif/non-aktif

// Mulai membangun query
$sql = "SELECT provinsi, provinsiaktif, idprovinsi_satusehat FROM default_mas_provinsi WHERE 1=1";

// Menambahkan kondisi pencarian jika ada
if (!empty($provinsi)) {
    $provinsi = $conn->real_escape_string($provinsi); // Sanitasi input untuk keamanan
    $sql .= " AND provinsi LIKE '%$provinsi%'";
}

// Menambahkan kondisi untuk status KFA jika dipilih
if ($idprovinsiStatus === 'sudah') {
    $sql .= " AND idprovinsi_satusehat IS NOT NULL AND idprovinsi_satusehat != ''";
} elseif ($idprovinsiStatus === 'belum') {
    $sql .= " AND (idprovinsi_satusehat IS NULL OR idprovinsi_satusehat = '')";
}

// Menambahkan kondisi untuk status aktif/non-aktif
if ($provinsiAktif === '1') {
    $sql .= " AND provinsiaktif = 1"; // Untuk provinsi yang aktif
} elseif ($provinsiAktif === '0') {
    $sql .= " AND (provinsiaktif = 0 OR provinsiaktif IS NULL)"; // Untuk provinsi yang non-aktif atau null
}

// Eksekusi query
$result = $conn->query($sql);
$ProvinsiList = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ProvinsiList[] = $row;
    }
}
$conn->close();

// Kembalikan hasil dalam format JSON
echo json_encode($ProvinsiList);
?>
