<?php
session_start();

$dataFile = 'apis.json';
$adminPass = "Zeldyhere1+"; // Şifren yerinde kanka

if (!file_exists($dataFile)) file_put_contents($dataFile, json_encode([]));
$apis = json_decode(file_get_contents($dataFile), true);

// 52 İKONLUK TAM HAVUZ
$available_icons = [
    // --- TEMEL VE SOSYAL ---
    'user' => '👤 Kişi / Üye',
    'users' => '👥 Çoklu Kişi / Grup',
    'id-card' => '🪪 Vesika / Kimlik / Pasaport',
    'home' => '🏠 Ev / İkamet / Adres',
    'map-pin' => '📍 Konum / Harita',
    'phone' => '📞 Telefon / GSM',
    'mail' => '✉️ E-Posta / Mail',
    'globe' => '🌐 Dünya / İnternet / Web',
    'camera' => '📷 Fotoğraf Makinesi / Görsel',
    'heart' => '💍 Eş / Yüzük / Evlilik',
    'baby' => '👶 Çocuk / Bebe / Veled',
    'users' => '👨‍👩‍👧‍👦 Aile / Soy ağacı sorgu',

    // --- SAĞLIK VE SİSTEM ---
    'syringe' => '💉 Aşı / Kan / Sağlık',
    'hospital' => '🏥 Hastane / Acil',
    'building-2' => '🏢 Sağlık Kurumu / Kurul / Devlet',
    'pill' => '💊 Eczane / İlaç / Reçete',
    'heart' => '❤️ Kalp / Nabız',

    // --- EĞİTİM VE İŞ ---
    'school' => '🏫 Okul / Üniversite / Mezuniyet',
    'briefcase' => '💼 İş Yeri / Firma / Şirket',
    'gavel' => '🔨 Adliye / Mahkeme / Hukuk',
    'landmark' => '🏛️ Banka / Resmi Daire',
    'tombstone' => '🪦 Mezar / Vefat / Ölüm',

    // --- TEKNOLOJİ VE SİBER ---
    'search' => '🔍 Büyüteç / Arama / Sorgu',
    'database' => '🗄️ Veritabanı / Log / Data',
    'shield' => '🛡️ Güvenlik / Koruma / Bypass',
    'shield-alert' => '⚠️ Güvenlik Uyarısı / Log Hata',
    'key-round' => '🔑 Anahtar / Şifre / Token',
    'lock' => '🔒 Kilitli / Özel',
    'unlock' => '🔓 Açık / Herkese Açık',
    'terminal' => '💻 Terminal / Konsol / API',
    'code-xml' => '👨‍💻 Kod / Script / Yazılım',
    'server' => '🖥️ Sunucu / VDS / Host',
    'cpu' => '🔌 Sistem / İşlemci / Donanım',
    'fingerprint' => '🫵 Parmak İzi / Biometrik',
    'wifi' => '📶 Wi-Fi / Ağ / Bağlantı',

    // --- ARTI, MEDYA VE ARAÇLAR ---
    'car' => '🚗 Araba / Araç / Plaka',
    'plane' => '✈️ Uçak / Pasaport / Seyahat',
    'gamepad-2' => '🎮 Oyun / Discord / Gaming',
    'list' => '📜 Liste / Tablo / Excel',
    'file-text' => '📄 Döküman / Metin / Not',
    'folder' => '📁 Klasör / Arşiv / Kütüphane',
    'download' => '📥 İndirme / Download',
    'upload' => '📤 Yükleme / Upload',
    'link' => '🔗 Bağlantı / Yönlendirme Linki',

    // --- İLLEGAL, SİLAH VE PARA KONSEPTLERİ ---
    'swords' => '⚔️ Silah / Çatışma / Askeri',
    'skull' => '💀 Kuru Kafa / Tehlike / Dead',
    'eye-off' => '👁️‍🗨️ Gizli / Anonim / Sızma',
    'bitcoin' => '🪙 Kripto Para / BTC / Cüzdan',
    'credit-card' => '💳 Kredi Kartı / Ödeme / CC',
    'dollar-sign' => '💵 Dolar / Bakiye / Finans',
    'package' => '📦 Kutu / Kargo / Paket',

    // --- ÖZEL VE PREMIUM İKONLAR ---
    'star' => '⭐ Yıldız / Favori / Popüler',
    'gem' => '💎 Elmas / VIP / Premium'
];

// GİRİŞ KONTROLÜ (InfinityFree Güvenli Yönlendirmeli Sürüm)
if (isset($_POST['login'])) {
    $pass = $_POST['password'] ?? '';
    if ($pass === $adminPass) { 
        $_SESSION['zeldy_admin'] = true; 
        echo '<script>window.location.href = "admin.php";</script>';
        echo '<meta http-equiv="refresh" content="0;url=admin.php">';
        exit; 
    } else { 
        $error = "Hatalı Admin Şifresi!"; 
    }
}

