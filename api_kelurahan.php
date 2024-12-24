<?php
session_start();  // Memulai sesi

// Fungsi untuk mendapatkan atau menggunakan token yang disimpan
function getCachedAccessToken() {
    if (isset($_SESSION['access_token']) && isset($_SESSION['token_expiration']) && time() < $_SESSION['token_expiration']) {
        // Gunakan token yang ada jika belum kedaluwarsa
        return $_SESSION['access_token'];
    }

    // Ambil token baru jika tidak ada atau sudah kedaluwarsa
    $newToken = getAccessToken();
    if ($newToken) {
        $_SESSION['access_token'] = $newToken;
        $_SESSION['token_expiration'] = time() + 3600;  // Token berlaku selama 1 jam
        return $newToken;
    }

    return null;  // Jika gagal mendapatkan token baru
}

// Fungsi untuk mendapatkan akses token dari API Kemenkes
function getAccessToken() {
    $tokenUrl = "https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials";
    $client_id = "2uB6eB6InLzaCIa1H6oFGAXvRScVTiLCyKYdo4LP25MRbrAG";
    $client_secret = "20E6tVDcOYxqKH1iKIgsoMo1GDoZIXGjcLncKcpRj5p0fkZPR72UkNmYq0qAE07B";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id' => $client_id,
        'client_secret' => $client_secret
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/x-www-form-urlencoded"
    ));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $response_data = json_decode($response, true);
    if (isset($response_data['access_token'])) {
        return $response_data['access_token'];
    } else {
        error_log("Failed to obtain access token: " . json_encode($response_data));
        return null;
    }
}



// Fungsi untuk mengambil data kota berdasarkan city_codes
function getKelurahanData($accessToken, $district_codes) {
    $url = "https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/sub-districts?district_codes=" . urlencode($district_codes);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $accessToken,
        "Accept: application/json"
    ));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return json_encode(['error' => "Gagal mengambil data kota dari Kemenkes."]);
    }
    curl_close($ch);

    return $response;
}

// Check if a POST request with 'IDPROVINSISATUSEHAT' is made
if (isset($_POST['IDKECAMATANSATUSEHAT']) && !empty($_POST['IDKECAMATANSATUSEHAT'])) {
    $IDKECAMATANSATUSEHAT = $_POST['IDKECAMATANSATUSEHAT'];
    error_log("Received IDKECAMATANSATUSEHAT: " . $IDKECAMATANSATUSEHAT);

    $accessToken = getCachedAccessToken();  // Gunakan token yang dicache
    if ($accessToken) {
        $KelurahanData = getKelurahanData($accessToken, $IDKECAMATANSATUSEHAT); // Panggil fungsi getCityData dengan ID Provinsi

        echo $KelurahanData;
    } else {
        echo json_encode(['error' => "Tidak dapat mengakses API tanpa token."]);
    }
} else {
    error_log("IDKECAMATANSATUSEHAT tidak diberikan.");
    echo json_encode(['error' => "IDKECAMATANSATUSEHAT tidak diberikan."]);
}

?>
