<?php
header('Content-Type: application/json; charset=utf-8');

function get_data_with_bypass($ara) {
    $url = "http://zeldysorguapi.fwh.is/eczane.php?ara=" . urlencode($ara);
    $cookie_file = tempnam(sys_get_temp_dir(), 'cookie');

    // 1. İlk istek: Challenge sayfasını al
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    $html = curl_exec($ch);
    
    // 2. JavaScript engelini aşma (AES çözme)
    if (strpos($html, 'slowAES.decrypt') !== false) {
        // Regex ile değerleri çek
        preg_match('/a=toNumbers\("([a-f0-9]+)"\)/', $html, $a);
        preg_match('/b=toNumbers\("([a-f0-9]+)"\)/', $html, $b);
        preg_match('/c=toNumbers\("([a-f0-9]+)"\)/', $html, $c);

        if (isset($a[1], $b[1], $c[1])) {
            // PHP'de AES şifresini çözmek için openssl kullanılır
            $key = hex2bin($a[1]);
            $iv = hex2bin($b[1]);
            $ct = hex2bin($c[1]);

            $decrypted = openssl_decrypt($ct, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
            
            // Cookie değerini ayarla
            $cookie_val = bin2hex($decrypted);
            
            // 3. İkinci istek: Cookie ile gerçek veriyi çek
            curl_setopt($ch, CURLOPT_COOKIE, "__test=" . $cookie_val);
            $html = curl_exec($ch);
        }
    }
    
    curl_close($ch);
    unlink($cookie_file);
    return $html;
}

$ara = $_GET['ara'] ?? '';
if ($ara) {
    echo get_data_with_bypass($ara);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parametre eksik.']);
}
?>