// ÇIKIŞ KONTROLÜ
if (isset($_GET['logout'])) { 
    session_destroy(); 
    echo '<script>window.location.href = "admin.php";</script>';
    echo '<meta http-equiv="refresh" content="0;url=admin.php">';
    exit; 
}

$isAdmin = isset($_SESSION['zeldy_admin']) && $_SESSION['zeldy_admin'] === true;

// API Ekleme
if (isset($_POST['add_api']) && $isAdmin) {
    $name = htmlspecialchars($_POST['api_name']);
    $url = htmlspecialchars($_POST['api_url']);
    $custom_logo = trim($_POST['data_logo'] ?? '');
    if (empty($custom_logo) || !array_key_exists($custom_logo, $available_icons)) $custom_logo = 'package';

    if($name && $url){
        array_push($apis, ['id' => time(), 'name' => $name, 'endpoint' => $url, 'logo' => htmlspecialchars($custom_logo)]);
        file_put_contents($dataFile, json_encode($apis));
        echo '<script>window.location.href = "admin.php";</script>';
        exit;
    }
}

// Sıralama Kaydetme
if (isset($_POST['update_order']) && $isAdmin) {
    $newOrder = $_POST['order'];
    $orderedApis = [];
    foreach ($newOrder as $id) {
        foreach ($apis as $api) {
            if ($api['id'] == $id) { $orderedApis[] = $api; break; }
        }
    }
    file_put_contents($dataFile, json_encode($orderedApis));
    exit('success');
}

