<?php
header('Content-Type: application/json; charset=utf-8');

// 1. Parametreleri al (Ad ve Soyad şart, İl ve İlçe isteğe bağlı)
$adi = isset($_REQUEST['adi']) ? $_REQUEST['adi'] : '';
$soyadi = isset($_REQUEST['soyadi']) ? $_REQUEST['soyadi'] : '';
$il = isset($_REQUEST['il']) ? $_REQUEST['il'] : '';
$ilce = isset($_REQUEST['ilce']) ? $_REQUEST['ilce'] : '';

if (empty($adi) || empty($soyadi)) {
    echo json_encode([
        "Success" => false,
        "Message" => "Kanka Ad ve Soyad girmeden sorgu yapamam.",
        "Telegram" => "https://t.me/Zeldyy_here"
    ], 256 | 128 | 64);
    exit;
}

// 2. Hedef API Linki (İl ve İlçe parametreleri dahil)
$target_url = "https://arastir.sbs/api/adsoyad.php?adi=" . urlencode($adi) . 
              "&soyadi=" . urlencode($soyadi) . 
              "&il=" . urlencode($il) . 
              "&ilce=" . urlencode($ilce);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_TIMEOUT, 25); 
$response = curl_exec($ch);
curl_close($ch);

if (empty($response)) {
    echo json_encode(["Success" => false, "Message" => "Eşleşen kimse bulunamadı.", "Telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
} else {
    $raw_data = json_decode($response, true);
    $clean_results = [];

    if (is_array($raw_data)) {
        foreach ($raw_data as $index => $person) {
            if (is_array($person)) {
                $formatted_person = [];
                foreach ($person as $key => $value) {
                    // Anahtarları ve değerleri baş harfi büyük hale getir
                    $new_key = mb_convert_case(mb_strtolower($key, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
                    
                    if (is_string($value) && !filter_var($value, FILTER_VALIDATE_URL) && !is_numeric($value)) {
                        $new_value = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
                    } else {
                        $new_value = $value;
                    }
                    $formatted_person[$new_key] = $new_value;
                }
                $clean_results[] = $formatted_person;
            }
        }

        echo json_encode([
            "Durum" => "Başarılı",
            "Toplam_Sonuc" => count($clean_results),
            "Veriler" => $clean_results,
            "Telegram" => "https://t.me/Zeldyy_here"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(["Success" => false, "Message" => "Veri formatı uyumsuz.", "Telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
    }
}
?>
