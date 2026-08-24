<?php
session_start();
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $db->query("SELECT remember_token FROM kullanicilar LIMIT 1");
} catch (PDOException $e) {
    try {
        $db->exec("ALTER TABLE kullanicilar ADD COLUMN remember_token VARCHAR(255) NULL");
    } catch (PDOException $e2) {
        $db->exec("DROP TABLE IF EXISTS kullanicilar");
        $db->exec("CREATE TABLE kullanicilar (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
            sifre VARCHAR(255) NOT NULL,
            ad_soyad VARCHAR(100),
            remember_token VARCHAR(255) NULL
        ) ENGINE=InnoDB");
        $db->exec("INSERT INTO kullanicilar (kullanici_adi, sifre, ad_soyad) VALUES ('admin', '123456', 'Muhammet Talha Caba')");
    }
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['carifix_hatirla'])) {
    $token = $_COOKIE['carifix_hatirla'];
    $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE remember_token = ? LIMIT 1");
    $sorgu->execute([$token]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if ($kullanici) {
        $_SESSION['user_id'] = $kullanici['id'];
        $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
        $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
        header("Location: anasayfa.php");
        exit;
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: anasayfa.php");
    exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $sifre = trim($_POST['sifre']);
    $beni_hatirla = isset($_POST['beni_hatirla']) ? true : false;

    if (empty($kullanici_adi) || empty($sifre)) {
        $hata = "Lütfen kullanıcı adı ve şifrenizi giriniz.";
    } else {
        $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ? LIMIT 1");
        $sorgu->execute([$kullanici_adi]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && $sifre === $kullanici['sifre']) {
            $_SESSION['user_id'] = $kullanici['id'];
            $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
            $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
            
            if ($beni_hatirla) {
                $token = bin2hex(random_bytes(32)); 
                setcookie('carifix_hatirla', $token, time() + (86400 * 30), "/"); 
                $db->prepare("UPDATE kullanicilar SET remember_token = ? WHERE id = ?")->execute([$token, $kullanici['id']]);
            }

            header("Location: anasayfa.php");
            exit;
        } else {
            $hata = "Kullanıcı adı veya şifre hatalı!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sisteme Giriş - CariFix Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen selection:bg-indigo-600 selection:text-white">

    <div class="w-full max-w-md p-8 bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-indigo-500"></div>

        <div class="flex flex-col items-center justify-center mb-8 mt-2">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30 mb-4 transform hover:scale-105 transition">
                <i data-lucide="layers" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">CariFix<span class="text-indigo-600">ERP</span></h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Fabrika Yönetim Paneli</p>
        </div>

        <?php if($hata): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2 animate-pulse">
                <i data-lucide="shield-alert" class="w-5 h-5"></i> <?= $hata ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-slate-700 text-xs font-bold uppercase tracking-wide mb-2">Kullanıcı Adı</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <input type="text" name="kullanici_adi" required autocomplete="off" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm font-medium focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-slate-700 text-xs font-bold uppercase tracking-wide mb-2">Şifre</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <input type="password" name="sifre" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm font-medium focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all">
                </div>
            </div>
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="beni_hatirla" class="w-4 h-4 border-2 border-slate-300 rounded text-indigo-600 focus:ring-indigo-600/20 cursor-pointer transition-all peer">
                    </div>
                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Beni Hatırla</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 mt-2">
                <i data-lucide="log-in" class="w-5 h-5"></i> Sisteme Giriş Yap
            </button>
        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
