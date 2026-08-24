<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['satis_ekle'])) {
    $tarih = $_POST['tarih'];
    $musteri_id = intval($_POST['musteri_id']);
    $urun_id = intval($_POST['urun_id']);
    $koli_adedi = intval($_POST['koli_adedi']);
    $toplam_tutar = floatval($_POST['toplam_tutar']);
    $odenen_tutar = floatval($_POST['odenen_tutar']);
    $vade_tarihi = $_POST['vade_tarihi'];
    $kalan_borc = $toplam_tutar - $odenen_tutar;
    $durum = ($kalan_borc <= 0) ? 'Kapandi' : 'Acik';
    $ekle = $db->prepare("INSERT INTO satislar (tarih, musteri_id, urun_id, koli_adedi, toplam_tutar, odenen_tutar, kalan_borc, vade_tarihi, durum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ekle->execute([$tarih, $musteri_id, $urun_id, $koli_adedi, $toplam_tutar, $odenen_tutar, $kalan_borc, $vade_tarihi, $durum]);
    
    header("Location: satislar.php?durum=basarili");
    exit;
}

$mesaj = (isset($_GET['durum']) && $_GET['durum'] == 'basarili') ? "Satış başarıyla kaydedildi!" : '';
$musteriler = $db->query("SELECT * FROM musteriler ORDER BY firma_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
$urunler = $db->query("SELECT * FROM urunler ORDER BY urun_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
$liste = $db->query("SELECT s.*, m.firma_adi, m.telefon, u.urun_adi, u.ebat FROM satislar s LEFT JOIN musteriler m ON s.musteri_id = m.id LEFT JOIN urunler u ON s.urun_id = u.id ORDER BY s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satış & Veresiye - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Satış ve Veresiye Takibi</h2>
                <p class="text-xs text-slate-500">Katalogdan otomatik fiyatlandırmalı satış ekranı.</p>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div>
            <?php endif; ?>

            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-4 gap-4">
                <input type="hidden" name="satis_ekle" value="1">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tarih</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Müşteri Seç</label>
                    <select name="musteri_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="">Müşteri Seçiniz...</option>
                        <?php foreach($musteriler as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['firma_adi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ürün Seç (Otomatik Fiyat)</label>
                    <select name="urun_id" id="urun_secici" onchange="fiyatHesapla()" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="" data-fiyat="0">Ürün Seçiniz...</option>
                        <?php foreach($urunler as $u): ?>
                            <!-- data-fiyat ile JavaScript'e ürünün koli fiyatını yolluyoruz -->
                            <option value="<?= $u['id'] ?>" data-fiyat="<?= $u['varsayilan_fiyat'] ?>">
                                <?= htmlspecialchars($u['urun_adi']) ?> (<?= htmlspecialchars($u['ebat']) ?>) - ₺<?= number_format($u['varsayilan_fiyat'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Koli Adedi</label>
                    <input type="number" name="koli_adedi" id="koli_girdisi" oninput="fiyatHesapla()" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Toplam Tutar (TL)</label>
                    <!-- JavaScript burayı otomatik dolduracak, ama istenirse elle de değiştirilebilir (İndirim vs.) -->
                    <input type="number" step="0.01" name="toplam_tutar" id="toplam_tutar_kutusu" required class="w-full bg-indigo-50 border border-indigo-200 text-indigo-900 font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Ödenen Tutar (Peşin)</label>
                    <input type="number" step="0.01" name="odenen_tutar" value="0" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Vade Tarihi</label>
                    <input type="date" name="vade_tarihi" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Satışı Kaydet</button>
                </div>
            </form>

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Müşteri (Cari Kart)</th>
                            <th class="p-4">Ürün & Ebat</th>
                            <th class="p-4">Toplam</th>
                            <th class="p-4">Kalan Borç</th>
                            <th class="p-4">Vade</th>
                            <th class="p-4">Durum</th>
                            <th class="p-4 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-700"><?= $row['tarih'] ?></td>
                            <td class="p-4 font-bold text-slate-800">
                                <a href="cari_detay.php?id=<?= $row['id'] ?>" class="text-indigo-600 hover:underline flex items-center gap-1.5">
                                    <i data-lucide="user-round" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($row['firma_adi'] ?? 'Bilinmeyen Müşteri') ?>
                                </a>
                                <span class="text-[11px] font-normal text-slate-400"><?= $row['telefon'] ?></span>
                            </td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($row['urun_adi'] ?? 'Ürün') ?> <span class="text-xs text-indigo-600 font-semibold">(<?= htmlspecialchars($row['ebat']) ?>)</span> <span class="text-xs text-slate-400">[<?= $row['koli_adedi'] ?> koli]</span></td>
                            <td class="p-4 text-emerald-600 font-semibold">₺<?= number_format($row['toplam_tutar'], 2) ?></td>
                            <td class="p-4 font-extrabold <?= $row['kalan_borc'] > 0 ? 'text-amber-600' : 'text-slate-400' ?>">₺<?= number_format($row['kalan_borc'], 2) ?></td>
                            <td class="p-4 text-slate-600 text-xs"><?= $row['vade_tarihi'] ?></td>
                            <td class="p-4">
                                <?php if($row['durum'] == 'Acik'): ?>
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Veresiyeli</span>
                                <?php else: ?>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Kapandı</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right">
                                <a href="cari_detay.php?id=<?= $row['id'] ?>" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 px-3 py-1.5 rounded-xl text-xs font-bold transition inline-flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detay
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        function fiyatHesapla() {
            var urunSelect = document.getElementById('urun_secici');
            var seciliOption = urunSelect.options[urunSelect.selectedIndex];
            var birimFiyat = parseFloat(seciliOption.getAttribute('data-fiyat')) || 0;
            var koliAdedi = parseInt(document.getElementById('koli_girdisi').value) || 0;
            var toplam = birimFiyat * koliAdedi;
            
            if (toplam > 0) {
                document.getElementById('toplam_tutar_kutusu').value = toplam.toFixed(2);
            } else {
                document.getElementById('toplam_tutar_kutusu').value = '';
            }
        }
    </script>
</body>
</html>
