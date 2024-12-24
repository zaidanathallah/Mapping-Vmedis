<?php
include 'db_connection.php';

// Menerima parameter pencarian
$obatnama = isset($_GET['obatnama']) ? $_GET['obatnama'] : '';
$kfaStatus = isset($_GET['kfaStatus']) ? $_GET['kfaStatus'] : ''; // Tambahkan parameter untuk status KFA

// Mulai membangun query
$sql = "SELECT obatkode, kfa_code, obatindekskeamananwanitahamil, obatkodebpom, obatnama, satuan1, katonama, pabnama, golonama FROM default_obat WHERE 1=1";

// Menambahkan kondisi pencarian jika ada
if (!empty($obatnama)) {
    $obatnama = $conn->real_escape_string($obatnama); // Sanitasi input untuk keamanan
    $sql .= " AND obatnama LIKE '%$obatnama%'";
}

// Menambahkan kondisi untuk status KFA jika dipilih
if ($kfaStatus === 'sudah') {
    $sql .= " AND kfa_code IS NOT NULL AND kfa_code != ''";
} elseif ($kfaStatus === 'belum') {
    $sql .= " AND (kfa_code IS NULL OR kfa_code = '')";
}

// Eksekusi query
$result = $conn->query($sql);
$obatList = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $obatList[] = $row;
    }
}
$conn->close();

// Kembalikan hasil dalam format JSON
echo json_encode($obatList);
?>
