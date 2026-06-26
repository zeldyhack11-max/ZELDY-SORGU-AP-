<?php
header('Content-Type: application/json; charset=utf-8');

$ara = $_GET['ara'] ?? '';

if (!$ara) {
    echo json_encode(['status' => 'error', 'message' => 'Lütfen ara=... parametresi girin.']);
    exit;
}

// 1. Hedef URL (İlçe desteği kaynak sitenin insiyatifindedir)
$url = "http://zeldysorguapi.fwh.is/eczane.php?ara=" . urlencode($ara);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt'); // Render'da temp dizini
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$html = curl_exec($ch);

// 2. JavaScript Engelini Aşma (Eğer varsa)
if (strpos($html, 'slowAES.decrypt') !== false) {
    preg_match('/a=toNumbers\("([a-f0-9]+)"\)/', $html, $a);
    preg_match('/b=toNumbers\("([a-f0-9]+)"\)/', $html, $b);
    preg_match('/c=toNumbers\("([a-f0-9]+)"\)/', $html, $c);

    if (isset($a[1], $b[1], $c[1])) {
        $key = hex2bin($a[1]);
        $iv = hex2bin($b[1]);
        $ct = hex2bin($c[1]);

        $decrypted = openssl_decrypt($ct, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        $cookie_val = bin2hex($decrypted);
        
        // Çerezi set edip tekrar isteği gönder
        curl_setopt($ch, CURLOPT_COOKIE, "__test=" . $cookie_val);
        $html = curl_exec($ch);
    }
}

curl_close($ch);

// Çıktıyı ver
echo $html; 
?>