// Silme
if (isset($_GET['delete']) && $isAdmin) {
    $id = $_GET['delete'];
    $apis = array_filter($apis, fn($a) => $a['id'] != $id);
    file_put_contents($dataFile, json_encode(array_values($apis)));
    echo '<script>window.location.href = "admin.php";</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeldy API - Yönetim Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { 
            --bg: #030712; /* Derin Gece Siyahı/Mavisi */
            --bg2: #070f2e; /* Koyu Lacivert */
            --p: #00f2fe; /* Neon Cyan */
            --p2: #2563eb; /* Canlı Mavi/Lacivert */
            --ind: #38bdf8; /* Siber Mavi */
            --border: rgba(56,189,248,0.12);
            --text: #f0fdfa;
            --muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            min-height: 100vh; 
            background-image: radial-gradient(circle at top right, rgba(37, 99, 235, 0.15), transparent), radial-gradient(circle at bottom left, rgba(0, 242, 254, 0.12), transparent); 
        }

        .login-wrap { height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { 
            background: rgba(7, 15, 46, 0.55); 
            padding: 50px 40px; 
            border-radius: 24px; 
            width: 90%; 
            max-width: 400px; 
            text-align: center; 
            border: 1px solid var(--border); 
            backdrop-filter: blur(14px); 
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .login-box h1 { font-weight: 800; font-size: 20px; margin-bottom: 30px; color: #fff; letter-spacing: 1px; }

        header { 
            padding: 20px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: rgba(3, 7, 18, 0.7); 
            backdrop-filter: blur(28px) saturate(200%); 
            position: sticky; top: 0; z-index: 100; 
            border-bottom: 1px solid var(--border); 
            gap: 15px; 
        }
        header h2 { 
            font-weight: 800; font-size: 22px; 
            background: linear-gradient(135deg, var(--p), var(--p2)); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
            white-space: nowrap; letter-spacing: 1px;
        }
        
        .header-buttons { display: flex; gap: 10px; align-items: center; flex-shrink: 0; }

        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        
        .api-card { 
            background: rgba(7, 15, 46, 0.55); 
            border-radius: 20px; 
            padding: 20px; 
            margin-bottom: 15px; 
            border: 1px solid rgba(56, 189, 248, 0.08); 
            backdrop-filter: blur(14px); 
            transition: 0.3s; cursor: default; position: relative; 
        }
        .api-card:hover {
            border-color: rgba(56, 189, 248, 0.35);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .sortable-ghost { opacity: 0.4; background: var(--bg2); border-color: var(--ind); }
        
        .data-ready-icon { 
            width: 38px; height: 38px; 
            display: flex; align-items: center; justify-content: center; 
            background: rgba(56, 189, 248, 0.08); 
            border-radius: 10px; 
            border: 1px solid rgba(56, 189, 248, 0.2); 
            color: var(--ind); 
        }
        .drag-handle { cursor: grab; color: var(--muted); margin-right: 15px; font-size: 20px; user-select: none; }
        .admin-item { display: flex; align-items: center; justify-content: space-between; }

        .btn { 
            padding: 11px 20px; border-radius: 11px; border: none; cursor: pointer; 
            font-weight: 700; font-size: 13px; transition: 0.25s; text-decoration: none; 
            display: inline-block; white-space: nowrap; letter-spacing: 0.5px;
        }
        .btn-go { 
            background: linear-gradient(135deg, var(--p2), var(--ind)); 
            color: #fff; 
            box-shadow: 0 4px 15px rgba(37,99,235,0.25);
        }
        .btn-go:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
        
        .btn-del { background: rgba(239, 68, 68, 0.08); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-del:hover { background: #ef4444; color: #fff; box-shadow: 0 4px 15px rgba(239,68,68,0.3); }
        
        .admin-form { 
            background: rgba(7, 15, 46, 0.4); 
            padding: 28px; 
            border-radius: 20px; 
            border: 1px solid var(--border); 
            margin-bottom: 30px; 
            backdrop-filter: blur(14px);
        }
        
        input, select { 
            width: 100%; padding: 14px; margin-bottom: 14px; 
            border-radius: 12px; border: 1px solid rgba(56, 189, 248, 0.1); 
            background: rgba(0, 0, 0, 0.4); color: #fff; outline: none; 
            font-family: inherit; transition: 0.3s;
        }
        input:focus, select:focus {
            border-color: var(--ind);
            box-shadow: 0 0 10px rgba(56,189,248,0.15);
        }
        select option { background: #070f2e; color: #fff; }

        .save-order-btn { background: #10b981; color: #fff; width: 100%; margin-bottom: 20px; display: none; box-shadow: 0 4px 15px rgba(16,185,129,0.25); }
        .save-order-btn:hover { background: #059669; }
        
        .logout { 
            color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600; 
            padding: 10px 18px; background: rgba(239, 68, 68, 0.08); 
            border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.15); 
            white-space: nowrap; transition: 0.3s; 
        }
        .logout:hover { background: rgba(239, 68, 68, 0.2); border-color: rgba(239,68,68,0.4); }
    </style>
</head>
<body>

<?php if (!$isAdmin): ?>
    <div class="login-wrap">
        <form class="login-box" method="POST">
            <h1>ZELDY APİ PANELİ</h1>
            <?php if(isset($error)): ?> <p style="color:#ef4444; font-size:13px; margin-bottom:10px;"><?= $error ?></p> <?php endif; ?>
            <input type="password" name="password" placeholder="Admin Şifresi" required>
            <button type="submit" name="login" class="btn btn-go" style="width:100%; padding: 15px;">PANELİ AÇ</button>
        </form>
    </div>
<?php else: ?>

    <header>
        <h2>Zeldy Yönetim Paneli</h2>
        <div class="header-buttons">
            <a href="index.php" class="logout" style="background:rgba(56, 189, 248, 0.08); border-color:rgba(56, 189, 248, 0.2); color: #38bdf8;">Siteye Git</a>
            <a href="?logout=1" class="logout">Çıkış Yap</a>
        </div>
    </header>

    <div class="container">
        <div class="admin-form">
            <h4 style="margin-bottom:18px; font-size:16px; letter-spacing:0.5px;">Yeni Apiyi Kaydet</h4>
            <form method="POST">
                <input type="text" name="api_name" placeholder="Servis İsmi" required>
                <input type="text" name="api_url" placeholder="URL" required>
                
                <select name="data_logo" required>
                    <option value="" disabled selected>İkon Seçimi Yapın</option>
                    <?php foreach($available_icons as $slug => $label): ?>
                        <option value="<?= $slug ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="add_api" class="btn btn-go" style="width:100%; padding:13px;">KAYDET</button>
            </form>
        </div>

        <button id="saveOrder" class="btn save-order-btn">Sıralamayı Kaydet</button>

        <div id="sortableList">
            <?php foreach ($apis as $api): ?>
                <div class="api-card admin-item" data-id="<?= $api['id'] ?>">
                    <div style="display: flex; align-items: center; flex-grow: 1;">
                        <div class="drag-handle">☰</div>
                        
                        <div style="margin-right: 14px; display:flex; align-items:center;">
                            <div class="data-ready-icon">
                                <i data-lucide="<?= htmlspecialchars($api['logo'] ?? 'package') ?>" style="width:18px; height:18px;"></i>
                            </div>
                        </div>

                        <div>
                            <h3 style="margin:0; font-size:16px; font-weight:600; color:#fff;"><?= $api['name'] ?></h3>
                        </div>
                    </div>
                    <a href="?delete=<?= $api['id'] ?>" class="btn btn-del" onclick="return confirm('Silinsin mi?')">Sil</a>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
            const el = document.getElementById('sortableList');
            const saveBtn = document.getElementById('saveOrder');
            const sortable = Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    saveBtn.style.display = 'block';
                }
            });

            saveBtn.addEventListener('click', () => {
                const order = sortable.toArray();
                const formData = new FormData();
                formData.append('update_order', '1');
                order.forEach(id => formData.append('order[]', id));

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(() => {
                    alert('Sıralama başarıyla güncellendi !');
                    location.reload();
                });
            });
        </script>
    </div>
<?php endif; ?>

<script>
lucide.createIcons();
</script>
</body>
</html>
