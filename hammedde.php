<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$hata = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarih = $_POST['tarih'];
    $tedarikci_adi = trim($_POST['tedarikci_adi']);
    $tonaj = floatval($_POST['tonaj']);
    $birim_fiyat = floatval($_POST['birim_fiyat']);
    $toplam_tutar = $tonaj * $birim_fiyat;
    $aciklama = trim($_POST['aciklama']);
    $fatura_dosya = '';

    if (isset($_FILES['fatura_dosya']) && $_FILES['fatura_dosya']['error'] == 0) {
        $izin_verilen_uzantilar = ['pdf', 'jpg', 'jpeg', 'png'];
        $uzanti = strtolower(pathinfo($_FILES['fatura_dosya']['name'], PATHINFO_EXTENSION));
        if (in_array($uzanti, $izin_verilen_uzantilar)) {
            $hedef_klasor = 'uploads/faturalar/';
            if (!is_dir($hedef_klasor)) mkdir($hedef_klasor, 0777, true);
            $hedef_yol = $hedef_klasor . uniqid() . '.' . $uzanti;
            if (move_uploaded_file($_FILES['fatura_dosya']['tmp_name'], $hedef_yol)) $fatura_dosya = $hedef_yol;
            else $hata = "Dosya yüklenemedi!";
        } else { $hata = "Geçersiz dosya formatı!"; }
    }

    if (empty($hata)) {
        $ekle = $db->prepare("INSERT INTO hammadde_girisleri (tarih, tedarikci_adi, tonaj, birim_fiyat, toplam_tutar, fatura_dosya, aciklama) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $ekle->execute([$tarih, $tedarikci_adi, $tonaj, $birim_fiyat, $toplam_tutar, $fatura_dosya, $aciklama]);
        $depo_guncelle = $db->prepare("UPDATE depo_stok SET mevcut_tonaj = mevcut_tonaj + ? WHERE id = 1");
        $depo_guncelle->execute([$tonaj]);

        header("Location: hammadde.php?durum=basarili");
        exit;
    }
}
$mesaj = (isset($_GET['durum']) && $_GET['durum'] == 'basarili') ? "Hammadde başarıyla kaydedildi ve depoya tonaj eklendi!" : '';

$liste = $db->query("SELECT * FROM hammadde_girisleri ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$depo = $db->query("SELECT * FROM depo_stok LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$anlik_depo = $depo['mevcut_tonaj'] ?? 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hammadde & Fatura - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Hammadde ve Fatura Yönetimi</h2>
                <p class="text-xs text-slate-500">Tonaj bazlı hammadde alımları ve fatura arşivi.</p>
            </div>
            <div class="bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-xl text-indigo-700 text-xs font-bold flex items-center gap-2">
                <i data-lucide="box" class="w-4 h-4"></i> Depodaki Anlık Kağıt: <?= number_format($anlik_depo, 2) ?> Ton
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?><div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div><?php endif; ?>
            <?php if($hata): ?><div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="alert-circle" class="w-5 h-5"></i> <?= $hata ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tarih</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tedarikçi Firma</label>
                    <input type="text" name="tedarikci_adi" placeholder="Örn: Kağıt A.Ş." required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tonaj (Ton)</label>
                    <input type="number" step="0.01" name="tonaj" placeholder="Örn: 1.5" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Birim Fiyat (TL)</label>
                    <input type="number" step="0.01" name="birim_fiyat" placeholder="Ton Başına Fiyat" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Fatura Dosyası (PDF / Resim)</label>
                    <input type="file" name="fatura_dosya" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-600 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Açıklama</label>
                    <input type="text" name="aciklama" placeholder="Notlar..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Hammadde ve Faturayı Kaydet</button>
                </div>
            </form>

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Tedarikçi</th>
                            <th class="p-4">Tonaj</th>
                            <th class="p-4">Toplam Tutar</th>
                            <th class="p-4">Fatura</th>
                            <th class="p-4">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-700"><?= $row['tarih'] ?></td>
                            <td class="p-4 font-bold text-slate-800"><?= $row['tedarikci_adi'] ?></td>
                            <td class="p-4 text-indigo-600 font-semibold"><?= number_format($row['tonaj'], 2) ?> Ton</td>
                            <td class="p-4 text-emerald-600 font-bold">₺<?= number_format($row['toplam_tutar'], 2) ?></td>
                            <td class="p-4">
                                <?php if(!empty($row['fatura_dosya'])): ?>
                                    <a href="<?= $row['fatura_dosya'] ?>" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 hover:underline font-semibold text-xs bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100"><i data-lucide="file-check" class="w-3.5 h-3.5"></i> Görüntüle</a>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs italic">Yok</span>
                                <?php endif; ?>
                            </td>
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
