<?php
session_start();  // Memulai sesi
header('Content-Type: application/json');  // Pastikan respons adalah JSON

function getCachedAccessToken() {
    if (isset($_SESSION['access_token']) && isset($_SESSION['token_expiration']) && time() < $_SESSION['token_expiration']) {
        return $_SESSION['access_token'];
    }

    $newToken = getAccessToken();
    if ($newToken) {
        $_SESSION['access_token'] = $newToken;
        $_SESSION['token_expiration'] = time() + 3600;
    }

    return $newToken;
}

function getAccessToken() {
    $tokenUrl = "https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials";
    $client_id = "2uB6eB6InLzaCIa1H6oFGAXvRScVTiLCyKYdo4LP25MRbrAG";
    $client_secret = "20E6tVDcOYxqKH1iKIgsoMo1GDoZIXGjcLncKcpRj5p0fkZPR72UkNmYq0qAE07B";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['client_id' => $client_id, 'client_secret' => $client_secret]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

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
        error_log("Failed to obtain access token: " . $response);
        return null;
    }
}

function getKemenkesData($accessToken, $keyword) {
    $url = "https://api-satusehat-stg.dto.kemkes.go.id/kfa-v2/products/all?page=1&size=30&product_type=farmasi&keyword=" . urlencode($keyword);

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
        return json_encode(['error' => "Gagal mengambil data dari Kemenkes."]);
    }
    curl_close($ch);

    if (json_decode($response) === null) {
        error_log("Invalid JSON response: " . $response);
        return json_encode(['error' => "Respons dari API tidak valid."]);
    }

    return $response;
}

if (isset($_POST['nama_obat']) && !empty($_POST['nama_obat'])) {
    $nama_obat = $_POST['nama_obat'];

    $accessToken = getCachedAccessToken();
    if ($accessToken) {
        $kemenkesData = getKemenkesData($accessToken, $nama_obat);

        echo $kemenkesData;
    } else {
        echo json_encode(['error' => "Tidak dapat mengakses API tanpa token."]);
    }
} else {
    echo json_encode(['error' => "Nama obat tidak diberikan."]);
}
?>
