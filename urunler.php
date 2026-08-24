<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['urun_ekle'])) {
    $urun_adi = trim($_POST['urun_adi']);
    $ebat = trim($_POST['ebat']);
    $koli_ici_adet = intval($_POST['koli_ici_adet']);
    $varsayilan_fiyat = floatval($_POST['varsayilan_fiyat']);

    $ekle = $db->prepare("INSERT INTO urunler (urun_adi, ebat, koli_ici_adet, varsayilan_fiyat) VALUES (?, ?, ?, ?)");
    $ekle->execute([$urun_adi, $ebat, $koli_ici_adet, $varsayilan_fiyat]);

    header("Location: urunler.php?durum=basarili");
    exit;
}
$mesaj = (isset($_GET['durum']) && $_GET['durum'] == 'basarili') ? "Ürün başarıyla kataloğa eklendi!" : '';
$liste = $db->query("SELECT * FROM urunler ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi - CariFix Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 flex h-screen overflow-hidden">
    
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 px-10 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Ürün ve Çeşit Yönetimi</h2>
                <p class="text-xs text-slate-500">Fabrikada üretilen bardak ve koli çeşitleri.</p>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div>
            <?php endif; ?>
            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-5 gap-4 items-end">
                <input type="hidden" name="urun_ekle" value="1">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ürün Adı</label>
                    <input type="text" name="urun_adi" placeholder="Örn: Karton Bardak" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ebat / Özellik</label>
                    <input type="text" name="ebat" placeholder="Örn: 7 oz" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Koli İçi Adet</label>
                    <input type="number" name="koli_ici_adet" value="3000" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Varsayılan Koli Fiyatı (TL)</label>
                    <input type="number" step="0.01" name="varsayilan_fiyat" placeholder="0.00" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Ürün Ekle</button>
                </div>
            </form>

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Ürün Adı</th>
                            <th class="p-4">Ebat</th>
                            <th class="p-4">Koli İçi Adet</th>
                            <th class="p-4">Varsayılan Koli Fiyatı</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $u): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($u['urun_adi']) ?></td>
                            <td class="p-4 text-indigo-600 font-semibold"><?= htmlspecialchars($u['ebat']) ?></td>
                            <td class="p-4 text-slate-600 font-medium"><?= number_format($u['koli_ici_adet']) ?> Adet</td>
                            <td class="p-4 text-emerald-600 font-bold">₺<?= number_format($u['varsayilan_fiyat'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
