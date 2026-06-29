<?php
header('Content-Type: application/json; charset=utf-8');

// 1. Kullanıcıdan gelen AD ve SOYAD parametrelerini al
$gelen_adi = isset($_REQUEST['adi']) ? trim($_REQUEST['adi']) : '';
$gelen_soyadi = isset($_REQUEST['soyadi']) ? trim($_REQUEST['soyadi']) : '';

if (empty($gelen_adi) || empty($gelen_soyadi)) {
    echo json_encode([
        "Success" => false,
        "Message" => "Lütfen Ad ve Soyad girin kanka.",
        "Telegram" => "https://t.me/Zeldyy_here"
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Karşı API'nin algılayabileceği muhtemel harf kombinasyonlarını hazırlıyoruz
$varyasyonlar = [
    [
        "adi" => mb_convert_case($gelen_adi, MB_CASE_UPPER, "UTF-8"),
        "soyadi" => mb_convert_case($gelen_soyadi, MB_CASE_UPPER, "UTF-8")
    ], // Varyasyon 1: ABDULSELAM DENİZ
    [
        "adi" => mb_convert_case($gelen_adi, MB_CASE_LOWER, "UTF-8"),
        "soyadi" => mb_convert_case($gelen_soyadi, MB_CASE_LOWER, "UTF-8")
    ], // Varyasyon 2: abdulselam deniz
    [
        "adi" => mb_convert_case($gelen_adi, MB_CASE_TITLE, "UTF-8"),
        "soyadi" => mb_convert_case($gelen_soyadi, MB_CASE_TITLE, "UTF-8")
    ]  // Varyasyon 3: Abdulselam Deniz
];

$final_response = "";
$basarili_veri = null;

// 2. Akıllı Döngü Başlıyor: Karşı API veri döndürene kadar varyasyonları dener
foreach ($varyasyonlar as $kombinasyon) {
    $target_url = "https://arastir.sbs/api/adsoyad.php?adi=" . urlencode($kombinasyon['adi']) . "&soyadi=" . urlencode($kombinasyon['soyadi']);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept: application/json"
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (!empty($response)) {
        $response_temiz = trim($response, "\xEF\xBB\xBF\x20\x1a\x0b\xa0");
        $check_json = json_decode($response_temiz, true);
        
        // Eğer karşı API "Eşleşen kimse bulunamadı" veya "Success => false" DÖNDÜRMEDİYSE, yani gerçek veri geldiyse
        if (is_array($check_json) && !isset($check_json['Success']) && !isset($check_json['Message'])) {
            $basarili_veri = $check_json;
            break; // Veriyi bulduk, diğer varyasyonları denemeye gerek yok, döngüden çık!
        } else {
            // Son gelen hata mesajını hafızada tutalım, eğer hiçbir varyasyon çalışmazsa ekrana basarız
            $final_response = $response_temiz;
        }
    }
}

// İsim formatlama fonksiyonu
function temizle_ad($veri) {
    if (is_null($veri)) return "";
    return mb_convert_case(trim(strip_tags($veri)), MB_CASE_TITLE, "UTF-8");
}

// 3. Veriyi İşleme ve Ekrana Basma Aşaması
if (is_array($basarili_veri)) {
    $clean_data = [];
    
    foreach ($basarili_veri as $index => $person) {
        if (is_array($person)) {
            foreach ($person as $key => $value) {
                $new_key = mb_convert_case(trim($key), MB_CASE_UPPER, "UTF-8");
                
                if (is_null($value)) {
                    $new_value = "";
                } elseif (is_numeric($value) || filter_var($value, FILTER_VALIDATE_URL)) {
                    $new_value = trim($value);
                } else {
                    $value_clean = trim(strip_tags($value));
                    if ($new_key === 'SOYADI') {
                        $new_value = mb_convert_case($value_clean, MB_CASE_UPPER, "UTF-8");
                    } elseif (in_array($new_key, ['ADI', 'ADI_SOYADI', 'ANNEADI', 'BABAADI', 'NUFUSIL', 'NUFUSILCE'])) {
                        $new_value = temizle_ad($value_clean);
                    } else {
                        $new_value = $value_clean;
                    }
                }
                $clean_data[$index][$new_key] = $new_value;
            }
        }
    }

    echo json_encode([
        "Sonuclar" => $clean_data,
        "Telegram" => "https://t.me/Zeldyy_here"
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} else {
    // Eğer 3 varyasyonda da veri bulunamadıysa karşı API'nin orijinal hata çıktısını basıyoruz
    if (!empty($final_response)) {
        echo $final_response;
    } else {
        echo json_encode([
            "Success" => false,
            "Message" => "Karşı sunucudan hiçbir kombinasyonda yanıt alınamadı kanka.",
            "Telegram" => "https://t.me/Zeldyy_here"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
?>
