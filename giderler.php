<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarih = $_POST['tarih'];
    $gider_turu = trim($_POST['gider_turu']);
    $tutar = floatval($_POST['tutar']);
    $vade_tarihi = $_POST['vade_tarihi'];
    $tekrar_tipi = $_POST['tekrar_tipi'];
    $aciklama = trim($_POST['aciklama']);

    $ekle = $db->prepare("INSERT INTO giderler (tarih, gider_turu, tutar, vade_tarihi, tekrar_tipi, aciklama) VALUES (?, ?, ?, ?, ?, ?)");
    $ekle->execute([$tarih, $gider_turu, $tutar, $vade_tarihi, $tekrar_tipi, $aciklama]);
    $mesaj = "Gider / Vergi kaydı başarıyla eklendi!";
}

$liste = $db->query("SELECT * FROM giderler ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giderler & Vergi - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Giderler, Vergiler ve Sabit Ödemeler</h2>
                <p class="text-xs text-slate-500">Kira, elektrik, muhasebe ve vergi ödemeleri takibi.</p>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?><div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div><?php endif; ?>

            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">İşlem Tarihi</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Gider Türü</label>
                    <select name="gider_turu" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="Kira">İşyeri Kirası</option>
                        <option value="Elektrik">Elektrik Faturası</option>
                        <option value="Su">Su Faturası</option>
                        <option value="KDV">KDV Ödemesi</option>
                        <option value="Muhtasar">Muhtasar Vergisi</option>
                        <option value="Kurumlar">Kurumlar Vergisi</option>
                        <option value="Muhasebe">Muhasebeci Ücreti</option>
                        <option value="Diger">Diğer Masraf</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tutar (TL)</label>
                    <input type="number" step="0.01" name="tutar" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ödeme / Vade Tarihi</label>
                    <input type="date" name="vade_tarihi" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Periyot / Tekrar</label>
                    <select name="tekrar_tipi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="Birinci">Tek Seferlik</option>
                        <option value="Aylik">Her Ay (Düzenli)</option>
                        <option value="Yillik">Her Yıl (Düzenli)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Açıklama</label>
                    <input type="text" name="aciklama" placeholder="Notlar..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Gideri Kaydet</button>
                </div>
            </form>

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Gider Türü</th>
                            <th class="p-4">Tutar</th>
                            <th class="p-4">Vade / Ödeme</th>
                            <th class="p-4">Periyot</th>
                            <th class="p-4">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-700"><?= $row['tarih'] ?></td>
                            <td class="p-4 font-bold text-indigo-600"><?= $row['gider_turu'] ?></td>
                            <td class="p-4 text-red-600 font-extrabold">₺<?= number_format($row['tutar'], 2) ?></td>
                            <td class="p-4 text-slate-600 text-xs"><?= $row['vade_tarihi'] ?></td>
                            <td class="p-4 text-slate-500 text-xs"><?= $row['tekrar_tipi'] ?></td>
                            <td class="p-4 text-slate-500 text-xs"><?= $row['aciklama'] ?></td>
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
