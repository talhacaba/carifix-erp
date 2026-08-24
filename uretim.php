<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarih = $_POST['tarih'];
    $koli_adedi = intval($_POST['koli_adedi']);
    $bardak_adedi = intval($_POST['bardak_adedi']);
    $fire_miktari = intval($_POST['fire_miktari']);
    $notlar = trim($_POST['notlar']);
    $ekle = $db->prepare("INSERT INTO gunluk_uretim (tarih, koli_adedi, bardak_adedi, fire_miktari, notlar) VALUES (?, ?, ?, ?, ?)");
    $ekle->execute([$tarih, $koli_adedi, $bardak_adedi, $fire_miktari, $notlar]);
    $harcanan_tonaj = ($bardak_adedi / 1000) * 0.02;
    $depo_dus = $db->prepare("UPDATE depo_stok SET mevcut_tonaj = mevcut_tonaj - ? WHERE id = 1");
    $depo_dus->execute([$harcanan_tonaj]);
    $mesaj = "Üretim başarıyla kaydedildi ve depodan hammadde düşüldü!";
}

$liste = $db->query("SELECT * FROM gunluk_uretim ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Üretim Takibi - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Günlük Üretim Takibi</h2>
                <p class="text-xs text-slate-500">Fabrika günlük koli ve bardak çıktıları (1 Koli = 3000 Adet).</p>
            </div>
        </header>
        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tarih</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Koli Adedi</label>
                    <input type="number" id="koli_adedi" name="koli_adedi" placeholder="Örn: 10" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600" oninput="hesaplaBardak()">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Toplam Bardak (Otomatik)</label>
                    <input type="number" id="bardak_adedi" name="bardak_adedi" required class="w-full bg-indigo-50/50 border border-indigo-200 text-indigo-900 font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Fire Miktarı</label>
                    <input type="number" name="fire_miktari" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Üretim Ekle</button>
                </div>
                <div class="col-span-5">
                    <input type="text" name="notlar" placeholder="Notlar veya vardiya açıklaması..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
            </form>

            <!-- Liste -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Koli Adedi</th>
                            <th class="p-4">Bardak Adedi</th>
                            <th class="p-4">Fire</th>
                            <th class="p-4">Notlar</th>
                            <th class="p-4">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 font-medium text-slate-700"><?= $row['tarih'] ?></td>
                            <td class="p-4 text-slate-600"><?= number_format($row['koli_adedi']) ?> koli</td>
                            <td class="p-4 font-bold text-indigo-600"><?= number_format($row['bardak_adedi']) ?> adet</td>
                            <td class="p-4 text-red-600 font-medium"><?= number_format($row['fire_miktari']) ?></td>
                            <td class="p-4 text-slate-500 text-xs"><?= htmlspecialchars($row['notlar']) ?></td>
                            <td class="p-4"><span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Tamamlandı</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <script>
        lucide.createIcons();
        function hesaplaBardak() {
            const koli = document.getElementById('koli_adedi').value;
            const bardakInput = document.getElementById('bardak_adedi');
            
            if (koli && !isNaN(koli)) {
                bardakInput.value = koli * 3000;
            } else {
                bardakInput.value = '';
            }
        }
    </script>
</body>
</html>
