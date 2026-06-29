<?php
header('Content-Type: application/json; charset=utf-8');

$tckn = $_GET['tckn'] ?? '';

if (!$tckn) {
    echo json_encode([
        "Success" => false,
        "Message" => "TCKN yok"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$url = "https://zeldyhackteamv2.pythonanywhere.com/api?tckn=" . urlencode($tckn);

// 🔥 API isteği (cURL)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $http_code != 200) {
    echo json_encode([
        "Success" => false,
        "Message" => "data hatası"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// JSON decode
$data = json_decode($response, true);

echo json_encode([
    "Success" => true,
    "Data" => $data["Data"] ?? [],
    "Telegram" => "https://t.me/zeldysorgu"
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>