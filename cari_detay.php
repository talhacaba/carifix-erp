<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$satis_id = intval($_GET['id'] ?? 0);
if ($satis_id <= 0) { header("Location: satislar.php"); exit; }

$sorgu = $db->prepare("SELECT * FROM satislar WHERE id = ?");
$sorgu->execute([$satis_id]);
$cari = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$cari) { header("Location: satislar.php"); exit; }

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tahsilat_ekle'])) {
    $tarih = $_POST['tarih'];
    $tutar = floatval($_POST['tutar']);
    $aciklama = trim($_POST['aciklama']);

    if ($tutar > 0 && $tutar <= $cari['kalan_borc']) {
        $ekle = $db->prepare("INSERT INTO cari_tahsilatlar (satis_id, tarih, alinan_tutar, aciklama) VALUES (?, ?, ?, ?)");
        $ekle->execute([$satis_id, $tarih, $tutar, $aciklama]);
        $yeni_kalan = $cari['kalan_borc'] - $tutar;
        $yeni_odenen = $cari['odenen_tutar'] + $tutar;
        $yeni_durum = ($yeni_kalan <= 0) ? 'Kapandi' : 'Acik';

        $guncelle = $db->prepare("UPDATE satislar SET odenen_tutar = ?, kalan_borc = ?, durum = ? WHERE id = ?");
        $guncelle->execute([$yeni_odenen, $yeni_kalan, $yeni_durum, $satis_id]);

        header("Location: cari_detay.php?id=" . $satis_id . "&durum=basarili");
        exit;
    } else {
        $hata = "Geçersiz tahsilat tutarı (Kalan borçtan büyük olamaz)!";
    }
}
$tahsilatlar = $db->prepare("SELECT * FROM cari_tahsilatlar WHERE satis_id = ? ORDER BY id DESC");
$tahsilatlar->execute([$satis_id]);
$gecmis_tahsilatlar = $tahsilatlar->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Cari Hesap Ekstresi - <?= htmlspecialchars($cari['musteri_adi']) ?></title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($cari['musteri_adi']) ?> - Cari Hesap Kartı</h2>
                <p class="text-xs text-slate-500">Müşteri ekstresi ve tahsilat yönetim paneli.</p>
            </div>
            <a href="satislar.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition">Geri Dön</a>
        </header>

        <div class="p-10 space-y-8 max-w-6xl mx-auto w-full">
            <?php if(isset($_GET['durum']) && $_GET['durum'] == 'basarili'): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> Tahsilat başarıyla alındı ve cari borçtan düşüldü!</div>
            <?php endif; ?>
            <div class="grid grid-cols-4 gap-6">
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Toplam Satış</span>
                    <span class="text-2xl font-black text-slate-900">₺<?= number_format($cari['toplam_tutar'], 2) ?></span>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Ödenen Tutar</span>
                    <span class="text-2xl font-black text-emerald-600">₺<?= number_format($cari['odenen_tutar'], 2) ?></span>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Kalan Borç</span>
                    <span class="text-2xl font-black text-amber-600">₺<?= number_format($cari['kalan_borc'], 2) ?></span>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-center">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Hesap Durumu</span>
                    <span class="text-sm font-bold <?= $cari['durum'] == 'Acik' ? 'text-amber-600' : 'text-emerald-600' ?>">
                        <?= $cari['durum'] == 'Acik' ? 'Açık Hesap (Veresiyeli)' : 'Hesap Kapandı' ?>
                    </span>
                </div>
            </div>
            <?php if($cari['kalan_borc'] > 0): ?>
            <form method="POST" class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm grid grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tahsilat_ekle" value="1">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tahsilat Tarihi</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Alınan Tutar (TL)</label>
                    <input type="number" step="0.01" max="<?= $cari['kalan_borc'] ?>" name="tutar" placeholder="Örn: 2500" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Açıklama / Ödeme Tipi</label>
                    <input type="text" name="aciklama" placeholder="Elden / Havale" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-emerald-600/20">Tahsilat Al & Borçtan Düş</button>
                </div>
            </form>
            <?php endif; ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-4">Geçmiş Tahsilat / Ödeme Hareketleri</h3>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase">
                            <th class="p-3">Tarih</th><th class="p-3">Alınan Tutar</th><th class="p-3">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(count($gecmis_tahsilatlar) > 0): foreach($gecmis_tahsilatlar as $t): ?>
                        <tr>
                            <td class="p-3 text-slate-700"><?= $t['tarih'] ?></td>
                            <td class="p-3 font-bold text-emerald-600">₺<?= number_format($t['alinan_tutar'], 2) ?></td>
                            <td class="p-3 text-slate-500 text-xs"><?= htmlspecialchars($t['aciklama']) ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="p-4 text-xs text-slate-400 italic">Henüz bu hesaba ait tahsilat kaydı bulunmuyor.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
