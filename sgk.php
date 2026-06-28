<?php
header("Content-Type: application/json; charset=UTF-8");

$tc = $_GET['tc'] ?? null;
if (!$tc) {
    echo json_encode(["status" => "error", "message" => "TC girin"], JSON_UNESCAPED_UNICODE);
    exit;
}

$apiUrl = "https://bedavasorguapilerimpro-eaz.onrender.com/sgk/api?tc=" . urlencode($tc);
$json_raw = @file_get_contents($apiUrl);

if (!$json_raw) {
    echo json_encode(["status" => "error", "message" => "dosya hatası"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Ham veriyi bir görelim, gerçekten kesik mi geliyor?
$data = json_decode($json_raw, true);

if ($data) {
    // Eğer ana API'den gelen veride 'durum' alanı eksikse veya kesikse
    // burada veriyi manüel olarak tam okumaya zorlayacağız.
    echo json_encode([
        "status" => "success",
        "data" => $data, 
        "telegram" => "t.me/Zeldy_here"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo " veri çözülemedi: " . $json_raw;
}
?>
