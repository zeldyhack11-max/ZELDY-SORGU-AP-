<?php
header('Content-Type: application/json; charset=utf-8');

$aranan = isset($_GET['sorgu']) ? preg_replace('/\D/', '', $_GET['sorgu']) : '';

if (empty($aranan)) {
    echo json_encode(['durum' => false, 'mesaj' => 'Lütfen sorgulamak için bir veri girin.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dosyaYolu = '10kturknet.sql';

if (!file_exists($dosyaYolu)) {
    echo json_encode(['durum' => false, 'mesaj' => '10kturknet.sql dosyası bulunamadı.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Bütün sütun isimlerini sırasıyla bir diziye alıyoruz kanka
$sutunlar = [
    "Id", "ContactId", "MUSTERI_ID", "DONDURMA_ID", "PlSegmentId", "DEVREADRESID", 
    "ABONE_ADRES_TESIS_ADRESID", "ABONE_ADRES_YERLESIM_ADRESID", "AD", "ENLEM", "SOYAD", 
    "UNVAN", "BOYLAM", "HAT_NO", "UPLOAD", "DOWNLOAD", "IsActive", "Username", "VERGI_NO", 
    "CreatedBy", "HIZMET_NO", "STATIK_IP", "TC_KIMLIK", "UpdatedBy", "HIZMETTIPI", "ABONE_BITIS", 
    "ABONE_UYRUK", "CreatedDate", "UpdatedDate", "ABONE_TARIFE", "MUSTERI_TURU", "ABONE_ANA_ADI", 
    "KULLANICI_ADI", "ABONE_BABA_ADI", "ABONE_BASLANGIC", "HIZMET_NUMARASI", "ISS_POP_BILGISI", 
    "ABONE_DOGUM_YERI", "MUSTERI_ALT_TURU", "MUSTERI_NUMARASI", "SEGMENT_ACIKLAMA", "TC_TELEKOM_DURUM", 
    "ABONE_KIMLIK_TIPI", "ABONE_PASAPORT_NO", "ABONE_DOGUM_TARIHI", "HIZMET_DURUM_DETAY", 
    "AKTIVASYON_BAYI_ADI", "ABONE_ADRES_TESIS_IL", "ABONE_KIMLIK_SERI_NO", "AKTIVASYON_KULLANICI", 
    "MUSTERI_HAREKET_KODU", "ABONE_ADRES_TESIS_BBK", "ABONE_MERSIS_NUMARASI", "ABONE_ADRES_TESIS_ILCE", 
    "AKTIVASYON_BAYI_ADRESI", "MUSTERI_HAREKET_ZAMANI", "ABONE_ADRES_TESIS_CADDE", "ABONE_ADRES_YERLESIM_IL", 
    "ABONE_ADRES_YERLESIM_BBK", "MUSTERI_HAREKET_ACIKLAMA", "ABONE_ADRES_TESIS_MAHALLE", 
    "ABONE_ADRES_YERLESIM_ILCE", "ABONE_ADRES_YERLESIM_CADDE", "ABONE_ADRES_TESIS_ADRESTIPI", 
    "ABONE_ADRES_TESIS_ADRES_KODU", "ABONE_ADRES_TESIS_IC_KAPI_NO", "ABONE_ADRES_TESIS_POSTA_KODU", 
    "ABONE_ADRES_YERLESIM_MAHALLE", "ABONE_ADRES_TESIS_DIS_KAPI_NO", "ABONE_ADRES_YERLESIM_ADRESTIPI", 
    "ABONE_ADRES_YERLESIM_ADRES_KODU", "ABONE_ADRES_YERLESIM_IC_KAPI_NO", "ABONE_ADRES_YERLESIM_POSTA_KODU", 
    "ABONE_ADRES_YERLESIM_DIS_KAPI_NO"
];

$handle = fopen($dosyaYolu, "r");
$bulunanSatir = null;

if ($handle) {
    while (($satir = fgets($handle)) !== false) {
        if (strpos($satir, $aranan) !== false) {
            $veriParcalari = str_getcsv($satir, ",");
            
            // TC_KIMLIK (Index 22) veya HAT_NO (Index 13) veya MUSTERI_ID (Index 2) eşleşiyor mu kontrol et
            if (
                (isset($veriParcalari[22]) && trim($veriParcalari[22]) === $aranan) || 
                (isset($veriParcalari[13]) && trim($veriParcalari[13]) === $aranan) ||
                (isset($veriParcalari[2]) && trim($veriParcalari[2]) === $aranan)
            ) {
                $bulunanSatir = $veriParcalari;
                break;
            }
        }
    }
    fclose($handle);
}

if ($bulunanSatir) {
    $formatliVeri = [];
    
    // Bütün sütunları sırasıyla eşleştirip yeni diziye atıyoruz
    foreach ($sutunlar as $index => $sutunAdi) {
        $formatliVeri[$sutunAdi] = isset($bulunanSatir[$index]) ? trim($bulunanSatir[$index]) : "NULL";
    }
    
    // İstediğin telegram adresini en sona ekliyoruz kanka
    $formatliVeri['telegram'] = 't.me/Zeldy_here';

    echo json_encode($formatliVeri, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} else {
    echo json_encode(['durum' => false, 'mesaj' => 'Kayıt bulunamadı.'], JSON_UNESCAPED_UNICODE);
}
