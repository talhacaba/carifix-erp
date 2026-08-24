<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['personel_ekle'])) {
    $ad_soyad = trim($_POST['ad_soyad']);
    $gorev = trim($_POST['gorev']);
    $telefon = trim($_POST['telefon']);
    $maas = floatval($_POST['maas']);
    $tarih = $_POST['tarih'];
    $ekle = $db->prepare("INSERT INTO personel (ad_soyad, gorev, telefon, maas, ise_baslama_tarihi) VALUES (?, ?, ?, ?, ?)");
    $ekle->execute([$ad_soyad, $gorev, $telefon, $maas, $tarih]);
    header("Location: personel.php?durum=basarili");
    exit;
}

$personeller = $db->query("SELECT * FROM personel ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personel ve Maaş Takibi - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Fabrika Personel ve Maaş Yönetimi</h2>
                <p class="text-xs text-slate-500">Çalışan kadrosu ve bordro takibi.</p>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if(isset($_GET['durum']) && $_GET['durum'] == 'basarili'): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> Personel başarıyla kaydedildi!</div>
            <?php endif; ?>
            <form method="POST" class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm grid grid-cols-5 gap-4 items-end">
                <input type="hidden" name="personel_ekle" value="1">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ad Soyad</label>
                    <input type="text" name="ad_soyad" placeholder="Personel Adı" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Görevi / Pozisyonu</label>
                    <input type="text" name="gorev" placeholder="Örn: Makine Operatörü" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Telefon</label>
                    <input type="text" name="telefon" placeholder="0555..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Aylık Maaş (TL)</label>
                    <input type="number" step="0.01" name="maas" placeholder="Net Maaş" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">İşe Başlama Tarihi</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div class="col-span-5">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Personeli Kaydet</button>
                </div>
            </form>
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase">
                            <th class="p-4">Ad Soyad</th><th class="p-4">Görev</th><th class="p-4">Telefon</th><th class="p-4">Aylık Maaş</th><th class="p-4">İşe Giriş</th><th class="p-4">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($personeller as $p): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($p['ad_soyad']) ?></td>
                            <td class="p-4 text-indigo-600 font-semibold"><?= htmlspecialchars($p['gorev']) ?></td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($p['telefon']) ?></td>
                            <td class="p-4 font-extrabold text-emerald-600">₺<?= number_format($p['maas'], 2) ?></td>
                            <td class="p-4 text-slate-500 text-xs"><?= $p['ise_baslama_tarihi'] ?></td>
                            <td class="p-4"><span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Aktif</span></td>
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
