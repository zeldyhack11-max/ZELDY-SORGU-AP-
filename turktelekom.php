<?php
header('Content-Type: application/json; charset=utf-8');

// Numarayı sadece rakamlara temizlemek için regex yerine hızlı yöntem
$arananNumara = isset($_GET['numara']) ? str_replace([' ', '-', '(', ')'], '', $_GET['numara']) : '';

if (empty($arananNumara)) {
    echo json_encode(['durum' => false, 'mesaj' => 'Lütfen bir numara gönderin.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dosyaYolu = 'telekom.txt';

if (!file_exists($dosyaYolu)) {
    echo json_encode(['durum' => false, 'mesaj' => 'telekom.txt bulunamadı.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$handle = fopen($dosyaYolu, "r");
$bulunanSatir = null;

if ($handle) {
    while (($satir = fgets($handle)) !== false) {
        // İşlemciyi yoran regex yerine hızlı boşluk temizleme
        $temizSatir = str_replace(' ', '', $satir);
        
        if (strpos($temizSatir, $arananNumara) !== false) {
            $bulunanSatir = trim($satir);
            break; 
        }
    }
    fclose($handle);
}

if ($bulunanSatir) {
    // İşlemci dostu parçalama (Boşluklara göre ayırıyoruz)
    $parcalar = explode(' ', $bulunanSatir);
    
    // Sabit formatımıza göre diziyoruz: Egemen(0) Kutay(1) 553(2) 984(3) 85(4) 00(5) TRABZON(6) GeriKalan(Adres)
    if (count($parcalar) >= 7) {
        $ad      = $parcalar[0];
        $soyad   = $parcalar[1];
        $telefon = $parcalar[2] . ' ' . $parcalar[3] . ' ' . $parcalar[4] . ' ' . $parcalar[5];
        $sehir   = $parcalar[6];
        
        // Şehirden sonrasını birleştirip adres yapıyoruz
        $adresParcalari = array_slice($parcalar, 7);
        $adresDetay = implode(' ', $adresParcalari);

        $formatliVeri = [
            'Ad' => $ad,
            'Soyad' => $soyad,
            'Telefon' => $telefon,
            'Şehir' => $sehir,
            'Adres' => $adresDetay,
            'telegram' => 't.me/Zeldy_here'
        ];

        echo json_encode($formatliVeri, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } else {
        // Eğer satır beklenen yapıda değilse direkt düz basın, CPU harcamasın
        echo json_encode([
            'veri' => $bulunanSatir,
            'telegram' => 't.me/Zeldy_here'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
} else {
    echo json_encode(['durum' => false, 'mesaj' => 'Numara bulunamadı.'], JSON_UNESCAPED_UNICODE);
}
