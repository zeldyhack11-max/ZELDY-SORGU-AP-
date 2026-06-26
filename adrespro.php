<?php
header('Content-Type: application/json; charset=utf-8');

// Gelen TC parametresini al ve temizle
$tc = isset($_GET['tc']) ? trim($_GET['tc']) : null;

// PARAMETRE EKSİKSE HATA DÖNDÜR
if ($tc === null || $tc === "") {
    echo json_encode([
        'status' => 'error',
        'message' => 'Eksik parametre! Lütfen sorgulamak için bir "tc" parametresi gönderin.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Python Anywhere Seçmen API linki
$target_url = "https://zeldyworld.pythonanywhere.com/secmen?tc=" . urlencode($tc);

// cURL ile PythonAnywhere API'sine istek atıyoruz
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Tarayıcı taklidi
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Accept-Language: tr,en-US;q=0.9,en;q=0.8'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Gelen cevabı kontrol et ve sadece istenen adres alanlarını filtrele
if ($http_code == 200 && $response) {
    $json_data = json_decode($response, true);
    
    // Gelen JSON geçerliyse ve içinde "Bilgiler" alanı varsa işleme başla
    if ($json_data && isset($json_data['Bilgiler'])) {
        $bilgiler = $json_data['Bilgiler'];

        // Sadece senin istediğin profesyonel adres alanlarını cımbızlıyoruz
        $filtrelenmis_veri = [
            'status'      => 'success',
            'Ad'          => isset($bilgiler['Ad']) ? $bilgiler['Ad'] : null,
            'Daire_No'    => isset($bilgiler['Daire_No']) ? $bilgiler['Daire_No'] : "",
            'Kapi_No'     => isset($bilgiler['Kapi_No']) ? $bilgiler['Kapi_No'] : null,
            'Mahalle'     => isset($bilgiler['Mahalle']) ? $bilgiler['Mahalle'] : null,
            'Nufus_Il'    => isset($bilgiler['Nufus_Il']) ? $bilgiler['Nufus_Il'] : null,
            'Nufus_Ilce'  => isset($bilgiler['Nufus_Ilce']) ? $bilgiler['Nufus_Ilce'] : null,
            'Sokak_Cadde' => isset($bilgiler['Sokak_Cadde']) ? $bilgiler['Sokak_Cadde'] : null,
            'Soyad'       => isset($bilgiler['Soyad']) ? $bilgiler['Soyad'] : null,
            'TC'          => isset($bilgiler['TC']) ? $bilgiler['TC'] : $tc,
            'telegram'    => 't.me/Zeldy_here'
        ];

        // İstediğin düzende çıktı veriyoruz
        echo json_encode($filtrelenmis_veri, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Veri bulunamadı veya geçersiz TC.'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Kaynak API\'den veri alınamadı.',
        'debug' => [
            'http_code' => $http_code,
            'curl_error' => $curl_error
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
