<?php
header('Content-Type: application/json; charset=utf-8');

// 1. Kullanıcıdan gelen AD ve SOYAD parametrelerini al
$adi = isset($_REQUEST['adi']) ? $_REQUEST['adi'] : '';
$soyadi = isset($_REQUEST['soyadi']) ? $_REQUEST['soyadi'] : '';

if (empty($adi) || empty($soyadi)) {
    echo json_encode([
        "Success" => false,
        "Message" => "Lütfen Ad ve Soyad girin kanka.",
        "Telegram" => "https://t.me/Zeldyy_here"
    ], 256 | 128 | 64);
    exit;
}

// 2. Hedef API Linki (Parametreleri URL'ye ekliyoruz)
$target_url = "https://arastir.vip/api/adsoyad.php?adi=" . urlencode($adi) . "&soyadi=" . urlencode($soyadi);

// 3. Veri Çekme İşlemi (cURL)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Ad soyad sorgu biraz uzun sürebilir
$response = curl_exec($ch);
curl_close($ch);

if (empty($response)) {
    echo json_encode(["Success" => false, "Message" => "Veri gelmedi veya eşleşme yok.", "Telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
} else {
    $raw_data = json_decode($response, true);
    $clean_data = [];

    if (is_array($raw_data)) {
        // Eğer API bir liste (array) döndürüyorsa her bir kişiyi tek tek gez
        foreach ($raw_data as $index => $person) {
            if (is_array($person)) {
                foreach ($person as $key => $value) {
                    // 1. ANAHTARLARI DÜZELT (ADI -> Adı, SOYADI -> Soyadı)
                    $new_key = mb_convert_case(mb_strtolower($key, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
                    
                    // 2. DEĞERLERİ DÜZELT (YILMAZ -> Yılmaz)
                    if (is_string($value) && !filter_var($value, FILTER_VALIDATE_URL) && !is_numeric($value)) {
                        $new_value = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
                    } else {
                        $new_value = $value;
                    }
                    $clean_data[$index][$new_key] = $new_value;
                }
            }
        }

        // Eğer sonuç listesi boş değilse imzamızı ekleyelim
        $final_output = [
            "Sonuclar" => $clean_data,
            "Telegram" => "https://t.me/Zeldyy_here"
        ];
        
        echo json_encode($final_output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(["Success" => false, "Message" => "Format hatası veya veri bulunamadı.", "Telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
    }
}
?>
