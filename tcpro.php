<?php
// Hedef API adresi
$api_url = "https://punisherservices.alwaysdata.net/apiservices/tcpro.php?tc=" . $_GET['tc'];

// Veriyi çek
$response = file_get_contents($api_url);

if ($response !== false) {
    $data = json_decode($response, true);

    // İstenmeyen anahtarları kaldır
    if (isset($data['developer'])) unset($data['developer']);
    if (isset($data['version'])) unset($data['version']);

    // 'results' anahtarını 'data' olarak değiştir
    if (isset($data['results'])) {
        $data['data'] = $data['results'];
        unset($data['results']);
    }

    // İstediğin telegram bilgisini en sona ekle
    $data['telegram'] = "t.me/Zeldy_here";

    // JSON formatında çıktı ver
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => "Veri çekilemedi."]);
}
?>
