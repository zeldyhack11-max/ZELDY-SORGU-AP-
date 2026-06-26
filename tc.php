<?php
header('Content-Type: application/json; charset=utf-8');

$tc = isset($_REQUEST['tc']) ? $_REQUEST['tc'] : '';

if (empty($tc)) {
    echo json_encode(["success" => false, "message" => "TC girin.", "telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
    exit;
}

$target_url = "https://arastir.vip/api/tc.php?tc=" . $tc;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
curl_close($ch);

if (empty($response)) {
    echo json_encode(["success" => false, "message" => "Veri yok.", "telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
} else {
    $raw_data = json_decode($response, true);
    $clean_data = [];

    if (is_array($raw_data)) {
        foreach ($raw_data as $key => $value) {
            // 1. ANAHTARLARI DÜZELT (ADI -> Adı, ANNEADI -> Anne Adı)
            // Önce alt tire ekleyip sonra baş harfleri büyütür
            $new_key = mb_convert_case(mb_strtolower($key, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
            
            // 2. DEĞERLERİ DÜZELT (ABDULSELAM -> Abdulselam)
            if (is_string($value) && !filter_var($value, FILTER_VALIDATE_URL) && !is_numeric($value)) {
                $new_value = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
            } else {
                $new_value = $value;
            }

            $clean_data[$new_key] = $new_value;
        }

        // Senin imzan en alta
        $clean_data['Telegram'] = "https://t.me/Zeldyy_here";
        
        echo json_encode($clean_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(["success" => false, "message" => "Hata.", "telegram" => "https://t.me/Zeldyy_here"], 256 | 128 | 64);
    }
}
?>
